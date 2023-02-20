<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Cart;
use Carbon\Carbon;
use Session;
use Auth;
use Hash;
use Response;


class OrdersController extends Controller
{    
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
        $order->user_id = $id;
        $order->contact_id = $request['contact_id'];
        $order->payment_id = $request['payment_id'];
        $order->status_id = 1;
        $order->order_date = Carbon::now('Asia/Ho_Chi_Minh');
        $order->estimate_date = Carbon::now('Asia/Ho_Chi_Minh')->addDays(3);;
        $order->total_price = $order_total_price;
        $order->note = $request['note'];
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
        }
        
        return response()->json([
            'success' => 'true',
            'message' => 'Đơn hàng đặt thành công.'
        ], 200);  
    }
}

