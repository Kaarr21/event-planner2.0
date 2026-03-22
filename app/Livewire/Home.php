<?php

namespace App\Livewire;

use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        $featuredEvents = \App\Models\Event::where('status', \App\Models\Event::STATUS_PUBLISHED)
            ->where('visibility', \App\Models\Event::VISIBILITY_PUBLISHED)
            ->where('end_at', '>=', now())
            ->with(['category', 'creator', 'ticketTypes'])
            ->withCount('rsvps')
            ->orderBy('start_at', 'asc')
            ->take(3)
            ->get();

        return view('livewire.home', [
            'featuredEvents' => $featuredEvents
        ])->layout('layouts.public');
    }
}
