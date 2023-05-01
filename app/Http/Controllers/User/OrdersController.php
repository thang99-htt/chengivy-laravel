<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ProductSize;
use App\Models\Size;
use App\Models\Cart;
use Carbon\Carbon;
use App\Http\Resources\OrderResource;

class OrdersController extends Controller
{    
    public function index()
    {
        $orders = Order::orderBy('created_at', 'DESC')->get();
        return response()->json($orders);
    }
    
    // id = id_user
    public function store($id, Request $request)
    {
        // Total_price of Order
        $getCartItems = Cart::with(['product'])->orderby('id', 'Desc')->where('user_id', $id)->get();
        $order_total_price = 0;
        
        foreach($getCartItems as $item) {
            $getDiscountPrice = Product::getDiscountPrice($item['product']['id']);
            $item_price = 0;
            if($getDiscountPrice > 0) {
                $item_price += $getDiscountPrice*$item['quantity'];
            } else {
                $item_price += $item['product']['price']*$item['quantity'];
            }
            $order_total_price += $item_price;
        }

        // Save table orders
        $order = new Order;
        $order->staff_id = 1;
        $order->user_id = $id;
        $order->contact_id = $request['contact_id'];
        $order->payment_id = $request['payment_id'];
        $order->status_id = 1;
        $order->order_date = Carbon::now('Asia/Ho_Chi_Minh');
        $order->estimate_date = Carbon::now('Asia/Ho_Chi_Minh')->addDays(3);;
        $order->total_price = $order_total_price;
        if($request['note'] != null)
            $order->note = $request['note'];
        $order->paid = $request->paid;        
        $order->save();

        $order_id = $order->id;

        
        foreach($getCartItems as $item) {
            // Save table order_product
            $orderItem = new OrderProduct;
            $orderItem->order_id = $order_id;
            $orderItem->product_id = $item['product_id'];
            $orderItem->size = $item['size'];
            $orderItem->quantity = $item['quantity'];
            $orderItem->price = $item['product']['price']*$item['quantity'];
            $orderItem->save();

            // Delete in cart
            Cart::where('id', $item->id)->delete();
            $size = Size::select('id')->where('name', $item['size'])->first();
            $getProductStock = ProductSize::getProductQuantity($item['product_id'], $size['id']);
            ProductSize::where(['product_id' => $item['product_id'], 'size_id' => $size['id']])->update([
                'stock' => $getProductStock-$item['quantity']
            ]);
        }
        
        return response()->json([
            'success' => 'true',
            'message' => 'Đơn hàng đặt thành công.'
        ], 200);  
    }

    // with id = user_id
    public function purchaseAll($id)
    {
        $orders = Order::with(['order_product.product'])->where('user_id', $id)->orderBy('created_at', 'DESC')->get();

        return response()->json(OrderResource::collection($orders));
    }

    // with id = order_id
    public function purchaseShow($id)
    {
        $order = Order::with('order_product.product')->find($id);
        return response()->json(new OrderResource($order));
    }

    public function cancleOrder($id) {
        $order = Order::find($id);
        $order->status_id = 10;
        $order->cancle_date = Carbon::now('Asia/Ho_Chi_Minh');
        $order->save();
        
        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }

    public function receiptOrder($id) {
        $order = Order::find($id);
        $order->status_id = 8;
        $order->receipt_date = Carbon::now('Asia/Ho_Chi_Minh');
        $order->save();
        
        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }
}

