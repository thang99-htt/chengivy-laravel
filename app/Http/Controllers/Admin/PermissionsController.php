<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::permissions();
        return response()->json($permissions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $permission = new Permission;
        $permission->name = $request['name'];
        $permission->description = $request['description'];
        $permission->save();
        return response()->json('Permission created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Permission = Permission::find($id);
        return response()->json($Permission);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $Permission = Permission::find($id);
        $Permission->update($request->all());
        return response()->json('Permission updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Permission = Permission::find($id);
        $Permission->delete();
        return response()->json(['success'=>'true'], 200);
    }

    public function destroyAll()
    {
        $permissions = Permission::all();
        
        foreach($permissions as $key => $value) {
            $Permission = Permission::find($permissions[$key]['id']);
            $Permission->delete();
        }
        return response()->json([
            'status' => false,
            'message' => "Deleted All."
        ], 200);
    }
}
