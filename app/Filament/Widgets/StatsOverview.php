<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\RSVP;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $organization = filament()->getTenant();

        return [
            Stat::make('Total Events', Event::where('organization_id', $organization->id)->count())
                ->description('All time events')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
            Stat::make('Total RSVPs', RSVP::whereHas('event', fn ($query) => $query->where('organization_id', $organization->id))->count())
                ->description('Confirmed attendance')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Team Members', $organization->members()->count())
                ->description('Organization staff')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}
