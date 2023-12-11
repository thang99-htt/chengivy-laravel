<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Order;

class AutoUpdateOrderStatus extends Command
{
    protected $signature = 'command:auto-update-status';
    protected $description = 'Automatically update order status after 3 days';

    public function handle()
    {
        $threeDaysAgo = Carbon::now('Asia/Ho_Chi_Minh')->subDays(3);
        
        $orders = Order::where('status_id', 7) // Assuming 7 is the status code for pending confirmation
            ->where('created_at', '<=', $threeDaysAgo)
            ->get();

        foreach ($orders as $order) {
            $order->status_id = 8;
            $order->receipted_at = Carbon::now('Asia/Ho_Chi_Minh');
            $order->save();
        }

        $this->info('Orders updated successfully.');
    }
}
