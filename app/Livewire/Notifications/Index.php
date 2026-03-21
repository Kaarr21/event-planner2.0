<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public $activeTab = 'received';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }
    public function markAsRead($notificationId)
    {
        $notification = Notification::where('user_id', Auth::id())->find($notificationId);
        if ($notification) {
            $notification->update(['read' => true]);
        }
    }

    public function acceptInvite($notificationId)
    {
        $notification = Notification::where('user_id', Auth::id())->find($notificationId);
        if ($notification && $notification->type === 'invite') {
            $invite = \App\Models\Invite::find($notification->related_id);
            if ($invite) {
                $invite->update(['status' => 'accepted', 'responded_at' => now()]);
                
                // Create RSVP
                \App\Models\RSVP::updateOrCreate(
                    ['user_id' => Auth::id(), 'event_id' => $invite->event_id],
                    ['status' => 'attending']
                );

                // Notify Creator
                \App\Models\Notification::create([
                    'user_id' => $invite->event->user_id,
                    'sender_id' => Auth::id(),
                    'type' => 'invite_accepted',
                    'title' => 'Invitation Accepted',
                    'message' => Auth::user()->name . " has accepted the invitation to: " . $invite->event->title,
                    'related_id' => $invite->event_id,
                ]);
            }
            $notification->update(['read' => true]);
            session()->flash('message', 'Invitation accepted!');
        }
    }

    public function declineInvite($notificationId)
    {
        $notification = Notification::where('user_id', Auth::id())->find($notificationId);
        if ($notification && $notification->type === 'invite') {
            $invite = \App\Models\Invite::find($notification->related_id);
            if ($invite) {
                $invite->update(['status' => 'declined', 'responded_at' => now()]);
                
                // Create/Update RSVP
                \App\Models\RSVP::updateOrCreate(
                    ['user_id' => Auth::id(), 'event_id' => $invite->event_id],
                    ['status' => 'declined']
                );
            }
            $notification->update(['read' => true]);
            session()->flash('message', 'Invitation declined.');
        }
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())->where('read', false)->update(['read' => true]);
    }

    public function render()
    {
        $query = $this->activeTab === 'sent' 
            ? Notification::where('sender_id', Auth::id())
            : Notification::where('user_id', Auth::id());

        return view('livewire.notifications.index', [
            'notifications' => $query->with(['invite', 'user', 'sender'])->latest()->get(),
            'unreadCount' => Notification::where('user_id', Auth::id())->where('read', false)->count(),
        ])->layout('layouts.app');
    }
}
