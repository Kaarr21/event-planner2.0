<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventMedia extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'folder_type',
        'visibility',
    ];

    /**
     * Get the event that owns the media.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who uploaded the media.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the users who have specific access to this media.
     */
    public function authorizedUsers()
    {
        return $this->belongsToMany(User::class, 'event_media_access', 'event_media_id', 'user_id');
    }

    /**
     * Check if the user has access to the private/restricted media.
     */
    public function canBeAccessedBy(User $user)
    {
        if ($this->visibility === 'public') {
            return true;
        }

        if ($this->user_id === $user->id) {
            return true;
        }

        if ($this->event->user_id === $user->id) {
            return true;
        }

        return $this->authorizedUsers()->where('user_id', $user->id)->exists();
    }
}
