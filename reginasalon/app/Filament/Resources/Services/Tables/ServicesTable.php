<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('id')->sortable(),
                TextColumn::make('category.name'),
                TextColumn::make('name')->searchable(),
                TextColumn::make('description')->limit('25'),
                TextColumn::make('duration')->searchable()->sortable()->suffix(' min'),
                TextColumn::make('price')->searchable()->sortable()->money('IDR')

            ])
            ->filters([
                //
            ])
            ->defaultPaginationPageOption(25)
            ->recordActions([
                // ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make('delete')
                        ->requiresConfirmation(),
                ]),
            ]);
    }
}
