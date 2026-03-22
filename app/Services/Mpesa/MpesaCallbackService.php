<?php

namespace App\Services\Mpesa;

use App\Models\Order;
use App\Models\MpesaTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MpesaCallbackService
{
    /**
     * Handle the STK Push callback from Safaricom.
     *
     * @param array $payload
     * @return array
     */
    public function handleStkCallback(array $payload)
    {
        $stkCallback = $payload['Body']['stkCallback'];
        $checkoutRequestId = $stkCallback['CheckoutRequestID'];
        $resultCode = $stkCallback['ResultCode'];
        $resultDesc = $stkCallback['ResultDesc'];

        $transaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)->first();

        if (!$transaction) {
            Log::error("MpesaCallback: Transaction not found for CheckoutRequestID: {$checkoutRequestId}");
            return ['status' => 'error', 'message' => 'Transaction not found'];
        }

        if ($resultCode == 0) {
            // Success
            $callbackMetadata = $stkCallback['CallbackMetadata']['Item'];
            $mpesaReceiptNumber = $this->extractMetadata($callbackMetadata, 'MpesaReceiptNumber');
            
            DB::transaction(function () use ($transaction, $resultCode, $resultDesc, $mpesaReceiptNumber) {
                $transaction->update([
                    'status' => 'success',
                    'result_code' => $resultCode,
                    'result_desc' => $resultDesc,
                    'mpesa_receipt_number' => $mpesaReceiptNumber,
                ]);

                $order = $transaction->order;
                $order->update([
                    'status' => 'confirmed',
                    'payment_method' => 'mpesa',
                    'payment_reference' => $mpesaReceiptNumber,
                ]);

                // Trigger ticket generation
                app(\App\Services\TicketService::class)->generateTickets($order);

                Log::info("MpesaCallback: Order {$order->id} paid successfully via M-Pesa. Receipt: {$mpesaReceiptNumber}");
            });

            return ['status' => 'success'];
        } else {
            // Failure
            $transaction->update([
                'status' => 'failed',
                'result_code' => $resultCode,
                'result_desc' => $resultDesc,
            ]);

            Log::warning("MpesaCallback: Payment failed for CheckoutRequestID: {$checkoutRequestId}. Code: {$resultCode}, Desc: {$resultDesc}");
            
            return ['status' => 'failed'];
        }
    }

    /**
     * Extract specific value from callback metadata.
     *
     * @param array $items
     * @param string $name
     * @return mixed|null
     */
    private function extractMetadata(array $items, string $name)
    {
        foreach ($items as $item) {
            if ($item['Name'] === $name) {
                return $item['Value'] ?? null;
            }
        }
        return null;
    }
}
