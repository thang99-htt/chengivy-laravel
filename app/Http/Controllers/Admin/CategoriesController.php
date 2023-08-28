<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Category;
use Intervention\Image\Facades\Image;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = Category::with(['parent'])->get();
        return response()->json($categories);
    }

    public function create()
    {     
        $categories = Category::categories();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        if($request->image) {
            $strpos = strpos($request->image, ';');
            $sub = substr($request->image, 0, $strpos);
            $ex = explode("/", $sub)[1];
            $imageName = time().".".$ex;
            $img = Image::make($request->image);
            $upload_path = public_path()."/storage/uploads/categories/";
            $img->save($upload_path.$imageName);
        }
        else {
            $imageName = "";
        }
        $category = new Category;
        $category->parent_id = $request['parent_id'];
        $category->name = $request['name'];
        $category->image = $imageName;
        $category->description = $request['description'];
        $category->url = $request['url'];
        $category->save();
        return response()->json($category);
    }

    public function show($id)
    {
        $category = Category::find($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $image_current = Category::select('image')->where('id', $id)->first();
        if($request->image == $image_current->image) {
            $imageName = $image_current->image;
        } else {
            $strpos = strpos($request->image, ';');
            $sub = substr($request->image, 0, $strpos);
            $ex = explode("/", $sub)[1];
            $imageName = time().".".$ex;
            $img = Image::make($request->image);
            $upload_path = public_path()."/storage/uploads/categories/";
            $img->save($upload_path.$imageName);
        }
        $category = Category::where('id', $id)->update([
            'parent_id' => $request['parent_id'],
            'name' => $request['name'],
            'image' => $imageName,
            'description' => $request['description'],
            'url' => $request['url']
        ]);

        return response()->json($category);
    }

    public function destroyIds(Request $request)
    {
        $selectedIds = $request->all(); // Lấy danh sách selectedIds từ request
        $categories = Category::whereIn('id', $selectedIds)->get(); // Sử dụng whereIn để lấy các bản ghi tương ứng với selectedIds
        foreach($categories as $category) {
            $category->delete(); // Xóa từng bản ghi Category
        }      
        return response()->json([
            'success' => true,
            'message' => "Deleted All."
        ], 200);
    }

    public function updateCategoryStatus($id, Request $request) {
        $category = Category::find($id);
        $category->status = !$request->status;
        $category->save();
        
        return response()->json([
            'success' => true,
            'category' => $category,
        ]);
    }
}
