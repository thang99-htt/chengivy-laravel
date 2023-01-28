<?php

namespace App\Http\Controllers\Back;

use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Auth;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    public function index()
    {
        // $roles = Role::all();
        // return array_reverse($roles); 
        $roles = Role::orderBy('created_at', 'DESC')->get();
        return response()->json($roles);   
    }
    public function store(Request $request)
    {
        // $rules = [
        //     'name' => 'required',
        //     'description' => 'required',

        // ];

        // $customMessage = [
        //     'name.required' => 'Role Name is required!',
        //     'description.required' => 'Role Description is required!',
        // ];

        // $this->validate($request, $rules, $customMessage);

        $role = new Role;
        $role->name = $request['name'];
        $role->description = $request['description'];
        $role->save();
        // $role = new Role([
        //     'name' => $request->input('name'),
        //     'description' => $request->input('description')
        // ]);
        // $role->save();
        return response()->json('Role created!');
    }
    public function show($id)
    {
        $role = Role::find($id);
        return response()->json($role);
    }
    public function update($id, Request $request)
    {
        $role = Role::find($id);
        $role->update($request->all());
        return response()->json('Role updated!');
    }
    public function destroy($id)
    {
        $role = Role::find($id);
        $role->delete();
        return response()->json(['success'=>'true'], 200);
    }

    public function destroyAll()
    {
        $role = Role::all();
        $role->delete();
        return response()->json("ok");
    }
    
    
}
