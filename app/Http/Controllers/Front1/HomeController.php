<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Type;
use App\Models\Product;
use App\Models\Images;
use Session;
use Image;

class HomeController extends Controller
{
    public function index()
    {
        // Trending Products
        $trendingFirstProduct = Product::where('type_id', 2)->first()->get();
        
        $trendingProducts = Product::with('category', 'images')->where('type_id', 2)->limit(4)->inRandomOrder()->get();

        // Special Products
        $specialNewProduct = Product::with('category')->where('type_id', 3)->orderBy('id','desc')->limit(1)->get();
        
        $specialHighestPriceProduct = Product::with('category')->where('type_id', 3)->orderBy('price','desc')->limit(1)->get();
                    
        $specialProducts = Product::with('category')->where('type_id', 3)->limit(3)->get();

        return response()->json([
            'trendingFirstProduct' => $trendingFirstProduct,
            'trendingProducts' => $trendingProducts,
            'specialNewProduct' => $specialNewProduct,
            'specialHighestPriceProduct' => $specialHighestPriceProduct,
            'specialProducts' => $specialProducts,
        ]);
    }

}
