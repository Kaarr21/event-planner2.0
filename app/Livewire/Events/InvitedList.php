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
        $this->syncInviteeIds();

        return view('livewire.events.invited-list', [
            'invites' => $this->event->invites()->latest()->get(),
        ]);
    }

    protected function syncInviteeIds()
    {
        $pendingWithNoId = $this->event->invites()
            ->whereNull('invitee_id')
            ->get();

        foreach ($pendingWithNoId as $invite) {
            $user = \App\Models\User::where('email', $invite->invitee_email)->first();
            if ($user) {
                $invite->update(['invitee_id' => $user->id]);
            }
        }
    }
}
