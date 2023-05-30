<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductSize;
use App\Http\Resources\ProductResource;

class ProductsController extends Controller
{
    public function index()
    {
        $product = Product::with('category','type', 'images', 'product_size.size', 'reviews.images_review')->orderBy('created_at', 'DESC')->get();
        return response()->json(ProductResource::collection($product));
    }
    
    public function type()
    {        
        $designProducts = Product::with('category', 'images')->where('type_id', 4)->orderBy('created_at','desc')->limit(6)->get();

        // Special Products
        $specialNewProduct = Product::with('category')->where('type_id', 3)->orderBy('created_at','asc')->limit(1)->get();
        
        $specialHighestPriceProduct = Product::with('category')->where('type_id', 3)->orderBy('price','desc')->limit(1)->get();
                    
        $limitProducts = Product::with('category')->where('type_id', 3)->orderBy('created_at','desc')->limit(3)->inRandomOrder()->get();

        foreach($designProducts as $key => $value) {
            $getDiscountPrice = Product::getDiscountPrice($designProducts[$key]['id']);
            if($getDiscountPrice > 0) {
                $designProducts[$key]['final_price'] = $getDiscountPrice;
            } else {
                $designProducts[$key]['final_price'] = $designProducts[$key]['price'];
            }
        }

        foreach($specialNewProduct as $key => $value) {
            $getDiscountPrice = Product::getDiscountPrice($specialNewProduct[$key]['id']);
            if($getDiscountPrice > 0) {
                $specialNewProduct[$key]['final_price'] = $getDiscountPrice;
            } else {
                $specialNewProduct[$key]['final_price'] = $specialNewProduct[$key]['price'];
            }
        }

        foreach($specialHighestPriceProduct as $key => $value) {
            $getDiscountPrice = Product::getDiscountPrice($specialHighestPriceProduct[$key]['id']);
            if($getDiscountPrice > 0) {
                $specialHighestPriceProduct[$key]['final_price'] = $getDiscountPrice;
            } else {
                $specialHighestPriceProduct[$key]['final_price'] = $specialHighestPriceProduct[$key]['price'];
            }
        }

        foreach($limitProducts as $key => $value) {
            $getDiscountPrice = Product::getDiscountPrice($limitProducts[$key]['id']);
            if($getDiscountPrice > 0) {
                $limitProducts[$key]['final_price'] = $getDiscountPrice;
            } else {
                $limitProducts[$key]['final_price'] = $limitProducts[$key]['price'];
            }
        }

        return response()->json([
            'designProducts' => $designProducts,
            'specialNewProduct' => $specialNewProduct,
            'specialHighestPriceProduct' => $specialHighestPriceProduct,
            'limitProducts' => $limitProducts,
        ]);
    }

    public function listing($url) {
        $categoryCount = Category::where(['url' => $url, 'status' => 1])->count();
        if($categoryCount > 0) {
            $categoryDetails = Category::categoryDetails($url);
            $products = Product::with('category','type', 'images', 'product_size.size', 'reviews.images_review')->whereIn('category_id', $categoryDetails['catIds'])->where('status', 1)->orderBy('created_at', 'DESC')->get();
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
        $products = Product::with('category','type', 'images', 'product_size.size', 'reviews.images_review')->where('status', 1)->orderBy('created_at', 'DESC')->get();
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
        $product = Product::with('category','type', 'images', 'product_size.size', 'reviews.images_review')->where('status', 1)->orderBy('created_at', 'DESC')->find($id);
        $getDiscountPrice = Product::getDiscountPrice($id);
        if($getDiscountPrice > 0) {
            $product['final_price'] = $getDiscountPrice;
        } else {
            $product['final_price'] = $product['price'];
        }
        return response()->json(new ProductResource($product));
    }

    
    public function getStock($product_id, $size_id)
    {
        $getProductStock = ProductSize::getProductQuantity($product_id, $size_id);
        return response()->json($getProductStock, 200);
    }
    
}
