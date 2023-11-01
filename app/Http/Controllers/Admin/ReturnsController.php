<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Returns;
use App\Models\ReturnImage;
use Intervention\Image\Facades\Image;
use App\Http\Resources\ReturnResource;
use App\Jobs\UploadReturnToGoogleDrive;
use Carbon\Carbon;
use App\Jobs\SendReturnMail;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;

class ReturnsController extends Controller
{
    public function index(Request $request)
    {
        $startDate = Carbon::createFromFormat('d/m/Y', $request->input('startDate'))->startOfDay();
        $endDate = Carbon::createFromFormat('d/m/Y', $request->input('endDate'))->endOfDay();

        $returns = Returns::with('order','staff')
            ->whereBetween('requested_at', [$startDate, $endDate])
            ->orderBy('created_at', 'DESC')->get();
        return response($returns);
    }

    public function show($id)
    {
        $return = Returns::with('order.user.profiles','return_image','return_product.product.product_image')->find($id);
        foreach ($return->return_product as $returnProduct) {
            $color = Color::where('name',$returnProduct->color)->first();
            $imageProduct = ProductImage::where(['product_id' => $returnProduct->product->id, 'color_id' => $color->id])->first();
            $returnProduct->image = $imageProduct->image;
        }
        return response()->json($return);
    }


    public function updateReturnStatus(Request $request) {
        $return = Returns::find($request->returnId);
        $return->staff_id = $request->staffId;
        $return->status = $request->status;
        $return->save();

        $order = Order::find($return->order_id);
        if($request->status === 'Đã gửi hướng dẫn hoàn trả') {
            $user = User::find($order->user_id);
            $returnProduct = $return;
            SendReturnMail::dispatch($user->name, $user->email, $returnProduct);
        }

        if($request->status === 'Đã xử lý hoàn trả') {
            $return->returned_at = Carbon::now('Asia/Ho_Chi_Minh');;
            $return->save();
        }

        return response()->json([
            'success' => true,
        ],200);
    }

    // public function update($id, Request $request)
    // {
    //     $review = Returns::find($id);
    //     $review->reply = $request->reply;
    //     $review->save();

    //     return response()->json([
    //         'success' => true,
    //     ],200);
    // }

    // public function hiddenIds(Request $request)
    // {
    //     $selectedIds = $request->all(); // Lấy danh sách selectedIds từ request
    //     $reviews = Returns::whereIn('id', $selectedIds['data'])->get(); // Sử dụng whereIn để lấy các bản ghi tương ứng với selectedIds
    //     foreach($reviews as $review) {
    //         if($review->status == 0) {
    //             $review->status = 1;
    //         } else {
    //             $review->status = 0;
    //         }
    //         $review->save();
    //     }      
    //     return response()->json(['success'=>true], 200);
    // }

}
