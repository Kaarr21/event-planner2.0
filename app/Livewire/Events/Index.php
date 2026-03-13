<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public function render()
    {
        return view('livewire.events.index', [
            'events' => Auth::user()->events()->withCount('rsvps')->latest()->get(),
        ])->layout('layouts.app');
    }
}
