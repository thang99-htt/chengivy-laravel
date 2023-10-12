<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Returns;
use App\Models\ReturnImage;
use Intervention\Image\Facades\Image;
use App\Http\Resources\ReturnResource;
use App\Jobs\UploadReturnToGoogleDrive;
use App\Models\ReturnProduct;
use Carbon\Carbon;

class ReturnsController extends Controller
{
    public function index()
    {
        $returns = Returns::with('order', 'return_image', 'return_product.product.product_image')
            ->orderBy('created_at', 'DESC')->get();
        return response($returns);
    }

    public function store(Request $request)
    {
        $return = new Returns;
        $return->requested_at = Carbon::now('Asia/Ho_Chi_Minh');
        $return->staff_id = 1;
        $return->order_id = $request['order_id'];
        $return->reason = $request['reason'];
        $return->description = $request['description'];
        $return->total_price = $request['total_price'];
        $return->status = 'Đã ghi nhận';
        $return->method = 'Tài khoản ngân hàng';
        $return->save();
        
        $returnId = $return->id;

        foreach($request['products'] as $item) {
            $returnItem = new ReturnProduct;
            $returnItem->return_id = $returnId;
            $returnItem->product_id = $item['id'];
            $returnItem->size = $item['size'];
            $returnItem->color = $item['color'];
            $returnItem->quantity = $item['quantity'];
            $returnItem->price = $item['price'];
            $returnItem->price_discount = $item['price_discount'];
            $returnItem->save();
        }

        $imagesData = $request['images'];

        if ($imagesData) {
            foreach ($imagesData as $image) {
                $base64Image = $image;
                UploadReturnToGoogleDrive::dispatch($returnId, $base64Image);
            }
        }

        return response()->json([
            'success' => 'success',
            'message' => 'Yêu cầu hoàn trả thành công!',
        ]);

    }

    public function cancelReturn($id) {
        $return = Returns::find($id);
        $return->status = 'Đã hủy yêu cầu';
        $return->save();

        return response()->json([
            'success' => true,
        ],200);
    }

    

}
