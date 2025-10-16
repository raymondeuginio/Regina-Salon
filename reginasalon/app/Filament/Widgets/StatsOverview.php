<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Service;
use App\Models\Booking;
use App\Models\Staff;

class StatsOverview extends StatsOverviewWidget
{
    // Posisi di dashboard paling pertama
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '15s';

    protected static bool $isLazy = true;
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $totalBookings = Booking::count();
        $totalServices = Service::count();
        $totalStaffs = Staff::count();

        return [
            // Stat::make('Users', $totalUsers)
            //     ->description('Total registered users')
            //     ->descriptionIcon('heroicon-o-users'),
            // // ->color('success'),
            Stat::make('Appointments',  $totalBookings)
                ->description('Total bookings')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('success')
                ->chart([65, 59, 84, 84, 51, 55, 40]),
            Stat::make('Revenue', 'IDR ' . number_format(20000, 0, ',', '.'))
                ->description('Total revenue')
                ->descriptionIcon('heroicon-o-currency-dollar'),
            // ->color('warning'),
            Stat::make('Services', $totalServices)
                ->description('Available services')
                ->descriptionIcon('heroicon-o-scissors'),
            // ->color('info'),
            Stat::make('Staffs', $totalStaffs)
                ->description('Total staff members')
                ->descriptionIcon('heroicon-o-user-group'),
            // ->color('success'),
        ];
    }
}
