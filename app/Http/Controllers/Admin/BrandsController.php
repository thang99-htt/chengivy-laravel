<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Brand;
use Carbon\Carbon;
use Intervention\Image\Facades\Image;

class BrandsController extends Controller
{
    public function index()
    {
        $brands = Brand::get();
        return response()->json($brands);
    }

    public function create()
    {     
        $brands = Brand::brands();
        return response()->json($brands);
    }

    public function store(Request $request)
    {
        if($request->image) {
            $strpos = strpos($request->image, ';');
            $sub = substr($request->image, 0, $strpos);
            $ex = explode("/", $sub)[1];
            $imageName = time().".".$ex;
            $img = Image::make($request->image);
            $upload_path = public_path()."/storage/uploads/brands/";
            $img->save($upload_path.$imageName);
        }
        else {
            $imageName = "";
        }
        $brand = new Brand;
        $brand->name = $request['name'];
        $brand->description = $request['description'];
        $brand->image = $imageName;
        $brand->save();
        return response()->json($brand);
    }

    public function show($id)
    {
        $brand = Brand::find($id);
        return response()->json($brand);
    }

    public function update(Request $request, $id)
    {
        $image_current = Brand::select('image')->where('id', $id)->first();
        if($request->image == $image_current->image) {
            $imageName = $image_current->image;
        } else {
            $strpos = strpos($request->image, ';');
            $sub = substr($request->image, 0, $strpos);
            $ex = explode("/", $sub)[1];
            $imageName = time().".".$ex;
            $img = Image::make($request->image);
            $upload_path = public_path()."/storage/uploads/brands/";
            $img->save($upload_path.$imageName);
        }
        $brand = Brand::where('id', $id)->update([
            'name' => $request['name'],
            'image' => "http://localhost:8000/storage/uploads/brands/".$imageName,
            'description' => $request['description'],
        ]);

        return response()->json($brand);
    }

    public function destroyIds(Request $request)
    {
        $selectedIds = $request->all(); // Lấy danh sách selectedIds từ request
        $brands = Brand::whereIn('id', $selectedIds)->get(); // Sử dụng whereIn để lấy các bản ghi tương ứng với selectedIds
        foreach($brands as $brand) {
            $brand->deleted_at = Carbon::now('Asia/Ho_Chi_Minh');
            $brand->save();
        }      
        return response()->json([
            'success' => true,
            'message' => "Deleted All."
        ], 200);
    }

    public function updateBrandStatus($id, Request $request) {
        $brand = Brand::find($id);
        $brand->status = !$request->status;
        $brand->save();
        
        return response()->json([
            'success' => true,
            'brand' => $brand,
        ]);
    }
}
