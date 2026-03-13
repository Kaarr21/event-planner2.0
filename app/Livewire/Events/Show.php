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
    public $newTaskTitle;
    public $newTaskDueDate;

    public function suggestAITasks(\App\Services\AIService $aiService)
    {
        $suggestions = $aiService->suggestTasks($this->event->title, $this->event->description ?: '');
        
        foreach ($suggestions as $taskTitle) {
            $this->event->tasks()->create([
                'title' => $taskTitle,
                'completed' => false,
            ]);
        }

        $this->event->refresh();
        session()->flash('task_message', 'AI suggested tasks added!');
    }

    protected $rules = [
        'newTaskTitle' => 'required|string|max:255',
        'newTaskDueDate' => 'nullable|date',
    ];

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function addTask()
    {
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

    public function toggleTask($taskId)
    {
        $task = Task::find($taskId);
        if ($task && $task->event_id === $this->event->id) {
            $task->completed = !$task->completed;
            $task->save();
            $this->event->refresh();
        }
    }

    public function deleteTask($taskId)
    {
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
