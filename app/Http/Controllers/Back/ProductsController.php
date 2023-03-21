<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Size;
use App\Models\Images;
use App\Models\ProductSize;
use App\Models\Category;
use App\Models\Type;
use Intervention\Image\Facades\Image;

class ProductsController extends Controller
{
    public function index()
    {
        $products = Product::with(['category' => function($query) {
            $query->select('id', 'name');
        }, 'type' => function($query) {
            $query->select('id', 'name');
        }])->orderBy('created_at', 'DESC')->get();
        return response()->json($products);
    }

    public function create()
    {
        $categories = Category::select('id', 'name')->get();
        $types = Type::select('id', 'name')->get();        
        return response()->json([
            'categories' => $categories,
            'types' => $types,
        ]);
    }

    public function store(Request $request)
    {
        if($request->image) {
            $strpos = strpos($request->image, ';');
            $sub = substr($request->image, 0, $strpos);
            $ex = explode("/", $sub)[1];
            $imageName = time().".".$ex;
            $img = Image::make($request->image);
            $upload_path = public_path()."/storage/uploads/products/";
            $img->save($upload_path.$imageName);
        }
        $product = new Product;
        $product->category_id = $request['category_id'];
        $product->name = $request['name'];
        $product->description = $request['description'];
        $product->price = $request['price'];
        $product->image = $imageName;
        $product->type_id = $request['type_id'];
        $product->discount_percent = $request['discount_percent'];
        $product->save();
        return response()->json($product);
    }

    public function show($id)
    {
        $product = Product::find($id);
        return response()->json($product);
    }

    public function view(Request $request)
    {
        $product = Product::with(['category' => function($query) {
            $query->select('id', 'name');
        }, 'type' => function($query) {
            $query->select('id', 'name');
        }, 'images', 'sizes' ])->where('id', $request->id)->first();
        $sizes = Size::select('id', 'name')->get();

        return response()->json([
            'product' => $product,
            'sizes' => $sizes,
        ]);
    }

    public function addImage(Request $request)
    {
        if($request->image) {
            $strpos = strpos($request->image, ';');
            $sub = substr($request->image, 0, $strpos);
            $ex = explode("/", $sub)[1];
            $imageName = time().".".$ex;
            $img = Image::make($request->image);
            $upload_path = public_path()."/storage/uploads/products/";
            $img->save($upload_path.$imageName);
        }
        $image = new Images();
        $image->product_id = $request['product.id'];
        $image->image = $imageName;
        $image->save();
        return response()->json("ok");
    }

    public function deleteImage(Request $request)
    {
        $productImage = Images::select('image')->where('id', $request->id)->first();
        unlink(public_path()."/storage/uploads/products/". $productImage->image);
        Images::where('id', $request->id)->delete();
        return response()->json(['success'=>'true'], 200);
    }

    public function addSize(Request $request) {
        $size = ProductSize::where(['product_id' => $request['product.id'], 'size_id' => $request['size']])->first();
        if(!$size) {
            $productsize = new ProductSize; 
            $productsize->product_id = $request['product.id'];
            $productsize->size_id =  $request['size'];
            $productsize->quantity =  $request['quantity'];
            $productsize->stock =  $request['stock'];
            $productsize->save();
            return response()->json(true);
        }
        return response()->json(false);

    }

    public function deleteSize(Request $request)
    {
        ProductSize::where('id', $request->id)->delete();
        return response()->json(['success'=>'true'], 200);
    }

    public function update($id, Request $request)
    {
        $image_current = Product::select('image')->where('id', $id)->first();
        if($request->image == $image_current->image) {
            $imageName = $image_current->image;
        } else {
            $strpos = strpos($request->image, ';');
            $sub = substr($request->image, 0, $strpos);
            $ex = explode("/", $sub)[1];
            $imageName = time().".".$ex;
            $img = Image::make($request->image);
            $upload_path = public_path()."/storage/uploads/products/";
            $img->save($upload_path.$imageName);
        }
        $product = Product::where('id', $id)->update([
            'category_id' => $request['category_id'],
            'name' => $request['name'],
            'description' => $request['description'],
            'purchase_price' => $request['purchase_price'],
            'price' => $request['price'],
            'image' => $imageName,
            'type_id' => $request['type_id'],
            'discount_percent' => $request['discount_percent']
        ]);

        return response()->json($product);
    }

    public function updateProductStatus($id, Request $request) {
        $product = Product::find($id);
        $product->status = !$request->status;
        $product->save();
        
        return response()->json([
            'success' => true,
            'product' => $product,
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if($product->image != null) {
            unlink(public_path()."/storage/uploads/products/". $product->image);
        }
        $product->delete();
        return response()->json(['success'=>'true'], 200);
    }

}
