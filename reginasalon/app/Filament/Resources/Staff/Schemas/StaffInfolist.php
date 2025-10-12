<?php

namespace App\Filament\Resources\Staff\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StaffInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make()
                    ->schema([
                        ImageEntry::make('image')
                            ->disk('public')
                            ->visibility('public')
                            ->imageHeight(300)
                            ->imageWidth(300)
                            ->circular()
                            ->alignCenter(),
                    ]),

                Section::make()
                    ->schema([
                        TextEntry::make('store.name')->label('Branch'),
                        TextEntry::make('name'),
                        TextEntry::make('email'),
                        TextEntry::make('phone'),
                        TextEntry::make('services_list')
                            ->label('Services')
                            ->getStateUsing(
                                fn($record) =>
                                $record->services->unique('id')->pluck('name')->join(', ')
                            )

                    ])
                    ->columnSpan(2)


            ]);
    }
}
