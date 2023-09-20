<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class VnpayController extends Controller
{
    private $vnpayApiUrl = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'; // or production URL

    public function createPayment(Request $request)
    {
        // Generate payment data and redirect to VNPAY
        // Implement your logic to create payment parameters here
        $paymentParams = [];

        $vnpayUrl = $this->vnpayApiUrl . '?' . http_build_query($paymentParams);
        return redirect($vnpayUrl);
    }

    public function handleCallback(Request $request)
    {
        return response()->json([
            'data' => 'true',
        ]);
    }
}
