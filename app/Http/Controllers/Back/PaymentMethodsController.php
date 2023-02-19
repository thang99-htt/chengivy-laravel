<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\PaymentMethod;
use Image;

class PaymentMethodsController extends Controller
{
    public function index()
    {
        $payment_methods = PaymentMethod::all();
        return response()->json($payment_methods);
    }

}
