<?php

namespace App\Http\Controllers\Admin;

use App\Events\SendNotification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Notification;
use App\Models\User;

class NotificationsController extends Controller
{    
    public function storeReview($id)
    {
        $user = User::find($id);
        event(new SendNotification(
            $user->name, 
            1,
            "Có đánh giá luận tiêu cực",
            "http://localhost:3000/admin/reviews",
        ));

        $notification = new Notification();
        $notification->user = $user->name;
        $notification->type = 2;
        $notification->message = "Có đánh giá luận tiêu cực";
        $notification->status = 'Chưa đọc';
        $notification->date = Carbon::now('Asia/Ho_Chi_Minh');
        $notification->link = "http://localhost:3000/admin/reviews";
        $notification->save();
        
        return response()->json([
            'success' => 'success',
            'message' => 'Thêm thông báo thành công.'
        ], 200);  
    }

    
}

