<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Size;
use App\Models\ProductImage;
use App\Models\Inventory;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Color;
use Intervention\Image\Facades\Image;
use App\Http\Resources\ProductResource;
use Carbon\Carbon;

class ProductsController extends Controller
{
    public function index()
    {
        $products = Product::with('category','brand', 'product_image', 'inventories.size', 'reviews.review_image')->orderBy('created_at', 'DESC')->get();
        return response()->json(ProductResource::collection($products));
    }

    public function create()
    {
        $categories = Category::select('id', 'name')->get();
        $brands = Brand::select('id', 'name', 'description')->get();        
        return response()->json([
            'categories' => $categories,
            'brands' => $brands,
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
        $product->brand_id = $request['brand_id'];
        $product->discount_percent = $request['discount_percent'];
        $product->save();
        return response()->json($product);
    }

    public function show($id)
    {
        $product = Product::find($id);
        return response()->json($product);
    }

    public function sizeAll()
    {
        $sizes = Size::get();
        return response()->json($sizes);
    }

    public function brandAll()
    {
        $brands = Brand::get();
        return response()->json($brands);
    }
    
    public function colorAll()
    {
        $colors = Color::get();
        return response()->json($colors);
    }

    public function view($id)
    {
        $product = Product::with('category','brand', 'product_image', 'inventories.size', 'reviews.images_review')->find($id);
        return response()->json(new ProductResource($product));
    }

    public function addImage(Request $request)
    {
        if($request->img) {
            $strpos = strpos($request->img, ';');
            $sub = substr($request->img, 0, $strpos);
            $ex = explode("/", $sub)[1];
            $imageName = time().".".$ex;
            $img = Image::make($request->img);
            $upload_path = public_path()."/storage/uploads/products/";
            $img->save($upload_path.$imageName);
        }
        $image = new ProductImage();
        $image->product_id = $request['id'];
        $image->image = $imageName;
        $image->save();
        return response()->json("ok");
    }

    public function deleteImage(Request $request)
    {
        $productImage = ProductImage::select('image')->where('id', $request->id)->first();
        unlink(public_path()."/storage/uploads/products/". $productImage->image);
        ProductImage::where('id', $request->id)->delete();
        return response()->json(['success'=>'true'], 200);
    }

    public function addSize(Request $request) {
        $size = Inventory::where(['product_id' => $request['id'], 'size_id' => $request['size']])->first();
        if(!$size) {
            $productsize = new Inventory; 
            $productsize->product_id = $request['id'];
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
        Inventory::where('id', $request->id)->delete();
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
            'brand_id' => $request['brand_id'],
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
        // $product->delete();
        // if($product->delete()) {
        //     if($product->image != null) {
        //         unlink(public_path()."/storage/uploads/products/". $product->image);
        //     }
        // }
        $product->deleted_at = Carbon::now('Asia/Ho_Chi_Minh');
        $product->save();
        return response()->json(['success'=>'true'], 200);
    }

}
