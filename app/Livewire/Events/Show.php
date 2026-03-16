<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use App\Models\Task;
use App\Models\RSVP;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Show extends Component
{
    public Event $event;
    public $userPermissions = [];
    public $userRole = null; // owner, organizer, guest, invited
    public $inviter = null;
    
    // Add task properties
    public $newTaskTitle;
    public $newTaskDueDate;
    public $newTaskAssignedTo;
    
    // Edit task properties
    public $editingTaskId = null;
    public $editTaskTitle;
    public $editTaskDueDate;
    public $editTaskDescription;
    public $editTaskAssignedTo;
    
    // Completion property
    public $completionComment;
    public $completingTaskId = null;

    // AI Suggestions
    public array $aiSuggestions = [];

    // Tab Management
    public $activeTab = 'overview';

    // Location Tracking
    public $latitude;
    public $longitude;
    public $googlePlaceId;
    public $locationSearch;

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    protected $rules = [
        'newTaskTitle' => 'required|string|max:255',
        'newTaskDueDate' => 'nullable|date',
        'newTaskAssignedTo' => 'nullable|exists:users,id',
        'editTaskTitle' => 'required|string|max:255',
        'editTaskDueDate' => 'nullable|date',
        'editTaskDescription' => 'nullable|string',
        'editTaskAssignedTo' => 'nullable|exists:users,id',
        'completionComment' => 'nullable|string',
        'locationSearch' => 'nullable|string|max:255',
    ];

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->latitude = $event->latitude;
        $this->longitude = $event->longitude;
        $this->googlePlaceId = $event->google_place_id;
        $this->locationSearch = $event->location;
        $this->authorizeUser();
    }

    public function syncLocation($lat, $lng, $placeId, $address)
    {
        if (!$this->hasPermission('edit_event')) return;

        $this->latitude = $lat;
        $this->longitude = $lng;
        $this->googlePlaceId = $placeId;
        $this->locationSearch = $address;

        $this->event->update([
            'latitude' => $lat,
            'longitude' => $lng,
            'google_place_id' => $placeId,
            'location' => $address,
        ]);

        $this->event->refresh();
        session()->flash('location_message', 'Location synced with Google Maps!');
    }

    public function bulkShareLocation()
    {
        if (!$this->hasPermission('manage_invites')) return;

        if (!$this->event->latitude || !$this->event->longitude) {
            session()->flash('location_message', 'Please sync a location first.');
            return;
        }

        $attendingGuests = $this->event->rsvps()
            ->where('status', 'attending')
            ->with('user')
            ->get()
            ->pluck('user');

        foreach ($attendingGuests as $guest) {
            $guest->notify(new \App\Notifications\LocationSharedNotification($this->event));
            
            \App\Models\Notification::create([
                'user_id' => $guest->id,
                'type' => 'location_shared',
                'title' => 'Location Pin Shared',
                'message' => Auth::user()->name . " shared the exact coordinates for " . $this->event->title,
                'related_id' => $this->event->id,
            ]);
        }

        session()->flash('location_message', 'Location pin shared with ' . $attendingGuests->count() . ' attending guests!');
    }

    protected function authorizeUser()
    {
        $userId = Auth::id();
        
        // Authorization check
        if ($this->event->user_id !== $userId) {
            $organizer = $this->event->organizers()->where('user_id', $userId)->first();
            
            if (!$organizer) {
                // If not an organizer, maybe they are a guest or have an invite?
                $isGuest = $this->event->rsvps()
                    ->where('user_id', $userId)
                    ->whereIn('status', ['attending', 'maybe'])
                    ->exists();
                $invite = $this->event->invites()->where('invitee_id', $userId)->where('status', 'pending')->first();
                
                if (!$isGuest && !$invite) {
                    return redirect()->route('events.index')->with('error', 'You do not have access to this event.');
                }
                
                $this->userPermissions = ['view_tasks'];
                $this->userRole = $isGuest ? 'guest' : 'invited';

                if ($invite) {
                    $this->inviter = $invite->inviter;
                }
            } else {
                $permissions = $organizer->pivot->permissions ?? [];
                if (!is_array($permissions)) {
                    $permissions = json_decode($permissions, true) ?? [];
                }
                $this->userPermissions = $permissions;
                $this->userRole = 'organizer';
            }
        } else {
            // Owner has all permissions
            $this->userPermissions = ['edit_event', 'manage_invites', 'manage_tasks', 'assign_tasks', 'manage_files', 'view_rsvps', 'owner'];
            $this->userRole = 'owner';
        }
    }

    public function hasPermission($permission)
    {
        $perms = $this->userPermissions ?: [];
        if (!is_array($perms)) {
            $perms = [];
        }
        return in_array($permission, $perms) || in_array('owner', $perms);
    }

    public function suggestAITasks(\App\Services\AIService $aiService)
    {
        if (!$this->hasPermission('manage_tasks')) return;
        
        $suggestions = $aiService->suggestTasks($this->event->title, $this->event->description ?: '');
        
        $this->aiSuggestions = array_map(function($title) {
            return ['title' => $title, 'selected' => true];
        }, $suggestions);

        session()->flash('task_message', 'AI suggestions ready for review!');
    }
    
    public function removeSuggestion($index)
    {
        if (!$this->hasPermission('manage_tasks')) return;
        
        if (isset($this->aiSuggestions[$index])) {
            unset($this->aiSuggestions[$index]);
            // Reindex array
            $this->aiSuggestions = array_values($this->aiSuggestions);
        }
    }
    
    public function saveSuggestions()
    {
        if (!$this->hasPermission('manage_tasks')) return;
        
        $count = 0;
        foreach ($this->aiSuggestions as $suggestion) {
            if ($suggestion['selected'] && !empty($suggestion['title'])) {
                $this->event->tasks()->create([
                    'title' => $suggestion['title'],
                    'completed' => false,
                ]);
                $count++;
            }
        }

        $this->aiSuggestions = [];
        $this->event->refresh();
        session()->flash('task_message', $count . ' AI suggested tasks added!');
    }

    public function addTask()
    {
        if (!$this->hasPermission('manage_tasks')) return;

        $this->validate([
            'newTaskTitle' => 'required|string|max:255',
            'newTaskDueDate' => 'nullable|date',
            'newTaskAssignedTo' => 'nullable|exists:users,id',
        ]);

        $this->event->tasks()->create([
            'title' => $this->newTaskTitle,
            'due_date' => $this->newTaskDueDate,
            'assigned_to' => $this->newTaskAssignedTo,
            'assignment_status' => $this->newTaskAssignedTo ? 'pending' : null,
            'completed' => false,
        ]);

        $this->reset(['newTaskTitle', 'newTaskDueDate', 'newTaskAssignedTo']);
        $this->event->refresh();

        session()->flash('task_message', 'Task added successfully.');
    }
    
    public function startEditTask($taskId)
    {
        if (!$this->hasPermission('manage_tasks')) return;

        $task = Task::find($taskId);
        if ($task && $task->event_id === $this->event->id) {
            $this->editingTaskId = $task->id;
            $this->editTaskTitle = $task->title;
            $this->editTaskDueDate = $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : null;
            $this->editTaskDescription = $task->description;
            $this->editTaskAssignedTo = $task->assigned_to;
        }
    }
    
    public function cancelEditTask()
    {
        $this->reset(['editingTaskId', 'editTaskTitle', 'editTaskDueDate', 'editTaskDescription', 'editTaskAssignedTo']);
    }
    
    public function saveTask()
    {
        if (!$this->hasPermission('manage_tasks')) return;

        $this->validate([
            'editTaskTitle' => 'required|string|max:255',
            'editTaskDueDate' => 'nullable|date',
            'editTaskDescription' => 'nullable|string',
            'editTaskAssignedTo' => 'nullable|exists:users,id',
        ]);

        if ($this->editingTaskId) {
            $task = Task::find($this->editingTaskId);
            if ($task && $task->event_id === $this->event->id) {
                $oldAssignedTo = $task->assigned_to;
                $task->update([
                    'title' => $this->editTaskTitle,
                    'due_date' => $this->editTaskDueDate,
                    'description' => $this->editTaskDescription,
                    'assigned_to' => $this->editTaskAssignedTo,
                    'assignment_status' => ($this->editTaskAssignedTo && $this->editTaskAssignedTo != $oldAssignedTo) ? 'pending' : $task->assignment_status,
                ]);
            }
        }

        $this->cancelEditTask();
        $this->event->refresh();
        session()->flash('task_message', 'Task updated successfully.');
    }

    public function toggleTask($taskId)
    {
        if (!$this->hasPermission('manage_tasks')) return;

        $task = Task::find($taskId);
        if ($task && $task->event_id === $this->event->id) {
            $task->completed = !$task->completed;
            $task->save();
            $this->event->refresh();
        }
    }

    public function deleteTask($taskId)
    {
        if (!$this->hasPermission('manage_tasks')) return;

        $task = Task::find($taskId);
        if ($task && $task->event_id === $this->event->id) {
            $task->delete();
            $this->event->refresh();
        }
    }

    public function updateRSVP($status)
    {
        RSVP::updateOrCreate(
            ['user_id' => Auth::id(), 'event_id' => $this->event->id],
            ['status' => $status]
        );

        // Sync pending invitation status
        \App\Models\Invite::where('event_id', $this->event->id)
            ->where('invitee_id', Auth::id())
            ->where('status', 'pending')
            ->update([
                'status' => ($status === 'declined' ? 'declined' : 'accepted'),
                'responded_at' => now(),
            ]);

        $this->event->refresh();
        $this->authorizeUser(); // Update permissions and role instantly
        session()->flash('rsvp_message', 'RSVP updated.');
    }

    // Task Assignment Handlers
    public function acceptTask($taskId)
    {
        $task = Task::find($taskId);
        if ($task && $task->assigned_to === Auth::id()) {
            $task->update(['assignment_status' => 'accepted']);
            $this->event->refresh();
        }
    }

    public function declineTask($taskId)
    {
        $task = Task::find($taskId);
        if ($task && $task->assigned_to === Auth::id()) {
            $task->update(['assignment_status' => 'declined']);
            $this->event->refresh();
        }
    }

    public function startCompletion($taskId)
    {
        $task = Task::find($taskId);
        if ($task && $task->assigned_to === Auth::id()) {
            $this->completingTaskId = $taskId;
            $this->completionComment = '';
        }
    }

    public function completeTask()
    {
        $this->validate(['completionComment' => 'nullable|string']);

        if ($this->completingTaskId) {
            $task = Task::find($this->completingTaskId);
            if ($task && $task->assigned_to === Auth::id()) {
                $task->update([
                    'completed' => true,
                    'assignment_status' => 'completed',
                    'completion_comment' => $this->completionComment,
                ]);
            }
        }

        $this->reset(['completingTaskId', 'completionComment']);
        $this->event->refresh();
        session()->flash('task_message', 'Task completed!');
    }

    public function getEligibleAssigneesProperty()
    {
        // Eligible: Organizers + Accepted (Attending) Guests
        $organizerIds = $this->event->organizers()->pluck('users.id')->toArray();
        $guestIds = $this->event->rsvps()->where('status', 'attending')->pluck('user_id')->toArray();
        
        $allIds = array_unique(array_merge($organizerIds, $guestIds));
        
        return User::whereIn('id', $allIds)->get();
    }

    public function render()
    {
        return view('livewire.events.show', [
            'tasks' => $this->event->tasks()
                ->with('assignee')
                ->orderBy('completed')
                ->orderBy('due_date')
                ->get(),
            'rsvps' => $this->event->rsvps()->with('user')->get(),
            'userRSVP' => RSVP::where('event_id', $this->event->id)->where('user_id', Auth::id())->first(),
            'eligibleAssignees' => $this->eligibleAssignees,
        ])->layout('layouts.app');
    }
}
