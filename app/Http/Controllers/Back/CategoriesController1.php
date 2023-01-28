<?php

namespace App\Http\Controllers\Back;

use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManager;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Image;
use Auth;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = Category::with(['parent'])->orderBy('created_at', 'DESC')->get();
        return response()->json($categories);
    }

    public function create()
    {     
        $categories = Category::categories();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        // $rules = [
        //     'image' => 'mimes:jpg,png,jpeg,gif,svg',
        // ];

        // $customMessage = [
        //     'image.mimetypes' => 'Hình ảnh phải là một tệp có định dạng: png, jpg, jpeg.',
        // ];

        // $this->validate($request, $rules);

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

    public function update($id, Request $request)
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

    public function updateCategoryStatus($id, Request $request) {
        $category = Category::find($id);
        $category->status = !$request->status;
        $category->save();
        
        return response()->json([
            'success' => true,
            'category' => $category,
        ]);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if($category->image != null) {
            unlink(public_path()."/storage/uploads/categories/". $category->image);
        }
        $category->delete();
        return response()->json(['success'=>'true'], 200);
    }

}
