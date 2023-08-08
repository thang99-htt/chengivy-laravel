<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\PermissionRole;
use App\Http\Resources\RoleResource;

class RolesController extends Controller
{

    public function index()
    {
        $roles = Role::with('permission_role.permission')->orderBy('created_at', 'DESC')->get();
        return response()->json(RoleResource::collection($roles));
    }

    public function store(Request $request)
    {
        $role = new Role;
        $role->name = $request['name'];
        $role->description = $request['description'];
        $role->save();

        $getPermissions = $request['permission_id'];      

        // create table permission_role
        foreach($getPermissions as $item) {
            $permissionRole = new PermissionRole;
            $permissionRole->role_id = $role->id;
            $permissionRole->permission_id = $item;
            $permissionRole->save();
        }

        return response()->json('Role created!');
    }

    public function show($id)
    {
        $role = Role::with('permission_role.permission')->find($id);
        return response()->json(new RoleResource($role));
    }

    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        $role->update($request->all());

        $getPermissions = $request['permission_id'];
        
        // delte permission of role if existed
        $permisisonsRoles = PermissionRole::where(['role_id' => $id])->get();
        foreach($permisisonsRoles as $role) {
            PermissionRole::where(['role_id' => $id])->delete();
        }

        // create table permission_role
        foreach($getPermissions as $item) {
            $permissionRole = new PermissionRole;
            $permissionRole->role_id = $id;
            $permissionRole->permission_id = $item;
            $permissionRole->save();
        }
    
        return response()->json('Role updated!');
    }

    public function destroyIds(Request $request)
    {
        $selectedIds = $request->all(); 
        $roles = Role::whereIn('id', $selectedIds)->get();  
        foreach($roles as $role) {
            PermissionRole::where(['role_id' => $role->id])->delete();
            $role->delete();
        }      
        return response()->json([
            'success' => true,
            'message' => "Deleted All."
        ], 200);
    }
}
