<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ImportCouponProduct;
use App\Models\ImportCoupon;
use App\Http\Resources\ImportCouponResource;
use Carbon\Carbon;
use Intervention\Image\Facades\Image;

class ImportCouponsController extends Controller
{
    public function index()
    {
        $orders = ImportCoupon::orderBy('created_at', 'DESC')->get();
        
        return response(ImportCouponResource::collection($orders));
    }

    public function show($id)
    {
        $order = ImportCoupon::with('import_coupon_product.product')->find($id);
        return response()->json(new ImportCouponResource($order));
    }

    public function store($id, Request $request)
    {
        $importCoupon = new ImportCoupon;
        $importCoupon->staff_id = $id;
        $importCoupon->supplier_id = $request->supplier_id;
        $importCoupon->payment_voucher_id = $request->payment_voucher_id;
        $importCoupon->date = $request->date;
        $importCoupon->total_price = $request->total_price;
        $importCoupon->value_added = $request->value_added;
        $importCoupon->total_value = $request->total_value;

        if($request->image) {
            $strpos = strpos($request->image, ';');
            $sub = substr($request->image, 0, $strpos);
            $ex = explode("/", $sub)[1];
            $imageName = time().".".$ex;
            $img = Image::make($request->image);
            $upload_path = public_path()."/storage/uploads/products/";
            $img->save($upload_path.$imageName);

            $importCoupon->image = $imageName;    
        } 

        $importCoupon->save();

        $importCouponId = $importCoupon->id;

        
        foreach($request->products as $item) {
            $importCouponProduct = new ImportCouponProduct();
            $importCouponProduct->import_coupon_id = $importCouponId;
            $importCouponProduct->product_id = $item['id'];
            $importCouponProduct->quantity = $item['quantity'];
            $importCouponProduct->price = $item['purchase_price'];
            $importCouponProduct->save();
        }
        
        return response()->json([
            'success' => 'true'
        ], 200);  
    }

    public function destroy($id)
    {
        $product = ImportCoupon::find($id);
        if($product->image != null) {
            unlink(public_path()."/storage/uploads/products/". $product->image);
        }
        $product->delete();
        return response()->json(['success'=>'true'], 200);
    }

}
