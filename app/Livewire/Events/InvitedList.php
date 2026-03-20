<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Livewire\Component;

class InvitedList extends Component
{
    public Event $event;
    public $selectedInviteIds = [];
    public bool $canEditEvent = false;

    protected $listeners = ['invite-sent' => '$refresh', 'notification-read' => '$refresh'];

    public function mount(Event $event, $selectedInviteIds = [], $canEditEvent = false)
    {
        $this->event = $event;
        $this->selectedInviteIds = $selectedInviteIds;
        $this->canEditEvent = $canEditEvent;
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
