<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\UpcomingBooking;
use App\Filament\Widgets\BookingsChart;
use App\Filament\Widgets\RevenueChart;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{

    use HasFiltersForm;
    protected static ?string $title = 'Regina Salon Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    public function filtersForm(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Filter Date')
                ->schema([
                    DatePicker::make('startDate')
                        ->label('Start Date')
                        ->default(now()->startOfMonth())
                        ->native(false)
                        ->live()
                        ->maxDate(fn(Get $get) => $get('endDate') ?: now()),
                    DatePicker::make('endDate')
                        ->label('End Date')
                        ->default(now())
                        ->native(false)
                        ->live()
                        ->minDate(fn(Get $get) => $get('startDate') ?: now()->startOfMonth()),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    /**
     * Summary of getWidgets
     * @return string[]
     */
    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            UpcomingBooking::class,

            BookingsChart::class,
            RevenueChart::class,
        ];
    }


    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'md' => 2,
            'xl' => 2,

        ];
    }
}
