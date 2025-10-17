<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Concerns\HasDashboardDataRange;

class BookingsChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 3;
    protected ?string $heading = 'Bookings Chart';

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ? Carbon::parse($this->filters['startDate']) : now()->startOfMonth();
        $endDate = $this->filters['endDate'] ? Carbon::parse($this->filters['endDate']) : now();

        $dates = [];
        $bookingCounts = [];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('d M');

            // Hitung jumlah booking berdasarkan booking_date
            $count = Booking::whereDate('booking_date', $currentDate->format('Y-m-d'))->count();
            $bookingCounts[] = $count;

            $currentDate->addDay();
        }
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Bookings',
                    'data' => $bookingCounts,
                    'backgroundColor' => 'rgba(234, 129, 171, 0.7)',
                    'borderColor' => 'rgb(234, 129, 171)',
                    'borderWidth' => 2,
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
                        'stepSize' => 1,
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
