<?php

namespace App\Filament\Resources\Staff\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\Service;
use App\Models\ServiceCategory;
use Filament\Forms\Components\CheckboxList;

class StaffForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('store_id')->relationship('store', 'name')->label('Store')->required(),
                TextInput::make('name')->required(),
                TextInput::make("email")->required()->unique(),
                TextInput::make('phone')->required()->unique(),
                CheckboxList::make('services')
                    ->label('Services')
                    ->required()
                    ->relationship('services', 'name')
                    ->helperText('Pilih service yang bisa dilakukan staff ini.')
                    ->columns(3)
                    ->options(fn() => Service::query()
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->columnSpanFull(),

            ]);
    }
}
