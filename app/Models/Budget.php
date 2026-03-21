<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'event_id',
        'category',
        'item_name',
        'description',
        'estimated_amount',
        'actual_amount',
        'paid_amount',
        'due_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'estimated_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
