<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Employee;
use Auth;

class EmployeesController extends Controller
{
    
    public function index()
    {
        $employees = Employee::all();
        return response()->json($employees);

    }

}
