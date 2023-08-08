<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Favorite;
use App\Models\Inventory;
use App\Models\Cart;
use App\Models\Size;

class FavoritesController extends Controller
{
    public function index($id)
    {
        $getFavoriteItems = Favorite::with(['product'])->orderby('created_at', 'Desc')->where('user_id', $id)->get();
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
        $product = Product::find($request->product_id);    
        if($favorite) {
            return response()->json([
                'success' => 'warning',
                'message' => $product->name . ' đã có trong danh sách yêu thích của bạn.'
            ]);
        } else {
            // Save Product in Carts table
            $item = new Favorite;
            $item->user_id = $id;
            $item->product_id = $request['product_id'];
            $item->save();
            return response()->json([
                'success' => 'success',
                'message' => $product->name . ' đã được thêm vào danh sách yêu thích của bạn.'
            ]);
        }    
    }


    // Add to cart multiple product with id=user
    public function addToCart($id, Request $request)
    {
        $check = false;
        $productUnavailables = [];
        $productAvailables = [];
        $selectedIds = $request->all();
        foreach ($selectedIds as $product) {
            // Tìm kiếm và lưu trữ $productSize vào mảng
            $productSize = Inventory::where('product_id', $product['id'])->first();
            $size = Size::select('name')->where('id', $productSize->size_id)->first();
            if($productSize->stock >= 1) {
                // Check existed Size
                $cart = Cart::where(['user_id' => $id, 'product_id' => $product['id'], 
                    'size' => $size->name])->first();     
                if($cart) {
                    $cartId = Cart::find($cart->id);
                    if($productSize->stock >= ($cartId->quantity + 1)) {
                        $cartId->quantity = $cartId->quantity + 1;
                        $cartId->save();
                        $check = true;
                        $productAvailables[] = $product['name'];
                        Favorite::where(['user_id' => $id, 'product_id' => $product['id']])->delete();
                    } else {
                        $check = false;
                        $productUnavailables[] = $product['name'];
                    }
                } else {
                    // Save Product in Carts table
                    $item = new Cart;
                    $item->user_id = $id;
                    $item->product_id = $product['id'];
                    $item->size = $size->name;
                    $item->quantity = 1;
                    $item->save();
                    $check = true;
                    $productAvailables[] = $product['name'];
                    Favorite::where(['user_id' => $id, 'product_id' => $product['id']])->delete();
                }
            }
            else {
                $check = false;
                $productUnavailables[] = $product['name'];
            }   
        }   
        if($check && !$productUnavailables) {
            return response()->json([
                'success'=>'success',
                'message'=> "Bạn đã thêm " . implode(', ', $productAvailables) . " vào giỏ hàng của mình."
            ]);
        } else {
            return response()->json([
                'success'=>'warning',
                'message'=>"Rất tiếc, số lượng sản phẩm " . implode(', ', $productUnavailables) . " không đủ."
            ]);
        }
    }

    public function destroy($id)
    {
        $favorite = Favorite::find($id);
        $product = Product::where('id', $favorite->product_id)->first();
        $product = $product->name;
        $favorite->delete();
        return response()->json([
            'success' => 'success',
            'message' => "Bạn đã xóa " . $product . " khỏi danh sách yêu thích."
        ], 200);
    }
    
    public function destroyByUser($user, $product)
    {
        $pro = Product::where('id', $product)->first();
        Favorite::where(['user_id' => $user, 'product_id' => $product])->delete();
        return response()->json([
            'success' => 'success',
            'message' => $pro->name . ' được xóa khỏi danh sách yêu thích.'
        ], 200);
    }
}
