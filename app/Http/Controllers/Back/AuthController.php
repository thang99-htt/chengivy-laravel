<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Staff;
use Hash;
use Auth;

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
        return $staff->createToken($request->device_name)->plainTextToken;
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        return response()->json(['msg' => 'Đăng xuất thành công.']);
    }
    
}
