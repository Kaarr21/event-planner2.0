<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
        'user_id',
    ];

    /**
     * Get the user who created the event.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the tasks for the event.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the RSVPs for the event.
     */
    public function rsvps()
    {
        return $this->hasMany(RSVP::class);
    }

    /**
     * Get the invites for the event.
     */
    public function invites()
    {
        return $this->hasMany(Invite::class);
    }

    /**
     * Get the organizers for the event.
     */
    public function organizers()
    {
        return $this->belongsToMany(User::class, 'event_organizers')
            ->using(EventOrganizer::class)
            ->withPivot('permissions')
            ->withTimestamps();
    }
}
