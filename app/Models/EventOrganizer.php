<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EventOrganizer extends Pivot
{
    protected $table = 'event_organizers';

    protected $casts = [
        'permissions' => 'array',
    ];
}
