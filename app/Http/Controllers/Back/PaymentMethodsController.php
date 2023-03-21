<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;

class PaymentMethodsController extends Controller
{
    public function index()
    {
        $payment_methods = PaymentMethod::all();
        return response()->json($payment_methods);
    }

}
