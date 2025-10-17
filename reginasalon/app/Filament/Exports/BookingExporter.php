<?php

namespace App\Filament\Exports;

use App\Models\Booking;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class BookingExporter extends Exporter
{
    protected static ?string $model = Booking::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('user.name')
                ->label('Customer'),
            ExportColumn::make('services')
                ->label('Services')
                ->state(function (Booking $record): string {
                    if ($record->bookingItems->isEmpty()) {
                        return 'No services';
                    }

                    return $record->bookingItems
                        ->map(fn($item) => $item->service->name ?? 'Unknown service')
                        ->implode(', ');
                }),
            ExportColumn::make('staff')
                ->label('Staff')
                ->state(function (Booking $record): string {
                    if ($record->bookingItems->isEmpty()) {
                        return 'No staff assigned';
                    }

                    return $record->bookingItems
                        ->map(fn($item) => $item->staff->name ?? 'Unassigned')
                        ->implode(', ');
                }),
            ExportColumn::make('prices')
                ->label('Prices')
                ->state(function (Booking $record): string {
                    if ($record->bookingItems->isEmpty()) {
                        return 'No prices';
                    }

                    return $record->bookingItems
                        ->map(fn($item) => number_format((float) $item->price, 0, ',', '.'))
                        ->implode(', ');
                }),
            ExportColumn::make('total_price')
                ->label('Total Price')
                ->state(fn(Booking $record): string => number_format((float) $record->bookingItems->sum('price'), 0, ',', '.')),
            ExportColumn::make('booking_date')
                ->label('Date')
                ->formatStateUsing(fn($state): ?string => $state?->format('Y-m-d')),
            ExportColumn::make('booking_time')
                ->label('Time')
                ->formatStateUsing(fn($state): ?string => $state?->format('H:i')),
            ExportColumn::make('status')
                ->label('Status')
                ->state(fn(Booking $record): string => Str::headline($record->status)),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your booking export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
