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
        $order->staff_id = 0;
        $order->user_id = $id;
        $order->contact_id = $request['contact_id'];
        $order->payment_id = $request['payment_id'];
        $order->status_id = 1;
        $order->order_date = Carbon::now('Asia/Ho_Chi_Minh');
        $order->estimate_date = Carbon::now('Asia/Ho_Chi_Minh')->addDays(3);;
        $order->total_price = $order_total_price;
        if($request['note'] != null)
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

    // with id = user_id
    public function purchaseAll($id)
    {
        $orders = Order::with(['user' => function($query) {
            $query->select('id', 'name');
        }, 'status' => function($query) {
            $query->select('id', 'name');
        }])->where('user_id', $id)->orderBy('created_at', 'DESC')->get();

        return response()->json($orders);
    }

    // with id = order_id
    public function purchaseShow($user, $id)
    {
        $order = Order::with(['user' => function($query) {
            $query->select('id', 'name', 'email');
        }, 'contact' => function($query) {
            $query->select('id', 'phone', 'address', 'ward_id');
        }, 'payment' => function($query) {
            $query->select('id', 'name');
        }, 'status' => function($query) {
            $query->select('id', 'name');
        }, 'order_product'])->where(['id' => $id, 'user_id'=> $user])->first();

        $getAddressDetail = Order::getAddressDetail($order['contact']['ward_id']);
        $order['contact']['address_detail'] = $getAddressDetail;

        foreach($order['order_product'] as $key => $value) {
            $productDetail = Product::where('id', $order['order_product'][$key]['product_id'])->first();
            $order['order_product'][$key]['product_detail'] = $productDetail;
        }

        return response()->json($order);
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
}

