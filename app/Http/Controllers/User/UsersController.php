<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use App\Http\Resources\UserResource;

class UsersController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => User::all(),
        ]);
    }

    public function infoAccount($id) {
        $user = User::with('contacts.ward.district.city')->find($id);
        return response()->json(new UserResource($user));
    }

    public function updateProfile($id, Request $request)
    {
        $user = User::where('id', $id)->update([
            'name' => $request['name']
        ]);

        $user = Profile::where('user_id', $id)->update([
            'phone' => $request['phone'],
            'birth_date' => $request['birth_date'],
            'gender' => $request['gender'],
            'account_number' => $request['account_number'],
        ]);

        return response()->json($user);
    }
}