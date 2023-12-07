<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Carbon\Carbon;

class SuppliersController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::where('deleted_at', null)
                    ->orderBy('created_at', 'DESC')
                    ->get();
        return response()->json($suppliers); 
    }

    public function store(Request $request)
    {
        $supplier = new Supplier;
        $supplier->name = $request['name'];
        $supplier->address = $request->input('address_detail') . ", " . $request->input('address');
        $supplier->phone = $request['phone'];
        $supplier->email = $request['email'];
        $supplier->bank_account = $request['bank_account'];
        $supplier->tax_code = $request['tax_code'];
        $supplier->date_cooperate = $request['date_cooperate'];
        $supplier->save();

        return response()->json([
            'success' => 'success',
            'message' => "Nhà cung cấp được thêm thành công."
        ]);
    }

    public function show($id)
    {
        $supplier = Supplier::find($id);

        $address_supplier = $supplier->address;
        $comma_position = strpos($address_supplier, ',');
        $supplier_address_detail = trim(substr($address_supplier, 0, $comma_position));
        $supplier_address = trim(substr($address_supplier, $comma_position + 1));
        $supplier->address_detail = $supplier_address_detail;
        $supplier->address = $supplier_address;

        return response()->json($supplier);
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        $supplier->name = $request['name'];
        $supplier->address = $request->input('address_detail') . ", " . $request->input('address');
        $supplier->phone = $request['phone'];
        $supplier->email = $request['email'];
        $supplier->bank_account = $request['bank_account'];
        $supplier->tax_code = $request['tax_code'];
        $supplier->date_cooperate = $request['date_cooperate'];
        $supplier->save();
        
        return response()->json([
            'success' => 'success',
            'message' => "Nhà cung cấp được cập nhật thành công."
        ]);
    }

    public function destroyIds(Request $request)
    {
        $selectedIds = $request->all(); // Lấy danh sách selectedIds từ request
        $suppliers = Supplier::whereIn('id', $selectedIds)->get(); // Sử dụng whereIn để lấy các bản ghi tương ứng với selectedIds
        foreach($suppliers as $supplier) {
            $supplier->deleted_at = Carbon::now('Asia/Ho_Chi_Minh');
            $supplier->save();
        }      
        return response()->json([
            'success' => true,
            'message' => "Deleted All."
        ], 200);
    }


}
