<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\RSVP;
use App\Models\Budget;
use App\Models\Task;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AnalyticsDashboard extends Component
{
    public Event $event;

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function getData()
    {
        // 1. RSVP Stats
        $rsvpStats = RSVP::where('event_id', $this->event->id)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // 2. Budget Stats
        $budgetStats = Budget::where('event_id', $this->event->id)
            ->select(
                DB::raw('SUM(estimated_amount) as total_estimated'),
                DB::raw('SUM(actual_amount) as total_actual'),
                DB::raw('SUM(paid_amount) as total_paid')
            )
            ->first();

        // 3. Budget by Category
        $budgetByCategory = Budget::where('event_id', $this->event->id)
            ->select('category', DB::raw('SUM(actual_amount) as spent'))
            ->groupBy('category')
            ->get()
            ->pluck('spent', 'category')
            ->toArray();

        // 4. Task Stats
        $taskStats = Task::where('event_id', $this->event->id)
            ->select(
                DB::raw('count(*) as total'),
                DB::raw('SUM(CASE WHEN completed THEN 1 ELSE 0 END) as completed')
            )
            ->first();

        return [
            'rsvp' => [
                'labels' => array_keys($rsvpStats),
                'series' => array_values($rsvpStats),
            ],
            'budget' => [
                'estimated' => (float)($budgetStats->total_estimated ?? 0),
                'actual' => (float)($budgetStats->total_actual ?? 0),
                'paid' => (float)($budgetStats->total_paid ?? 0),
                'by_category' => [
                    'labels' => array_keys($budgetByCategory),
                    'series' => array_values($budgetByCategory),
                ]
            ],
            'tasks' => [
                'total' => (int)($taskStats->total ?? 0),
                'completed' => (int)($taskStats->completed ?? 0),
                'percentage' => $taskStats->total > 0 ? round(($taskStats->completed / $taskStats->total) * 100) : 0,
            ]
        ];
    }

    public function render()
    {
        return view('livewire.events.analytics-dashboard', [
            'data' => $this->getData()
        ]);
    }
}
