<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\District;
use App\Models\Ward;
use App\Models\Contact;

use App\Http\Resources\ContactResource;


class AddressesController extends Controller
{    
    // Find all address by user_id
    public function addresses($id) {
        $contacts = Contact::with(['ward.district.city'])
                ->whereHas('user', function($query) use ($id) {
                   $query->where('id', $id);
                })->get();
        
        return response(ContactResource::collection($contacts));
    }

    // Find address by contact_id
    public function addressOrder($id) {
        $contact = Contact::with(['ward.district.city'])->find($id);
        return response()->json(new ContactResource($contact));
    }

    public function getCities() {
        $cities = City::select('id', 'name')->get();
        return response()->json($cities);
    }

    public function getDistricts($city)
    {
        $districts = District::where("city_id", $city)->get();
        return response()->json($districts);
    }

    public function getWards($district)
    {
        $wards = Ward::where('district_id', $district)->get();

        return response()->json($wards);

    }


    // Create new contact with $user = id_user
    public function store($user, Request $request) {
        $contactOld = Contact::where(['user_id' =>$user, 'ward_id' => $request->ward_id, 
            'address' => $request->address])->first();

        $contact = new Contact;

        if(!$contactOld) {
            $contact->user_id = $user;
            $contact->name = $request['name'];
            $contact->ward_id = $request['ward_id'];
            $contact->address = $request['address'];
            $contact->phone = $request['phone'];
            $contact->save();
            return response()->json([
                'success' => true,
                'contact' => $contact,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Địa chỉ hệ đã tồn tại."
            ]);
        }
    }
}

