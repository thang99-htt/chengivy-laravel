<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Size;
use App\Models\Inventory;
use App\Models\Cart;
use App\Models\ProductImage;
use App\Models\User;

class CartsController extends Controller
{
    public function index($id)
    {
        $getCartItems = Cart::with(['product.inventories'
            => function ($query) {
                $query->where('month_year', function ($subQuery) {
                    $subQuery->selectRaw('max(month_year)')
                            ->from('inventories');
                });
            }])->orderby('created_at', 'Desc')->where('user_id', $id)->get();
        $into_money = 0;
        $total_price = 0;
        $count_item = 0;
        
        foreach($getCartItems as $item) {
            $productImage = ProductImage::where(['product_id' => $item->product_id, 'color_id' => $item->color_id])->first();
            $item['total_price'] = 0;
            $item['total_value'] = 0;
            $item['size_name'] = $item->size->name;
            $item['color_name'] = $item->color->name;
            $item['image'] = $productImage->image;

            
            $inventory = Inventory::where(['product_id' => $item->product_id, 
                'color_id' => $item->color_id, 'size_id' => $item->size_id])->orderByDesc('month_year')->first();
                
            $item['inventory'] = $inventory;
            
            if($item['quantity'] > $item->inventory->total_final) {
                Cart::where([
                        'user_id' => $id,
                        'product_id' => $item['product_id'], 
                        'size_id' => $item['size_id'],
                        'color_id' => $item['color_id']
                    ])->update(['quantity' => $item->inventory->total_final]);
            }
            if($item->inventory->total_final > 0){
                $item['total_price'] += $item['product']['price']*$item['quantity'];
                $item['total_value'] += $item['product']['price_final']*$item['quantity'];
            }
            $total_price += $item['total_price'];
            $into_money += $item['total_value'];

            $count_item++;
        }
        return response()->json([
            'getCartItems' => $getCartItems,
            'total_price' => $total_price,
            'into_money' => $into_money,
            'count_item' => $count_item
        ]);
    }

    public function store($id, Request $request)
    {
        $product = Product::find($request->product_id);
        $inventory = Inventory::where(['product_id' => $request->product_id, 
                'size_id' => $request->size_id, 'color_id' => $request->color_id])
                ->where('total_final', '>', 0)
                ->orderByDesc('month_year')
                ->orderBy('color_id')
                ->orderBy('size_id')
                ->first();
        if($inventory->total_final >= $request->quantity) {
            // Check existed Size
            $cart = Cart::where(['user_id' => $id, 'product_id' => $request->product_id, 
                'size_id' => $request->size_id, 'color_id' => $request->color_id])->first();    

            if($cart) {
                if($inventory->total_final >= ($cart->quantity + $request->quantity)) {
                    Cart::where(['user_id' => $id, 'product_id' => $request->product_id, 
                        'size_id' => $request->size_id, 'color_id' => $request->color_id])
                        ->update(['quantity' => $cart->quantity + $request->quantity]);
                    return response()->json([
                        'success'=>'success',
                        'message'=> "Bạn đã thêm " . $product->name . " vào giỏ hàng của mình."
                    ]);
                } else {
                    return response()->json([
                        'success'=>'warning',
                        'message'=>"Rất tiếc, bạn chỉ có thể mua tối đa " . $inventory->total_final . " sản phẩm"
                    ]);
                }
            } else {
                // Save Product in Carts table
                $item = new Cart;
                $item->user_id = $id;
                $item->product_id = $request->product_id;
                $item->size_id = $request->size_id;
                $item->color_id = $request->color_id;
                $item->quantity = $request->quantity;
                $item->save();
                return response()->json([
                    'success'=>'success',
                    'message'=> "Bạn đã thêm " . $product->name . " vào giỏ hàng của mình."
                ]);
            }
        }
        else {
            return response()->json([
                'success'=>'warning',
                'message'=>"Rất tiếc, bạn chỉ có thể mua tối đa " . $inventory->total_final . " sản phẩm"
            ]);
        }       
    }

    public function updateQuantity(Request $request) {
        $product = Product::find($request->product_id);
        
        $inventory = Inventory::where(['product_id' => $request->product_id, 
            'size_id' => $request->size_id, 'color_id' => $request->color_id])->orderByDesc('month_year')->first();
            
        if($inventory->total_final >= $request->quantity) {
            Cart::where(['user_id' => $request->user_id, 'product_id' => $request->product_id, 
                'color_id' => $request->color_id, 'size_id' => $request->size_id])->update(['quantity' => $request->quantity]);
            return response()->json([
                'success' => 'success',
                'message' => "Cập nhật số lượng của " . $product->name . " thành công.",
            ]);
        } else {
            return response()->json([
                'success' => 'warning',
                'message' => "Rất tiếc, bạn chỉ có thể mua tối đa " . $inventory->total_final . " sản phẩm",
            ]);
        }
    }

    public function updateColorAndSize(Request $request) {
        // Find the existing cart item
        $cart_existed = Cart::where([
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
            'color_id' => $request->color_id,
            'size_id' => $request->size_id
        ])->first();

        // Find the product
        $product = Product::find($request->product_id);

        // Find the inventory
        $inventory = Inventory::where([
            'product_id' => $request->product_id,
            'size_id' => $request->size_id,
            'color_id' => $request->color_id
        ])->orderByDesc('month_year')->first();

        if ($cart_existed) {
            $new_quantity = $cart_existed->quantity + $request->quantity;   
        } else {
            $new_quantity = $request->quantity;
        }

        if ($inventory && $inventory->total_final >= $new_quantity) {
            if($cart_existed) {
                Cart::where([
                    'user_id' => $request->user_id,
                    'product_id' => $request->product_id,
                    'color_id' => $request->color_id,
                    'size_id' => $request->size_id
                ])->update(['quantity' => $new_quantity]);
            } else {
                $cart = new Cart();
                $cart->user_id = $request->user_id;
                $cart->product_id = $request->product_id;
                $cart->color_id = $request->color_id;
                $cart->size_id = $request->size_id;
                $cart->quantity = $new_quantity;
                $cart->save();
            }

            Cart::where([
                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
                'color_id' => $request->color_id_old,
                'size_id' => $request->size_id_old
            ])->delete();

            return response()->json([
                'success' => 'success',
                'message' => "Cập nhật " . $product->name . " thành công.",
            ]);
        } else {
            return response()->json([
                'success' => 'warning',
                'message' => "Rất tiếc, bạn chỉ có thể mua tối đa " . $inventory->total_final . " sản phẩm",
            ]);
        }
    }

    public function destroy($user, $product, $color, $size)
    {
        Cart::where([
            'user_id' => $user,
            'product_id' => $product,
            'color_id' => $color,
            'size_id' => $size
        ])->delete();
        $product = Product::find($product);
        return response()->json([
            'success' => 'success',
            'message' => $product->name . " được xóa khỏi giỏ hàng của bạn.",
        ]);
    }

}
