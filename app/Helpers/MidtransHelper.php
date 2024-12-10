<?php

namespace App\Helpers;

class MidtransHelper
{
    public static function generateSignature($orderId, $statusCode, $grossAmount)
    {
        $serverKey = config('services.midtrans.server_key');
        return hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
    }
}
