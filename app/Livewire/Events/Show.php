<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use App\Models\Task;
use App\Models\RSVP;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\GuestListExport;
use App\Exports\TasksExport;
use App\Exports\GuestTemplateExport;
use App\Imports\GuestImport;
use Livewire\WithFileUploads;

class Show extends Component
{
    use WithFileUploads;
    public Event $event;
    public $userPermissions = [];
    public $userRole = null; // owner, organizer, guest, invited
    public $inviter = null;
    
    // Edit event properties
    public $isEditingEvent = false;
    public $editTitle;
    public $editDescription;
    public $editDate;
    public $editLocation;
    public $editCategoryId;
    public $newCategoryName;

    public $notifyGuests = false;

    // Notify Later properties
    public $isNotifyingLater = false;
    public $notifyLaterMessage = '';
    public $notifyLaterFields = ['title', 'date', 'location'];

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

    // Cancellation properties
    public $isConfirmingCancellation = false;
    public $cancellationReason = '';

    // Import & Draft properties
    public $isImporting = false;
    public $guestImportFile;
    public $importedGuests = [];
    public $draftSearch = '';
    public $isEditingDraft = false;
    public $editingDraftId = null;
    public $editDraftName;
    public $editDraftEmail;
    public $editDraftPhone;

    // Bulk Notification properties
    public $selectedInviteIds = [];
    public $showBulkNotificationModal = false;
    public $bulkNotificationMessage = '';

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
        'cancellationReason' => 'nullable|string|max:1000',
        'editDraftName' => 'required|string|max:255',
        'editDraftEmail' => 'required|email|max:255',
        'editDraftPhone' => 'nullable|string|max:20',
        'bulkNotificationMessage' => 'required|string|max:5000',
    ];

    public function getCategoriesProperty()
    {
        return \App\Models\Category::whereNull('user_id')
            ->orWhere('user_id', Auth::id())
            ->orderBy('name')
            ->get();
    }

    public function openCategoryModal()
    {
        $this->dispatch('open-modal', 'custom-category');
    }

    public function closeCategoryModal()
    {
        $this->newCategoryName = '';
        $this->resetErrorBag('newCategoryName');
        $this->dispatch('close-modal', 'custom-category');
    }

    public function saveCustomCategory()
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = \App\Models\Category::create([
            'name' => $this->newCategoryName,
            'user_id' => Auth::id(),
        ]);

        $this->editCategoryId = $category->id;
        $this->closeCategoryModal();
    }

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->latitude = $event->latitude;
        $this->longitude = $event->longitude;
        $this->googlePlaceId = $event->google_place_id;
        $this->locationSearch = $event->location;
        
        // Set Spatie Team context
        setPermissionsTeamId($event->id);
        
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
                'sender_id' => Auth::id(),
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
        $user = Auth::user();
        if (!$user) {
             $this->redirect(route('login'));
             return;
        }

        // Set Spatie context
        setPermissionsTeamId($this->event->id);
        
        // Check for access to the event
        $isOrganizer = $user->hasRole('organizer');
        $isOwner = $user->hasRole('owner');
        $isGuest = $user->hasRole('guest');
        $invite = $this->event->invites()->where('invitee_id', $user->id)->where('status', 'pending')->first();
        
        $isCreator = $this->event->user_id === $user->id;
        
        if (!$isOrganizer && !$isOwner && !$isGuest && !$invite && !$isCreator) {
             $this->redirect(route('events.index'), navigate: true);
             return;
        }

        // Determine user role and permissions for the view
        if ($isOwner || $isCreator) {
            $this->userRole = 'owner';
            $this->userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        } elseif ($isOrganizer) {
            $this->userRole = 'organizer';
            $this->userPermissions = $user->getPermissionNames()->toArray();
        } elseif ($isGuest) {
            $this->userRole = 'guest';
            $this->userPermissions = $user->getPermissionNames()->toArray();
        } else {
            $this->userRole = 'invited';
            $this->userPermissions = [];
            if ($invite) {
                $this->inviter = $invite->inviter;
            }
        }
    }

    public function toggleGuestPermission($rsvpId, $permission)
    {
        if (!$this->hasPermission('owner') && !$this->hasPermission('edit_event')) {
            return;
        }

        $rsvp = \App\Models\RSVP::with('user')->find($rsvpId);
        if ($rsvp && $rsvp->event_id === $this->event->id && $rsvp->user) {
            setPermissionsTeamId($this->event->id);
            
            // Map view_tasks and view_guest_list
            $spatiePermission = ($permission === 'can_view_checklist') ? 'view_tasks' : 'view_guest_list';
            
            if ($rsvp->user->hasPermissionTo($spatiePermission)) {
                $rsvp->user->revokePermissionTo($spatiePermission);
            } else {
                $rsvp->user->givePermissionTo($spatiePermission);
            }

            // Sync with old columns for compatibility
            $rsvp->update([
                $permission => !$rsvp->$permission
            ]);

            $this->authorizeUser();
            session()->flash('permission_message', 'Guest permissions updated.');
        }
    }

    public function hasPermission($permission)
    {
        // If event is cancelled, only allow 'view' and 'owner'.
        if ($this->event->status === Event::STATUS_CANCELLED && !in_array($permission, ['view', 'owner'])) {
            return false;
        }

        if ($permission === 'view') return true;

        $user = Auth::user();
        if (!$user) return false;

        // The owner of the event (or user with 'owner' role in teams context) always has full access.
        if ($this->event->user_id === $user->id) return true;

        setPermissionsTeamId($this->event->id);
        
        // If they specifically asked for 'owner' permission (checking role in context)
        if ($permission === 'owner') {
            return $user->hasRole('owner');
        }

        // Return true if they are an owner OR have the specific permission.
        return $user->hasRole('owner') || $user->hasPermissionTo($permission);
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

        // Spatie Role Assignment
        setPermissionsTeamId($this->event->id);
        if ($status === 'attending' || $status === 'maybe') {
            Auth::user()->assignRole('guest');
        } elseif ($status === 'declined') {
            Auth::user()->removeRole('guest');
        }

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

    public function exportToCSV()
    {
        if (!$this->hasPermission('manage_invites')) return abort(403);
        
        return Excel::download(new GuestListExport($this->event->id), 'guest-list-' . \Illuminate\Support\Str::slug($this->event->title) . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportToExcel()
    {
        if (!$this->hasPermission('manage_invites')) return abort(403);

        return Excel::download(new GuestListExport($this->event->id), 'guest-list-' . \Illuminate\Support\Str::slug($this->event->title) . '.xlsx');
    }

    public function exportToPDF()
    {
        if (!$this->hasPermission('manage_invites')) return abort(403);

        $guests = $this->event->rsvps()
            ->whereIn('status', ['attending', 'maybe'])
            ->with('user')
            ->get();

        $pdf = Pdf::loadView('exports.guest-list-pdf', [
            'event' => $this->event,
            'guests' => $guests
        ]);

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->stream();
        }, 'guest-list-' . \Illuminate\Support\Str::slug($this->event->title) . '.pdf');
    }

    public function exportTasksToCSV()
    {
        if (!$this->hasPermission('manage_tasks')) return abort(403);
        
        return Excel::download(new TasksExport($this->event->id), 'tasks-' . \Illuminate\Support\Str::slug($this->event->title) . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportTasksToExcel()
    {
        if (!$this->hasPermission('manage_tasks')) return abort(403);

        return Excel::download(new TasksExport($this->event->id), 'tasks-' . \Illuminate\Support\Str::slug($this->event->title) . '.xlsx');
    }

    public function exportTasksToPDF()
    {
        if (!$this->hasPermission('manage_tasks')) return abort(403);

        $tasks = $this->event->tasks()
            ->with('assignee')
            ->orderBy('completed')
            ->orderBy('due_date')
            ->get();

        $pdf = Pdf::loadView('exports.tasks-pdf', [
            'event' => $this->event,
            'tasks' => $tasks
        ]);

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->stream();
        }, 'tasks-checklist-' . \Illuminate\Support\Str::slug($this->event->title) . '.pdf');
    }

    public function publishEvent($sendNow = false)
    {
        if (!$this->hasPermission('owner')) return abort(403);

        $this->event->update(['status' => Event::STATUS_PUBLISHED]);
        
        if ($sendNow) {
            $this->sendInvitations();
        }

        session()->flash('message', 'Event published successfully!');
        $this->dispatch('event-updated');
    }

    public function archiveEvent()
    {
        if (!$this->hasPermission('owner')) return abort(403);

        $this->event->update(['status' => Event::STATUS_ARCHIVED]);
        session()->flash('message', 'Event archived.');
        $this->dispatch('event-updated');
    }

    public function downloadTemplate()
    {
        if (!$this->hasPermission('manage_invites')) return abort(403);
        return Excel::download(new GuestTemplateExport, 'guest-import-template.xlsx');
    }

    public function startImport()
    {
        if (!$this->hasPermission('manage_invites')) return abort(403);
        $this->isImporting = true;
        $this->importedGuests = [];
        $this->guestImportFile = null;
    }

    public function uploadGuests()
    {
        if (!$this->hasPermission('manage_invites')) return abort(403);
        
        $this->validate([
            'guestImportFile' => 'required|mimes:xlsx,csv,xls|max:10240',
        ]);

        $rows = Excel::toCollection(new GuestImport, $this->guestImportFile->getRealPath())->first();
        
        if ($rows) {
            $this->importedGuests = $rows->map(function ($row) {
                // Determine keys based on the template headings
                // Maatwebsite Excel by default slugifies headings
                $name = $row['name'] ?? null;
                $email = $row['email'] ?? null;
                $phone = $row['phone_number'] ?? null;

                return [
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'errors' => $this->validateRow($name, $email),
                ];
            })->toArray();
        }

        $this->guestImportFile = null;
    }

    protected function validateRow($name, $email)
    {
        $errors = [];
        if (empty($name)) $errors['name'] = 'Name is required';
        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        return $errors;
    }

    public function updateImportedGuest($index, $field, $value)
    {
        if (!$this->hasPermission('manage_invites')) return abort(403);
        
        $this->importedGuests[$index][$field] = $value;
        $this->importedGuests[$index]['errors'] = $this->validateRow(
            $this->importedGuests[$index]['name'],
            $this->importedGuests[$index]['email']
        );
    }

    public function removeImportedGuest($index)
    {
        unset($this->importedGuests[$index]);
        $this->importedGuests = array_values($this->importedGuests);
    }

    public function finalizeImport($asDraft = true)
    {
        if (!$this->hasPermission('manage_invites')) return abort(403);

        // Check for any remaining errors
        foreach ($this->importedGuests as $guest) {
            if (!empty($guest['errors'])) {
                session()->flash('error', 'Please correct all errors before importing.');
                return;
            }
        }

        $count = count($this->importedGuests);
        foreach ($this->importedGuests as $guestData) {
            // Check if already invited to avoid duplicates
            $existingInvite = $this->event->invites()
                ->where('invitee_email', $guestData['email'])
                ->first();
            
            if ($existingInvite) continue;

            $this->event->invites()->create([
                'inviter_id' => \Illuminate\Support\Facades\Auth::id(),
                'invitee_name' => $guestData['name'],
                'invitee_email' => $guestData['email'],
                'invitee_phone' => $guestData['phone'],
                'status' => $asDraft ? 'draft' : 'pending',
            ]);
        }

        $this->isImporting = false;
        $this->importedGuests = [];
        $this->draftSearch = '';
        session()->flash('message', $count . ' guests ' . ($asDraft ? 'saved as drafts.' : 'processed.'));
        $this->dispatch('event-updated');
    }

    public function deleteInvite($inviteId)
    {
        if (!$this->hasPermission('manage_invites')) return abort(403);

        $invite = $this->event->invites()->find($inviteId);
        if ($invite) {
            $invite->delete();
            if ($inviteId == $this->editingDraftId) {
                $this->isEditingDraft = false;
                $this->editingDraftId = null;
            }
            $this->event->refresh();
            session()->flash('message', 'Invitation removed.');
        }
    }

    public function sendDraftInvite($inviteId)
    {
        if (!$this->hasPermission('manage_invites')) return abort(403);

        $invite = $this->event->invites()->where('status', 'draft')->find($inviteId);
        if ($invite) {
            $this->processInvite($invite);
            session()->flash('message', 'Invitation sent to ' . $invite->invitee_email);
        }
    }

    public function sendAllDrafts()
    {
        if (!$this->hasPermission('manage_invites')) return abort(403);

        $drafts = $this->event->invites()->where('status', 'draft')->get();
        $count = 0;

        foreach ($drafts as $invite) {
            $this->processInvite($invite);
            $count++;
        }

        session()->flash('message', $count . ' invitations sent successfully!');
        $this->dispatch('event-updated');
    }

    public function openEditDraft($inviteId)
    {
        if (!$this->hasPermission('manage_invites')) return abort(403);

        $invite = $this->event->invites()->where('status', 'draft')->find($inviteId);
        if ($invite) {
            $this->editingDraftId = $invite->id;
            $this->editDraftName = $invite->invitee_name;
            $this->editDraftEmail = $invite->invitee_email;
            $this->editDraftPhone = $invite->invitee_phone;
            $this->isEditingDraft = true;
        }
    }

    public function updateDraft()
    {
        if (!$this->hasPermission('manage_invites')) return abort(403);

        $this->validate([
            'editDraftName' => 'required|string|max:255',
            'editDraftEmail' => 'required|email|max:255',
            'editDraftPhone' => 'nullable|string|max:20',
        ]);

        $invite = $this->event->invites()->where('status', 'draft')->find($this->editingDraftId);
        if ($invite) {
            $invite->update([
                'invitee_name' => $this->editDraftName,
                'invitee_email' => $this->editDraftEmail,
                'invitee_phone' => $this->editDraftPhone,
            ]);
            $this->isEditingDraft = false;
            $this->event->refresh();
            session()->flash('message', 'Draft invitation updated!');
        }
    }

    protected function processInvite($invite)
    {
        // Add notification for registered users
        $user = User::where('email', $invite->invitee_email)->first();
        if ($user) {
            $invite->update(['invitee_id' => $user->id]);
            
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'sender_id' => Auth::id(),
                'type' => 'invite',
                'title' => 'Event Invitation',
                'message' => Auth::user()->name . " has invited you to: " . $this->event->title,
                'related_id' => $invite->id,
            ]);
        }

        // Send Email
        \Illuminate\Support\Facades\Mail::to($invite->invitee_email)
            ->send(new \App\Mail\EventInvitation($this->event, Auth::user(), $invite->message));
        
        $invite->update([
            'status' => 'pending',
            'invited_at' => now()
        ]);
    }

    public function toggleInviteSelection($inviteId)
    {
        if (in_array($inviteId, $this->selectedInviteIds)) {
            $this->selectedInviteIds = array_diff($this->selectedInviteIds, [$inviteId]);
        } else {
            $this->selectedInviteIds[] = (int) $inviteId;
        }
        $this->selectedInviteIds = array_values($this->selectedInviteIds);
    }

    public function selectAllInvites()
    {
        $allIds = $this->event->invites()->pluck('id')->toArray();
        if (count($this->selectedInviteIds) === count($allIds)) {
            $this->selectedInviteIds = [];
        } else {
            $this->selectedInviteIds = $allIds;
        }
    }

    public function openBulkNotificationModal()
    {
        if (empty($this->selectedInviteIds)) {
            session()->flash('error', 'Please select at least one guest to notify.');
            return;
        }
        $this->showBulkNotificationModal = true;
    }

    public function closeBulkNotificationModal()
    {
        $this->showBulkNotificationModal = false;
        $this->bulkNotificationMessage = '';
    }

    public function sendBulkNotification()
    {
        if (!$this->hasPermission('edit_event')) return;
        if (empty($this->selectedInviteIds)) return;

        $this->validate(['bulkNotificationMessage' => 'required|string|max:5000']);

        $invites = \App\Models\Invite::whereIn('id', $this->selectedInviteIds)->get();

        foreach ($invites as $invite) {
            \Illuminate\Support\Facades\Mail::to($invite->invitee_email)
                ->send(new \App\Mail\BulkEventNotificationMail($this->event, $this->bulkNotificationMessage));

            if ($invite->invitee_id) {
                \App\Models\Notification::create([
                    'user_id' => $invite->invitee_id,
                    'sender_id' => Auth::id(),
                    'type' => 'info',
                    'title' => 'Event Update',
                    'message' => $this->bulkNotificationMessage,
                    'related_id' => $this->event->id,
                ]);
            }
        }

        $count = $invites->count();
        $this->reset(['selectedInviteIds', 'showBulkNotificationModal', 'bulkNotificationMessage']);
        
        session()->flash('message', 'Notifications sent successfully to ' . $count . ' guests!');
        $this->dispatch('event-updated');
    }

    public function confirmCancellation()
    {
        if (!$this->hasPermission('edit_event')) return abort(403);
        $this->isConfirmingCancellation = true;
    }

    public function startEditEvent()
    {
        if (!$this->hasPermission('edit_event')) return;

        $this->editTitle = $this->event->title;
        $this->editDescription = $this->event->description;
        $this->editDate = $this->event->date->format('Y-m-d\TH:i');
        $this->editLocation = $this->event->location;
        $this->editCategoryId = $this->event->category_id;
        $this->notifyGuests = false; // User requested that they choose to update
        $this->isEditingEvent = true;
    }

    public function cancelEditEvent()
    {
        $this->isEditingEvent = false;
        $this->reset(['editTitle', 'editDescription', 'editDate', 'editLocation', 'notifyGuests']);
    }

    public function updateEvent()
    {
        if (!$this->hasPermission('edit_event')) return;

        $this->validate([
            'editTitle' => 'required|string|max:255',
            'editDescription' => 'nullable|string',
            'editDate' => 'required|date|after_or_equal:now',
            'editLocation' => 'nullable|string|max:255',
            'editCategoryId' => 'required|exists:categories,id',
        ]);

        $oldData = [
            'title' => $this->event->title,
            'date' => $this->event->date->format('Y-m-d H:i'),
            'location' => $this->event->location,
        ];

        $this->event->title = $this->editTitle;
        $this->event->description = $this->editDescription;
        $this->event->date = \Carbon\Carbon::parse($this->editDate);
        $this->event->location = $this->editLocation;
        $this->event->category_id = $this->editCategoryId;

        $changes = [];
        if ($this->event->isDirty('title')) $changes['title'] = ['old' => $oldData['title'], 'new' => $this->editTitle];
        if ($this->event->isDirty('date')) $changes['date'] = ['old' => $oldData['date'], 'new' => \Carbon\Carbon::parse($this->editDate)->format('Y-m-d H:i')];
        if ($this->event->isDirty('location')) $changes['location'] = ['old' => $oldData['location'] ?: 'None', 'new' => $this->editLocation ?: 'None'];

        $this->event->save();

        if ($this->notifyGuests && !empty($changes)) {
            $this->notifyParticipantsOfUpdate($changes);
        }

        $this->isEditingEvent = false;
        $this->event->refresh();
        session()->flash('message', 'Event updated successfully.');
    }

    public function openNotifyLater()
    {
        if (!$this->hasPermission('edit_event')) return;
        $this->isNotifyingLater = true;
    }

    public function closeNotifyLater()
    {
        $this->isNotifyingLater = false;
        $this->reset(['notifyLaterMessage', 'notifyLaterFields']);
    }

    public function sendManualUpdate()
    {
        if (!$this->hasPermission('edit_event')) return;

        $changes = [];
        if (in_array('title', $this->notifyLaterFields)) {
            $changes['title'] = ['old' => 'Previous Title', 'new' => $this->event->title];
        }
        if (in_array('date', $this->notifyLaterFields)) {
            $changes['date'] = ['old' => 'Previous Date', 'new' => $this->event->date->format('Y-m-d H:i')];
        }
        if (in_array('location', $this->notifyLaterFields)) {
            $changes['location'] = ['old' => 'Previous Location', 'new' => $this->event->location ?: 'None'];
        }

        if (empty($changes) && empty($this->notifyLaterMessage)) {
            session()->flash('notification_error', 'Please select at least one change to notify or enter a message.');
            return;
        }

        $this->notifyParticipantsOfUpdate($changes, $this->notifyLaterMessage);

        $this->closeNotifyLater();
        session()->flash('message', 'Notification sent to all guests and participants.');
    }

    protected function notifyParticipantsOfUpdate($changes, $customMessage = null)
    {
        $updatedBy = Auth::user();
        
        // 1. Get all invited users
        $inviteesWithUser = $this->event->invites()->whereNotNull('invitee_id')->with('invitee')->get()->pluck('invitee');
        $inviteesEmailOnly = $this->event->invites()->whereNull('invitee_id')->get();
        
        // 2. Get all RSVPs
        $rsvpUsers = $this->event->rsvps()->with('user')->get()->pluck('user');
        
        // 3. Get all organizers
        $organizers = $this->event->organizers()->get();

        // Unique recipients
        $recipients = collect()
            ->merge($inviteesWithUser)
            ->merge($rsvpUsers)
            ->merge($organizers)
            ->where('id', '!==', $updatedBy->id)
            ->unique('id');

        $notification = new \App\Notifications\EventUpdatedNotification($this->event, $changes, $updatedBy, $customMessage);
        
        // Send Notifications
        foreach ($recipients as $recipient) {
            $recipient->notify($notification);
        }

        // Handle email-only invitees
        foreach ($inviteesEmailOnly as $invite) {
            if ($invite->invitee_email) {
                \Illuminate\Support\Facades\Mail::to($invite->invitee_email)
                    ->send(new \App\Mail\EventUpdatedMail($this->event, $changes, $updatedBy, $customMessage));
            }
        }
    }

    public function cancelEvent()
    {
        if (!$this->hasPermission('edit_event')) return abort(403);

        $this->validate(['cancellationReason' => 'nullable|string|max:1000']);

        $this->event->update([
            'status' => Event::STATUS_CANCELLED,
            'cancellation_reason' => $this->cancellationReason,
        ]);

        // If the event was published, notify all invited users and organizers
        if ($this->event->status === Event::STATUS_CANCELLED) {
            $this->notifyParticipantsOfCancellation();
        }

        $this->isConfirmingCancellation = false;
        session()->flash('message', 'Event cancelled successfully.');
    }

    protected function notifyParticipantsOfCancellation()
    {
        $cancelledBy = Auth::user();
        
        // 1. Get all invited users (those with an entry in the 'invites' table)
        $invites = $this->event->invites()->get();
        // 2. Get all RSVPs (those who responded)
        $rsvps = $this->event->rsvps()->with('user')->get();
        // 3. Get all organizers
        $organizers = $this->event->organizers()->get();

        // Track who we notified to avoid duplicates
        $notifiedUserIds = [];
        $notifiedEmails = [];

        // Notify Invitees (Database + Email if possible)
        foreach ($invites as $invite) {
            if ($invite->invitee_id && !in_array($invite->invitee_id, $notifiedUserIds)) {
                $this->sendCancellationInAppNotification($invite->invitee_id, $this->event, $cancelledBy, $this->cancellationReason);
                $notifiedUserIds[] = $invite->invitee_id;
            }
            
            if ($invite->invitee_email && !in_array($invite->invitee_email, $notifiedEmails)) {
                \Illuminate\Support\Facades\Mail::to($invite->invitee_email)
                    ->send(new \App\Mail\EventCancelledMail($this->event, $cancelledBy, $this->cancellationReason));
                $notifiedEmails[] = $invite->invitee_email;
            }
        }

        // Notify RSVPs (In-app)
        foreach ($rsvps as $rsvp) {
            if ($rsvp->user_id && !in_array($rsvp->user_id, $notifiedUserIds)) {
                $this->sendCancellationInAppNotification($rsvp->user_id, $this->event, $cancelledBy, $this->cancellationReason);
                $notifiedUserIds[] = $rsvp->user_id;

                if ($rsvp->user->email && !in_array($rsvp->user->email, $notifiedEmails)) {
                    \Illuminate\Support\Facades\Mail::to($rsvp->user->email)
                        ->send(new \App\Mail\EventCancelledMail($this->event, $cancelledBy, $this->cancellationReason));
                    $notifiedEmails[] = $rsvp->user->email;
                }
            }
        }

        // Notify Organizers (Exclude the one who cancelled)
        foreach ($organizers as $organizer) {
            if ($organizer->id !== $cancelledBy->id && !in_array($organizer->id, $notifiedUserIds)) {
                $this->sendCancellationInAppNotification($organizer->id, $this->event, $cancelledBy, $this->cancellationReason);
                $notifiedUserIds[] = $organizer->id;

                 if ($organizer->email && !in_array($organizer->email, $notifiedEmails)) {
                    \Illuminate\Support\Facades\Mail::to($organizer->email)
                        ->send(new \App\Mail\EventCancelledMail($this->event, $cancelledBy, $this->cancellationReason));
                    $notifiedEmails[] = $organizer->email;
                }
            }
        }
    }

    protected function sendCancellationInAppNotification($userId, $event, $cancelledBy, $reason)
    {
        \App\Models\Notification::create([
            'user_id' => $userId,
            'sender_id' => $cancelledBy->id,
            'type' => 'event_cancelled',
            'title' => 'Event Cancelled',
            'message' => "The event '{$event->title}' has been cancelled by {$cancelledBy->name}." . ($reason ? " Reason: {$reason}" : ""),
            'related_id' => $event->id,
        ]);
    }

    public function sendInvitations()
    {
        if (!$this->hasPermission('manage_invites')) return abort(403);

        $invites = $this->event->invites()->where('status', 'pending')->get();
        $notifiedCount = 0;

        foreach ($invites as $invite) {
            // Check if notification already exists to avoid duplicates
            $exists = \App\Models\Notification::where('user_id', $invite->invitee_id)
                ->where('related_id', $invite->id)
                ->where('type', 'invite')
                ->exists();

            if (!$exists) {
                if ($invite->invitee_id) {
                    \App\Models\Notification::create([
                        'user_id' => $invite->invitee_id,
                        'sender_id' => Auth::id(),
                        'type' => 'invite',
                        'title' => 'Event Invitation',
                        'message' => Auth::user()->name . " has invited you to: " . $this->event->title,
                        'related_id' => $invite->id,
                    ]);
                }

                \Illuminate\Support\Facades\Mail::to($invite->invitee_email)
                    ->send(new \App\Mail\EventInvitation($this->event, Auth::user(), $invite->message));
                
                $notifiedCount++;
            }
        }

        // Also notify organizers who haven't been notified
        $organizers = $this->event->organizers()->get();
        foreach ($organizers as $organizer) {
            $exists = \App\Models\Notification::where('user_id', $organizer->id)
                ->where('related_id', $this->event->id)
                ->where('type', 'info')
                ->where('title', 'Organizer Role Assigned')
                ->exists();

            if (!$exists) {
                \App\Models\Notification::create([
                    'user_id' => $organizer->id,
                    'sender_id' => Auth::id(),
                    'type' => 'info',
                    'title' => 'Organizer Role Assigned',
                    'message' => "You have been assigned as an organizer for: " . $this->event->title,
                    'related_id' => $this->event->id,
                ]);
                $notifiedCount++;
            }
        }

        session()->flash('message', "Sent $notifiedCount invitations/notifications.");
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
