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
            return response()->json([
                'success' => false,
                'message' => "Rất tiếc, bạn chỉ có thể mua tối đa " . $getProductQuantity . " sản phẩm",
            ]);
        }        
    }

    public function updateQuantity($id, $quantity) {
        $cart = Cart::find($id);
        $size = Size::where('name', $cart->size)->first();
        $getProductQuantity = ProductSize::getProductQuantity($cart->product_id, $size->id);
        // $maxProductQuantity = $getProductQuantity - $quantity;
        if($getProductQuantity >= $quantity) {
            $cart->quantity = $quantity;
            $cart->save();
            return response()->json([
                'success' => true,
                'cart' => $cart,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Rất tiếc, bạn chỉ có thể mua tối đa " . $getProductQuantity . " sản phẩm",
            ]);
        }
        
    }

    public function updateSize($id, $size, $quantity) {
        $cart = Cart::find($id);
        $cart_existed = Cart::where('size', $size)->first();
        $sizeName = Size::where('name', $size)->first();
        $getProductQuantity = ProductSize::getProductQuantity($cart->product_id, $sizeName->id);

        if($cart_existed && $cart_existed != $cart) {
            if($getProductQuantity >= ($cart_existed->quantity + $quantity)) {
                $cart_existed->quantity = $cart_existed->quantity + $quantity;
                $cart_existed->save();
                $cart->delete();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Rất tiếc, bạn chỉ có thể mua tối đa " . $getProductQuantity . " sản phẩm",
                ]);
            }
        } else {
            $cart->size = $size;
            $cart->save();
        }
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
