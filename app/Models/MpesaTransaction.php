<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaTransaction extends Model
{
    protected $fillable = [
        'order_id',
        'phone_number',
        'amount',
        'merchant_request_id',
        'checkout_request_id',
        'status',
        'mpesa_receipt_number',
        'result_code',
        'result_desc',
    ];

    /**
     * Get the order associated with the transaction.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
