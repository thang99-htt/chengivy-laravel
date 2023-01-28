<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Image;
use Auth;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::with(['parent'])->get()->toArray();

        // dd($categories);
        return view('admin.categories.categories')->with(compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::with(['parent'])->get()->toArray();

        if (Gate::forUser(Auth::guard('admin')->user())->allows('isEmployee')) {
            abort(403);
        }

        return view('admin.categories.add_category')->with(compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $categories = Category::select('name')->get()->toArray();
        $category = new Category;
        if($request->isMethod('post')) {
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            $rules = [
                'category_parent' => 'required',
                'category_name' => 'required',
                'category_url' => 'required',
            ];

            $customMessage = [
                'category_parent.required' => 'Category Parent ID is required!',
                'category_name.required' => 'Category Name is required!',
                'category_url.required' => 'Category URL is required!',
            ];

            $this->validate($request, $rules, $customMessage);
            
            // Upload category image
            if($request->hasFile('category_image')) {
                $image_tmp = $request->file('category_image');
                if($image_tmp->isValid()) {
                    // Get image extension
                    $extension = $image_tmp->getClientOriginalExtension();
                    // Generate new image name
                    $imageName = rand(111,99999).'.'.$extension;
                    $imagePath = 'storage/images/categories/'.$imageName;
                    // Upload image
                    Image::make($image_tmp)->save($imagePath);
                }
            } else {
                $imageName = "" ;
            }

            if($data['category_name'] != $categories) {
                $category->parent_id = $data['category_parent'];
                $category->name = $data['category_name'];
                $category->image = $imageName;
                $category->description = $data['category_des'];
                $category->url = $data['category_url'];
                $category->save();
            } else {
                return redirect()->back()->with('error_message','Category already existed!');
            }
        }
        
        return redirect()->back()->with('success_message','Category new has been create successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $categories = Category::get()->toArray();
        $category = Category::with(['parent'])->find($request->id)->toArray();
        // dd ($category);

        if (Gate::forUser(Auth::guard('admin')->user())->allows('isEmployee')) {
            abort(403);
        }

        return view('admin.categories.update_category', compact('category', 'categories'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $category = Category::find($request->id);
        // $categories = Category::where(['parent_id' => $request->cat_parent, 'name' => $request->cat_name])->first();

        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            $rules = [
                'cat_name' => 'required',
                'cat_url' => 'required',
            ];

            $customMessage = [
                'cat_name.required' => 'Category Name is required!',
                'cat_url.required' => 'Category URL is required!',
            ];

            $this->validate($request, $rules, $customMessage);

            // Upload category image
            if($request->hasFile('cat_image')) {
                $image_tmp = $request->file('cat_image');

                if($image_tmp->isValid()) {
                    // Get image extension
                    $extension = $image_tmp->getClientOriginalExtension();
                    // Generate new image name
                    $imageName = rand(111,99999).'.'.$extension;
                    $imagePath = 'storage/images/categories/'.$imageName;
                    // Upload new image
                    Image::make($image_tmp)->save($imagePath);

                    // Delete old image in folder
                    unlink('storage/images/categories/'.$data['current_category_image']);
                }
            } else if (!empty($data['current_category_image'])) {
                $imageName = $data['current_category_image'];
            } else {
                $imageName = "" ;
            }

            // Update category
            // if($category->parent_id != $data['cat_parent'] || $category->name != $data['cat_name'] ) {
            Category::where('id', $request->id)->update([
                'parent_id' => $data['cat_parent'],
                'name' => $data['cat_name'],
                'image' => $imageName,
                'description' => $data['cat_des'],
                'url' => $data['cat_url']
            ]);

            return redirect('/admin/categories')->with('success_message', 'Category new has been updated successfully');
        }

        return view('admin.categories.update_category', compact('category'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (Gate::forUser(Auth::guard('admin')->user())->allows('isEmployee')) {
            abort(403);
        }

        $category = Category::find($request->id);
        $childs = $category->childs;
        
        $product_category = $category->products;
        // dd(count($childs)); die;
        if(count($product_category) != 0) {
            return redirect()->back()->with('error_message','Must delete the product belongs to '.$category->name.' first!');
        } 
        
        foreach($childs as $c){
            $child = Category::find($c->id);
            $product_child = $child->products;
            if(count($product_child) != 0) {
                return redirect()->back()->with('error_message','Must delete the product belongs to '.$category->name.' first!');
            } 
            // dd($child->image); die;
            if($child->image != null) {
                unlink("storage/images/categories/". $child->image);
            }
            $child->delete();
        }

        if($category->image != null) {
            unlink("storage/images/categories/". $category->image);
        }
        $category->delete();
        
        $message = "Category has been deleted successfully!";
        return redirect()->back()->with('success_message', $message);
    }

    public function updateCategoryStatus(Request $request) {
        if (Gate::forUser(Auth::guard('admin')->user())->allows('isEmployee')) {
            abort(403);
        }

        if($request->ajax()) {
            $data = $request->all();

            if($data['status'] == 'Active') {
                $status = 0;
            } else {
                $status = 1;
            }

            Category::where('id', $data['category_id'])->update(['status' => $status]);
            return response()->json(['status'=>$status, 'category_id'=> $data['category_id']]);
        }
    }
}
