<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Type;
use App\Models\Product;
use App\Models\Images;
use App\Models\Size;
use App\Models\ProductSize;
use Session;
use Image;
use Auth;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categoryDetails = Category::all();

        $products = Product::with(['category' => function($query) {
                $query->select('id', 'name');
            }, 'type' => function($query) {
                $query->select('id', 'name');
            }]);

            // Check for sort
            if(isset($_GET['sort']) && !empty($_GET['sort'])) {
                if($_GET['sort']=='newest') {
                    $products->orderBy('products.id','Desc');
                } else if($_GET['sort']=='lastest') {
                    $products->orderBy('products.id','Asc');
                } else if($_GET['sort']=='name_a_z') {
                    $products->orderBy('products.name','Asc');
                } else if($_GET['sort']=='name_z_a') {
                    $products->orderBy('products.name','Desc');
                }
            }
            $products = $products->paginate(10);
        return view('admin.products.products')->with(compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::get()->toArray();
        $types = Type::get()->toArray();
        // dd($categories);

        if (Gate::forUser(Auth::guard('admin')->user())->allows('isEmployee')) {
            abort(403);
        } 
        
        return view('admin.products.add_product')->with(compact('categories', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = new Product;
        if($request->isMethod('post')) {
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            $rules = [
                'product_category' => 'required',
                'product_name' => 'required',
                'product_des' => 'required',
                'product_purchase_price' => 'required',
                'product_price' => 'required',
                'product_image' => 'required',
                'product_type' => 'required',

            ];

            $customMessage = [
                'product_parent.required' => 'Product Parent ID is required!',
                'product_name.required' => 'Product Name is required!',
                'product_des.required' => 'Product Description is required!',
                'product_purchase_price.required' => 'Product Purchase Price is required!',
                'product_price.required' => 'Product Price is required!',
                'product_image.required' => 'Product Image is required!',
                'product_type.required' => 'Product Type is required!',
            ];

            $this->validate($request, $rules, $customMessage);

            // Upload product image
            if($request->hasFile('product_image')) {
                $image_tmp = $request->file('product_image');
                if($image_tmp->isValid()) {
                    // Get image extension
                    $extension = $image_tmp->getClientOriginalExtension();
                    // Generate new image name
                    $imageName = rand(111,99999).'.'.$extension;
                    $imagePath = 'storage/images/products/'.$imageName;
                    // Upload image
                    Image::make($image_tmp)->save($imagePath);
                }
            } else {
                $imageName = "" ;
            }

            $product->category_id = $data['product_category'];
            $product->name = $data['product_name'];
            $product->description = $data['product_des'];
            $product->purchase_price = $data['product_purchase_price'];
            $product->price = $data['product_price'];
            $product->image = $imageName;
            $product->type_id = $data['product_type'];
            if($data['product_dis'] != null) {
                $product->discount_percent = $data['product_dis'];
            } else {
                $product->discount_percent = 0;
            }
            $product->save();
        }
        
        return redirect()->back()->with('success_message','Product new has been create successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $categories = Category::get()->toArray();
        $types = Type::get()->toArray();
        $product = Product::with(['category' => function($query) {
                $query->select('id', 'name');
            }, 'type' => function($query) {
                $query->select('id', 'name');
            }])->find($request->id)->toArray();
        // dd ($product);

        if (Gate::forUser(Auth::guard('admin')->user())->allows('isEmployee')) {
            abort(403);
        }

        return view('admin.products.update_product', compact('product', 'categories', 'types'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $product = Product::find($request->id);
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            $rules = [
                'pro_category' => 'required',
                'pro_name' => 'required',
                'pro_des' => 'required',
                'pro_purchase_price' => 'required|numeric',
                'pro_price' => 'required|numeric',
                'pro_type' => 'required',
                'pro_dis' => 'required',

            ];

            $customMessage = [
                'pro_category.required' => 'Product Parent ID is required!',
                'pro_name.required' => 'Product Name is required!',
                'pro_des.required' => 'Product Description is required!',
                'pro_purchase_price.required' => 'Product Purchase Price is required!',
                'pro_purchase_price.numeric' => 'Product Purchase Price must be number!',
                'pro_price.required' => 'Product Price is required!',
                'pro_price.numeric' => 'Product Price must be number!',
                'pro_type.required' => 'Product Type is required!',
                'pro_dis.required' => 'Product Discount is required!',
            ];

            $this->validate($request, $rules, $customMessage);

            // Upload product image
            if($request->hasFile('pro_image')) {
                $image_tmp = $request->file('pro_image');

                if($image_tmp->isValid()) {
                    // Get image extension
                    $extension = $image_tmp->getClientOriginalExtension();
                    // Generate new image name
                    $imageName = rand(111,99999).'.'.$extension;
                    $imagePath = 'storage/images/products/'.$imageName;
                    // Upload new image
                    Image::make($image_tmp)->save($imagePath);

                    // Delete old image in folder
                    unlink('storage/images/products/'.$data['current_product_image']);
                }
            } else if (!empty($data['current_product_image'])) {
                $imageName = $data['current_product_image'];
            } else {
                $imageName = "" ;
            }

            Product::where('id', $request->id)->update([
                'category_id'=>$data['pro_category'],
                'name'=>$data['pro_name'], 
                'description'=>$data['pro_des'], 
                'purchase_price'=>$data['pro_purchase_price'],
                'price'=>$data['pro_price'],
                'image'=>$imageName, 
                'type_id'=>$data['pro_type'], 
                'discount_percent'=>$data['pro_dis']
            ]);

            return redirect('admin/products')->with('success_message','Product update successfully!');
        }

        return view('admin.products.update_product', compact('product'));
    }

    public function addImages(Request $request) {
        Session::put('page', 'products');
        $product = Product::select('id', 'name', 'price', 'image')->find($request->id);

        if($request->isMethod('post')){
            $data = $request->all();

            // Upload product image
            if($request->hasFile('images')) {
                $images = $request->file('images');
                
                foreach($images as $image) {
                    $image_tmp = Image::make($image);

                    // $image_name = $image->getClientOriginalName();
                    $extension = $image->getClientOriginalExtension();

                    $imageName = rand(111,99999).'.'.$extension;

                    $imagePath = 'storage/images/products/'.$imageName;
                    Image::make($image_tmp)->save($imagePath);

                    $image = new Images();
                    $image->image = $imageName;
                    $image->product_id = $request->id;
                    $image->save();

                }
            }
    
            return redirect()->back()->with('success_message','Product Images update successfully!');
        }    
            
        if (Gate::forUser(Auth::guard('admin')->user())->allows('isEmployee')) {
            abort(403);
        }

        return view('admin.images.add_images')->with(compact('product'));

    }

    public function deleteImage(Request $request)
    {
        // Get product image
        $productImage = Images::select('image')->where('id', $request->id)->first();

        // Get product image path
        $imagePath = 'storage/images/products/';

        // Delete product image in folder
        if(file_exists($imagePath.$productImage->image)) {
            unlink($imagePath.$productImage->image);
        }

        // Delete product image form products table
        Images::where('id', $request->id)->delete();

        return redirect()->back()->with('success_message','Product Image has been deleted successfully!');
    }

    public function addSizes(Request $request) {
        Session::put('page', 'products');
        $product = Product::select('id', 'name', 'price', 'image')->find($request->id);
        $sizes = Size::select('id', 'name')->get()->toArray();
        
        $productsize = new ProductSize;
        
        // Check Name Size
        $idSize = $request->add_product_size;
        $size = ProductSize::where(['product_id' => $request->id, 'size_id' => $idSize])->first();
        
        if($request->isMethod('post')){
            $data = $request->all();

            $rules = [
                'add_product_size' => 'required',
                'add_quantity_size' => 'required|numeric',

            ];
            
            $customMessage = [
                'add_product_size.required' => 'Product Size is required!',
                'add_quantity_size.required' => 'Product Quantity is required!',
                'add_quantity_size.required' => 'Product Quantity must be number!',
            ];
            
            $this->validate($request, $rules, $customMessage);

            if(!$size) {
                $productsize->product_id = $request->id;
                $productsize->size_id = $data['add_product_size'];
                $productsize->quantity = $data['add_quantity_size'];
                $productsize->save();

                return redirect()->back()->with('success_message','Product Size add successfully!');
            } else {
                return redirect()->back()->with('error_message','Product Size already existed!');
            }

        }

        if (Gate::forUser(Auth::guard('admin')->user())->allows('isEmployee')) {
            abort(403);
        }

        return view('admin.sizes.add_sizes')->with(compact('product', 'sizes'));

    }

    public function deleteSize(Request $request)
    {
        // Delete product image form products table
        ProductSize::where('id', $request->id)->delete();
        // dd($request->id);
        return redirect()->back()->with('success_message','Product Size has been deleted successfully!');
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (Gate::forUser(Auth::guard('admin')->user())->allows('isEmployee')) {
            abort(403);
        }

        $product = Product::find($request->id);

        $images = $product->images;
        foreach($images as $i){
            unlink("storage/images/products/". $i->image);
            $i->delete();
        }
        
        $sizes = $product->product_size;
        foreach($sizes as $s){
            $s->delete();
        }
        
        unlink("storage/images/products/". $product->image);
        $product->delete();
        $message = "Product has been deleted successfully!";
        return redirect()->back()->with('success_message', $message);
    }

    public function updateProductStatus(Request $request) {
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

            Product::where('id', $data['product_id'])->update(['status' => $status]);
            return response()->json(['status'=>$status, 'product_id'=> $data['product_id']]);
        }
    }
}
