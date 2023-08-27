<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\Order;
use App\Models\User;
use Intervention\Image\Facades\Image;

class VouchersController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::get();
        return response()->json($vouchers);
    }

    public function voucherByUser($id)
    {
        $vouchers = null;

        $user = User::find($id);
        $order = Order::where('user_id', $user->id)->first(); // Lấy order đầu tiên của người dùng

        if ($order) {
            $vouchers = Voucher::get()->slice(1);
        } else {
            $vouchers = Voucher::get();
        }

        return response()->json($vouchers);
    }

    public function create()
    {     
        $vouchers = Voucher::vouchers();
        return response()->json($vouchers);
    }

    public function store(Request $request)
    {
        if($request->image) {
            $strpos = strpos($request->image, ';');
            $sub = substr($request->image, 0, $strpos);
            $ex = explode("/", $sub)[1];
            $imageName = time().".".$ex;
            $img = Image::make($request->image);
            $upload_path = public_path()."/storage/uploads/vouchers/";
            $img->save($upload_path.$imageName);
        }
        else {
            $imageName = "";
        }
        $voucher = new voucher;
        $voucher->name = $request['name'];
        $voucher->description = $request['description'];
        $voucher->image = $imageName;
        $voucher->save();
        return response()->json($voucher);
    }

    public function show($id)
    {
        $voucher = Voucher::find($id);
        return response()->json($voucher);
    }

    public function update(Request $request, $id)
    {
        $image_current = Voucher::select('image')->where('id', $id)->first();
        if($request->image == $image_current->image) {
            $imageName = $image_current->image;
        } else {
            $strpos = strpos($request->image, ';');
            $sub = substr($request->image, 0, $strpos);
            $ex = explode("/", $sub)[1];
            $imageName = time().".".$ex;
            $img = Image::make($request->image);
            $upload_path = public_path()."/storage/uploads/vouchers/";
            $img->save($upload_path.$imageName);
        }
        $voucher = Voucher::where('id', $id)->update([
            'name' => $request['name'],
            'image' => $imageName,
            'description' => $request['description'],
        ]);

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
