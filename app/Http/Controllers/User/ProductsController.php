<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use App\Http\Resources\ProductResource;

class ProductsController extends Controller
{
    public function index()
    {
        $product = Product::with('category','brand', 'product_image', 'inventories.size', 'reviews.images_review')->orderBy('created_at', 'DESC')->get();
        return response()->json(ProductResource::collection($product));
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
            $products = Product::with('category','brand', 'product_image', 'inventories.size', 'reviews.images_review')->whereIn('category_id', $categoryDetails['catIds'])->where('status', 1)->orderBy('created_at', 'DESC')->get();
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
        $products = Product::with('category','brand', 'product_image', 'inventories.size', 'reviews.images_review')->where('status', 1)->orderBy('created_at', 'DESC')->get();
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
        $product = Product::with(['category','brand', 'product_image', 
            'inventories' => function ($query) {
                $query->where('month_year', function ($subQuery) {
                    $subQuery->selectRaw('max(month_year)')
                             ->from('inventories');
                });
            }, 'reviews.images_review'])
            ->where('status', 1)->find($id);

        return response()->json(new ProductResource($product));
    }

    
    public function getInventory($product_id, $size_id)
    {
        $getProductStock = Inventory::where(['$product_id' => $product_id, 'size_id' => $size_id]);
        return response()->json($getProductStock, 200);
    }
    
}
