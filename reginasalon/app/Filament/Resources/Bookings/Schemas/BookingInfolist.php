<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('store.name')->label('Store'),
                        TextEntry::make('user.name')->label('Customer'),
                        TextEntry::make('staff.name')->label('Staff'),
                        TextEntry::make('booking_date')

                            ->label('Date')
                            ->date('D, M j, Y'),
                        TextEntry::make('booking_time')

                            ->label('Time')
                            ->dateTime('H:i')
                            ->suffix(' WIB'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->formatStateUsing(fn($state) => ucfirst($state)),
                    ]),
            ]);
    }
}
