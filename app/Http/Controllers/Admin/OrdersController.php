<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Http\Resources\OrderResource;
use Carbon\Carbon;
use App\Jobs\SendMailProductsCanceled;
use App\Models\Color;
use App\Models\Inventory;
use App\Models\Size;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $startDate = Carbon::createFromFormat('d/m/Y', $request->input('startDate'))->startOfDay();
        $endDate = Carbon::createFromFormat('d/m/Y', $request->input('endDate'))->endOfDay();

        $orders = Order::whereBetween('ordered_at', [$startDate, $endDate])
            ->orderBy('ordered_at', 'DESC')->get();
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

    public function cancelOrder(Request $request)
    {
        $selectedIds = $request->input('data'); // Lấy danh sách selectedIds từ request
        $orders = Order::whereIn('id', $selectedIds)->get(); // Sử dụng whereIn để lấy các bản ghi tương ứng với selectedIds
        
        $notcancel = [];
        $canceled = [];

        foreach ($orders as $order) {
            if ($order->status_id < 4) {
                $canceled[] = $order->id;

                $order->status_id = 10;
                $order->canceled_at = now()->timezone('Asia/Ho_Chi_Minh');
                // $order->save();
                
                // $inputDate = Carbon::now('Asia/Ho_Chi_Minh');
                $inputDate = '2023-12-30 14:27:53';
                $carbonDate = Carbon::parse($inputDate);
                $currentMonthYear = $carbonDate->format('Ym');

                $user = $order->user;
                $productsCanceled = [];
                foreach ($order->order_product as $item) {
                    $productsCanceled[] = $item;
        
                    $color = Color::where('name', $item['color'])->first();
                    $size = Size::where('name', $item['size'])->first();
                    $existingInventory = Inventory::where(['month_year' => 202312, 
                        'product_id' => $item['product_id'], 
                        'color_id' => $color->id, 
                        'size_id' => $size->id])->first();
                        
                    if($existingInventory) {
                        Inventory::where(['month_year' => $currentMonthYear, 
                            'product_id' => $item['product_id'], 
                            'color_id' => $color->id, 
                            'size_id' => $size->id])
                            ->update([
                                'total_final' => $existingInventory['total_final'] + $item['quantity'],
                                'total_export' => $existingInventory['total_export'] - $item['quantity']
                            ]);
                        } 
                }
                SendMailProductsCanceled::dispatch($user->name, $user->email, $order, $productsCanceled);

            } else {
                $notcancel[] = $order->id;
            }
        }
        
        if($notcancel) {
            $notcancelString = implode(', ', $notcancel);
            return response()->json([
                'success' => 'warning',
                'message' => 'Hủy đơn ' .$notcancelString. ' thất bại do hàng đã được vận chuyển.',
            ]);
        } else {
            return response()->json([
                'success' => 'success',
                'message' => 'Hủy đơn thành công.',
            ], 200);
        }
    }


}
