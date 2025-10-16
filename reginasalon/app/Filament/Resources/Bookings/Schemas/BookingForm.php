<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Illuminate\Support\Carbon;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // NANTI DIHAPUS
                Section::make()
                    ->schema([
                        Select::make('store_id')
                            ->relationship('store', 'name')
                            ->label('Store')
                            ->required(),
                        TextInput::make('user_id')
                            ->required(),
                        DatePicker::make('booking_date')
                            ->native(false)
                            ->minDate(now())
                            ->firstDayOfWeek(7)
                            ->required(),
                        TimePicker::make('booking_time')
                            ->datalist([
                                '09:00',
                                '09:30',
                                '10:00',
                                '10:30',
                                '11:00',
                                '11:30',
                                '12:00',
                            ])
                            ->seconds(false)
                            ->required(),
                        TextInput::make('staff_id')
                            ->required(),


                    ])
            ]);
    }
}
