<?php

namespace App\Filament\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Staff;
use function Laravel\Prompts\search;

class StaffRanking extends TableWidget
{
    protected static ?string $heading = 'Staff Ranking';

    protected static ?int $sort = 5;
    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Staff::query())
            ->columns([
                TextColumn::make('no')
                    ->rowIndex()
                    ->label('No'),
                TextColumn::make('name')
                    ->label('Staff Name')
                    ->searchable(),
                TextColumn::make('bookings_count')
                    ->label('Total Bookings')
                    ->counts('bookings')
                    ->sortable()
                    ->default(0),
            ])
            ->defaultPaginationPageOption(5)
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ])
            ->emptyStateHeading('No Staff Found')
            ->emptyStateDescription('Please add some staff to see the ranking.')
            ->emptyStateIcon('heroicon-o-user-group');
    }
}
