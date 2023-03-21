<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoriesController extends Controller
{
    public function category()
    {        
        $categories = Category::categories();
        return response()->json($categories);
    }

}
