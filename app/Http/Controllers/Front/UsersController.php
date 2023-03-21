<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\User;

class UsersController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => User::all(),
        ]);
    }
}