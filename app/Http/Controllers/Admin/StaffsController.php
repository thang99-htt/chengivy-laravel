<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class StaffsController extends Controller
{
    public function index()
    {
        $staffs = Staff::orderBy('created_at', 'DESC')->get();
        return response()->json($staffs); 
    }

    public function store(Request $request)
    {
        $staff = new Staff;
        $staff->name = $request['name'];
        $staff->email = $request['email'];
        $staff->password = Hash::make($request['password']);
        $staff->phone = $request['phone'];
        $staff->identity_card = $request['identity_card'];
        $staff->gender = $request['gender'];
        $staff->birth_date = $request['birth_date'];
        $staff->address = $request['address'];
        if($request['gender'] == 'Female') {
            $staff->image = 'female.jpg';
        } else {
            $staff->image = 'male.jpg';
        }
        $staff->save();

        // Gửi email chứa mật khẩu cho người dùng
        $toEmail = $staff->email;
        $subject = 'Mật khẩu đăng nhập Chengivy Store';
        $message = 'Xin chào, ' . $staff->name . '!' . "\n" . 
        'Mật khẩu đăng nhập của bạn là: ' . $request->password;

        Mail::raw($message, function ($mail) use ($toEmail, $subject) {
            $mail->to($toEmail)
                ->subject($subject);
        });

        return response()->json($staff, 200);
    }

    public function show($id)
    {
        $staff = Staff::find($id);
        return response()->json($staff);
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::find($id);
        $staff->update($request->all());
        return response()->json('Staff updated!');
    }

    public function destroy($id)
    {
        $staff = Staff::find($id);
        $staff->delete();
        return response()->json(['success'=>'true'], 200);
    }

    public function destroyAll()
    {
        $staffs = Staff::all();
        
        foreach($staffs as $key => $value) {
            $staff = Staff::find($staffs[$key]['id']);
            $staff->delete();
        }
        return response()->json([
            'status' => false,
            'message' => "Deleted All."
        ], 200);
    }

    public function updateStaffStatus($id, Request $request) {
        $staff = Staff::find($id);
        $staff->status = !$request->status;
        $staff->save();
        
        return response()->json([
            'success' => true,
            'staff' => $staff,
        ]);
    }
}
