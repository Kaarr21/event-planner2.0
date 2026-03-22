<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted()
    {
        static::deleting(function ($event) {
            $event->tasks()->delete();
            $event->budgets()->delete();
            $event->invites()->delete();
            $event->rsvps()->delete();
            $event->media()->delete();
        });
    }
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ONGOING = 'ongoing';
    const STATUS_ENDED = 'ended';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_CANCELLED = 'cancelled';

    const VISIBILITY_DRAFT = 'draft';
    const VISIBILITY_PUBLISHED = 'published';
    const VISIBILITY_PRIVATE = 'private';

    const RECURRENCE_DAILY = 'daily';
    const RECURRENCE_WEEKLY = 'weekly';
    const RECURRENCE_BI_WEEKLY = 'bi-weekly';
    const RECURRENCE_MONTHLY = 'monthly';
    const RECURRENCE_YEARLY = 'yearly';
    const RECURRENCE_CUSTOM = 'custom';

    const UNIT_DAY = 'day';
    const UNIT_WEEK = 'week';
    const UNIT_MONTH = 'month';
    const UNIT_YEAR = 'year';

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
        'cancellation_reason',
        'organization_id',
        'category_id',
        'banner_image_path',
        'start_at',
        'end_at',
        'timezone',
        'venue_type',
        'online_link',
        'capacity',
        'visibility',
        'is_recurring',
        'recurrence_frequency',
        'recurrence_interval',
        'recurrence_unit',
        'recurrence_end_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'date' => 'datetime',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'status' => 'string',
        'is_recurring' => 'boolean',
        'recurrence_interval' => 'integer',
        'recurrence_end_at' => 'datetime',
    ];

    /**
     * Get the category for the event.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user who created the event.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the organization that owns the event.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
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
     * Get the budget items for the event.
     */
    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    /**
     * Scope for cancelled events relevant to a user.
     */
    public function scopeCancelledForUser($query, $userId)
    {
        return $query->where('status', self::STATUS_CANCELLED)
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhereHas('organizers', function ($oq) use ($userId) {
                        $oq->where('user_id', $userId);
                    })
                    ->orWhereHas('invites', function ($iq) use ($userId) {
                        $iq->where('invitee_id', $userId);
                    })
                    ->orWhereHas('rsvps', function ($rq) use ($userId) {
                        $rq->where('user_id', $userId);
                    });
            });
    }

    /**
     * Get a specific setting or a default value.
     */
    public function getSetting(string $key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Check if the event is currently ongoing.
     */
    public function getIsOngoingAttribute()
    {
        if ($this->status === self::STATUS_CANCELLED) return false;
        
        $now = now();
        return $this->start_at && $this->end_at && 
               $now->between($this->start_at, $this->end_at);
    }

    /**
     * Check if the event has ended.
     */
    public function getIsEndedAttribute()
    {
        if ($this->status === self::STATUS_CANCELLED) return false;

        return $this->end_at && now()->greaterThan($this->end_at);
    }

    /**
     * Scope for upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where('start_at', '>', now());
    }

    /**
     * Scope for ongoing events.
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now());
    }
}
