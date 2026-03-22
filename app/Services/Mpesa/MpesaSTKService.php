<?php

namespace App\Services\Mpesa;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\PhoneHelper;

class MpesaSTKService
{
    protected $authService;

    public function __construct(MpesaAuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Initiate STK Push request.
     *
     * @param string $phone
     * @param float $amount
     * @param string $reference
     * @param string $description
     * @return array
     */
    public function initiatePush($phone, $amount, $reference, $description = 'Payment')
    {
        $accessToken = $this->authService->getAccessToken();
        $url = config('mpesa.environment') === 'sandbox'
            ? 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'
            : 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $timestamp = now()->format('YmdHis');
        $password = base64_encode(config('mpesa.shortcode') . config('mpesa.passkey') . $timestamp);

        $response = Http::withToken($accessToken)->post($url, [
            'BusinessShortCode' => config('mpesa.shortcode'),
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => round($amount),
            'PartyA' => PhoneHelper::format($phone),
            'PartyB' => config('mpesa.shortcode'),
            'PhoneNumber' => PhoneHelper::format($phone),
            'CallBackURL' => config('mpesa.callback_url'),
            'AccountReference' => $reference,
            'TransactionDesc' => $description,
        ]);

        $data = $response->json();

        if ($response->failed()) {
            Log::error('MpesaSTKService: STK Push failed', [
                'status' => $response->status(),
                'response' => $data,
            ]);
        }

        return $data;
    }
}
