<?php

namespace App\Http\Controllers\Front;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Ward;
use App\Models\District;
use App\Models\City;
use App\Models\Contact;
use Session;
use Image;
use Auth;


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

}
