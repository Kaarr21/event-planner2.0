<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class Cancelled extends Component
{
    public function render()
    {
        $userId = Auth::id();
        
        $events = Event::cancelledForUser($userId)
            ->withCount('rsvps')
            ->with([
                'receivedInvites' => function($query) use ($userId) {
                    $query->where('invitee_id', $userId);
                },
                'rsvps' => function($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ])
            ->latest()
            ->get();

        return view('livewire.events.cancelled', [
            'events' => $events,
        ])->layout('layouts.app');
    }
}
