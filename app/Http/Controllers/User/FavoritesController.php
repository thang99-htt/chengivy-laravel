<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Favorite;

class FavoritesController extends Controller
{
    public function index($id)
    {
        $getFavoriteItems = Favorite::with(['product'])->orderby('id', 'Desc')->where('user_id', $id)->get();
        $favoriteCount = $getFavoriteItems->count();
        return response()->json([
            'getFavoriteItems' => $getFavoriteItems,
            'favoriteCount' => $favoriteCount
        ]);
    }

    public function store($id, Request $request)
    {
        // Check existed Size
        $favorite = Favorite::where(['user_id' => $id, 'product_id' => $request->product_id])->first();     
        if($favorite) {
            return response()->json(false);
        } else {
            // Save Product in Carts table
            $item = new Favorite;
            $item->user_id = $id;
            $item->product_id = $request['product_id'];
            $item->save();
            return response()->json(true);
        }    
    }

    public function destroy($user, $product)
    {
        Favorite::where(['user_id' => $user, 'product_id' => $product])->delete();
        return response()->json([
            'success' => 'true',
            'message' => 'Sản phẩm được xóa khỏi danh sách yêu thích.'
        ], 200);
    }
    
}
