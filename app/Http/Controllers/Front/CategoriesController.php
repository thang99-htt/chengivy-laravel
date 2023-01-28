<?php

namespace App\Http\Controllers\Front;

use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManager;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Image;
use Auth;

class CategoriesController extends Controller
{

    public function category()
    {        
        $categories = Category::categories();
        return response()->json($categories);
    }

}
