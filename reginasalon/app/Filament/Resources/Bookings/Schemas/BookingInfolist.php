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
                Section::make('Booking Details')
                    ->schema([
                        TextEntry::make('id')
                            ->label('Booking ID'),

                        TextEntry::make('user.name')
                            ->label('Customer Name'),

                        TextEntry::make('booking_date')
                            ->label('Booking Date')
                            ->date('D, M j, Y'),

                        TextEntry::make('booking_time')
                            ->label('Booking Time')
                            ->formatStateUsing(fn($state) => $state->format('H:i') . ' WIB'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'Pending' => 'warning',
                                'Confirmed' => 'info',
                                'Completed' => 'success',
                                'Cancelled' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columnSpan(1),

                Section::make('Service Information')
                    ->schema([
                        TextEntry::make('services_list')
                            ->label('Service(s)')
                            ->getStateUsing(function ($record) {
                                if (!$record->bookingItems || $record->bookingItems->isEmpty()) {
                                    return 'No services';
                                }

                                return $record->bookingItems->map(function ($item, $index) {
                                    return ($index + 1) . ". " . ($item->service->name ?? 'Unknown');
                                })->join("\n");
                            })
                            ->html()
                            ->formatStateUsing(fn($state) => nl2br($state)),

                        TextEntry::make('staff_list')
                            ->label('Staff(s)')
                            ->getStateUsing(function ($record) {
                                if (!$record->bookingItems || $record->bookingItems->isEmpty()) {
                                    return 'No staff assigned';
                                }

                                return $record->bookingItems->map(function ($item, $index) {
                                    $staffName = $item->staff ? $item->staff->name : 'Unassigned';
                                    return ($index + 1) . ". " . $staffName;
                                })->join("\n");
                            })
                            ->html()
                            ->formatStateUsing(fn($state) => nl2br($state)),

                        TextEntry::make('price_list')
                            ->label('Price(s)')
                            ->getStateUsing(function ($record) {
                                if (!$record->bookingItems || $record->bookingItems->isEmpty()) {
                                    return 'No prices';
                                }

                                return $record->bookingItems->map(function ($item, $index) {
                                    $price = number_format($item->price ?? 0, 0, ',', '.');
                                    return ($index + 1) . ". Rp " . $price;
                                })->join("\n");
                            })
                            ->html()
                            ->formatStateUsing(fn($state) => nl2br($state)),

                        TextEntry::make('total_price')
                            ->label('Total Price')
                            ->getStateUsing(function ($record) {
                                $total = $record->bookingItems->sum('price') ?? 0;
                                return 'Rp ' . number_format($total, 0, ',', '.');
                            }),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(2);
    }
}
