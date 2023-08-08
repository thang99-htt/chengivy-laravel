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
        $staff = Staff::with('roles.permission_role.permission')->where('email', $request->email)->first();

        $permissionIDs = [];
        foreach ($staff->roles as $role) {
            foreach ($role->permission_role as $permissionRole) {
                $permissionIDs[] = $permissionRole->permission->id;
            }
        }

        $staff->permissionIDs = $permissionIDs;

        $staffData = [
            'id' => $staff->id,
            'name' => $staff->name,
            'email' => $staff->email,
            'password' => $staff->password,
            'permissionIDs' => $permissionIDs,
        ];

        if(!Auth::guard('admin')->attempt(['email'=>$request['email'], 'password'=>$request->password,'actived'=>1])) {
            throw ValidationException::withMessages([
                'email' => ['Email hoặc mật khẩu không đúng.'],
            ]);
        }

        $token = $staff->createToken($request->device_name)->plainTextToken;
        
        return response()->json([
            'token' => $token,
            'staff' => $staffData
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['msg' => 'Đăng xuất thành công.']);
    }
    
}
