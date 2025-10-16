<?php

namespace App\Filament\Resources\Staff\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StaffInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(fn($record) => $record->name)
                    ->schema([
                        Grid::make(2)->schema([
                            // KIRI 
                            Section::make()
                                ->schema([
                                    ImageEntry::make('image')
                                        ->disk('public')
                                        ->visibility('public')
                                        ->imageHeight(310)
                                        ->imageWidth(310)
                                        ->circular()
                                        ->alignCenter(),
                                ]),

                            // KANAN 
                            Section::make()
                                ->schema([
                                    TextEntry::make('store.name')->label('Branch'),
                                    TextEntry::make('name')->label('Name'),
                                    TextEntry::make('email')->label('Email'),
                                    TextEntry::make('phone')->label('Phone'),
                                    TextEntry::make('services_list')
                                        ->label('Services')
                                        ->getStateUsing(
                                            fn($record) =>
                                            $record->services->unique('id')->pluck('name')->join(', ')
                                        ),
                                ])


                        ]),
                    ])
                    ->columnSpanFull(),

            ]);
    }
}
