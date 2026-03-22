<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_id',
        'name',
        'description',
        'price',
        'capacity',
        'sold_count',
        'sale_start_date',
        'sale_end_date',
        'min_per_purchase',
        'max_per_purchase',
        'is_transferable',
        'type',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'capacity' => 'integer',
        'sold_count' => 'integer',
        'sale_start_date' => 'datetime',
        'sale_end_date' => 'datetime',
        'min_per_purchase' => 'integer',
        'max_per_purchase' => 'integer',
        'is_transferable' => 'boolean',
    ];

    /**
     * Get the event that owns the ticket type.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the tickets issued for this type.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Check if the ticket type is currently available for sale.
     */
    public function getIsAvailableAttribute()
    {
        $now = now();

        // Check date window
        if ($this->sale_start_date && $now->lessThan($this->sale_start_date)) {
            return false;
        }

        if ($this->sale_end_date && $now->greaterThan($this->sale_end_date)) {
            return false;
        }

        // Check capacity
        if ($this->capacity !== null && $this->sold_count >= $this->capacity) {
            return false;
        }

        return true;
    }
}
