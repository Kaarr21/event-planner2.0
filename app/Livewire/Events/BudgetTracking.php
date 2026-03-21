<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use App\Models\Budget;
use Illuminate\Support\Facades\Auth;

class BudgetTracking extends Component
{
    public Event $event;
    public $userPermissions = [];
    public $userRole = null;

    public $category;
    public $item_name;
    public $description;
    public $estimated_amount;
    public $actual_amount;
    public $paid_amount;
    public $due_date;
    public $status = 'pending';
    public $notes;

    public $editingBudgetId = null;
    public $isAddingItem = false;

    protected $rules = [
        'category' => 'required|string|max:255',
        'item_name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'estimated_amount' => 'required|numeric|min:0',
        'actual_amount' => 'nullable|numeric|min:0',
        'paid_amount' => 'nullable|numeric|min:0',
        'due_date' => 'nullable|date',
        'status' => 'required|string|in:pending,partially_paid,paid,cancelled',
        'notes' => 'nullable|string',
    ];

    public function mount(Event $event, $userPermissions = [], $userRole = null)
    {
        $this->event = $event;
        $this->userPermissions = $userPermissions;
        $this->userRole = $userRole;
    }

    public function hasPermission($permission)
    {
        if ($this->event->status === Event::STATUS_CANCELLED && $permission !== 'view') {
            return false;
        }

        $user = Auth::user();
        if (!$user) return false;

        setPermissionsTeamId($this->event->id);
        return $user->hasRole('owner') || $user->hasPermissionTo($permission);
    }

    public function saveBudgetItem()
    {
        if (!$this->hasPermission('edit_event')) return;

        $this->validate();

        if ($this->editingBudgetId) {
            $budget = Budget::find($this->editingBudgetId);
            if ($budget && $budget->event_id === $this->event->id) {
                $budget->update([
                    'category' => $this->category,
                    'item_name' => $this->item_name,
                    'description' => $this->description,
                    'estimated_amount' => $this->estimated_amount,
                    'actual_amount' => $this->actual_amount,
                    'paid_amount' => $this->paid_amount ?? 0,
                    'due_date' => $this->due_date,
                    'status' => $this->status,
                    'notes' => $this->notes,
                ]);
            }
        } else {
            $this->event->budgets()->create([
                'category' => $this->category,
                'item_name' => $this->item_name,
                'description' => $this->description,
                'estimated_amount' => $this->estimated_amount,
                'actual_amount' => $this->actual_amount,
                'paid_amount' => $this->paid_amount ?? 0,
                'due_date' => $this->due_date,
                'status' => $this->status,
                'notes' => $this->notes,
            ]);
        }

        $this->resetForm();
        session()->flash('budget_message', 'Budget item saved successfully.');
    }

    public function editBudgetItem($id)
    {
        if (!$this->hasPermission('edit_event')) return;

        $budget = Budget::find($id);
        if ($budget && $budget->event_id === $this->event->id) {
            $this->editingBudgetId = $budget->id;
            $this->category = $budget->category;
            $this->item_name = $budget->item_name;
            $this->description = $budget->description;
            $this->estimated_amount = $budget->estimated_amount;
            $this->actual_amount = $budget->actual_amount;
            $this->paid_amount = $budget->paid_amount;
            $this->due_date = $budget->due_date ? $budget->due_date->format('Y-m-d') : null;
            $this->status = $budget->status;
            $this->notes = $budget->notes;
            $this->isAddingItem = true;
        }
    }

    public function deleteBudgetItem($id)
    {
        if (!$this->hasPermission('edit_event')) return;

        $budget = Budget::find($id);
        if ($budget && $budget->event_id === $this->event->id) {
            $budget->delete();
        }
    }

    public function resetForm()
    {
        $this->reset(['category', 'item_name', 'description', 'estimated_amount', 'actual_amount', 'paid_amount', 'due_date', 'status', 'notes', 'editingBudgetId', 'isAddingItem']);
    }

    public function render()
    {
        $budgets = $this->event->budgets()->orderBy('category')->orderBy('item_name')->get();
        
        $totalEstimated = $budgets->sum('estimated_amount');
        $totalActual = $budgets->sum('actual_amount');
        $totalPaid = $budgets->sum('paid_amount');
        
        $categories = $budgets->groupBy('category')->map(function ($items) {
            return [
                'estimated' => $items->sum('estimated_amount'),
                'actual' => $items->sum('actual_amount'),
                'paid' => $items->sum('paid_amount'),
                'count' => $items->count(),
            ];
        });

        return view('livewire.events.budget-tracking', [
            'budgets' => $budgets,
            'totalEstimated' => $totalEstimated,
            'totalActual' => $totalActual,
            'totalPaid' => $totalPaid,
            'categories' => $categories,
        ]);
    }
}
