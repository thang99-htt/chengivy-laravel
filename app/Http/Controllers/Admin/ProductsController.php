<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Size;
use App\Models\ProductImage;
use App\Models\Inventory;
use App\Models\Category;
use App\Models\Color;
use Intervention\Image\Facades\Image;
use App\Http\Resources\ProductResource;
use Carbon\Carbon;

use Illuminate\Support\Facades\Validator;
use App\Jobs\UploadToGoogleDrive;
use App\Jobs\DeleteFromGoogleDrive;

class ProductsController extends Controller
{
    public function index()
    {
        $product = Product::with('category','brand', 'product_image', 'inventories.size', 
            'reviews.review_image')->orderBy('created_at', 'DESC')->get();
        return response()->json(ProductResource::collection($product));
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'images' => 'required',
            'price' => 'required',
            'discount_percent' => 'max:2',
            'description' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'description.required' => 'Vui lòng nhập mô tả.',
            'price.required' => 'Vui lòng nhập giá.',
            'discount_percent.max' => 'Phần trăm giảm giá có nhiều nhất 2 chữ số.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'brand_id.required' => 'Vui lòng chọn thương hiệu.',
            'images.required' => 'Vui lòng thêm các hình ảnh.',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);

        } else {
            $product = new Product;
            $product->category_id = $request['category_id'];
            $product->brand_id = $request['brand_id'];
            $product->name = $request['name'];
            $product->description = $request['description'];
            $product->price = $request['price'];
    
            if ($request['discount_percent']) {
                $product->discount_percent = $request['discount_percent'];
                $product->price_final = $request['price'] - ($request['price'] * $request['discount_percent']) / 100;
            } else {
                $product->discount_percent = 0;
                $product->price_final = $request['price'];
            }
    
            $product->save();
            $productId = $product->id;
            $productCat = $product->category_id;
            $productBra = $product->brand_id;
    
            $imagesData = $request['images'];
    
            if ($imagesData) {
                foreach ($imagesData as $colorItem) {
                    $colorId = $colorItem['color_id'];
    
                    foreach ($colorItem['items'] as $imageItem) {
                        $base64Image = $imageItem['image'];
                        $colorId = $colorItem['color_id'];
    
                        // Enqueue job to upload image
                        UploadToGoogleDrive::dispatch(
                            $productId,
                            $productCat,
                            $productBra,
                            $colorId,
                            $base64Image
                        );
                    }
                }
            }
            return response()->json($product);
        }
    }

    public function show($id)
    {
        $product = Product::find($id);
        return response()->json($product);
    }

    public function update($id, Request $request)
    {
        $product = Product::find($id);
        $product->category_id = $request['category_id'];
        $product->brand_id = $request['brand_id'];
        $product->name = $request['name'];
        $product->description = $request['description'];
        $product->price = $request['price'];

        if ($request['discount_percent']) {
            $product->discount_percent = $request['discount_percent'];
            $product->price_final = $request['price'] - ($request['price'] * $request['discount_percent']) / 100;
        } else {
            $product->discount_percent = 0;
            $product->price_final = $request['price'];
        }

        $product->save();
        $productId = $product->id;
        $productCat = $product->category_id;
        $productBra = $product->brand_id;

        $existingImages = [];
        $existingColorIds = [];
        $newImages = [];  
        $removedImages = [];

        $imagesData = $request['images'];
        if ($imagesData) {
            // Assuming you already have $productId defined
            $existingImages = ProductImage::where(['product_id' => $productId])->get();
            
            // Extract existing color_ids from $existingImages
            $existingColorIds = $existingImages->pluck('color_id')->toArray();

            // Filter imagesData to include only images with color_ids not in existingColorIds
            $newImages = array_filter($imagesData, function ($colorItem) use ($existingColorIds) {
                return !in_array($colorItem['color_id'], $existingColorIds);
            });

            // Find images in existingImages that are not in newImages, excluding common ones
            $removedImages = $existingImages->filter(function ($existingImage) use ($imagesData) {
                return !in_array($existingImage->color_id, array_column($imagesData, 'color_id'));
            });

            if ($newImages) { 
                foreach ($newImages as $colorItem) {
                    $colorId = $colorItem['color_id'];
                    foreach ($colorItem['items'] as $imageItem) {
                        $base64Image = $imageItem['image'];    
                        // Enqueue job to upload image
                        UploadToGoogleDrive::dispatch(
                            $productId,
                            $productCat,
                            $productBra,
                            $colorId,
                            $base64Image
                        );
                    }
                }
            }

            if ($removedImages) {
                foreach ($removedImages as $image) {
                    $imageLink = $image->image;
                    DeleteFromGoogleDrive::dispatch($image->id, $imageLink);
                }
            }
        }

        return response()->json([
            'success' => 'success',
            'message' => "Cập nhật thành công",
        ]);
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

    public function sizeAll()
    {
        $sizes = Size::get();
        return response()->json($sizes);
    }
    
    public function colorAll()
    {
        $colors = Color::get();
        return response()->json($colors);
    }

    public function type()
    {        
        $newProducts = Product::with('category', 'product_image', 'brand')->orderBy('created_at','DESC')->limit(4)->get();

        // Special Products
        $specialNewProduct = Product::with('category', 'brand')->where('brand_id', 3)->orderBy('created_at','asc')->limit(1)->get();
        $specialHighestPriceProduct = Product::with('category')->where('brand_id', 3)->orderBy('price','desc')->limit(1)->get();
        $bestSellerProducts = Product::with('category', 'brand')->orderBy('created_at','desc')->limit(8)->inRandomOrder()->get();

        return response()->json([
            'newProducts' => ProductResource::collection($newProducts),
            'specialNewProduct' => ProductResource::collection($specialNewProduct),
            'specialHighestPriceProduct' => ProductResource::collection($specialHighestPriceProduct),
            'bestSellerProducts' => ProductResource::collection($bestSellerProducts),
        ]);
    }

    public function listing($url) {
        $categoryCount = Category::where(['url' => $url, 'status' => 1])->count();
        if($categoryCount > 0) {
            $categoryDetails = Category::categoryDetails($url);
            $products = Product::with('category','brand', 'product_image', 'inventories.size', 'reviews.review_image')->whereIn('category_id', $categoryDetails['catIds'])->where('status', 1)->orderBy('created_at', 'DESC')->get();
                foreach($products as $key => $value) {
                    $getDiscountPrice = Product::getDiscountPrice($products[$key]['id']);
                    if($getDiscountPrice > 0) {
                        $products[$key]['final_price'] = $getDiscountPrice;
                    } else {
                        $products[$key]['final_price'] = $products[$key]['price'];
                    }
                }
                return response()->json(ProductResource::collection($products));
        } else {
            $message = "Category URL incorect!";
            return response()->json([
                'status' => false,
                'message' => $message
            ], 422);
        }

    }

    public function listingAll() {
        $products = Product::with('category','brand', 'product_image', 'inventories.size', 'reviews.review_image')->where('status', 1)->orderBy('created_at', 'DESC')->get();
        foreach($products as $key => $value) {
            $getDiscountPrice = Product::getDiscountPrice($products[$key]['id']);
            if($getDiscountPrice > 0) {
                $products[$key]['final_price'] = $getDiscountPrice;
            } else {
                $products[$key]['final_price'] = $products[$key]['price'];
            }
        }
        return response()->json(ProductResource::collection($products));
    }

    public function detail($id) {
        $maxMonthYear = Inventory::where('product_id', $id)->orderBy('month_year', 'desc')->first();

        if ($maxMonthYear) {
            $maxMonthYear = $maxMonthYear->month_year;
    
            $product = Product::with(['category','brand', 'product_image', 
                'inventories' => function ($query) use ($maxMonthYear) {
                    $query->where('month_year', $maxMonthYear);
                }, 'reviews.review_image'])
                ->where('status', 1)->find($id);
        } else {
            $product = Product::with(['category','brand', 'product_image', 'reviews.review_image'])
            ->where('status', 1)->find($id);
        }
        return response()->json(new ProductResource($product));
    }

    
    public function getInventory($product_id, $size_id)
    {
        $getProductStock = Inventory::where(['$product_id' => $product_id, 'size_id' => $size_id]);
        return response()->json($getProductStock, 200);
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


}
