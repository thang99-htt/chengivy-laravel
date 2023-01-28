<?php

namespace App\Http\Controllers\Front;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductImages;
use App\Models\Ward;
use Session;
use Image;
use Auth;


class ContactsController extends Controller
{
    public function addresses() {
        Session::put('page', 'contacts');
        $user = Auth::user()->id;
        return view('front.profiles.addresses')->with(compact('user'));

    }

}
