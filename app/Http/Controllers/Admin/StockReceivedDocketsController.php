<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockReceivedDocket;
use App\Models\StockReceivedDocketProduct;
use App\Http\Resources\StockReceivedDocketResource;
use App\Models\Inventory;
use App\Models\PaymentVoucher;
use Illuminate\Support\Facades\Validator;
use App\Jobs\UploadImportToGoogleDrive;

class StockReceivedDocketsController extends Controller
{
    public function index()
    {
        $payments = StockReceivedDocket::orderBy('created_at', 'DESC')->get();
        
        return response(StockReceivedDocketResource::collection($payments));
    }

    public function show($id)
    {
        $order = StockReceivedDocket::with('import_coupon_product.product')->find($id);
        // return response()->json(new StockReceivedDocketResource($order));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required',
            'payment_voucher_id' => 'required',
            'items' => 'required',
            'inventories' => 'required',
            'form' => 'required',
            'total_price' => 'required',
            'value_added' => 'required',
            'total_value' => 'required',
            'description' => 'required',
            'image' => 'required',
        ], [
            'supplier_id.required' => 'Vui lòng chọn nhà cung cấp.',
            'payment_voucher_id.required' => 'Vui lòng chọn phiếu chi.',
            'total_price.required' => 'Vui lòng nhập tổng tiền.',
            'value_added.required' => 'Vui lòng nhập giá trị gia tăng.',
            'total_value.required' => 'Vui lòng nhập tổng giá trị.',
            'form.required' => 'Vui lòng chọn hình thức.',
            'description.required' => 'Vui lòng nhập mô tả.',
            'image.required' => 'Vui lòng chọn chứng từ.',
            'items.required' => 'Vui lòng chọn các sản phẩm.',
            'inventories.required' => 'Vui lòng nhập phân loại sản phẩm.',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);

        } else {
            $payment_voucher = PaymentVoucher::find($request->payment_voucher_id);
            $stock_received_docket = StockReceivedDocket::where('payment_voucher_id', $request->payment_voucher_id)->get();
            $importTotal = 0;
            foreach($stock_received_docket as $import) {
                $importTotal += $import['total_value'] + $request->total_value;
            }
            if($payment_voucher['total_price'] < $importTotal ) {
                return response()->json([
                    'success' => 'warning',
                    'message' => "Tổng giá trị phiếu nhập vượt quá phiếu chi.",
                ]); 

            } else {
                $import = new StockReceivedDocket;
                $import->staff_id = $request->staff_id;
                $import->supplier_id = $request->supplier_id;
                $import->payment_voucher_id = $request->payment_voucher_id;
                $import->date = $request->date;
                $import->total_price = $request->total_price;
                $import->value_added = $request->value_added;
                $import->total_value = $request->total_value;
                $import->form = $request->form;
                $import->description = $request->description;
        
                $base64Image = $request->image;
        
                $import->save();
        
                UploadImportToGoogleDrive::dispatch($import, $base64Image);

                $importId = $import->id;
        
                
                foreach($request->items as $item) {
                    $product = Product::find($item['product_id']);
                    if($product['price'] != $item['price']) {
                        $product->price = $item['price'];
                        $product->price_final = ($item['price']*(100-$product['discount_percent']))/100;
                        $product->save();
                    }
                    $importProduct = new StockReceivedDocketProduct();
                    $importProduct->stock_received_docket_id = $importId;
                    $importProduct->product_id = $item['product_id'];
                    $importProduct->quantity = $item['quantity'];
                    $importProduct->price = $item['price_purchase'];
                    $importProduct->save();
                }
                
                foreach($request->inventories as $inventory) {
                    $currentMonthYear = date('Ym');  // Lấy tháng và năm hiện tại dưới dạng chuỗi "YYYYMM"
                    $existingInventory = Inventory::where(['month_year' => $currentMonthYear, 
                        'product_id' => $inventory['product_id'], 'color_id' => $inventory['color_id'], 
                        'size_id' => $inventory['size_id']])->first();
        
                    if(!$existingInventory) {
                        $importInventory = new Inventory();
                        $importInventory->month_year = $currentMonthYear;
                        $importInventory->product_id = $inventory['product_id'];
                        $importInventory->color_id = $inventory['color_id'];
                        $importInventory->size_id = $inventory['size_id'];
                        $importInventory->total_initial = 0;
                        $importInventory->total_import = $inventory['quantity'];
                        $importInventory->total_export = 0;
                        $importInventory->total_final = $inventory['quantity'];
                        $importInventory->save();
                    } else {
                        Inventory::where(['month_year' => $currentMonthYear, 
                            'product_id' => $inventory['product_id'], 'color_id' => $inventory['color_id'], 
                            'size_id' => $inventory['size_id']])->update([
                                'total_import' => $existingInventory['total_import'] + $inventory['quantity'],
                            ]);
                    }
        
                }
        
                return response()->json([
                    'success' => 'success',
                    'message' => "Phiếu nhập được lập thành công",
                ]); 
            }

        }
        

    }

    public function destroy($id)
    {
        $product = StockReceivedDocket::find($id);
        if($product->image != null) {
            unlink(public_path()."/storage/uploads/products/". $product->image);
        }
        $product->delete();
        return response()->json(['success'=>'true'], 200);
    }

}
