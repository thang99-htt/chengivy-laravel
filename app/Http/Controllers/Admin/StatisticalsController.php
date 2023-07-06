<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Staff;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\PaymentVoucher;
use App\Models\Product;

class StatisticalsController extends Controller
{
    public function index() {
        $staffs = count(Staff::get()) ? count(Staff::get()) : 0;
        $users = count(User::get()) ? count(User::get()) : 0;
        $customers = Order::with('user')->where('status_id',9)->selectRaw('user_id, COUNT(user_id) as count')
        ->groupBy('user_id')->orderByDesc('count')->first();
        $customers = $customers ? $customers->user->name : 0;

        $orders = count(Order::get());
        $orders_cancle = count(Order::where('status_id',10)->get());
        $orders_success = count(Order::where('status_id',9)->get());

        $products = count(Product::get());
        $products_orders = Order::with('order_product.product')->where('status_id',9)->get();
        $products_orders = $products_orders
            ->flatMap(function ($order) {
                return $order->order_product;
            })
            ->groupBy('product_id')
            ->map(function ($orders) {
                return [
                    'product' => $orders->first()->product,
                    'count' => $orders->count()
                ];
            });
        
        $products_best_seller = $products_orders->sortByDesc('count')->first();
        $products_best_seller = $products_best_seller ? $products_best_seller['product']['name'] : 0;
        
        $products_flop = $products_orders->sortBy('count')->first();
        $products_flop = $products_flop ? $products_flop['product']['name'] : 0;

        $revenues = Invoice::sum('total_price') ?? 0;
        $revenues_per_month = Invoice::selectRaw('MONTH(date) as month, MONTH(date) as month, SUM(total_price) as revenue')
            ->groupBy('month')->orderBy('month','asc')->get();
        $revenues_today = Order::whereDate('created_at', today())->sum('total_price') ?? 0;
        $revenues_month = Order::whereMonth('created_at', today()->month)->sum('total_price') ?? 0;

        $payments = PaymentVoucher::sum('total_price') ?? 0;
        $payments_per_month = PaymentVoucher::selectRaw('MONTH(date) as month, SUM(total_price) as payment')
            ->groupBy('month')->orderBy('month','asc')->get();

        $profits_per_month = collect();
        foreach ($revenues_per_month as $key => $revenue) {
            $payment = $payments_per_month->where('month', $revenue->month)->first();
            $profit = (object) [
                'month' => $revenue->month,
                'profit' => $revenue->revenue - ($payment ? $payment->payment : 0)
            ];
            $profits_per_month->push($profit);
        }
            
        $profits = ($revenues - $payments) || 0;

        return response()->json([
            'staffs' => $staffs,
            'users' => $users,
            'customers' => $customers,
            'orders' => $orders,
            'orders_cancle' => $orders_cancle,
            'orders_success' => $orders_success,
            'products' => $products,
            'products_best_seller' => $products_best_seller,
            'products_flop' => $products_flop,
            'revenues' => $revenues,
            'revenues_per_month' => $revenues_per_month,
            'payments' => $payments,
            'payments_per_month' => $payments_per_month,
            'profits' => $profits,
            'profits_per_month' => $profits_per_month
        ], 200);
    }


}
