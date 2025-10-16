<?php
// filepath: c:\akuliah\Regina-Salon\reginasalon\app\Filament\Pages\Dashboard.php

namespace App\Filament\Pages;


use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\UpcomingBooking;
use App\Filament\Widgets\StaffRanking;
use App\Filament\Widgets\BookingsChart;
use App\Filament\Widgets\MostBookedServiceChart;
use App\Filament\Widgets\RevenueChart;
use Filament\Schemas\Components\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;


class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Regina Salon Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';



    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            UpcomingBooking::class,
            StaffRanking::class,
            BookingsChart::class,
            RevenueChart::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'md' => 2,
            'xl' => 3,
        ];
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('dateRange')
                    ->label('Date Range')
                    ->options([
                        'today' => 'Today',
                        'week' => 'This Week',
                        'month' => 'This Month',
                        'year' => 'This Year',
                        'custom' => 'Custom Range',
                    ])
                    ->default('today')
                    ->live(),
                DatePicker::make('startDate')
                    ->label('From')
                    ->native(false)
                    ->visible(fn($get) => $get('dateRange') === 'custom'),
                DatePicker::make('endDate')
                    ->label('To')
                    ->native(false)
                    ->visible(fn($get) => $get('dateRange') === 'custom'),
            ]);
    }
}
