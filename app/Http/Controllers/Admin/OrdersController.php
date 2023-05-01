<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Http\Resources\OrderResource;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use Carbon\Carbon;

class OrdersController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('created_at', 'DESC')->get();
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

            $invoice = new Invoice();
            $invoice->date = Carbon::now('Asia/Ho_Chi_Minh');
            $invoice->total_price = $order->total_price;
            $invoice->save();
    
            $invoice_id = $invoice->id;
    
            $getOrderItems = OrderProduct::with(['product'])->where('order_id', $order->id)->get();
            
            foreach($getOrderItems as $item) {
                $invoiceItem = new InvoiceProduct;
                $invoiceItem->invoice_id = $invoice_id;
                $invoiceItem->product_id = $item['product_id'];
                $invoiceItem->size = $item['size'];
                $invoiceItem->quantity = $item['quantity'];
                $invoiceItem->price = $item['product']['price']*$item['quantity'];
                $invoiceItem->save();
            }
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
