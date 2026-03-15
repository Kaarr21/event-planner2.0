<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use App\Models\Task;
use App\Models\RSVP;
use Illuminate\Support\Facades\Auth;

class Show extends Component
{
    public Event $event;
    public $userPermissions = [];
    
    // Add task properties
    public $newTaskTitle;
    public $newTaskDueDate;
    
    // Edit task properties
    public $editingTaskId = null;
    public $editTaskTitle;
    public $editTaskDueDate;
    public $editTaskDescription;
    
    // AI Suggestions
    public array $aiSuggestions = [];

    protected $rules = [
        'newTaskTitle' => 'required|string|max:255',
        'newTaskDueDate' => 'nullable|date',
        'editTaskTitle' => 'required|string|max:255',
        'editTaskDueDate' => 'nullable|date',
        'editTaskDescription' => 'nullable|string',
    ];

    public function mount(Event $event)
    {
        $this->event = $event;
        
        // Authorization check
        if ($event->user_id !== Auth::id()) {
            $organizer = $event->organizers()->where('user_id', Auth::id())->first();
            
            if (!$organizer) {
                return redirect()->route('events.index')->with('error', 'You do not have access to this event.');
            }
            
            $permissions = $organizer->pivot->permissions ?? [];
            if (!is_array($permissions)) {
                $permissions = json_decode($permissions, true) ?? [];
            }
            $this->userPermissions = $permissions;
        } else {
            // Owner has all permissions
            $this->userPermissions = ['edit_event', 'manage_invites', 'manage_tasks', 'view_rsvps', 'owner'];
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
        ]);

        $this->event->tasks()->create([
            'title' => $this->newTaskTitle,
            'due_date' => $this->newTaskDueDate,
            'completed' => false,
        ]);

        $this->reset(['newTaskTitle', 'newTaskDueDate']);
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
        }
    }
    
    public function cancelEditTask()
    {
        $this->reset(['editingTaskId', 'editTaskTitle', 'editTaskDueDate', 'editTaskDescription']);
    }
    
    public function saveTask()
    {
        if (!$this->hasPermission('manage_tasks')) return;

        $this->validate([
            'editTaskTitle' => 'required|string|max:255',
            'editTaskDueDate' => 'nullable|date',
            'editTaskDescription' => 'nullable|string',
        ]);

        if ($this->editingTaskId) {
            $task = Task::find($this->editingTaskId);
            if ($task && $task->event_id === $this->event->id) {
                $task->update([
                    'title' => $this->editTaskTitle,
                    'due_date' => $this->editTaskDueDate,
                    'description' => $this->editTaskDescription,
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

        $this->event->refresh();
        session()->flash('rsvp_message', 'RSVP updated.');
    }

    public function render()
    {
        return view('livewire.events.show', [
            'tasks' => $this->event->tasks()->orderBy('completed')->orderBy('due_date')->get(),
            'rsvps' => $this->event->rsvps()->with('user')->get(),
            'userRSVP' => RSVP::where('event_id', $this->event->id)->where('user_id', Auth::id())->first(),
        ])->layout('layouts.app');
    }
}
