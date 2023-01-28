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
        if(Auth::check()) {
            $getCartItems = Cart::with(['product', 'sizes'])->orderby('id', 'Desc')->where('user_id', Auth::user()->id)->paginate(2);
            return response()->json($getCartItems);
        } else 
            return response()->json("Bạn phải là thành viên");
    }
    
    public function store(Request $request)
    {
        if(Auth::check()) {
            $getProductQuantity = ProductSize::getProductQuantity($data['product_id'],$data['product_size']);
            if($getProductQuantity < $data['product_quantity']) {
                return response()->json("Số lượng không được phép.");
            }

            // Save Product in Carts table
            $item = new Cart;
            $item->user_id = Auth::user()->id;
            $item->product_id = $data['product_id'];
            $item->size = $data['product_size'];
            $item->quantity = $data['product_quantity'];
            $item->save();
            return response()->json("Sản phẩm được thêm vào giỏ hàng.");
        } else {
            return response()->json("Bạn phải là thành viên");
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
