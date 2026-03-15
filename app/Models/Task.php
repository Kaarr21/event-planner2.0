<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'completed',
        'due_date',
        'event_id',
        'assigned_to',
        'assignment_status',
        'completion_comment',
    ];

    /**
     * Get the event that owns the task.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user assigned to the task.
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
