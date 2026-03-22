<?php

namespace App\Services\Mpesa;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MpesaAuthService
{
    /**
     * Get the M-Pesa OAuth Access Token.
     *
     * @return string
     */
    public function getAccessToken(): string
    {
        return Cache::remember('mpesa_access_token', 3500, function () {
            $credentials = base64_encode(config('mpesa.consumer_key') . ':' . config('mpesa.consumer_secret'));
            
            $url = config('mpesa.environment') === 'sandbox' 
                ? 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
                : 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $credentials,
            ])->get($url);

            $data = $response->json();

            if (!isset($data['access_token'])) {
                Log::error('MpesaAuthService: Failed to get access token', [
                    'status' => $response->status(),
                    'response' => $data,
                ]);
                throw new \Exception('Failed to authenticate with M-Pesa API. Please check your credentials.');
            }

            return $data['access_token'];
        });
    }
}
