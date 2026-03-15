<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use App\Models\Invite;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class InviteForm extends Component
{
    public Event $event;
    public $email;
    public $message;

    protected $rules = [
        'email' => 'required|email',
        'message' => 'nullable|string|max:500',
    ];

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function invite()
    {
        $this->validate();

        // Check if already invited
        if (Invite::where('event_id', $this->event->id)->where('invitee_email', $this->email)->exists()) {
            $this->addError('email', 'This person has already been invited.');
            return;
        }

        $invitee = User::where('email', $this->email)->first();

        $invite = Invite::create([
            'event_id' => $this->event->id,
            'inviter_id' => Auth::id(),
            'invitee_email' => $this->email,
            'invitee_id' => $invitee?->id,
            'message' => $this->message,
            'status' => 'pending',
        ]);

        if ($invitee) {
            Notification::create([
                'user_id' => $invitee->id,
                'type' => 'invite',
                'title' => 'New Event Invitation',
                'message' => Auth::user()->name . " has invited you to: " . $this->event->title,
                'related_id' => $invite->id,
            ]);
        }

        \Illuminate\Support\Facades\Mail::to($this->email)->send(new \App\Mail\EventInvitation($this->event, Auth::user(), $this->message));

        $this->reset(['email', 'message']);
        session()->flash('invite_message', 'Invitation sent to ' . $this->email);
        $this->dispatch('invite-sent');
    }

    public function render()
    {
        return view('livewire.events.invite-form');
    }
}
