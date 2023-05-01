<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SuppliersController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('created_at', 'DESC')->get();
        return response()->json($suppliers); 
    }

    public function store(Request $request)
    {
        $supplier = new Supplier;
        $supplier->name = $request['name'];
        $supplier->address = $request['address'];
        $supplier->phone = $request['phone'];
        $supplier->email = $request['email'];
        $supplier->save();
        return response()->json($supplier);
    }

    public function show($id)
    {
        $supplier = Supplier::find($id);
        return response()->json($supplier);
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        $supplier->update($request->all());
        return response()->json($supplier);
    }

    public function destroy($id)
    {
        $supplier = Supplier::find($id);
        $supplier->delete();
        return response()->json(['success'=>'true'], 200);
    }

}
