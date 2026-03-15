<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Livewire\Component;

class InvitedList extends Component
{
    public Event $event;

    protected $listeners = ['invite-sent' => '$refresh', 'notification-read' => '$refresh'];

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function render()
    {
        return view('livewire.events.invited-list', [
            'invites' => $this->event->invites()->latest()->get(),
        ]);
    }
}
