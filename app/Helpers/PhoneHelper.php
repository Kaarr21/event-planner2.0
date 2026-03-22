<?php

namespace App\Helpers;

class PhoneHelper
{
    /**
     * Format phone number to 254XXXXXXXXX format.
     *
     * @param string $phone
     * @return string
     */
    public static function format($phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }

        if (strlen($phone) == 9) {
            $phone = '254' . $phone;
        }

        return $phone;
    }
}
