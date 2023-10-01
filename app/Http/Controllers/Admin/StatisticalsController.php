<?php

namespace App\Http\Controllers\Admin;

use App\Events\SendNotification;
use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Inventory;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Staff;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PaymentVoucher;
use App\Models\Product;
use App\Models\StockReceivedDocketProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticalsController extends Controller
{
    public function index()
    {
        $staffs = count(Staff::get()) ? count(Staff::get()) : 0;
        $users = count(User::get()) ? count(User::get()) : 0;
        $customers = Order::with('user')->where('status_id', 9)->selectRaw('user_id, COUNT(user_id) as count')
            ->groupBy('user_id')->orderByDesc('count')->first();
        $customers = $customers ? $customers->user->name : 0;

        $orders = count(Order::get());
        $orders_confirm = count(Order::where('status_id', 1)->get());
        $orders_cancel = count(Order::where('status_id', 10)->get());
        $orders_success = count(Order::where('status_id', 9)->get());

        $products = count(Product::get());

        $products_all = Product::get();

        $products_out_of_stock = [];

        foreach ($products_all as $product) {
            // Tìm month_year cuối cùng có product_id đó
            $lastMonthYear = Inventory::where('product_id', $product->id)
                ->where('total_final', 0)
                ->latest('month_year')
                ->pluck('month_year')
                ->first();

            if ($lastMonthYear) {
                // Tìm sản phẩm có product_id và month_year tương ứng
                $outOfStockProduct = Inventory::where('product_id', $product->id)
                    ->where('month_year', $lastMonthYear)
                    ->first();

                if ($outOfStockProduct) {
                    // Lấy thông tin sản phẩm hết hàng
                    $products_out_of_stock[] = $outOfStockProduct;
                }
            }
        }

        $products_out_of_stock = count($products_out_of_stock);

        $products_orders = Order::with('order_product.product')
            ->where('status_id', 9)
            ->get();

        $products_sold_counts = [];

        foreach ($products_orders as $order) {
            foreach ($order->order_product as $orderProduct) {
                $product = $orderProduct->product;
                $product_id = $product->id;
                $colorName = $orderProduct->color;

                $selectedImage = null;

                $color = Color::where('name', $colorName)->first();

                // Tìm hình ảnh với color_id phù hợp
                foreach ($product->product_image as $image) {
                    if ($image->color_id === $color->id) {
                        $selectedImage = $image->image;
                        break;
                    }
                }

                if (!isset($products_sold_counts[$product_id])) {
                    $products_sold_counts[$product_id] = [
                        'name' => $product->name,
                        'image' => $selectedImage,
                        'quantity_sold' => 0,
                    ];
                }

                // Cộng số lượng bán của sản phẩm vào tổng số lượng bán
                $products_sold_counts[$product_id]['quantity_sold'] += $orderProduct->quantity;
            }
        }

        // Sắp xếp danh sách sản phẩm theo số lượng bán giảm dần
        uasort($products_sold_counts, function ($a, $b) {
            return $b['quantity_sold'] - $a['quantity_sold'];
        });

        // Lấy top 10 sản phẩm bán chạy nhất theo số lượng bán
        $top_10_best_sellers = array_slice($products_sold_counts, 0, 10);

        // $products_flop = $products_orders->sortBy('count')->first();
        // $products_flop = $products_flop ? $products_flop['product']['name'] : 0;

        $currentYear = Carbon::now()->year; // Lấy năm hiện tại

        $revenues = Order::sum('total_value') ?? 0;

        $revenues_per_month = Order::whereNotNull('receipted_at')
            ->whereYear('receipted_at', $currentYear) // Lọc theo năm hiện tại
            ->selectRaw('YEAR(receipted_at) as year, MONTH(receipted_at) as month, SUM(total_value) as revenue')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $payments = PaymentVoucher::sum('total_price') ?? 0;
        $payments_per_month = PaymentVoucher::whereYear('date', $currentYear) // Lọc theo năm hiện tại
            ->selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(total_price) as payment')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $profits_per_month = collect();
        foreach ($revenues_per_month as $key => $revenue) {
            $payment = $payments_per_month->where('year', $revenue->year)
                ->where('month', $revenue->month)
                ->first();

            $profit = (object) [
                'year' => $revenue->year,
                'month' => $revenue->month,
                'profit' => $revenue->revenue - ($payment ? $payment->payment : 0)
            ];

            $profits_per_month->push($profit);
        }

        $profits = ($revenues - $payments) ?: 0;

        return response()->json([
            'staffs' => $staffs,
            'users' => $users,
            'customers' => $customers,
            'orders' => $orders,
            'orders_cancel' => $orders_cancel,
            'orders_success' => $orders_success,
            'orders_confirm' => $orders_confirm,
            'products' => $products,
            'products_out_of_stock' => $products_out_of_stock,
            'top_10_best_sellers' => $top_10_best_sellers,
            'revenues' => $revenues,
            'revenues_per_month' => $revenues_per_month,
            'payments' => $payments,
            'payments_per_month' => $payments_per_month,
            'profits' => $profits,
            'profits_per_month' => $profits_per_month
        ], 200);
    }

    public function getRangeDate()
    {
        // $orders_cancel = DB::table('orders')
        //     ->where('status_id', 10)
        //     ->select(DB::raw('DATE_FORMAT(canceled_at, "%d/%m/%Y") as cancel_date'), DB::raw('count(*) as total'))
        //     ->groupBy('cancel_date')
        //     ->get();

        // $orders_success = DB::table('orders')
        //     ->where('status_id', 9)
        //     ->select(DB::raw('DATE_FORMAT(receipted_at, "%d/%m/%Y") as receipt_date'), DB::raw('count(*) as total'))
        //     ->groupBy('receipt_date')
        //     ->get();


        // $dates = collect()
        //     ->concat($orders_cancel->pluck('cancel_date'))
        //     ->concat($orders_success->pluck('receipt_date'))
        //     ->unique()
        //     ->map(function ($date) {
        //         $carbonDate = Carbon::createFromFormat('d/m/Y', $date); // Chuyển đổi từ định dạng "d/m/Y"
        //         return $carbonDate->format('d/m/Y'); // Chuyển đổi lại thành định dạng "d/m/Y"
        //     })
        //     ->unique()
        //     ->sortBy(function ($date) {
        //         return Carbon::createFromFormat('d/m/Y', $date); // Sắp xếp theo thứ tự tăng dần dựa trên Carbon
        //     })
        //     ->values()
        //     ->toArray();

        // return response()->json([
        //     'start' => reset($dates),
        //     'end' => end($dates)
        // ], 200);


        // Lấy tháng và năm hiện tại
        $currentMonth = Carbon::now('Asia/Ho_Chi_Minh')->month;
        $currentYear = Carbon::now('Asia/Ho_Chi_Minh')->year;

        // Tính toán ngày đầu tiên của tháng
        $firstDayOfMonth = Carbon::createFromDate($currentYear, $currentMonth, 1);

        // Tính toán ngày cuối cùng của tháng
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();

        return response()->json([
            'start' => $firstDayOfMonth->format('d/m/Y'),
            'end' => $lastDayOfMonth->format('d/m/Y')
        ], 200);
    }

    public function getOrders(Request $request)
    {
        if ($request->input('startDate') && $request->input('endDate')) {
            $startDate = Carbon::createFromFormat('d/m/Y', $request->input('startDate'))->startOfDay();
            $endDate = Carbon::createFromFormat('d/m/Y', $request->input('endDate'))->endOfDay();
            $orders = DB::table('orders')
                ->select(DB::raw('DATE_FORMAT(ordered_at, "%d/%m/%Y") as order_date'), DB::raw('count(*) as total'))
                ->whereBetween('ordered_at', [$startDate, $endDate])
                ->groupBy('order_date')
                ->get();


            $orders_cancel = DB::table('orders')
                ->where('status_id', 10)
                ->select(DB::raw('DATE_FORMAT(canceled_at, "%d/%m/%Y") as cancel_date'), DB::raw('count(*) as total'))
                ->whereBetween('canceled_at', [$startDate, $endDate])
                ->groupBy('cancel_date')
                ->get();

            $orders_success = DB::table('orders')
                ->where('status_id', 9)
                ->select(DB::raw('DATE_FORMAT(receipted_at, "%d/%m/%Y") as receipt_date'), DB::raw('count(*) as total'))
                ->whereBetween('receipted_at', [$startDate, $endDate])
                ->groupBy('receipt_date')
                ->get();
        } else {
            $orders = DB::table('orders')
                ->select(DB::raw('DATE_FORMAT(ordered_at, "%d/%m/%Y") as order_date'), DB::raw('count(*) as total'))
                ->groupBy('order_date')
                ->get();


            $orders_cancel = DB::table('orders')
                ->where('status_id', 10)
                ->select(DB::raw('DATE_FORMAT(canceled_at, "%d/%m/%Y") as cancel_date'), DB::raw('count(*) as total'))
                ->groupBy('cancel_date')
                ->get();

            $orders_success = DB::table('orders')
                ->where('status_id', 9)
                ->select(DB::raw('DATE_FORMAT(receipted_at, "%d/%m/%Y") as receipt_date'), DB::raw('count(*) as total'))
                ->groupBy('receipt_date')
                ->get();
        }

        $dates = collect()
            ->concat($orders_cancel->pluck('cancel_date'))
            ->concat($orders_success->pluck('receipt_date'))
            ->unique()
            ->map(function ($date) {
                $carbonDate = Carbon::createFromFormat('d/m/Y', $date); // Chuyển đổi từ định dạng "d/m/Y"
                return $carbonDate->format('d/m/Y'); // Chuyển đổi lại thành định dạng "d/m/Y"
            })
            ->unique()
            ->sortBy(function ($date) {
                return Carbon::createFromFormat('d/m/Y', $date); // Sắp xếp theo thứ tự tăng dần dựa trên Carbon
            })
            ->values()
            ->toArray();



        return response()->json([
            'dates' => $dates,
            'orders' => $orders,
            'orders_cancel' => $orders_cancel,
            'orders_success' => $orders_success,
        ], 200);
    }

    public function getSales(Request $request)
    {
        if ($request->input('startDate') && $request->input('endDate')) {
            $startDate = Carbon::createFromFormat('d/m/Y', $request->input('startDate'))->startOfMonth();
            $endDate = Carbon::createFromFormat('d/m/Y', $request->input('endDate'))->endOfMonth();

            $revenues = DB::table('orders')
                ->where('status_id', 9)
                ->where('total_value', '>', 0)
                ->select(DB::raw('DATE_FORMAT(receipted_at, "%m/%Y") as date'), DB::raw('sum(total_value) as total'))
                ->whereBetween('receipted_at', [$startDate, $endDate])
                ->groupBy('date')
                ->get();

            $payments = DB::table('payment_voucher')
                ->select(DB::raw('DATE_FORMAT(date, "%m/%Y") as date'), DB::raw('sum(total_price) as total'))
                ->whereBetween('date', [$startDate, $endDate])
                ->groupBy('date')
                ->get();

            // Tạo một danh sách tất cả các ngày từ cả revenues và payments
            $revenueDates = $revenues->pluck('date')->toArray();
            $paymentDates = $payments->pluck('date')->toArray();
            $allDates = array_values(array_unique(array_merge($revenueDates, $paymentDates)));
            usort($allDates, function ($date1, $date2) {
                $carbon1 = Carbon::createFromFormat('m/Y', $date1);
                $carbon2 = Carbon::createFromFormat('m/Y', $date2);

                return $carbon1->timestamp - $carbon2->timestamp;
            });

            // Tính lợi nhuận cho tất cả các ngày từ cả revenues và payments
            $profits = [];
            foreach ($allDates as $date) {
                $revenueAmount = $revenues->where('date', $date)->sum('total');
                $paymentAmount = $payments->where('date', $date)->sum('total');
                $profit = $revenueAmount - $paymentAmount;
                $profits[] = [
                    'date' => $date,
                    'total' => $profit,
                ];
            }
        } else {
            $revenues = DB::table('orders')
                ->where('status_id', 9)
                ->where('total_value', '>', 0)
                ->select(DB::raw('DATE_FORMAT(receipted_at, "%m/%Y") as date'), DB::raw('sum(total_value) as total'))
                ->groupBy('date')
                ->get();

            $payments = DB::table('payment_voucher')
                ->select(DB::raw('DATE_FORMAT(date, "%m/%Y") as date'), DB::raw('sum(total_price) as total'))
                ->groupBy('date')
                ->get();

            // Tạo một danh sách tất cả các ngày từ cả revenues và payments
            $revenueDates = $revenues->pluck('date')->toArray();
            $paymentDates = $payments->pluck('date')->toArray();
            $allDates = array_values(array_unique(array_merge($revenueDates, $paymentDates)));
            usort($allDates, function ($date1, $date2) {
                $carbon1 = Carbon::createFromFormat('m/Y', $date1);
                $carbon2 = Carbon::createFromFormat('m/Y', $date2);

                return $carbon1->timestamp - $carbon2->timestamp;
            });
            // Tính lợi nhuận cho tất cả các ngày từ cả revenues và payments
            $profits = [];
            foreach ($allDates as $date) {
                $revenueAmount = $revenues->where('date', $date)->sum('total');
                $paymentAmount = $payments->where('date', $date)->sum('total');
                $profit = $revenueAmount - $paymentAmount;
                $profits[] = [
                    'date' => $date,
                    'total' => $profit,
                ];
            }
        }

        return response()->json([
            'dates' => $allDates,
            'revenues' => $revenues,
            'payments' => $payments,
            'profits' => $profits,
        ], 200);
    }

    public function getProducts(Request $request)
    {
        $products = Product::get();

        if ($request->input('startDate') && $request->input('endDate')) {
            $startDate = Carbon::createFromFormat('d/m/Y', $request->input('startDate'))->startOfMonth()->format('Ym');
            $endDate = Carbon::createFromFormat('d/m/Y', $request->input('endDate'))->endOfMonth()->format('Ym');

            $products_sell = DB::table('inventories')
                ->select(DB::raw('month_year as date'), DB::raw('SUM(total_export) as total'))
                ->whereBetween('month_year', [$startDate, $endDate])
                ->groupBy('date') // Nhóm theo month_year
                ->orderBy('date')
                ->get();
        } else {
            $products_sell = DB::table('inventories')
                ->select(DB::raw('month_year as date'), DB::raw('SUM(total_export) as total'))
                ->groupBy('date') // Nhóm theo month_year
                ->orderBy('date')
                ->get();
        }

        $dates = []; // Mảng để lưu các giá trị định dạng mới
        $products_sell_data = []; // Mảng để lưu thông tin sản phẩm bán

        foreach ($products_sell as $product) {
            // Chuyển đổi month_year thành định dạng mm/yyyy
            $year = substr($product->date, 0, 4);
            $month = substr($product->date, 4, 2);
            $formattedDate = $month . '/' . $year;

            // Lưu vào mảng dates
            $dates[] = $formattedDate;

            // Lọc ra dữ liệu trong $products_sell với ngày hiện tại

            // Nếu có dữ liệu cho ngày hiện tại, lưu vào mảng $products_sell_data
            $products_sell_data[] = [
                'date' => $formattedDate,
                'total' => $product->total
            ];
        }



        $orders = Order::get();
        $totalOrders = count($orders); // Số lượng đơn hàng
        $totalQuantity = 0; // Tổng số sản phẩm

        foreach ($orders as $order) {
            foreach ($order->order_product as $item) {
                $totalQuantity += $item->quantity;
            }
        }

        if ($totalOrders > 0) {
            $averageQuantityPerOrder = intval($totalQuantity / $totalOrders);
        }
        return response()->json([
            'products' => count($products),
            'averageQuantityPerOrder' => $averageQuantityPerOrder,
            'dates' => $dates,
            'products_sell' => $products_sell_data
        ], 200);
    }

    public function getTopProducts()
    {
        $products = Product::get();

        $products_orders = Order::with('order_product.product')
            ->where('status_id', 9)
            ->get();
        $products_top = [];

        foreach ($products_orders as $order) {
            foreach ($order->order_product as $orderProduct) {
                $product = $orderProduct->product;
                $product_id = $product->id;
                $colorName = $orderProduct->color;
                $productPrice = $orderProduct->price;

                $stock_received_dockets = $product->stock_received_docket;
                foreach($stock_received_dockets as $stock_received_docket) {
                    $productPricePurchase = $stock_received_docket->price;
                }
                $selectedImage = null;

                $color = Color::where('name', $colorName)->first();

                // Tìm hình ảnh với color_id phù hợp
                foreach ($product->product_image as $image) {
                    if ($image->color_id === $color->id) {
                        $selectedImage = $image->image;
                        break;
                    }
                }
                
                if (!isset($products_top[$product_id])) {
                    $products_top[$product_id] = [
                        'name' => $product->name,
                        'price' => $productPrice,
                        'price_purchase' => $productPricePurchase,
                        'image' => $selectedImage,
                        'profit' => 0,
                        'quantity_sold' => 0
                    ];
                }

                // Cộng số lượng bán của sản phẩm vào tổng số lượng bán
                $products_top[$product_id]['quantity_sold'] += $orderProduct->quantity;
                $products_top[$product_id]['profit'] = ($productPrice - $productPricePurchase) * $products_top[$product_id]['quantity_sold'] ;
            }
        }

        // Sắp xếp danh sách sản phẩm theo số lượng bán giảm dần
        uasort($products_top, function ($a, $b) {
            return $b['quantity_sold'] - $a['quantity_sold'];
        });

        // Lấy top 10 sản phẩm bán chạy nhất theo số lượng bán
        $top_10_best_sellers = array_slice($products_top, 0, 10);

        uasort($products_top, function ($a, $b) {
            return $a['quantity_sold'] - $b['quantity_sold'];
        });

        // Lấy top 10 sản phẩm bán ít nhất theo số lượng bán
        $top_10_least_sellers = array_slice($products_top, 0, 10);

        // Sắp xếp sản phẩm theo lợi nhuận giảm dần
        uasort($products_top, function ($a, $b) {
            return $b['profit'] - $a['profit'];
        });

        // Lấy top 10 sản phẩm có lợi nhuận cao nhất
        $top_10_high_profit = array_slice($products_top, 0, 10);

        // Sắp xếp sản phẩm theo lợi nhuận tăng dần
        uasort($products_top, function ($a, $b) {
            return $a['profit'] - $b['profit'];
        });

        // Lấy top 10 sản phẩm có lợi nhuận thấp nhất
        $top_10_low_profit = array_slice($products_top, 0, 10);


        $users = Order::with('user')
            ->where('status_id', 9)
            ->get();

        $users_top = [];

        foreach ($users as $user) {
            $userId = $user->user->id;
            
            $orderValue = 0;
    
            foreach ($order->order_product as $orderProduct) {
                $orderValue += $orderProduct->price * $orderProduct->quantity;
            }

            if (!isset($users_top[$userId])) {
                $users_top[$userId] = [
                    'user_id' => $userId,
                    'user_name' => $user->user->name,
                    'order_count' => 1,
                    'order_value' => 0,
                ];
            } else {
                $users_top[$userId]['order_count']++;
                $users_top[$userId]['order_value'] += $orderValue;
            }
        }

        // Sắp xếp danh sách khách hàng theo số đơn hàng giảm dần
        usort($users_top, function ($a, $b) {
            return $b['order_count'] - $a['order_count'];
        });

        // Lấy top 5 khách hàng mua nhiều nhất
        $top_5_sell_users = array_slice($users_top, 0, 5);

        // Sắp xếp danh sách khách hàng theo giá trị đơn hàng giảm dần
        usort($users_top, function ($a, $b) {
            return $b['order_value'] - $a['order_value'];
        });

        // Lấy top 5 khách hàng có giá trị đơn hàng lớn nhất
        $top_5_value_users = array_slice($users_top, 0, 5);

        return response()->json([
            'products' => count($products),
            // 'dates' => $dates,
            'top_10_best_sellers' => $top_10_best_sellers,
            'top_10_least_sellers' => $top_10_least_sellers,
            'top_10_high_profit' => $top_10_high_profit,
            'top_10_low_profit' => $top_10_low_profit,
            'top_5_sell_users' => $top_5_sell_users,
            'top_5_value_users' => $top_5_value_users
        ], 200);
    }

    public function getInventories()
    {
        // Lấy danh sách sản phẩm từ bảng Product
        $products = Product::all();

        // Lấy danh sách product_id từ bảng stock_received_docket_product
        $receivedProducts = StockReceivedDocketProduct::pluck('product_id')->toArray();

        // Tìm những sản phẩm chưa nhập hàng
        $missingProducts = $products->filter(function ($product) use ($receivedProducts) {
            return !in_array($product->id, $receivedProducts);
        });

        $missingProductInfo = $missingProducts->map(function ($product) {
            $image = $product->product_image->first();
            return [
                'id' => $product->id,
                'category_id' => $product->category_id,
                'brand_id' => $product->brand_id,
                'name' => $product->name,
                'price' => $product->price_final,
                'image' => $image ? $image['image'] : null
            ];
        });

        $missingProductArray = $missingProductInfo->values()->toArray();

        $products_all = Product::get();

        $outOfStockProducts = [];

        foreach ($products_all as $product) {
            // Tìm month_year cuối cùng có product_id đó
            $lastMonthYear = Inventory::where('product_id', $product->id)
                ->where('total_final', 0)
                ->latest('month_year')
                ->pluck('month_year')
                ->first();

            if ($lastMonthYear) {
                // Tìm sản phẩm có product_id và month_year tương ứng
                $productOutOfStock = Inventory::where('product_id', $product->id)
                    ->where('month_year', $lastMonthYear)
                    ->first();

                if ($productOutOfStock) {
                    // Lấy thông tin sản phẩm hết hàng và ánh xạ qua product
                    $outOfStockProducts[] = [
                        'id' => $product->id,
                        'category_id' => $product->category_id,
                        'brand_id' => $product->brand_id,
                        'name' => $product->name,
                        'price' => $product->price_final,
                        'image' => $product->product_image->first() ? $product->product_image->first()->image : null,
                        // Thêm thông tin từ bảng Inventory
                        'lastMonthYear' => $lastMonthYear,
                        'totalExport' => $productOutOfStock->total_export,
                        'color' => $productOutOfStock->color->name,
                        'size' => $productOutOfStock->size->name

                        // Thêm thông tin khác cần thiết từ bảng Inventory
                    ];
                }
            }
        }


        // Lấy danh sách product_id từ bảng order_product
        $soldProducts = OrderProduct::pluck('product_id')->toArray();

        // Tìm những sản phẩm chưa bán được (chưa có trong danh sách soldProducts)
        $unsoldProducts = $products->filter(function ($product) use ($soldProducts) {
            return !in_array($product->id, $soldProducts);
        });

        // Sử dụng map để chỉ lấy thông tin sản phẩm cần thiết
        $unsoldProductInfo = $unsoldProducts->map(function ($product) {
            $image = $product->product_image->first();
            return [
                'id' => $product->id,
                'category_id' => $product->category_id,
                'brand_id' => $product->brand_id,
                'name' => $product->name,
                'price' => $product->price_final,
                'image' => $image ? $image['image'] : null
            ];
        });

        // Chuyển danh sách sản phẩm chưa bán được thành mảng
        $unsoldProductInfoArray = $unsoldProductInfo->values()->toArray();

        return response()->json([
            'missing_products' => $missingProductArray, // Trả về danh sách sản phẩm chưa nhập hàng
            'out_of_stock_products' => $outOfStockProducts, // Trả về danh sách sản phẩm hết hàng,
            'unsold_products' => $unsoldProductInfoArray // Trả về danh sách sản phẩm chưa bán được
        ], 200);
    }

    public function sendNotification(Request $request)
    {

        event(new SendNotification(
            $request['user'],
            $request['type'],
            $request['message'],
            $request['link'],
        ));

        $notification = new Notification;
        $notification->user = $request['user'];
        $notification->type = $request['type'];
        $notification->message = $request['message'];
        $notification->status = 'Chưa đọc';
        $notification->date = Carbon::now('Asia/Ho_Chi_Minh');
        $notification->link = $request['link'];
        $notification->save();

        return response()->json(['success' => true], 200);
    }

    public function getNotification(Request $request)
    {

        $notifications = Notification::orderByDesc('date')->get();

        return response()->json($notifications);
    }
}
