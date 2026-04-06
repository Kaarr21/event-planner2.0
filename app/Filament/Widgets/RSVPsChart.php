<?php

namespace App\Filament\Widgets;

use App\Models\RSVP;
use Filament\Widgets\ChartWidget;

class RSVPsChart extends ChartWidget
{
    protected static ?string $heading = 'RSVPs Distribution';

    protected function getData(): array
    {
        $userId = auth()->id();
        $filter = $this->filter;

        $days = match ($filter) {
            'today' => 0,
            'week' => 7,
            'month' => 30,
            'year' => 365,
            default => 30,
        };

        $query = RSVP::whereHas('event', fn ($query) => $query->where('user_id', $userId));

        if ($filter !== 'all') {
            $query->where('created_at', '>=', now()->subDays($days));
        }

        $data = $query->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'RSVPs',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#257bf4',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                    ],
                ],
            ],
            'labels' => $data->pluck('status')->map(fn ($s) => ucfirst($s))->toArray(),
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
            'all' => 'All time',
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
