<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentVoucher;

class PaymentVouchersController extends Controller
{
    public function index()
    {
        $payment_vouchers = PaymentVoucher::with('staff', 'supplier')->orderBy('created_at', 'DESC')->get();
        return response()->json($payment_vouchers); 
    }

    public function store(Request $request)
    {
        $payment_voucher = new PaymentVoucher;
        $payment_voucher->staff_id = $request['staff_id'];
        $payment_voucher->supplier_id = $request['supplier_id'];
        $payment_voucher->date = $request['date'];
        $payment_voucher->total_price = $request['total_price'];
        $payment_voucher->description = $request['description'];
        $payment_voucher->save();
        return response()->json([
            'success' => 'success',
            'message' => "Phiếu chi được thêm thành công."
        ]);
    }

    public function show($id)
    {
        $payment_voucher = PaymentVoucher::with('staff', 'supplier')->find($id);
        return response()->json($payment_voucher);
    }

    public function update(Request $request, $id)
    {
        $payment_voucher = PaymentVoucher::find($id);
        $payment_voucher->update($request->all());
        return response()->json([
            'success' => 'success',
            'message' => "Phiếu chi được cập nhật thành công."
        ]);
    }

    public function destroy($id)
    {
        $payment_voucher = PaymentVoucher::find($id);
        $payment_voucher->delete();
        return response()->json(['success'=>'true'], 200);
    }

}
