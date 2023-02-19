<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
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
    public function store(Request $request)
    {
        $order = new Order;
        $order->user_id = $request['user_id'];
        $order->contact_id = $request['contact_id'];
        $order->payment_id = $request['payment_id'];
        $order->status_id = 1;
        $order->order_date = Carbon::now('Asia/Ho_Chi_Minh');
        $order->estimate_date = Carbon::now('Asia/Ho_Chi_Minh')->addDays(3);;
        $order->total_price = $request['total_price'];
        $order->save();

        $order_id = $order->id;

        $getCartItems = Cart::with(['product'])->orderby('id', 'Desc')->where('user_id', $request['user_id'])->get();
        
        
        foreach($getCartItems as $item) {
            $orderItem = new OrderProduct;
            $orderItem->order_id = $order_id;
            $orderItem->product_id = $item['product_id'];
            $orderItem->size = $item['size'];
            $orderItem->quantity = $item['quantity'];
            $orderItem->price = $item['product']['price']*$item['quantity'];
            $orderItem->save();
        }
        
        return response()->json($getCartItems);   
    }
}

