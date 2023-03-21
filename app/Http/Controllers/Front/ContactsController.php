<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Contact;


class ContactsController extends Controller
{
    public function index($id) {
        $user = User::find($id);

        $wards = $user->wards;
        foreach($wards as $key => $value) {
            $district = $wards[$key]->district->name;
            $city = $wards[$key]->district->city->name;
        }

        return response()->json($wards);
    }

    public function store($user, Request $request) {
        $contactOld = Contact::where(['user_id' =>$user, 'ward_id' => $request->ward_id, 
            'address' => $request->address])->first();

        $contact = new Contact;

        if(!$contactOld) {
            $contact->user_id = $user;
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
