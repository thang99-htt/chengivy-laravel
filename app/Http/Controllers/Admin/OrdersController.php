<?php

namespace App\Http\Controllers\Admin;

use App\Events\SendNotification;
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
use App\Models\Notification;
use App\Models\Profile;
use App\Models\Size;
use App\Models\Staff;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function updateOrderStatus(Request $request) {
        $order = Order::find($request->orderId);
        $order->staff_id = $request->staffId;
        $order->status_id = $request->status;

        if($request->status == 2) {
            $order->confirmed_at = Carbon::now('Asia/Ho_Chi_Minh');
        }

        if($request->status == 9) {
            $order->paid = 1;
        }
        $order->save();
        
        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }

    public function deliveryOrder(Request $request) {
        $order = Order::find($request->orderId);
        if($order->staff_delivery_id == NULL && $order->status_id == 2) {
            $order->staff_delivery_id = $request->staff_delivery_id;
        }
        if($order->status_id == 2 && $request['receiveOrder']) {
            $order->status_id = 3;
        } else if($order->status_id == 2 || ($order->status_id == 2 && $request['refuseOrder'])) {
            $order->status_id = 2;
            $order->staff_delivery_id = null;
        } else if($order->status_id == 3) {
            $order->status_id = 4;
        } else if($order->status_id == 4) {
            $order->status_id = 5;
        } else if($order->status_id == 5) {
            $order->status_id = 6;
        } else if($order->status_id == 6 && $request['unreceipt']) {
            $order->status_id = 12;
        } else {
            $order->status_id = 7;
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
                $order->save();
                
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

    public function soldAtStore(Request $request)
    {
        $getItems = $request->items;

        // Save table orders
        $order = new Order;
        $order->staff_id = $request['staff_id'];
        $order->user_id = $request['user_id'];
        $order->payment_method = 'Thanh toán tiền mặt';
        if($request['voucher_id']) {
            $order->voucher_id = $request['voucher_id'];
            $voucher = Voucher::find($request['voucher_id']);
            $voucher->quantity_remain = $voucher->quantity_remain - 1;
            $voucher->save();
        }

        $order->name_receiver = $request['name_receiver'];
        $order->phone_receiver = $request['phone_receiver'];

        $order->status_id = 11;
        $order->ordered_at = Carbon::now('Asia/Ho_Chi_Minh');
        $order->estimated_at = Carbon::now('Asia/Ho_Chi_Minh');
        $order->receipted_at = Carbon::now('Asia/Ho_Chi_Minh');

        $order->total_price = $request['total_price'];
        $order->total_discount = $request['total_discount'];
        $order->total_value = $request['total_value'];

        $order->fee = 0;  
        $order->point = $request['point'];
        $order->paid = 1;      
        
        $pdfBase64 = $request->input('bill');
        $pdfBinary = base64_decode(Str::after($pdfBase64, ','));
        $pdfFilename = uniqid('pdf_') . '.pdf';
        $disk = 'public';
        Storage::disk($disk)->put('uploads/orders/' . $pdfFilename, $pdfBinary);
        $order->bill = "http://localhost:8000/storage/uploads/orders/".$pdfFilename;
        
        $order->save();

        $order_id = $order->id;

        foreach($getItems as $item) {
            $inventory = Inventory::where([
                'product_id' => $item['product_id'], 'color_id' => $item['color_id'], 
                'size_id' => $item['size_id']])->orderByDesc('month_year')->first();

            // Save table order_product
            $orderItem = new OrderProduct;
            $orderItem->order_id = $order_id;
            $orderItem->product_id = $item['product_id'];
            $orderItem->size = $item['size'];
            $orderItem->color = $item['color'];
            $orderItem->quantity = $item['quantity'];
            $orderItem->price = $item['price'];
            $orderItem->price_discount = $item['price'] - $item['price_final'];
            $orderItem->save();
            
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

        if($request['user_id']) {
            $user = User::find($request['user_id']);
            $user->point = $user->point - $request['point'];
            $pointPlus = intval($request['total_value'] / 100000);
            if($user->level == 'GOLD') {
                $user->point = $user->point + $pointPlus*3;
            } else if($user->level == 'PLATINUM') {
                $user->point = $user->point + $pointPlus*5;
            } else if($user->level == 'DIAMOND') {
                $user->point = $user->point + $pointPlus*10;
            } else  {
                $user->point = $user->point + $pointPlus;
            }
    
            $orders = Order::where('user_id', $request['user_id'])->get();
            $totalValue = 0;
            foreach($orders as $order) {
                $totalValue = $totalValue + $order->total_value;
            }
            if($totalValue >= 15000000 && $totalValue <= 39999999) {
                $user->level = 'SILVER';
            } else if($totalValue >= 40000000 && $totalValue <= 79999999) {
                $user->level = 'GOLD';
            } else if($totalValue >= 80000000 && $totalValue <= 119999999) {
                $user->level = 'PLATINUM';
            } else if($totalValue >= 120000000) {
                $user->level = 'DIAMOND';
            }
    
            $user->save();
        }
        
        return response()->json([
            'success' => 'success',
            'message' => 'Tạo đơn hàng thành công.',
            'bill' => $pdfFilename
        ], 200);  
    }

    public function getAllShippers() {
        $staffs = Staff::with('roles')
            ->whereHas('roles', function ($query) {
                $query->where('role_id', 5);
            })
            ->get();

        foreach($staffs as $staff) {
            $totalOrder = 0;
            $staff['current_orders'] = $staff->orders->whereBetween('status_id', [3, 7])->values();
            foreach($staff->orders as $order) {
                $totalOrder = $totalOrder + 1;
            }
            $staff['total_order'] = $totalOrder;
        }
        return response()->json($staffs);
    }

    public function assignmentShipper(Request $request) {
        $order = Order::find($request->orderId);
        $order->staff_delivery_id = $request->staff_delivery_id;
        $order->save();
        
        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }

}
