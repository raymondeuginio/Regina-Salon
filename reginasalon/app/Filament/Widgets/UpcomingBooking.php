<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Booking;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Select;

class UpcomingBooking extends TableWidget
{
    protected static ?string $heading = 'Upcoming Bookings';
    protected static ?int $sort = 2;

    protected int | string | array  $columnSpan = 'full';

    public function table(Table $table): Table
    {

        return $table
            ->query(fn(): Builder => Booking::query()
                ->with(['user', 'services'])
                ->where('booking_date', '>=', now()->toDateString())
                ->orderBy('booking_date', 'asc'))
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer Name')
                    ->searchable(),
                TextColumn::make('services_list')
                    ->label('Services')
                    ->wrap()
                    ->limit(25)
                    ->getStateUsing(function ($record) {
                        if (!$record->services || $record->services->isEmpty()) {
                            return 'No services';
                        }

                        return $record->services
                            ->pluck('name')
                            ->join(', ');
                    })
                    ->tooltip(function ($record) {
                        if (!$record->services || $record->services->isEmpty()) {
                            return 'No services booked';
                        }

                        return $record->services->map(function ($service) {
                            return $service->name . ', ';
                        })->join("\n");
                    }),
                TextColumn::make('total_price')
                    ->label('Total Price')
                    ->money('IDR', locale: 'id')
                    ->default(0),
                TextColumn::make('booking_time')
                    ->label('Time')
                    ->dateTime('H:i')
                    ->suffix(' WIB'),
                TextColumn::make('booking_date')
                    ->date('D, M j, Y'),
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'completed' => 'primary',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    }),
            ])
            ->emptyStateHeading('No Bookings')
            ->emptyStateDescription('There are no bookings scheduled.')
            ->emptyStateIcon('heroicon-o-calendar')
            ->filters([
                // Filter untuk status booking
                Filter::make('date_filter')
                    ->form([
                        Select::make('type')
                            ->label('Date Filter')
                            ->options([
                                'today' => 'Today',
                                'tomorrow' => 'Tomorrow',
                                'week' => 'This Week',
                                'all' => 'All Upcoming',
                                'custom' => 'Select Date',
                            ])
                            ->default('today')
                            ->live()
                            ->reactive(),
                        DatePicker::make('date')
                            ->label('Select Date')
                            ->native(false)
                            ->visible(fn($get) => $get('type') === 'custom')
                            ->required(fn($get) => $get('type') === 'custom'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $type = $data['type'] ?? 'today';

                        return match ($type) {
                            'today' => $query->whereDate('booking_date', today()),
                            'tomorrow' => $query->whereDate('booking_date', today()->addDay()),
                            'week' => $query->whereBetween('booking_date', [
                                today(),
                                today()->addWeek(),
                            ]),
                            'all' => $query->where('booking_date', '>=', today()),
                            'custom' => $query->when(
                                $data['date'] ?? null,
                                fn(Builder $q, $date) => $q->whereDate('booking_date', $date)
                            ),
                            default => $query,
                        };
                    })
                    ->indicateUsing(function (array $data): ?string {
                        $type = $data['type'] ?? null;

                        if ($type === 'custom' && isset($data['date'])) {
                            return 'Date: ' . Carbon::parse($data['date'])->format('d M, Y');
                        }

                        return match ($type) {
                            'today' => 'Today',
                            'tomorrow' => 'Tomorrow',
                            'week' => 'This Week',
                            'all' => 'All Upcoming',
                            default => null,
                        };
                    }),

            ])
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->headerActions([
                //
                Action::make('refresh')
                    ->label('Refresh')
                    ->icon('heroicon-o-arrow-path')
                    ->action(fn() => $this->resetTable())
                    ->color('gray'),
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ])
            ->defaultSort('booking_date', 'asc');
    }
}
