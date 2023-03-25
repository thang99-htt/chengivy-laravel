<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Size;
use App\Models\ProductSize;
use App\Models\Cart;

class CartsController extends Controller
{
    public function index($id)
    {
        $getCartItems = Cart::with(['product'])->orderby('id', 'Desc')->where('user_id', $id)->get();
        $into_money = 0;
        $count_item = 0;
        
        foreach($getCartItems as $key => $value) {
            $getDiscountPrice = Product::getDiscountPrice($getCartItems[$key]['product']['id']);
            $getCartItems[$key]['total_price'] = 0;

            if($getDiscountPrice > 0) {
                $getCartItems[$key]['final_price'] = $getDiscountPrice;
                $getCartItems[$key]['total_price'] += $getDiscountPrice*$getCartItems[$key]['quantity'];
            } else {
                $getCartItems[$key]['final_price'] = $getCartItems[$key]['product']['price'];
                $getCartItems[$key]['total_price'] += $getCartItems[$key]['product']['price']*$getCartItems[$key]['quantity'];
            }
            $into_money += $getCartItems[$key]['total_price'];
            $count_item++;
        }
        return response()->json([
            'getCartItems' => $getCartItems,
            'into_money' => $into_money,
            'count_item' => $count_item
        ]);
    }

    public function store($id, Request $request)
    {
        $getProductQuantity = ProductSize::getProductQuantity($request['product_id'], $request['size']);
        $size = Size::select('name')->where('id', $request['size'])->first();
        
        if($getProductQuantity >= $request['quantity']) {
            // Check existed Size
            $cart = Cart::where(['user_id' => $id, 'product_id' => $request['product_id'], 
                'size' => $size->name])->first();     
            if($cart) {
                $cartId = Cart::find($cart->id);
                if($getProductQuantity >= ($cartId->quantity + $request['quantity'])) {
                    $cartId->quantity = $cartId->quantity + $request['quantity'];
                    $cartId->save();
                    return response()->json(true);
                } else {
                    return response()->json(false);
                }
            } else {
                // Save Product in Carts table
                $item = new Cart;
                $item->user_id = $id;
                $item->product_id = $request['product_id'];
                $item->size = $size->name;
                $item->quantity = $request['quantity'];
                $item->save();
                return response()->json(true);
            }
        }
        else {
            return response()->json(false);
        }        
    }
    
    public function update(Request $request)
    {
        $cart = Cart::find($request->id);
        $getProductQuantity = ProductSize::getProductQuantity($request['cart_product_id'],$request['cart_size_id']);
        
        if($getProductQuantity < $request['cart_quantity']) {
            return response()->json("Số lượng không được phép.");
        } else {
            Cart::where('id', $request->id)->update([
                'quantity'=>$request['cart_quantity']
            ]);

            return response()->json("Sản phẩm được thêm vào giỏ hàng.");
        }

        return view('front.carts.carts');
    }

    public function updateQuantity($id, Request $request) {
        $cart = Cart::find($id);
        $cart->quantity = $request->quantity;
        $cart->save();
        
        return response()->json([
            'success' => true,
            'cart' => $cart,
        ]);
    }

    public function destroy($id)
    {
        Cart::where('id', $id)->delete();
        return response()->json([
            'success' => 'true',
            'message' => 'Sản phẩm được xóa khỏi giỏ hàng.'
        ], 200);
    }

}