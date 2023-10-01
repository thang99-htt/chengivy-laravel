<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\Order;
use App\Models\User;
use Intervention\Image\Facades\Image;
use App\Jobs\UploadVoucherToGoogleDrive;
use App\Jobs\DeleteVoucherFromGoogleDrive;

class VouchersController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::get();
        return response()->json($vouchers);
    }

    public function voucherByUser($id)
    {
        $vouchers = [];

        $user = User::find($id);
        $order = Order::where('user_id', $user->id)->first(); // Lấy order đầu tiên của người dùng

        // Lấy ngày hiện tại
        $currentDate = now();

        if ($order) {
            // Lấy danh sách các phiếu giảm giá với điều kiện ngày kết thúc lớn hơn ngày hiện tại và id khác 1 và quantity_remain lớn hơn 0
            $vouchers = Voucher::where('date_end', '>', $currentDate)
                               ->where('id', '<>', 1)
                               ->where('quantity_remain', '>', 0)
                               ->get();
        } else {
            // Lấy danh sách các phiếu giảm giá với điều kiện ngày kết thúc lớn hơn ngày hiện tại và quantity_remain lớn hơn 0
            $vouchers = Voucher::where('date_end', '>', $currentDate)
                   ->where('quantity_remain', '>', 0)
                   ->get();

            $voucherId1 = Voucher::find(1);

            if ($voucherId1) {
                $vouchers->prepend($voucherId1);
            }
        }

        return response()->json($vouchers);
    }

    public function store(Request $request)
    {
        $voucher = new voucher;
        $voucher->name = $request['name'];
        $voucher->date_start = $request['date_start'];
        $voucher->date_end = $request['date_end'];
        $voucher->condition = $request['condition'];
        $voucher->level = $request['level'];
        $voucher->discount = $request['discount'];
        $voucher->quantity_initial = $request['quantity_initial'];
        $voucher->quantity_remain = $request['quantity_initial'];
        $voucher->save();
        $base64Image = $request->image;
        
        $voucher->save();

        UploadVoucherToGoogleDrive::dispatch($voucher, $base64Image);

        return response()->json($voucher);
    }

    public function show($id)
    {
        $voucher = Voucher::find($id);
        return response()->json($voucher);
    }

    public function update(Request $request, $id)
    {
        $voucher = Voucher::find($id);
        $voucher->name = $request['name'];
        $voucher->date_start = $request['date_start'];
        $voucher->date_end = $request['date_end'];
        $voucher->level = $request['level'];
        $voucher->discount = $request['discount'];
        $voucher->quantity_remain = $request['quantity_initial'];
        $voucher->quantity_initial = $request['quantity_initial'];
        $voucher->condition = $request['condition'];
        $voucher->save();

        $imageLink = $voucher->image;
        
        if($imageLink) {
            DeleteVoucherFromGoogleDrive::dispatch($imageLink);
        }
        $base64Image = $request->image;
        
        UploadVoucherToGoogleDrive::dispatch($voucher, $base64Image);

        return response()->json($voucher);
    }

    public function destroyIds(Request $request)
    {
        $selectedIds = $request->all(); // Lấy danh sách selectedIds từ request
        $vouchers = Voucher::whereIn('id', $selectedIds)->get(); // Sử dụng whereIn để lấy các bản ghi tương ứng với selectedIds
        foreach($vouchers as $voucher) {
            $voucher->delete(); // Xóa từng bản ghi voucher
        }      
        return response()->json([
            'success' => true,
            'message' => "Deleted All."
        ], 200);
    }

    public function updatevoucherStatus($id, Request $request) {
        $voucher = Voucher::find($id);
        $voucher->status = !$request->status;
        $voucher->save();
        
        return response()->json([
            'success' => true,
            'voucher' => $voucher,
        ]);
    }
}
