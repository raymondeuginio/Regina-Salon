<?php

namespace App\Filament\Resources\Stores\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;

class StoreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Store Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Store Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter store name'),

                        Textarea::make('address')
                            ->label('Address')
                            ->required()
                            ->rows(4)
                            ->placeholder('Enter store address'),
                    ])
                    ->columnSpan(1),

                Section::make('Location Details')
                    ->schema([
                        TextInput::make('district')
                            ->label('District')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter district'),

                        TextInput::make('city')
                            ->label('City')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter city'),

                        TextInput::make('province')
                            ->label('Province')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter province'),

                        TextInput::make('postal_code')
                            ->label('Postal Code')
                            ->required()
                            ->maxLength(10)
                            ->placeholder('Enter postal code'),

                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->placeholder('Enter phone number'),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(2);
    }
}
