<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use App\Models\Review;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => User::all(),
        ]);
    }

    public function infoAccount($id) {
        $user = User::with('delivery_address')->find($id);
        return response()->json(new UserResource($user));
    }

    public function updateProfile($id, Request $request)
    {
        Profile::where('user_id', $id)->update([
            'name' => $request['name'],
            'phone' => $request['phone'],
            'birth_date' => $request['birth_date'],
            'gender' => $request['gender'],
            'bank_account' => $request['bank_account'],
        ]);

        return response()->json([
            'success'=>'success',
            'message'=>'Tài khoản được cập nhật thành công.'
        ]);
    }

    public function updatePassword($id, Request $request)
    {
        $user = User::find($id);
        $password_old = $user->password;
        if (Hash::check($request->password, $password_old)) {
            $user->password = Hash::make($request->new_password);
            $user->save();
            return response()->json([
                'success'=>'success',
                'message'=>'Mật khẩu được cập nhật thành công.'
            ]);
        } else {
            return response()->json([
                'success'=>'warning',
                'message'=>'Mật khẩu không đúng.'
            ]);
        }
    }

    public function getReviews($id)
    {
        $reviews = Review::with('product.product_image', 'review_image', 'user')->where('user_id', $id)
            ->orderBy('created_at', 'DESC')->get();
        return response($reviews);
    }

    public function deleteReview($id)
    {
        $review = Review::find($id);
        $review->delete();
        return response()->json([
            'success'=>true,
        ], 200);
    }
}