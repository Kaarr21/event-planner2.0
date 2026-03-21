<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class RSVP extends Model
{
    use SoftDeletes;
    protected $table = 'rsvps';

    protected $fillable = [
        'user_id',
        'event_id',
        'status',
        'message',
        'can_view_guests',
        'can_view_checklist',
    ];

    /**
     * Get the user that made the RSVP.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event that the RSVP is for.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
