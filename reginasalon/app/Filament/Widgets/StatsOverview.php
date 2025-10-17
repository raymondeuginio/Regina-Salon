<?php

namespace App\Filament\Widgets;

use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use App\Models\User;
use App\Models\Service;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Staff;

class StatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    // Posisi di dashboard paling pertama
    protected static ?int $sort = 1;
    protected ?string $pollingInterval = '15s';

    protected static bool $isLazy = true;
    protected function getStats(): array
    {

        $startDate = $this->filters['startDate'] ?? now()->startOfMonth();
        $endDate = $this->filters['endDate'] ?? now()->endOfDay();

        $totalServices = Service::count();
        $totalStaffs = Staff::count();
        $totalBookings = Booking::whereBetween('booking_date', [$startDate, $endDate])
            ->count();
        $totalRevenue = BookingItem::whereHas('booking', function ($query) use ($startDate, $endDate) {
            $query->where('status', 'completed')
                ->whereBetween('booking_date', [$startDate, $endDate]);
        })->sum('price');



        return [
            Stat::make('Bookings',  $totalBookings)
                ->description('Total appointments')
                ->descriptionIcon('heroicon-o-calendar'),
            Stat::make('Revenue', 'IDR ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Total revenue')
                ->descriptionIcon('heroicon-o-currency-dollar'),
            Stat::make('Services', $totalServices)
                ->description('Available services')
                ->descriptionIcon('heroicon-o-scissors'),
            Stat::make('Staffs', $totalStaffs)
                ->description('Total staff members')
                ->descriptionIcon('heroicon-o-user-group'),
        ];
    }
}
