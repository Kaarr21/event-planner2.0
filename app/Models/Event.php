<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
        'user_id',
        'settings',
        'latitude',
        'longitude',
        'google_place_id',
        'status',
    ];

    protected $casts = [
        'settings' => 'array',
        'date' => 'datetime',
        'status' => 'string',
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
     * Alias for invites, often used when filtering for the current user.
     */
    public function receivedInvites()
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

    /**
     * Get the media for the event.
     */
    public function media()
    {
        return $this->hasMany(EventMedia::class);
    }

    /**
     * Get a setting value.
     */
    public function getSetting($key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }
}
