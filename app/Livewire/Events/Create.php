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
    public $inviteEmails;
    public $inviteMessage;
    public $category_id;
    public $newCategoryName;

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

        $this->category_id = $category->id;
        $this->closeCategoryModal();
    }

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
        'inviteEmails' => 'nullable|string',
        'inviteMessage' => 'nullable|string|max:500',
        'category_id' => 'required|exists:categories,id',
    ];

    public function save()
    {
        $this->validate();

        $event = Event::create([
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date,
            'location' => $this->location,
            'user_id' => Auth::id(),
            'status' => Event::STATUS_PUBLISHED, // Ensure it's published to send invites
            'category_id' => $this->category_id,
        ]);

        if ($this->inviteEmails) {
            $emails = array_map('trim', explode(',', str_replace(["\n", "\r"], ',', $this->inviteEmails)));
            $emails = array_filter($emails, fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL));

            foreach ($emails as $email) {
                $invitee = \App\Models\User::where('email', $email)->first();
                
                $invite = \App\Models\Invite::create([
                    'event_id' => $event->id,
                    'inviter_id' => Auth::id(),
                    'invitee_email' => $email,
                    'invitee_id' => $invitee?->id,
                    'message' => $this->inviteMessage,
                    'status' => 'pending',
                ]);

                // Send Mail
                \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\EventInvitation($event, Auth::user(), $this->inviteMessage));
            }
        } else {
            session()->flash('show_invite_reminder', true);
            session()->flash('new_event_id', $event->id);
        }

        session()->flash('message', 'Event successfully created.');

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.events.create')->layout('layouts.app');
    }
}
