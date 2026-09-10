<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\Department;
use App\Models\Location;
use App\Models\MaintenanceLog;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Gate;

class InventoryOverview extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return Gate::allows('view assets');
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total devices', Asset::query()->count())
                ->description('All registered NRZ assets')
                ->icon('heroicon-o-computer-desktop')
                ->color('primary')
                ->url('/wizy/assets'),
            Stat::make('Active devices', Asset::query()->where('status', 'active')->count())
                ->description('Currently in service')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->url('/wizy/assets?tableFilters[status][value]=active'),
            Stat::make('Warranty watch', Asset::query()
                ->whereDate('warranty_expiry', '>', now())
                ->whereDate('warranty_expiry', '<=', now()->addDays(30))
                ->count())
                ->description('Expiring within 30 days')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->url('/wizy/assets'),
            Stat::make('Open maintenance', MaintenanceLog::query()
                ->whereIn('status', ['pending', 'in_progress'])
                ->count())
                ->description('Pending or in progress')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('danger')
                ->url('/wizy/maintenance-logs'),
            Stat::make('Decommissioned', Asset::query()->where('status', 'decommissioned')->count())
                ->description('Removed from service')
                ->icon('heroicon-o-archive-box')
                ->color('gray')
                ->url('/wizy/assets'),
            Stat::make('NRZ locations', Location::query()->count())
                ->description('Sites with inventory coverage')
                ->icon('heroicon-o-map-pin')
                ->color('info')
                ->url('/wizy/reports'),
            Stat::make('Departments', Department::query()->count())
                ->description('Business units tracked')
                ->icon('heroicon-o-building-office-2')
                ->color('primary')
                ->url('/wizy/reports'),
        ];
    }
}
