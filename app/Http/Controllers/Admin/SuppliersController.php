<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SuppliersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Supplier::orderBy('created_at', 'DESC')->get();
        return response()->json($roles); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role = new Supplier;
        $role->name = $request['name'];
        $role->description = $request['description'];
        $role->save();
        return response()->json('Role created!');;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Supplier::find($id);
        return response()->json($role);
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
        $role = Supplier::find($id);
        $role->update($request->all());
        return response()->json('Role updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Supplier::find($id);
        $role->delete();
        return response()->json(['success'=>'true'], 200);
    }

    public function destroyAll()
    {
        $roles = Supplier::all();
        
        foreach($roles as $key => $value) {
            $role = Supplier::find($roles[$key]['id']);
            $role->delete();
        }
        return response()->json([
            'status' => false,
            'message' => "Deleted All."
        ], 200);
    }
}