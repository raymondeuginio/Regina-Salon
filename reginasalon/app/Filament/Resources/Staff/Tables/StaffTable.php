<?php

namespace App\Filament\Resources\Staff\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\TagsColumn;
use League\Flysystem\Visibility;

class StaffTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('store.name'),
                ImageColumn::make('image')
                    ->circular()
                    ->disk('public')
                    ->visibility('public'),
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('phone'),
                TextColumn::make('services_list')
                    ->label('Services')
                    ->getStateUsing(
                        fn($record) =>
                        $record->services->unique('id')->pluck('name')->implode(', ')
                    )
                    ->limit(25)
                    ->tooltip(fn($record) => $record->services->pluck('name')->join(', '))




            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
