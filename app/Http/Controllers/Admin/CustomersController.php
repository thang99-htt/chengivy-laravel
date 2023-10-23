<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CustomersController extends Controller
{
    public function index()
    {
        $customers = Profile::with('user.delivery_address')
            ->orderBy('created_at', 'DESC')
            ->get();

        
        foreach($customers as $customer) {
            if($customer->user)
                $customer->address = $customer->user->delivery_address->where('default', 1)->first();
        }
        return response()->json($customers);
    }


    public function store(Request $request)
    {
        $customer = new Profile();
        $customer->name = $request->input('name');
        $customer->phone = $request->input('phone');
        $customer->gender = $request->input('gender');
        
        $customer->save();

        return response()->json($customer, 200);
    }

    public function filterGhost() {
        $order = Order::with('user')->where('status_id', 13)->get();
        return response()->json($order, 200);
    }


    // public function show($id)
    // {
    //     $staff = User::with('roles.permission_role.permission')->find($id);

    //     $address_staff = $staff->address;
    //     $comma_position = strpos($address_staff, ',');
    //     $staff_address_detail = trim(substr($address_staff, 0, $comma_position));
    //     $staff_address = trim(substr($address_staff, $comma_position + 1));
    //     $staff->address_detail = $staff_address_detail;
    //     $staff->address = $staff_address;

    //     return response()->json(new StaffResource($staff));
    // }

    // public function update(Request $request, $id)
    // {
    //     $staff = Staff::find($id);
    //     $staff->name = $request->name;
    //     $staff->email = $request->email;
    //     if($staff->password) {
    //         $staff->password = Hash::make($request->password);
    //     }
    //     $staff->phone = $request->phone;
    //     $staff->identity_card = $request->identity_card;
    //     $staff->gender = $request->gender;
    //     $staff->birth_date = $request->birth_date;

    //     $address_staff = $staff->address;
    //     $comma_position = strpos($address_staff, ',');
    //     $staff_address_detail = trim(substr($address_staff, 0, $comma_position));

    //     if($staff_address_detail != $request->address_detail) {
    //         $staff->address = $request->address_detail . ", " . $request->address;
    //     } else {
    //         $staff->address =  $staff_address_detail . ", " . $request->address;
    //     }

    //     $staff->save();

    //     $staffRoles = RoleStaff::where(['staff_id' => $id])->get();
    //     foreach($staffRoles as $staff) {
    //         RoleStaff::where(['staff_id' => $staff->staff_id])->delete();
    //     }

    //     $getRoles = $request['role_id'];
    //     foreach($getRoles as $item) {
    //         $roleStaff = new RoleStaff;
    //         $roleStaff->staff_id = $id;
    //         $roleStaff->role_id = $item;
    //         $roleStaff->save();
    //     } 
        
    //     return response()->json('Staff updated!');
    // }

    // public function destroyIds(Request $request)
    // {
    //     $selectedIds = $request->all(); 
    //     $staffs = Staff::whereIn('id', $selectedIds)->get(); 
    //     foreach($staffs as $staff) {
    //         $staff->delete(); 
    //     }      
    //     return response()->json([
    //         'success' => true,
    //         'message' => "Deleted All."
    //     ], 200);
    // }

    // public function destroy($id)
    // {
    //     $staff = Staff::find($id);
    //     $staff->delete();
    //     return response()->json(['success'=>'true'], 200);
    // }

    // public function updateStaffStatus($id, Request $request) {
    //     $staff = Staff::find($id);
    //     $staff->status = !$request->status;
    //     $staff->save();
        
    //     return response()->json([
    //         'success' => true,
    //         'staff' => $staff,
    //     ]);
    // }
}
