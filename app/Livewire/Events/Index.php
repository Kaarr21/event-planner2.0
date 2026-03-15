<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public function render()
    {
        $userId = Auth::id();
        
        $events = Event::where('user_id', $userId)
            ->orWhereHas('organizers', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->whereNotNull('permissions');
            })
            ->withCount('rsvps')
            ->latest()
            ->get();

        return view('livewire.events.index', [
            'events' => $events,
        ])->layout('layouts.app');
    }
}
