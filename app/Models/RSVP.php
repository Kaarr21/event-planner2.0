<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RSVP extends Model
{
    protected $table = 'rsvps';

    protected $fillable = [
        'user_id',
        'event_id',
        'status',
        'message',
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
