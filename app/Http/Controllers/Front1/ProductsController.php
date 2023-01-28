<?php

namespace App\Http\Controllers\Front;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Cart;
use Auth;

class ProductsController extends Controller
{
    public function listing(Request $request) {
        if($request->ajax()) {
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            $url = $data['url'];
            $_GET['sort'] = $data['sort'];
            $categoryCount = Category::where(['url' => $url, 'status' => 1])->count();
            if($categoryCount>0) {
                $categoryDetails = Category::categoryDetails($url);
                $products = Product::with('category')->whereIn('category_id', $categoryDetails['catIds'])->where('status', 1);

                // Check for sort
                if(isset($_GET['sort']) && !empty($_GET['sort'])) {
                    if($_GET['sort']=='price_lowest') {
                        $products->orderBy('products.price','Asc');
                    } else if($_GET['sort']=='price_highest') {
                        $products->orderBy('products.price','Desc');
                    } else if($_GET['sort']=='name_a_z') {
                        $products->orderBy('products.name','Asc');
                    } else if($_GET['sort']=='name_z_a') {
                        $products->orderBy('products.name','Desc');
                    }
                }
                $products = $products->paginate(8);
                // echo 'exist'; die;

                return view('front.products.ajax_products_listing')->with(compact('categoryDetails', 'products', 'url'));
            } else {
                abort(404);
            }
        } else {
            $url = Route::getFacadeRoot()->current()->uri();
            $categoryCount = Category::where(['url' => $url, 'status' => 1])->count();
            if($categoryCount>0) {
                $categoryDetails = Category::categoryDetails($url);
                $products = Product::with(['category' => function($query) {
                    $query->select('id', 'name');
                }])->whereIn('category_id', $categoryDetails['catIds'])->where('status', 1);

                // Check for sort
                if(isset($_GET['sort']) && !empty($_GET['sort'])) {
                    if($_GET['sort']=='price_lowest') {
                        $products->orderBy('products.price','Asc');
                    } else if($_GET['sort']=='price_highest') {
                        $products->orderBy('products.price','Desc');
                    } else if($_GET['sort']=='name_a_z') {
                        $products->orderBy('products.name','Asc');
                    } else if($_GET['sort']=='name_z_a') {
                        $products->orderBy('products.name','Desc');
                    }
                }
                $products = $products->paginate(8);
                // echo 'exist'; die;

                return view('front.products.listing')->with(compact('categoryDetails', 'products', 'url'));
            } else {
                abort(404);
            }
        }

    }

    // public function all(Request $request) {
    //     $url = Route::getFacadeRoot()->current()->uri();
    //     $categoryCount = Category::all()->count();
    //     if($categoryCount>0) {
    //         $categoriesDetails = Category::categoriesDetails($url);
    //         $products = Product::with('category');

    //         // Check for sort
    //         if(isset($_GET['sort']) && !empty($_GET['sort'])) {
    //             if($_GET['sort']=='price_lowest') {
    //                 $products->orderBy('products.price','Asc');
    //             } else if($_GET['sort']=='price_highest') {
    //                 $products->orderBy('products.price','Desc');
    //             } else if($_GET['sort']=='name_a_z') {
    //                 $products->orderBy('products.name','Asc');
    //             } else if($_GET['sort']=='name_z_a') {
    //                 $products->orderBy('products.name','Desc');
    //             }
    //         }
    //         $products = $products->paginate(10);
    //         // echo 'exist'; die;

    //         return view('front.products.listing')->with(compact('categoriesDetails', 'products', 'url'));
    //     } else {
    //         abort(404);
    //     }
    // }
    public function all(Request $request) {
        if($request->ajax()) { 
            $categoryCount = Category::where('status', 1)->count();
            if($categoryCount>0) {
                $products = Product::with('category');

                // Check for sort
                if(isset($_GET['sort']) && !empty($_GET['sort'])) {
                    if($_GET['sort']=='price_lowest') {
                        $products->orderBy('products.price','Asc');
                    } else if($_GET['sort']=='price_highest') {
                        $products->orderBy('products.price','Desc');
                    } else if($_GET['sort']=='name_a_z') {
                        $products->orderBy('products.name','Asc');
                    } else if($_GET['sort']=='name_z_a') {
                        $products->orderBy('products.name','Desc');
                    }
                }
                $products = $products->paginate(10);
                // echo 'exist'; die;

                return view('front.products.listing1')->with(compact('products'));
            } else {
                abort(404);
            }
        }
    }

    public function detail(Request $request) {
        $productDetails = Product::with('category', 'images', 'sizes')->find($request->id)->toArray();
        // dd($productDetails);
        return view('front.products.detail')->with(compact('productDetails'));
    }

    public function search(Request $request) {
        if($request->ajax()) { 
            $products = Product::where('id', 'LIKE', '%'.$request->search.'%')
                    ->orWhere('name', 'LIKE', '%'.$request->search.'%')
                    ->orWhere('description', 'LIKE', '%'.$request->search.'%')->get();
            if(count($products)>0) {
                return view('front.products.search')->with(compact('products'));
            } else {
                return '<h3>NO RESULTS FOUND FOR "'.$request->search.'"</h3>';
            }
        }
    }


}