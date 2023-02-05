<?php

namespace App\Http\Controllers\Front;

use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManager;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Size;
use App\Models\Images;
use App\Models\ProductSize;
use App\Models\Category;
use App\Models\Type;
use Image;
use Auth;

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

        return response()->json([
            'trendingProducts' => $trendingProducts,
            'specialNewProduct' => $specialNewProduct,
            'specialHighestPriceProduct' => $specialHighestPriceProduct,
            'specialProducts' => $specialProducts,
        ]);
    }

    public function listing(Request $request) {
        $url = $request['url'];
        $categoryDetails = Category::categoryDetails($url);
        $products = Product::with('category')->whereIn('category_id', $categoryDetails['catIds'])->where('status', 1)->get();
        return response()->json($products);

    }

    public function detail(Request $request) {
        $productDetails = Product::with(['category' => function($query) {
            $query->select('id', 'name');
        }, 'images', 'sizes' ])->find($request->id);

        if ($productDetails) {
            return response()->json($productDetails);
        } else {
            return response()->json(array(
                'code'      =>  404,
                'message'   =>  "Error"
            ), 404);
        }
    }
    
}
