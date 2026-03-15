<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'related_id',
        'read',
    ];

    /**
     * Get the invite related to the notification.
     */
    public function invite()
    {
        return $this->belongsTo(Invite::class, 'related_id');
    }
}
