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

        $existingInvite = Invite::where('event_id', $this->event->id)
            ->where('invitee_email', $this->email)
            ->first();

        if ($existingInvite && $existingInvite->status !== 'declined') {
            session()->flash('invite_warning', 'This person has already been invited. Re-inviting will send another email.');
            $this->dispatch('resend-confirmation');
            return;
        }

        $this->processInvitation();
    }

    public function resendInvite()
    {
        $this->processInvitation();
    }

    protected function processInvitation()
    {
        $invitee = User::where('email', $this->email)->first();

        $invite = Invite::updateOrCreate(
            ['event_id' => $this->event->id, 'invitee_email' => $this->email],
            [
                'inviter_id' => Auth::id(),
                'invitee_id' => $invitee?->id,
                'message' => $this->message,
                'status' => 'pending',
                'responded_at' => null,
            ]
        );

        if ($this->event->status === Event::STATUS_PUBLISHED) {
            if ($invitee) {
                Notification::create([
                    'user_id' => $invitee->id,
                    'type' => 'invite',
                    'title' => 'Event Invitation',
                    'message' => Auth::user()->name . " has invited you to: " . $this->event->title,
                    'related_id' => $invite->id,
                ]);
            }

            \Illuminate\Support\Facades\Mail::to($this->email)->send(new \App\Mail\EventInvitation($this->event, Auth::user(), $this->message));
            session()->flash('invite_message', 'Invitation sent to ' . $this->email);
        } else {
            session()->flash('invite_message', 'Invitation saved. It will be sent once the event is published.');
        }
        $this->dispatch('invite-sent');
    }

    public function render()
    {
        return view('livewire.events.invite-form');
    }
}
