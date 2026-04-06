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
        $userId = auth()->id();

        return [
            Stat::make('My Events', Event::where('user_id', $userId)->count())
                ->description('All time events')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
            Stat::make('Total RSVPs', RSVP::whereHas('event', fn ($query) => $query->where('user_id', $userId))->count())
                ->description('Confirmed attendance')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Upcoming invitations', \App\Models\Invite::where('invitee_id', $userId)->count())
                ->description('Pending invites')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('info'),
        ];
    }
}
