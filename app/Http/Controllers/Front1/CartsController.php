<?php

namespace App\Http\Controllers\Front;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Type;
use App\Models\Product;
use App\Models\ProductImages;
use App\Models\Size;
use App\Models\ProductSize;
use App\Models\Cart;
use App\Models\CartItem;
use Session;
use Image;
use Auth;


class CartsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::check()) {
            $getCartItems = Cart::with(['product', 'sizes'])->orderby('id', 'Desc')->where('user_id', Auth::user()->id)->paginate(2);
            return view('front.carts.carts')->with(compact('getCartItems'));
        } else {
            return redirect('/login')->with('error_message', "You must be a member!");
        }
    }

    public function cartAdd(Request $request) {
        if(Auth::check()) {
            if($request->isMethod('post')) {
                $data = $request->all();
                // echo "<pre>"; print_r($data); die;
    
                $rules = [
                    'product_size' => 'required',
                ];
    
                $customMessage = [
                    'product_size.required' => 'Product Size is required!',
                ];
    
                $this->validate($request, $rules, $customMessage);
    
                // Check Product Quantity is available or not
                $getProductQuantity = ProductSize::getProductQuantity($data['product_id'],$data['product_size']);
                if($getProductQuantity < $data['product_quantity']) {
                    return redirect()->back()->with('error_message', 'Required Quantity is not available!');
                }
    
                // Save Product in Carts table
                $item = new Cart;
                $item->user_id = Auth::user()->id;
                $item->product_id = $data['product_id'];
                $item->size = $data['product_size'];
                $item->quantity = $data['product_quantity'];
                $item->save();
                return redirect()->back()->with('success_message', 'Product has been added in Cart!');
            }
        } else {
            return redirect('/login')->with('error_message', "You must be a member!");
        }
    }

    public function update(Request $request)
    {
        $cart = Cart::find($request->id);

        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            // Check Product Quantity is available or not
            $getProductQuantity = ProductSize::getProductQuantity($data['cart_product_id'],$data['cart_size_id']);
            
            if($getProductQuantity < $data['cart_quantity']) {
                return redirect()->back()->with('error_message', 'Required Quantity is not available!');
            } else {
                Cart::where('id', $request->id)->update([
                    'quantity'=>$data['cart_quantity']
                ]);
    
                return redirect('carts')->with('success_message','Cart update successfully!');
            }

        }

        return view('front.carts.carts');
    }

    public function destroy($id)
    {
        Cart::where('id', $id)->delete();
        $message = "Item has been deleted successfully!";
        return redirect()->back()->with('success_message', $message);
    }

}
