<?php

namespace App\Filament\Resources\Staff\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StaffInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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

            ]);
    }
}
