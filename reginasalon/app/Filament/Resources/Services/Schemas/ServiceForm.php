<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('store_id')
                            ->relationship('store', 'name')
                            ->label('Store')
                            ->placeholder('Select store branch')
                            ->required(),
                        Select::make('service_category_id')
                            ->label('Service Category')
                            ->relationship('category', 'name')
                            ->placeholder('Select category')
                            ->required(),
                        Section::make('Service Details')
                            ->columns(2)
                            ->description('Please fill in the details of the service.')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->placeholder('Enter the name of the service'),
                                TextInput::make('duration')
                                    ->required()
                                    ->label('Duration (minutes)')
                                    ->numeric()
                                    ->rules(['numeric', 'min:1', 'max:1000'])
                                    ->step(1)
                                    ->placeholder('Enter the duration in minutes'),
                                TextInput::make('price')
                                    ->required()
                                    ->label('Price')
                                    ->numeric()
                                    ->rules(['numeric', 'min:0', 'max:1000000'])
                                    ->step(1)
                                    ->placeholder('Enter the price'),
                                Textarea::make('description')
                                    ->required()
                                    ->placeholder('Enter the description')
                                    ->rows(3),
                            ]),



                    ])


            ]);
    }
}
