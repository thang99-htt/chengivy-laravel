<?php

namespace App\Http\Controllers\Admin;

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
