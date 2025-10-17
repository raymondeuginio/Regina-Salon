<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Booking;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class RevenueChart extends ChartWidget
{
    use InteractsWithPageFilters;
    protected ?string $heading = 'Revenue Chart';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ? Carbon::parse($this->filters['startDate']) : now()->startOfMonth();
        $endDate = $this->filters['endDate'] ? Carbon::parse($this->filters['endDate']) : now();

        $dates = [];
        $revenues = [];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('d M');

            // Ambil semua booking pada tanggal ini dengan status 'completed'
            $bookings = Booking::whereDate('booking_date', $currentDate->format('Y-m-d'))
                ->where('status', 'completed')
                ->with('bookingItems')
                ->get();

            // Total revenue dari semua booking_items
            $revenue = 0;
            foreach ($bookings as $booking) {
                foreach ($booking->bookingItems as $item) {
                    $revenue += $item->price ?? 0;
                }
            }

            $revenues[] = $revenue;
            $currentDate->addDay();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (IDR)',
                    'data' => $revenues,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $dates,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
