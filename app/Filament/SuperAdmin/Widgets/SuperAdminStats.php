<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Admin;
use App\Models\User;
use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SuperAdminStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Admins', Admin::count())
                ->description('All registered admin accounts')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('primary'),
            Stat::make('Active Admins', Admin::where('is_active', true)->count())
                ->description('Currently active administrators')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Total Users', User::count())
                ->description('Total clients in the system')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            Stat::make('Total Bookings', Booking::count())
                ->description('All time appointments')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),
        ];
    }
}
