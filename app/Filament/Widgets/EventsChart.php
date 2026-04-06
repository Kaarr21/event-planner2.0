<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class EventsChart extends ChartWidget
{
    protected static ?string $heading = 'Events Over Time';

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

        $query = Event::where('user_id', $userId);
        
        if ($filter !== 'all') {
            $query->where('created_at', '>=', now()->subDays($days));
        }

        $data = $query->selectRaw('DATE(created_at) as date, count(*) as total')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Events Created',
                    'data' => $data->pluck('total')->toArray(),
                    'borderColor' => '#257bf4',
                    'backgroundColor' => '#257bf433',
                    'fill' => true,
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
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
        return 'line';
    }
}
