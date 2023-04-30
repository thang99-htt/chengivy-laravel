<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Role;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\PermissionStaff;
use App\Models\RoleStaff;
use App\Http\Resources\RoleResource;
use App\Http\Resources\StaffResource;

class AuthorizationController extends Controller
{
    public function getStaff($id) {
        $staff = Staff::with('role_staff.role.permission_role.permission')->find($id);
        return response()->json(new StaffResource($staff));
    }

    public function authorizationByRole() {
        $roles = Role::with('permission_role.permission', 'role_staff.staff')->get();
        return response()->json(RoleResource::collection($roles));
    }

    public function authorizationByStaff() {
        $staffs = Staff::with('role_staff')->get();
        return response()->json(StaffResource::collection($staffs));
    }   

    public function storeRoleStaff(Request $request)
    {
        $getRoles = $request['role_id'];
        $test = 0;
        
        $staffRoles = RoleStaff::where(['staff_id' => $request['staff_id']])->get();
        foreach($staffRoles as $staff) {
            $staff->delete();
        }

        foreach($getRoles as $item) {
            $role_staff = RoleStaff::where(['staff_id' => $request['staff_id'], 'role_id' => $item])->first();
            if($role_staff) {
                $test++;
            } else {
                $roleStaff = new RoleStaff;
                $roleStaff->staff_id = $request['staff_id'];
                $roleStaff->role_id = $item;
                $roleStaff->save();
            }
        } 
        
        return response()->json($test, 200);

    }

    public function getPermssionRole($id) {
        $role = Role::find($id);
        return response()->json(new RoleResource($role));
    }

    public function storePermssionRole(Request $request)
    {
        $getPermissions = $request['permission_id'];
        $test1 = 0;

        $getStaffs = $request['staff_id'];
        $test2 = 0;
        
        
        // delte permission of role if existed
        $permisisonsRoles = PermissionRole::where(['role_id' => $request['role_id']])->get();
        foreach($permisisonsRoles as $permisison) {
            $permisison->delete();
        }

        // create table permission_role
        foreach($getPermissions as $item) {
            $permisison_role = PermissionRole::where(['role_id' => $request['role_id'], 'permission_id' => $item])->first();
            if($permisison_role) {
                $test1++;
            } else {
                $permissionRole = new PermissionRole;
                $permissionRole->role_id = $request['role_id'];
                $permissionRole->permission_id = $item;
                $permissionRole->save();
            }
        }

        // delte permission of staff if existed
        foreach($getStaffs as $staff) {
            $staffsRoles = PermissionStaff::where(['staff_id' => $staff])->get();
            foreach($staffsRoles as $staff) {
                $staff->delete();
            }
        }

        // create table permission_staff
        foreach($getStaffs as $staff) {
            foreach($getPermissions as $permisison) {
                $permisison_staff = PermissionStaff::where(['staff_id' => $staff, 'permission_id' => $permisison])->first();
                if($permisison_staff) {
                    $test2++;
                } else {
                    $permissionStaff = new PermissionStaff;
                    $permissionStaff->staff_id = $staff;
                    $permissionStaff->permission_id = $permisison;
                    $permissionStaff->save();
                }
            }
        } 
        
        return response()->json(['success' => true], 200);

    }

    public function getRoleStaff($id) {
        $staff = Staff::with('role_staff')->find($id);
        return response()->json(new StaffResource($staff));
    }

}
