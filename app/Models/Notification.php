<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'sender_id',
        'type',
        'title',
        'message',
        'related_id',
        'read',
    ];

    /**
     * Get the user who received the notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who sent the notification.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the invite related to the notification.
     */
    public function invite()
    {
        return $this->belongsTo(Invite::class, 'related_id');
    }
}
