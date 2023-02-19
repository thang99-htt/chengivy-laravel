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
use App\Models\Payment;
use Session;
use Auth;
use Hash;
use Response;


class AddressesController extends Controller
{    
    // Find all address by user_id
    public function addresses($id) {
        $user = User::find($id);
        $wards = $user->wards;
        foreach($wards as $key => $value) {
            $district = $wards[$key]->district->name;
            $city = $wards[$key]->district->city->name;
        }

        return response()->json($wards);
    }


    // Find address by contact_id
    public function addressOrder($id) {
        $contact = Contact::find($id)->with(['ward', 'user' => function($query) {
            $query->select('id', 'name');
        }])->first();
        $district = $contact->ward->district;
        $city = $contact->ward->district->city;
        return response()->json($contact);
    }

}

