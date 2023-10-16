<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Status;

class StatusesController extends Controller
{
    public function index()
    {
        $statuses = Status::get();
        return response()->json($statuses);
    }
}
