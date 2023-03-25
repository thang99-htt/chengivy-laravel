<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Staff;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function login(Request $request) {
        $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required',
                'device_name' => 'required',
            ],
        );
        $staff = Staff::where('email', $request->email)->first();

        if(!Auth::guard('admin')->attempt(['email'=>$request['email'], 'password'=>$request->password,'status'=>1])) {
            throw ValidationException::withMessages([
                'email' => ['Email hoặc mật khẩu không đúng.'],
            ]);
        }

        $token = $staff->createToken($request->device_name)->plainTextToken;
        
        return response()->json([
            'token' => $token,
            'staff' => $staff
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['msg' => 'Đăng xuất thành công.']);
    }
    
}
