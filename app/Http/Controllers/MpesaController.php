<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\MpesaTransaction;
use App\Services\Mpesa\MpesaSTKService;
use App\Services\Mpesa\MpesaCallbackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    protected $stkService;
    protected $callbackService;

    public function __construct(MpesaSTKService $stkService, MpesaCallbackService $callbackService)
    {
        $this->stkService = $stkService;
        $this->callbackService = $callbackService;
    }

    /**
     * Initiate STK Push for an order.
     */
    public function initiatePush(Request $request, Order $order)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $response = $this->stkService->initiatePush(
            $request->phone,
            $order->total_amount,
            "Order #{$order->id}",
            "Payment for {$order->event->title}"
        );

        if (isset($response['ResponseCode']) && $response['ResponseCode'] === '0') {
            MpesaTransaction::create([
                'order_id' => $order->id,
                'phone_number' => $request->phone,
                'amount' => $order->total_amount,
                'merchant_request_id' => $response['MerchantRequestID'],
                'checkout_request_id' => $response['CheckoutRequestID'],
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'STK Push initiated successfully. Please check your phone.',
                'checkout_request_id' => $response['CheckoutRequestID'],
            ]);
        }

        Log::error('Mpesa STK Push failed', ['response' => $response]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to initiate STK Push. Please try again.',
        ], 400);
    }

    /**
     * Handle STK Push callback from Safaricom.
     */
    public function handleCallback(Request $request)
    {
        Log::info('Mpesa Callback received', $request->all());

        $this->callbackService->handleStkCallback($request->all());

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    /**
     * Check payment status of an order.
     */
    public function checkStatus(Order $order)
    {
        return response()->json([
            'status' => $order->status,
        ]);
    }
}
