<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use App\Models\City;
use App\Models\District;
use App\Models\Ward;
use App\Models\Contact;
use Session;
use Auth;
use Hash;
use Response;


class ProfilesController extends Controller
{
    public function index() {
        return view('front.profiles.profile');
    }

    public function updateProfileDetails(Request $request) {
        $profile = Profile::with('user')->where('id', Auth::user()->id)->first();
        Profile::where('id',Auth::user()->id)->update(['phone'=>$data['profile_phone'], 'birth_date'=>$data['profile_birth_date'], 'gender'=>$data['profile_gender']]);
        return redirect()->back()->with('success_message','Profile has been update successfully!');
    }

    public function updateEmail(Request $request) {
        if(Hash::check($data['profile_current_password'],Auth::user()->password)) { 
            User::where('id',Auth::user()->id)->update(['email'=>$data['profile_email']]);
            return redirect()->back()->with('success_message','Email has been update successfully!');
        } else {
            return redirect()->back()->with('error_message','Your current password is Incorrect!');
        }
    }

    public function updatePassword(Request $request) {
        // Check if current password entered by admin is correct
        if(Hash::check($data['profile_current_password'],Auth::user()->password)) {
            // Check if new password is matching with confirm password
            if($data['profile_confirm_password'] == $data['profile_new_password']) {
                User::where('id',Auth::user()->id)->update(['password'=>bcrypt($data['profile_new_password'])]);
                return redirect()->back()->with('success_message','Password has been update successfully!');
            } else {
                return redirect()->back()->with('error_message','New password and Cofirm password does not match!');
            }
        } else {
            return redirect()->back()->with('error_message','Your current password is Incorrect!');
        }

        $userPassword = User::where('email', Auth::user()->email)->first();
        return response()->json($userPassword);
    }

    public function checkUserPassword(Request $request) {
        $data = $request->all();
        // echo "<pre>"; print_r($data); die;
        if(Hash::check($data['profile_current_password'],Auth::user()->password)) {
            return "true";
        } else {
            return "false";
        }
        
    }

    public function addresses(Request $request) {
        $user = User::find(Auth::user()->id);
        $cities = City::select('id', 'name')->get()->toArray();
        return view('front.profiles.addresses')->with(compact('user', 'cities'));
        return response()->json([
            'user' => $user,
            'cities' => $cities
        ]);
    }

    public function addressDefault(Request $request) {
        $data = $request->all();
        return $data['addressCheck'];
        
    }
    
    public function addAddress(Request $request) {
        $phone = Contact::where('user_id', Auth::user()->id)->get('phone')->first();
        $contactOld = Contact::where(['user_id' => Auth::user()->id, 'ward_id' => $request->address_ward, 
            'address' => $request->address_detail])->first();

        $contact = new Contact;

        if(!$contactOld) {
            $contact->user_id = Auth::user()->id;
            $contact->ward_id = $data['address_ward'];
            $contact->address = $data['address_detail'];
            $contact->phone = $phone->phone;
            $contact->save();
            return redirect()->back()->with('success_message','Contact add successfully!');
        } else {
            return redirect()->back()->with('error_message','Contact already existed!');
        }
    }

    public function updateAdress(Request $request) {
        $contact = Contact::find($request->id);
        $phone = Contact::where('user_id', Auth::user()->id)->get('phone')->first();
        
        Contact::where('id', $request->id)->update([
            'user_id' => Auth::user()->id,
            'ward_id'=>$data['update_address_ward'], 
            'address'=>$data['update_address_detail'],
            'phone'=>$phone->phone
        ]);

        return redirect()->back()->with('success_message','Contact updated successfully!');

    }

    public function getDistricts(Request $request)
    {
        $districts = District::where("city_id", $request->address_city)->get();
        return response()->json($districts);
    }

    public function getWards(Request $request)
    {
        $wards = Ward::where('district_id', $request->address_district)->get();

        return response()->json($wards);

    }

    public function deleteAddress(Request $request)
    {
        // Delete product image form products table
        Contact::where('id', $request->id)->delete();
        // dd($request->id);
        return redirect()->back()->with('success_message','Contact has been deleted successfully!');
    }

}

