<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;

class StatisticalController extends Controller
{
    public function index() {
        $users = count(User::get());
        $orders = count(Order::get());
        $orders_new = count(Order::where('status_id',1)->get());
        $products = count(Product::get());
        $revenues = Order::sum('total_price');
        $revenues_per_month = Order::selectRaw('MONTH(created_at) as month, SUM(total_price) as revenue')
            ->groupBy('month')->orderBy('month','asc')->get();
        $revenues_today = Order::whereDate('created_at', today())->sum('total_price');
        $revenues_month = Order::whereMonth('created_at', today()->month)->sum('total_price');
        return response()->json([
            'users' => $users,
            'orders' => $orders,
            'products' => $products,
            'revenues' => $revenues,
            'revenues_per_month' => $revenues_per_month
        ], 200);
    }


}
