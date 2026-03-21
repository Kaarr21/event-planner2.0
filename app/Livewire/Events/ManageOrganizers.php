<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\User;
use App\Models\Notification;
use Livewire\Component;

class ManageOrganizers extends Component
{
    public Event $event;
    public $email;
    public $selectedPermissions = [];

    protected $availablePermissions = [
        'edit_event' => 'Edit Event Details',
        'manage_invites' => 'Manage Invitations',
        'manage_tasks' => 'Manage Tasks',
        'assign_tasks' => 'Assign Tasks',
        'manage_files' => 'Manage Files',
        'view_rsvps' => 'View RSVPs',
    ];

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->selectedPermissions = ['manage_tasks', 'view_rsvps']; // Defaults
    }

    public function addOrganizer()
    {
        $this->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            $this->addError('email', 'User not found in our system.');
            return;
        }

        if ($user->id === $this->event->user_id) {
            $this->addError('email', 'The owner is already the main organizer.');
            return;
        }

        if ($this->event->organizers()->where('user_id', $user->id)->exists()) {
            $this->addError('email', 'This user is already an organizer.');
            return;
        }

        $this->event->organizers()->attach($user->id, [
            'permissions' => $this->selectedPermissions,
        ]);

        // Spatie Role Assignment
        setPermissionsTeamId($this->event->id);
        $user->assignRole('organizer');
        $user->givePermissionTo($this->selectedPermissions);

        if ($this->event->status === Event::STATUS_PUBLISHED) {
            Notification::create([
                'user_id' => $user->id,
                'sender_id' => \Illuminate\Support\Facades\Auth::id(),
                'type' => 'info',
                'title' => 'Organizer Role Assigned',
                'message' => "You have been assigned as an organizer for: " . $this->event->title,
                'related_id' => $this->event->id,
            ]);
        }

        $this->reset('email');
        session()->flash('organizer_message', 'Organizer added successfully.');
    }

    public function removeOrganizer($userId)
    {
        $user = User::find($userId);
        if ($user) {
            setPermissionsTeamId($this->event->id);
            $user->removeRole('organizer');
            $user->revokePermissionTo($user->getPermissionNames());
        }

        $this->event->organizers()->detach($userId);
        session()->flash('organizer_message', 'Organizer removed.');
    }

    public function togglePermission($userId, $permission)
    {
        $user = User::find($userId);
        $organizer = $this->event->organizers()->where('user_id', $userId)->first();
        if ($user && $organizer) {
            setPermissionsTeamId($this->event->id);
            
            if ($user->hasPermissionTo($permission)) {
                $user->revokePermissionTo($permission);
            } else {
                $user->givePermissionTo($permission);
            }

            // Sync with old pivot for compatibility
            $permissions = $organizer->pivot->permissions ?? [];
            if (!is_array($permissions)) {
                $permissions = json_decode($permissions, true) ?? [];
            }

            if (in_array($permission, $permissions)) {
                $permissions = array_values(array_diff($permissions, [$permission]));
            } else {
                $permissions[] = $permission;
            }

            $this->event->organizers()->updateExistingPivot($userId, [
                'permissions' => $permissions,
            ]);
            
            session()->flash('organizer_message', 'Permissions updated.');
        }
    }

    public function render()
    {
        return view('livewire.events.manage-organizers', [
            'organizers' => $this->event->organizers()->get(),
            'availablePermissions' => $this->availablePermissions,
        ]);
    }
}
