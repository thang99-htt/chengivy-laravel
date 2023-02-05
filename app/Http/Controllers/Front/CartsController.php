<?php

namespace App\Http\Controllers\Front;

use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Size;
use App\Models\Images;
use App\Models\ProductSize;
use App\Models\Category;
use App\Models\Type;
use App\Models\Cart;
use Image;
use Auth;

class CartsController extends Controller
{
    public function index()
    {
        $getCartItems = Cart::with(['product', 'sizes'])->orderby('id', 'Desc')->where('user_id', $request['user_id']);
        return response()->json($getCartItems);
    }
    
    public function store(Request $request)
    {
        $getProductQuantity = ProductSize::getProductQuantity($request['product_id'], $request['size']);
        $size = Size::select('name')->where('id', $request['size'])->first();

        if($getProductQuantity > $request['quantity']) {
            // Save Product in Carts table
            $item = new Cart;
            $item->user_id = $request['user_id'];
            $item->product_id = $request['product_id'];
            $item->size = $size->name;
            $item->quantity = $request['quantity'];
            $item->save();
            return response()->json(true);
        }
        else {
            return response()->json(false);
        }        
    }
    
    public function update(Request $request)
    {
        $cart = Cart::find($request->id);
        $getProductQuantity = ProductSize::getProductQuantity($data['cart_product_id'],$data['cart_size_id']);
        
        if($getProductQuantity < $data['cart_quantity']) {
            return response()->json("Số lượng không được phép.");
        } else {
            Cart::where('id', $request->id)->update([
                'quantity'=>$data['cart_quantity']
            ]);

            return response()->json("Sản phẩm được thêm vào giỏ hàng.");
        }

        return view('front.carts.carts');
    }

    public function destroy($id)
    {
        Cart::where('id', $id)->delete();
        return response()->json("Sản phẩm được xóa khỏi giỏ hàng.");
    }
}
