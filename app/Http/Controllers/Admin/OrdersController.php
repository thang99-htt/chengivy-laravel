<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Http\Resources\OrderResource;
use Carbon\Carbon;

class OrdersController extends Controller
{
    public function index()
    {
        $orders = Order::whereDate('ordered_at', '<', '2023-10-01')->orderBy('ordered_at', 'DESC')->get();
        return response(OrderResource::collection($orders));
    }

    public function show($id)
    {
        $order = Order::with('order_product.product')->find($id);
        return response()->json(new OrderResource($order));
    }

    public function updateOrderStatus($staff, $id, Request $request) {
        $order = Order::find($id);
        if($request->status == 1) {
            $order->staff_id = $staff;
            $order->status_id = 2;
            $order->confirmed_at = Carbon::now('Asia/Ho_Chi_Minh');
        }
        if($request->status == 2)
            $order->status_id = 3;
        if($request->status == 3)
            $order->status_id = 4;
        if($request->status == 4)
            $order->status_id = 5;
        if($request->status == 5)
            $order->status_id = 6;
        if($request->status == 6)
            $order->status_id = 7;
        if($request->status == 8) {
            $order->status_id = 9;
            $order->paid = 1;
        }
        $order->save();
        
        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if($product->image != null) {
            unlink(public_path()."/storage/uploads/products/". $product->image);
        }
        $product->delete();
        return response()->json(['success'=>'true'], 200);
    }

}
