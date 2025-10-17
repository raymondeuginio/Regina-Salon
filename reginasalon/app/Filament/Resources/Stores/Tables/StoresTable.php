<?php

namespace App\Filament\Resources\Stores\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StoresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('id'),
                TextColumn::make('name'),
                TextColumn::make('address'),
                TextColumn::make('district'),
                TextColumn::make('city'),
                TextColumn::make('province'),
                TextColumn::make('postal_code'),
                TextColumn::make('phone'),
            ])
            ->emptyStateHeading('No Stores')
            ->emptyStateDescription('There are no store yet.')
            ->emptyStateIcon('heroicon-o-map-pin')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
