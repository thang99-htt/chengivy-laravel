<?php

namespace App\Http\Controllers\User;

use App\Events\SendNotification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Inventory;
use App\Models\Voucher;
use App\Models\Cart;
use Carbon\Carbon;
use App\Http\Resources\OrderResource;
use App\Models\Notification;
use App\Models\User;

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
        $getCartItems = Cart::with(['product.inventories'
            => function ($query) {
                $query->where('month_year', function ($subQuery) {
                    $subQuery->selectRaw('max(month_year)')
                            ->from('inventories');
                });
            }])->orderby('created_at', 'Desc')->where('user_id', $id)->get();

        // Save table orders
        $order = new Order;
        $order->staff_id = 1;
        $order->user_id = $id;
        $order->payment_method = $request['payment_method'];
        if($request['voucher_id']) {
            $order->voucher_id = $request['voucher_id'];

            $voucher = Voucher::find($request['voucher_id']);
            $voucher->quantity_remain = $voucher->quantity_remain -1;
            $voucher->save();
        }
        $order->address_receiver = $request['delivery_address']['address_detail'] . ", " . $request['delivery_address']['address'];
        $order->name_receiver = $request['delivery_address']['name'];
        $order->phone_receiver = $request['delivery_address']['phone'];
        $order->status_id = 1;
        $order->ordered_at = Carbon::now('Asia/Ho_Chi_Minh');
        $order->estimated_at = Carbon::now('Asia/Ho_Chi_Minh')->addDays(3);

        $order->total_price = $request['total_price'];
        $order->fee = 25000;
        $order->total_discount = $request['total_discount'];
        $order->total_value = $request['total_value'];

        if($request['note'] != null)
            $order->note = $request['note'];
        $order->paid = $request->paid;        
        $order->save();

        $order_id = $order->id;

        
        foreach($getCartItems as $item) {
            
            $inventory = Inventory::where(['product_id' => $item->product_id, 
            'color_id' => $item->color_id, 'size_id' => $item->size_id])->orderByDesc('month_year')->first();
            $item['inventory'] = $inventory;
            
            if($item->inventory->total_final > 0){
                // Save table order_product
                $orderItem = new OrderProduct;
                $orderItem->order_id = $order_id;
                $orderItem->product_id = $item['product_id'];
                $orderItem->size = $item->size->name;
                $orderItem->color = $item->color->name;
                $orderItem->quantity = $item['quantity'];
                $orderItem->price = $item['product']['price'];
                if($item['product']['discount_percent'] > 0) {
                    $orderItem->price_discount = $item['product']['price'] - $item['product']['price_final'];
                } else {
                    $orderItem->price_discount = 0;
                }
                $orderItem->save();
                // Delete in cart
                Cart::where([
                    'user_id' => $id,
                    'product_id' => $item->product_id,
                    'color_id' => $item->color->id,
                    'size_id' => $item->size->id
                ])->delete();
                
                $latestMonthYear = Inventory::where([
                    'product_id' => $item['product_id'],
                    'size_id' => $item['size_id'],
                    'color_id' => $item['color_id']
                ])->max('month_year');
                
                Inventory::where(['product_id' => $item['product_id'], 
                    'size_id' => $item['size_id'],
                    'color_id' => $item['color_id'],
                    'month_year' => $latestMonthYear]
                    )->update([
                        'total_export' => $inventory->total_export + $item['quantity'],
                        'total_final' => $inventory->total_final - $item['quantity']
                ]);
            }

        }

        $user = User::find($id);
        event(new SendNotification(
            $user->name, 
            1,
            "Vừa đặt hàng",
            "http://localhost:3000/admin/orders",
        ));

        $notification = new Notification();
        $notification->user = $user->name;
        $notification->type = 1;
        $notification->message = "Vừa đặt hàng";
        $notification->status = 'Chưa đọc';
        $notification->date = Carbon::now('Asia/Ho_Chi_Minh');
        $notification->link = "http://localhost:3000/admin/orders";
        $notification->save();
        
        return response()->json([
            'success' => 'success',
            'message' => 'Đơn hàng đặt thành công.'
        ], 200);  
    }

    public function addBuyNow($id, Request $request)
    {               
        // Save table orders
        $order = new Order;
        $order->staff_id = 1;
        $order->user_id = $id;
        $order->payment_method = $request['payment_method'];
        if($request['voucher_id']) {
            $order->voucher_id = $request['voucher_id'];
        }
        $order->address_receiver = $request['delivery_address']['address'];
        $order->name_receiver = $request['delivery_address']['name'];
        $order->phone_receiver = $request['delivery_address']['phone'];
        $order->status_id = 1;
        $order->ordered_at = Carbon::now('Asia/Ho_Chi_Minh');
        $order->estimated_at = Carbon::now('Asia/Ho_Chi_Minh')->addDays(3);

        $order->total_price = $request['total_price'];
        $order->fee = 25000;
        $order->total_discount = $request['total_discount'];
        $order->total_value = $request['total_value'];

        if($request['note'] != null)
            $order->note = $request['note'];
        $order->paid = $request->paid;        
        $order->save();

        $order_id = $order->id;

        $orderItem = new OrderProduct;
        $orderItem->order_id = $order_id;
        $orderItem->product_id = $request['product_id'];
        $orderItem->size = $request->size_name;
        $orderItem->color = $request->color_name;
        $orderItem->quantity = 1;
        $orderItem->price = $request['price'];
        if($request['discount_percent'] > 0) {
            $orderItem->price_discount = $request['price'] - $request['price_final'];
        } else {
            $orderItem->price_discount = 0;
        }
        $orderItem->save();
        
        return response()->json([
            'success' => 'success',
            'message' => 'Đơn hàng đặt thành công.'
        ], 200);  
    }

    // with id = user_id
    public function purchaseAll($id)
    {
        $orders = Order::with(['order_product.product'])->where('user_id', $id)->orderBy('ordered_at', 'DESC')->get();
        return response()->json(OrderResource::collection($orders));
    }

    // with id = order_id
    public function purchaseShow($id)
    {
        $order = Order::with('order_product.product')->find($id);
        return response()->json(new OrderResource($order));
    }

    public function cancelOrder($id) {
        $order = Order::find($id);
        $order->status_id = 10;
        $order->canceled_at = Carbon::now('Asia/Ho_Chi_Minh');
        $order->save();
        
        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }

    public function receiptOrder($id) {
        $order = Order::find($id);
        $order->status_id = 8;
        $order->receipted_at = Carbon::now('Asia/Ho_Chi_Minh');
        $order->save();
        
        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }
}

