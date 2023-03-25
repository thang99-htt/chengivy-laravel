<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Staff;
use Auth;

class StaffsController extends Controller
{
    public function index()
    {
        $staffs = Staff::all();
        return response()->json($staffs);

    }

    public function store(Request $request) {
        $staff = new Staff([
            'fullname' => $request->input('fullname'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'phone' => $request->input('phone'),
            'gender' => $request->input('gender'),
            'birth_date' => $request->input('birth_date'),
            'address' => $request->input('address'),
        ]);
        $staff->save();
        return response()->json('Staff new has been create successfully!');
        
    }
    
    // public function index()
    // {
    //     $staffs = Staff::with(['role' => function($query) {
    //     $query->select('id', 'name'); }])->get()->toArray();
        
    //     if (! Gate::forUser(Auth::guard('admin')->user())->allows('isAdmin')) {
    //         abort(403);
    //     } 

    //     return view('admin.staffs.staffs')->with(compact('staffs'));

    // }

    public function updateStaffStatus(Request $request) {
        if($request->ajax()) {
            $data = $request->all();

            if($data['status'] == 'Active') {
                $status = 0;
            } else {
                $status = 1;
            }

            Staff::where('id', $data['staff_id'])->update(['status' => $status]);
            return response()->json(['status'=>$status, 'staff_id'=> $data['staff_id']]);
        }
    }
    
    
    public function create()
    {
        $roles = Role::get()->toArray();
        if (! Gate::forUser(Auth::guard('admin')->user())->allows('isAdmin')) {
            abort(403);
        }
        return view('admin.staffs.add_staff')->with(compact('roles'));
    }

    
    // public function store(Request $request)
    // {
    //     $staff = new Staff;
    //     if($request->isMethod('post')) {
    //         $data = $request->all();
    //         // echo "<pre>"; print_r($data); die;

    //         $rules = [
    //             'staff_fullname' => 'required',
    //             'staff_email' => 'required|email|max:255',
    //             'staff_pass' => 'required',
    //             'staff_phone' => 'required',
    //             'staff_gender' => 'required',
    //             'staff_birth_date' => 'required',
    //             'staff_address' => 'required',

    //         ];

    //         $customMessage = [
    //             'staff_fullname.required' => 'Staff Full Name is required!',
    //             'staff_email.required' => 'Staff Email is required!',
    //             'staff_email.required' => 'Valid Email is required!',
    //             'staff_pass.required' => 'Staff Password is required!',
    //             'staff_phone.required' => 'Staff Phone is required!',
    //             'staff_gender.required' => 'Staff Gender is required!',
    //             'staff_birth_date.required' => 'Staff Birth Date is required!',
    //             'staff_address.required' => 'Staff Address is required!',
    //         ];

    //         $this->validate($request, $rules, $customMessage);


    //         $staff->fullname = $data['staff_fullname'];
    //         $staff->email = $data['staff_email'];
    //         $staff->password = bcrypt($data['staff_pass']);
    //         $staff->phone = $data['staff_phone'];
    //         $staff->gender = $data['staff_gender'];
    //         $staff->birth_date = $data['staff_birth_date'];
    //         $staff->address = $data['staff_address'];
    //         if($data['staff_gender'] == 'Male') {
    //             $staff->image = 'male.jpg';
    //         } else if($data['staff_gender'] == 'Female') {
    //             $staff->image = 'female.jpg';
    //         }
    //         $staff->save();
    //     }
        
    //     return redirect()->back()->with('success_message','Staff new has been create successfully');
    // }

    
    public function show(Request $request)
    {
        $roles = Role::get()->toArray();
        $staff = Staff::with(['role' => function($query) {
                $query->select('id', 'name');
            }])->find($request->id)->toArray();
        
        if (! Gate::forUser(Auth::guard('admin')->user())->allows('isAdmin')) {
            abort(403);
        }

        return view('admin.staffs.update_staff', compact('staff', 'roles'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function edit(Staff $staff)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $staff = Staff::find($request->id);
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            $rules = [
                'sta_fullname' => 'required',
                'sta_email' => 'required',
                'sta_phone' => 'required',
                'sta_birth_date' => 'required',
                'sta_address' => 'required',
            ];

            $customMessage = [
                'sta_fullname.required' => 'Staff Full Name is required!',
                'sta_email.required' => 'Staff Email is required!',
                'sta_phone.required' => 'Staff Phone is required!',
                'sta_birth_date.required' => 'Staff Birth Date is required!',
                'sta_address.required' => 'Staff Address is required!',
            ];

            $this->validate($request, $rules, $customMessage);


            Staff::where('id', $request->id)->update([
                'fullname'=>$data['sta_fullname'],
                'email'=>$data['sta_email'], 
                'phone'=>$data['sta_phone'], 
                'gender'=>$data['sta_gender'], 
                'birth_date'=>$data['sta_birth_date'],
                'address'=>$data['sta_address'],
            ]);

            return redirect('admin/staffs')->with('success_message','Staff update successfully!');
        }

        return view('admin.staffs.update_staff', compact('staff'));
    }

    
    public function showRole(Request $request)
    {
        $roles = Role::get()->toArray();
        $staff = Staff::with(['role' => function($query) {
                $query->select('id', 'name');
            }])->find($request->id)->toArray();
        
        if (! Gate::forUser(Auth::guard('admin')->user())->allows('isAdmin')) {
            abort(403);
        }

        return view('admin.staffs.update_staff_role', compact('staff', 'roles'));
    }

    public function updateRole(Request $request)
    {
        $staff = Staff::find($request->id);
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            Staff::where('id', $request->id)->update([
                'role_id'=>$data['sta_role'],
            ]);

            return redirect('admin/staffs')->with('success_message','Staff Role update successfully!');
        }

        return view('admin.staffs.update_staff_role', compact('staff'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Staff  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $staff = Staff::find($request->id);
        $staff->delete();
        $message = "Staff has been deleted successfully!";
        return redirect()->back()->with('success_message', $message);
    }
}
