<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Favorite;
use App\Models\Inventory;
use App\Models\Cart;
use App\Http\Resources\ProductResource;

class FavoritesController extends Controller
{
    public function index($id)
    {
        $getFavoriteItems = Favorite::with(['product'])->orderBy('created_at', 'desc')->where('user_id', $id)->get();

        foreach ($getFavoriteItems as $key => $value) {
            $item = new ProductResource($value->product);

            $getFavoriteItems[$key]->products = $item;
        }

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
        
        foreach ($selectedIds as $product_id) {
            $product = Product::find($product_id);

            $inventory = Inventory::where('product_id', $product_id)
                ->where('total_final', '>', 0)
                ->orderByDesc('month_year')
                ->orderBy('color_id')
                ->orderBy('size_id')
                ->first();

            if($inventory->total_final >= 1) {
                // Check existed Size
                $cart = Cart::where(['user_id' => $id, 'product_id' => $product_id, 
                    'size_id' => $inventory->size_id, 'color_id' => $inventory->color_id])->first();    

                if($cart) {
                    if($inventory->total_final >= ($cart->quantity + 1)) {
                        Cart::where(['user_id' => $id, 'product_id' => $product_id, 
                            'size_id' => $inventory->size_id, 'color_id' => $inventory->color_id])
                            ->update(['quantity' => $cart->quantity + 1]);

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
                    $item->product_id = $product_id;
                    $item->size_id = $inventory->size_id;
                    $item->color_id = $inventory->color_id;
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
                'success'=>$productAvailables,
                'message'=>"Rất tiếc, số lượng sản phẩm " . implode(', ', $productUnavailables) . " không đủ."
            ]);
        }
    }

    public function destroy($user, $product)
    {
        $pro = Product::where('id', $product)->first();
        Favorite::where(['user_id' => $user, 'product_id' => $product])->delete();
        return response()->json([
            'success' => 'success',
            'message' => $pro->name . ' được xóa khỏi danh sách yêu thích.'
        ], 200);
    }    
    
}
