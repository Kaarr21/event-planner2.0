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
            ->orWhereHas('receivedInvites', function ($query) use ($userId) {
                $query->where('invitee_id', $userId)
                      ->where('status', 'pending');
            })
            ->orWhereHas('rsvps', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->whereIn('status', ['attending', 'maybe']);
            })
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

        return view('livewire.events.index', [
            'events' => $events,
        ])->layout('layouts.app');
    }
}
