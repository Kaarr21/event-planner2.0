<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use App\Models\Category;
use Livewire\WithPagination;

class Discovery extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryId = null;
    public $sort = 'upcoming'; // upcoming, latest

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryId' => ['except' => null],
        'sort' => ['except' => 'upcoming'],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Event::where('status', Event::STATUS_PUBLISHED)
            ->where('visibility', Event::VISIBILITY_PUBLISHED)
            ->where('end_at', '>=', now())
            ->with(['category', 'creator'])
            ->withCount('rsvps');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }

        if ($this->sort === 'upcoming') {
            $query->orderBy('start_at', 'asc');
        } else {
            $query->latest();
        }

        $events = $query->paginate(12);
        
        $categories = Category::whereHas('events', function($q) {
            $q->where('status', Event::STATUS_PUBLISHED)
              ->where('visibility', Event::VISIBILITY_PUBLISHED);
        })->get();

        return view('livewire.events.discovery', [
            'events' => $events,
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
