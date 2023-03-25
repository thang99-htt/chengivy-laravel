<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductSize;

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
    
    public function type()
    {        
        $trendingProducts = Product::with('category', 'images')->where('type_id', 2)->limit(4)->inRandomOrder()->get();

        // Special Products
        $specialNewProduct = Product::with('category')->where('type_id', 3)->orderBy('id','desc')->limit(1)->get();
        
        $specialHighestPriceProduct = Product::with('category')->where('type_id', 3)->orderBy('price','desc')->limit(1)->get();
                    
        $specialProducts = Product::with('category')->where('type_id', 3)->limit(3)->get();

        foreach($trendingProducts as $key => $value) {
            $getDiscountPrice = Product::getDiscountPrice($trendingProducts[$key]['id']);
            if($getDiscountPrice > 0) {
                $trendingProducts[$key]['final_price'] = $getDiscountPrice;
            } else {
                $trendingProducts[$key]['final_price'] = $trendingProducts[$key]['price'];
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

        foreach($specialProducts as $key => $value) {
            $getDiscountPrice = Product::getDiscountPrice($specialProducts[$key]['id']);
            if($getDiscountPrice > 0) {
                $specialProducts[$key]['final_price'] = $getDiscountPrice;
            } else {
                $specialProducts[$key]['final_price'] = $specialProducts[$key]['price'];
            }
        }

        return response()->json([
            'trendingProducts' => $trendingProducts,
            'specialNewProduct' => $specialNewProduct,
            'specialHighestPriceProduct' => $specialHighestPriceProduct,
            'specialProducts' => $specialProducts,
        ]);
    }

    public function listing($url) {
        $categoryCount = Category::where(['url' => $url, 'status' => 1])->count();
        if($categoryCount > 0) {
            $categoryDetails = Category::categoryDetails($url);
            $products = Product::with('category')->whereIn('category_id', $categoryDetails['catIds'])->where('status', 1)->get();
            
            foreach($products as $key => $value) {
                $getDiscountPrice = Product::getDiscountPrice($products[$key]['id']);
                if($getDiscountPrice > 0) {
                    $products[$key]['final_price'] = $getDiscountPrice;
                } else {
                    $products[$key]['final_price'] = $products[$key]['price'];
                }
            }

            return response()->json($products);
        } else {
            $message = "Category URL incorect!";
            return response()->json([
                'status' => false,
                'message' => $message
            ], 422);
        }

    }

    public function listingAll() {
        $categoryCount = Category::where(['status' => 1])->count();
        if($categoryCount > 0) {
            $products = Product::with('category')->where('status', 1)->get();
            
            foreach($products as $key => $value) {
                $getDiscountPrice = Product::getDiscountPrice($products[$key]['id']);
                if($getDiscountPrice > 0) {
                    $products[$key]['final_price'] = $getDiscountPrice;
                } else {
                    $products[$key]['final_price'] = $products[$key]['price'];
                }
            }

            return response()->json($products);
        } else {
            $message = "Category URL incorect!";
            return response()->json([
                'status' => false,
                'message' => $message
            ], 422);
        }

    }

    public function detail(Request $request) {
        $productDetails = Product::with(['category' => function($query) {
            $query->select('id', 'name');
        }, 'images', 'sizes' ])->find($request->id);

        $getDiscountPrice = Product::getDiscountPrice($productDetails['id']);
        if($getDiscountPrice > 0) {
            $productDetails['final_price'] = $getDiscountPrice;
        } else {
            $productDetails['final_price'] = $productDetails['price'];
        }

        if ($productDetails) {
            return response()->json($productDetails);
        } else {
            return response()->json(array(
                'code'      =>  404,
                'message'   =>  "Error"
            ), 404);
        }
    }

    
    public function getStock($product_id, $size_id)
    {
        $getProductStock = ProductSize::getProductQuantity($product_id, $size_id);
        return response()->json($getProductStock, 200);
    }
    
}
