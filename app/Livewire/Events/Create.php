<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    public $title;
    public $description;
    public $date;
    public $location;

    public function generateAIDescription(\App\Services\AIService $aiService)
    {
        if (!$this->title) {
            $this->addError('title', 'Please provide a title first.');
            return;
        }

        $this->description = $aiService->generateDescription($this->title, $this->location ?: '');
    }

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'date' => 'required|date|after_or_equal:now',
        'location' => 'nullable|string|max:255',
    ];

    public function save()
    {
        $this->validate();

        Event::create([
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date,
            'location' => $this->location,
            'user_id' => Auth::id(),
        ]);

        session()->flash('message', 'Event successfully created.');

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.events.create')->layout('layouts.app');
    }
}
