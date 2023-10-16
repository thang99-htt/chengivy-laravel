<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
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
        

        $user = new User();
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        $profile = new Profile();
        $profile->user_id = $user->id;
        $profile->name = $request->name;
        $profile->phone = $request->phone;
        $profile->save();


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
