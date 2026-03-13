<?php

namespace App\Livewire;

use Livewire\Component;

class Contact extends Component
{
    public $name = '';
    public $email = '';
    public $phone = '';
    public $message = '';
    public $submitted = false;

    public function submit()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email',
            'message' => 'required|min:10',
        ]);

        // In a real app, you'd send an email here.
        // For now, we'll just log it and show a success message.
        \Illuminate\Support\Facades\Log::info("Contact form submitted: " . json_encode([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'message' => $this->message,
        ]));

        $this->reset(['name', 'email', 'phone', 'message']);
        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.contact')->layout('layouts.public');
    }
}
