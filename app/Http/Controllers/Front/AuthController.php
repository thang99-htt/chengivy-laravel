<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', 'confirmed'],
                'password_confirmation' => 'required',
            ],
            [
                'email.unique' => 'Email đã tồn tại.',
                'password.confirmed' => 'Mật khẩu không khớp.',
            ]
        );
        
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['msg' => 'Đăng ký thành công.']);
    }

    public function login(Request $request) {
        $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required',
                'device_name' => 'required',
            ],
                        
        );
        $user = User::where('email', $request->email)->first();

        if(! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email hoặc mật khẩu không đúng.'],
            ]);
        }
        $token = $user->createToken($request->device_name)->plainTextToken;
        
        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['msg' => 'Đăng xuất thành công.']);
    }
    
}
