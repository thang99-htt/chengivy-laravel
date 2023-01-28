<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Hash;
use Auth;
use Image;

class AdminController extends Controller
{
    public function dashboard() {
        $userTotal = User::all()->count();
        $userTotalToday = User::whereDate('created_at', Carbon::today())->count();
        $categoryTotal = Category::all()->count();
        $productTotal = Product::all()->count();
        return view('admin.dashboard')->with(compact('userTotal', 'userTotalToday', 'categoryTotal', 'productTotal'));
    }

    public function login(Request $request) {
        
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            $rules = [
                'email' => 'required|email|max:255',
                'password' => 'required',
            ];

            $customMessage = [
                'email.required' => 'Email is required!',
                'email.email' => 'Valid Email is required!',
                'password.required' => 'Password is required!',
            ];

            $this->validate($request, $rules, $customMessage);

            if(Auth::guard('admin')->attempt(['email'=>$data['email'], 'password'=>$data['password'],'status'=>1])) {
                return redirect('admin/dashboard');
            } else {
                return redirect()->back()->with('error_message', 'Invalid Email or Password');
            }
        }
        return view('admin.login');
    }
    
    public function updateAdminPassword(Request $request) {
        if($request->isMethod('post')) {
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            $rules = [
                'current_password' => 'required',
                'new_password' => 'required',
                'confirm_password' => 'required',
            ];

            $customMessage = [
                'current_password.required' => 'Current Password is required!',
                'new_password.required' => 'New Password is required!',
                'confirm_password.required' => 'Confirm Password is required!',
            ];

            $this->validate($request, $rules, $customMessage);

            // Check if current password entered by admin is correct
            if(Hash::check($data['current_password'],Auth::guard('admin')->user()->password)) {
                // Check if new password is matching with confirm password
                if($data['confirm_password'] == $data['new_password']) {
                    Admin::where('id',Auth::guard('admin')->user()->id)->update(['password'=>bcrypt($data['new_password'])]);
                    return redirect()->back()->with('success_message','Password has been update successfully!');
                } else {
                    return redirect()->back()->with('error_message','New password and Cofirm password does not match!');
                }
            } else {
                return redirect()->back()->with('error_message','Your current password is Incorrect!');
            }
        }

        $adminDetails = Admin::where('email',Auth::guard('admin')->user()->email)->first()->toArray();
        return view('admin.settings.update_admin_password')->with(compact('adminDetails'));
    }

    public function checkAdminPassword(Request $request) {
        $data = $request->all();
        // echo "<pre>"; print_r($data); die;
        if(Hash::check($data['current_password'],Auth::guard('admin')->user()->password)) {
            return "true";
        } else {
            return "false";
        }
    }

    public function updateAdminDetails(Request $request) {
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            $rules = [
                'admin_fullname' => 'required|regex:/^[\pL\s\-]+$/u',
                'admin_phone' => 'required|numeric|digits:10',
                'admin_birth_date' => 'required',
                'admin_address' => 'required',
            ];

            $customMessage = [
                'admin_fullname.required' => 'Name is required!',
                'admin_fullname.regex' => 'Valid Name is required!',
                'admin_phone.required' => 'Phone is required!',
                'admin_phone.numeric' => 'Valid Phone is required!',
                'admin_birth_date.required' => 'Birth Date is required!',
                'admin_address.required' => 'Address is required!',
            ];

            $this->validate($request, $rules, $customMessage);

            // Upload admin photo
            if($request->hasFile('admin_image')) {
                $image_tmp = $request->file('admin_image');
                if($image_tmp->isValid()) {
                    // Get image extension
                    $extension = $image_tmp->getClientOriginalExtension();
                    // Generate new image name
                    $imageName = rand(111,99999).'.'.$extension;
                    $imagePath = 'storage/images/admin/photos/'.$imageName;
                    // Upload image
                    Image::make($image_tmp)->save($imagePath);
                }
            } else if (!empty($data['current_admin_image'])) {
                $imageName = $data['current_admin_image'];
            } else {
                $imageName = "" ;
            }

            // Update admin details
            Admin::where('id', Auth::guard('admin')->user()->id)->update(['fullname'=>$data['admin_fullname'], 
                'phone'=>$data['admin_phone'], 'gender'=>$data['admin_gender'],
                'birth_date'=>$data['admin_birth_date'], 'address'=>$data['admin_address'],
                'image'=>$imageName]);
            return redirect()->back()->with('success_message','Admin details update successfully!');
        }

        return view('admin.settings.update_admin_details');
    }

    public function logout() {
        Auth::guard('admin')->logout();
        return redirect('admin/login');
    }

    public function search(Request $request) {
        if($request->ajax()) { 
            $products = Product::where('id', 'LIKE', '%'.$request->search.'%')
                    ->orWhere('name', 'LIKE', '%'.$request->search.'%')
                    ->orWhere('description', 'LIKE', '%'.$request->search.'%')->get();
            if(count($products)>0) {
                return view('front.products.search')->with(compact('products'));
            } else {
                return '<h3>NO RESULTS FOUND FOR "'.$request->search.'"</h3>';
            }
        }
    }
}

