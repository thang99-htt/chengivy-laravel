<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Category;
use Intervention\Image\Facades\Image;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // try {
        //     $request->validate([
        //         'parent_id' => 'required',
        //         'name' => 'required',
        //         'description' => 'required',
        //         'url' => 'required',
        //     ]);
        // } catch (ValidationException $e) {
        //     return response()->json(['error' => $e->errors()], 400);
        // }

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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::find($id);
        return response()->json($category);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if($category->image != null) {
            unlink(public_path()."/storage/uploads/categories/". $category->image);
        }
        $category->delete();
        return response()->json(['success'=>'true'], 200);
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
