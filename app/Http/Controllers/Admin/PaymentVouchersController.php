<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentVoucher;

class PaymentVouchersController extends Controller
{
    public function index()
    {
        $payment_vouchers = PaymentVoucher::orderBy('created_at', 'DESC')->get();
        return response()->json($payment_vouchers); 
    }

    public function store(Request $request)
    {
        $payment_voucher = new PaymentVoucher;
        $payment_voucher->date = $request['date'];
        $payment_voucher->total_price = $request['total_price'];
        $payment_voucher->description = $request['description'];
        $payment_voucher->save();
        return response()->json($payment_voucher);
    }

    public function show($id)
    {
        $payment_voucher = PaymentVoucher::find($id);
        return response()->json($payment_voucher);
    }

    public function update(Request $request, $id)
    {
        $payment_voucher = PaymentVoucher::find($id);
        $payment_voucher->update($request->all());
        return response()->json($payment_voucher);
    }

    public function destroy($id)
    {
        $payment_voucher = PaymentVoucher::find($id);
        $payment_voucher->delete();
        return response()->json(['success'=>'true'], 200);
    }

}
