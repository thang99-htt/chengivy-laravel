<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RolesController extends Controller
{

    public function index()
    {
        $roles = Role::orderBy('created_at', 'DESC')->get();
        return response()->json($roles); 
    }

    public function store(Request $request)
    {
        $role = new Role;
        $role->name = $request['name'];
        $role->description = $request['description'];
        $role->save();
        return response()->json('Role created!');
    }

    public function show($id)
    {
        $role = Role::find($id);
        return response()->json($role);
    }

    public function update(Request $request, $id)
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
        $roles = Role::all();
        
        foreach($roles as $key => $value) {
            $role = Role::find($roles[$key]['id']);
            $role->delete();
        }
        return response()->json([
            'status' => false,
            'message' => "Deleted All."
        ], 200);
    }
}
