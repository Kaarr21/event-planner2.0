<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Invite extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'event_id',
        'inviter_id',
        'invitee_name',
        'invitee_email',
        'invitee_phone',
        'invitee_id',
        'status',
        'message',
        'responded_at',
    ];

    /**
     * Get the event the invite is for.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who sent the invite.
     */
    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }

    /**
     * Get the user who received the invite.
     */
    public function invitee()
    {
        return $this->belongsTo(User::class, 'invitee_id');
    }
}
