<?php

namespace App\Filament\Resources\Bookings\Tables;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('services_list')
                    ->label('Services(s)')
                    ->getStateUsing(function ($record) {
                        if (!$record->bookingItems || $record->bookingItems->isEmpty()) {
                            return 'No services';
                        }

                        return $record->bookingItems->map(function ($item, $index) {
                            return ($index + 1) . ". " . $item->service->name;
                        })->join("\n");
                    })
                    ->html()
                    ->formatStateUsing(fn($state) => nl2br($state))
                    ->tooltip(function ($record) {
                        if (!$record->bookingItems || $record->bookingItems->isEmpty()) {
                            return 'No services booked';
                        }

                        return $record->bookingItems->map(function ($item, $index) {
                            return ($index + 1) . ". " . $item->service->name;
                        })->join("\n");
                    }),
                TextColumn::make('staff_list')
                    ->label('Staff(s)')
                    ->getStateUsing(function ($record) {
                        if (!$record->bookingItems || $record->bookingItems->isEmpty()) {
                            return 'No staff assigned';
                        }

                        return $record->bookingItems->map(function ($item, $index) {
                            $staffName = $item->staff ? $item->staff->name : 'Unassigned';
                            return ($index + 1) . ". " . $staffName;
                        })->join("\n");
                    })
                    ->html()
                    ->formatStateUsing(fn($state) => nl2br($state))
                    ->tooltip(function ($record) {
                        if (!$record->bookingItems || $record->bookingItems->isEmpty()) {
                            return 'No staff assigned';
                        }

                        return $record->bookingItems->map(function ($item, $index) {
                            $staffName = $item->staff ? $item->staff->name : 'Unassigned';
                            $serviceName = $item->service->name;

                            return ($index + 1) . ". " . $staffName . " - " . $serviceName;
                        })->join("\n");
                    }),
                TextColumn::make('price_list')
                    ->label('Price(s)')
                    ->getStateUsing(function ($record) {
                        if (!$record->bookingItems || $record->bookingItems->isEmpty()) {
                            return 'No prices';
                        }

                        return $record->bookingItems->map(function ($item, $index) {
                            $price = number_format($item->price ?? 0, 0, ',', '.');
                            return ($index + 1) . ". Rp " . $price;
                        })->join("\n");
                    })
                    ->html()
                    ->formatStateUsing(fn($state) => nl2br($state))
                    ->tooltip(function ($record) {
                        if (!$record->bookingItems || $record->bookingItems->isEmpty()) {
                            return 'No prices';
                        }

                        return $record->bookingItems->map(function ($item, $index) {
                            $price = number_format($item->price ?? 0, 0, ',', '.');
                            $serviceName = $item->service->name;
                            return ($index + 1) . ". " . $serviceName . " - Rp " . $price;
                        })->join("\n");
                    }),
                TextColumn::make('total_price')
                    ->label('Total Price')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state ?? 0, 0, ',', '.'))
                    ->default(0)
                    ->sortable(),
                TextColumn::make('booking_date')
                    ->label('Date')
                    ->date('D, M j, Y')
                    ->sortable(),
                TextColumn::make('booking_time')
                    ->label('Time')
                    ->dateTime('H:i')
                    ->suffix(' WIB')
                    ->sortable(),
                SelectColumn::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->selectablePlaceholder(false),
            ])
            ->filters([
                //
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Filter::make('booking_date')
                    ->form([
                        DatePicker::make('from')
                            ->label('Start Date')
                            ->maxDate(fn($get) => $get('until') ?: now())
                            ->reactive(),
                        DatePicker::make('until')
                            ->label('End Date')
                            ->minDate(fn($get) => $get('from'))
                            ->reactive(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('booking_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('booking_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators[] = 'From: ' . Carbon::parse($data['from'])->format('d M Y');
                        }

                        if ($data['until'] ?? null) {
                            $indicators[] = 'Until: ' . Carbon::parse($data['until'])->format('d M Y');
                        }

                        return $indicators;
                    }),
            ])
            ->emptyStateHeading('No Bookings')
            ->emptyStateDescription('There are no appointment.')
            ->emptyStateIcon('heroicon-o-calendar')
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
