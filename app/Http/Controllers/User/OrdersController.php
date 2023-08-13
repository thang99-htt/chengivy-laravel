<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Inventory;
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
        $getCartItems = Cart::with(['product.inventories'
            => function ($query) {
                $query->where('month_year', function ($subQuery) {
                    $subQuery->selectRaw('max(month_year)')
                            ->from('inventories');
                });
            }])->orderby('created_at', 'Desc')->where('user_id', $id)->get();

        $order_total_price = 0;
        $order_total_value = 0;
        
        foreach($getCartItems as $item) {
            $item_total_price = 0;
            $item_total_value = 0;

            $inventory = Inventory::where(['product_id' => $item->product_id, 
                'color_id' => $item->color_id, 'size_id' => $item->size_id])->orderByDesc('month_year')->first();
            $item['inventory'] = $inventory;
            
            if($item->inventory->total_final > 0){
                $item_total_price += $item['product']['price_final']*$item['quantity'];
                $item_total_value += $item['product']['price']*$item['quantity'];
            }
            
            $order_total_price += $item_total_price;
            $order_total_value += $item_total_value;
        }

        // Save table orders
        $order = new Order;
        $order->staff_id = 1;
        $order->user_id = $id;
        $order->payment_method_id = $request['payment_method_id'];
        $order->voucher_id = $request['voucher_id'];
        $order->address_receiver = $request['delivery_address']['address'];
        $order->name_receiver = $request['delivery_address']['name'];
        $order->phone_receiver = $request['delivery_address']['phone'];
        $order->name_receiver = 'Thang';
        $order->phone_receiver = '0399191404';
        $order->status_id = 1;
        $order->ordered_at = Carbon::now('Asia/Ho_Chi_Minh');
        $order->estimated_at = Carbon::now('Asia/Ho_Chi_Minh')->addDays(3);
        $order->total_value = $order_total_value;
        $order->fee = 25000;
        $order->total_discount = $order->total_value - $order_total_price;
        $order->total_price = $order_total_price + $order->fee;
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
                    $orderItem->price_discount = $item['product']['price_final'];
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
                
                Inventory::where(['product_id' => $item['product_id'], 'size_id' => $item['size_id'],
                    'color_id' => $item['color_id']])->update([
                        'total_export' => $inventory->total_export + $item['quantity'],
                        'total_final' => $inventory->total_final - $item['quantity']
                ]);
            }

        }
        
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
        $order->contact_id = $request->contact_id;
        $order->payment_id = $request->payment_id;
        $order->status_id = 1;
        $order->order_date = Carbon::now('Asia/Ho_Chi_Minh');
        $order->estimate_date = Carbon::now('Asia/Ho_Chi_Minh')->addDays(3);

        if($request->discount_percent > 0) {
            $order->total_price = $request->final_price + 25000;
        } else {
            $order->total_price = $request->price + 25000;
        }
        
        if($request['note'] != null)
            $order->note = $request['note'];

        $order->paid = $request->paid;        
        $order->save();

        $order_id = $order->id;

        // Save table order_product
        $orderItem = new OrderProduct;
        $orderItem->order_id = $order_id;
        $orderItem->product_id = $request['product_id'];
        $orderItem->size = $request['sizes'][0]['size_name'];
        $orderItem->quantity = 1;

        if($request->discount_percent > 0) {
            $orderItem->price = $request->final_price;
        } else {
            $orderItem->price = $request->price + 25000;
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

