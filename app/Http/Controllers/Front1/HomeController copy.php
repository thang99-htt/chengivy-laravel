<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Type;
use App\Models\Product;
use App\Models\Images;
use Session;
use Image;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        // Banners
        $sliderBanners = Banner::where('type', 'Slider')->get()->toArray();
        $fixBanners = Banner::where('type', 'Fix')->get()->toArray();
        $advertiseBanners = Banner::where('type', 'Advertise')->get()->toArray();

        // Trending Products
        $trendingFirstProduct = Product::where('type_id', 2)->first()->toArray();
        
        $trendingProducts = Product::with('category', 'images')->where('type_id', 2)->limit(4)->inRandomOrder()->get()->toArray();

        // Special Products
        $specialNewProduct = Product::with('category')->where('type_id', 3)->orderBy('id','desc')->limit(1)->get()->toArray();
        
        $specialHighestPriceProduct = Product::with('category')->where('type_id', 3)->orderBy('price','desc')->limit(1)->get()->toArray();
            
                    
        $specialProducts = Product::with('category')->where('type_id', 3)->limit(3)->get()->toArray();

        return view('welcome')->with(compact('sliderBanners', 'fixBanners', 'advertiseBanners', 
            'trendingProducts', 'trendingFirstProduct', 'specialProducts', 'specialNewProduct', 'specialHighestPriceProduct'));
    }

    public function products()
    {
        $products = Product::with(['category' => function($query) {
                $query->select('id', 'name');
            }, 'type' => function($query) {
                $query->select('id', 'name');
            }])->get()->toArray();
        // dd($products);
        return view('products.products')->with(compact('products'));
    }

}
