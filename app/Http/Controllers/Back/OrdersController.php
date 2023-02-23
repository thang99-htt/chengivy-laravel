<?php

namespace App\Http\Controllers\Back;

use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManager;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderProduct;
use Image;
use Auth;

class OrdersController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user' => function($query) {
            $query->select('id', 'name', 'email');
        }, 'contact' => function($query) {
            $query->select('id', 'phone');
        }, 'payment' => function($query) {
            $query->select('id', 'name');
        }, 'status' => function($query) {
            $query->select('id', 'name');
        }])->orderBy('created_at', 'DESC')->get();

        return response()->json($orders);
    }

    public function show($id)
    {
        $order = Order::with(['user' => function($query) {
            $query->select('id', 'name', 'email');
        }, 'contact' => function($query) {
            $query->select('id', 'phone', 'address', 'ward_id');
        }, 'payment' => function($query) {
            $query->select('id', 'name');
        }, 'status' => function($query) {
            $query->select('id', 'name');
        }, 'order_product'])->find($id);

        $getAddressDetail = Order::getAddressDetail($order['contact']['ward_id']);
        $order['contact']['address_detail'] = $getAddressDetail;

        foreach($order['order_product'] as $key => $value) {
            $productDetail = Product::where('id', $order['order_product'][$key]['product_id'])->first();
            $order['order_product'][$key]['product_detail'] = $productDetail;
        }

        $userProfile = User::where('id', $order['user_id'])->first();
        $order['user']['user_detail'] = $userProfile->profiles->first();

        return response()->json($order);
    }

    public function detail(Request $request)
    {
        // $orderDetail = OrderProduct::where('id', $request['id'])
    }

    public function update($id, Request $request)
    {
        $image_current = Product::select('image')->where('id', $id)->first();
        if($request->image == $image_current->image) {
            $imageName = $image_current->image;
        } else {
            $strpos = strpos($request->image, ';');
            $sub = substr($request->image, 0, $strpos);
            $ex = explode("/", $sub)[1];
            $imageName = time().".".$ex;
            $img = Image::make($request->image);
            $upload_path = public_path()."/storage/uploads/products/";
            $img->save($upload_path.$imageName);
        }
        $product = Product::where('id', $id)->update([
            'category_id' => $request['category_id'],
            'name' => $request['name'],
            'description' => $request['description'],
            'purchase_price' => $request['purchase_price'],
            'price' => $request['price'],
            'image' => $imageName,
            'type_id' => $request['type_id'],
            'discount_percent' => $request['discount_percent']
        ]);

        return response()->json($product);
    }

    public function updateProductStatus($id, Request $request) {
        $product = Product::find($id);
        $product->status = !$request->status;
        $product->save();
        
        return response()->json([
            'success' => true,
            'product' => $product,
        ]);
    }

    public function updateOrderStatus($id, Request $request) {
        $order = Order::find($id);
        if($request->status == 1)
            $order->status_id = 2;
        if($request->status == 2)
            $order->status_id = 3;
        if($request->status == 3)
            $order->status_id = 4;
        if($request->status == 4)
            $order->status_id = 5;
        if($request->status == 5)
            $order->status_id = 6;
        if($request->status == 6)
            $order->status_id = 7;
        if($request->status == 7)
            $order->status_id = 8;
        if($request->status == 8)
            $order->status_id = 9;
        $order->save();
        
        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if($product->image != null) {
            unlink(public_path()."/storage/uploads/products/". $product->image);
        }
        $product->delete();
        return response()->json(['success'=>'true'], 200);
    }

}
