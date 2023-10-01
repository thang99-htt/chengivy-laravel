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
use App\Models\StockReceivedDocketProductDetail;
use App\Jobs\UploadImportToGoogleDrive;
use App\Jobs\DeleteImportFromGoogleDrive;
use App\Models\Order;
use App\Models\OrderProduct;
use Carbon\Carbon;
class StockReceivedDocketsController extends Controller
{
    public function index()
    {
        $import = StockReceivedDocket::orderBy('date', 'DESC')->get();
        
        return response(StockReceivedDocketResource::collection($import));
    }

    public function show($id)
    {
        $import = StockReceivedDocket::with('stock_received_docket_product.product.product_image')->find($id);
        return response()->json(new StockReceivedDocketResource($import));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required',
            'inventories' => 'required',
            'supplier_id' => 'required',
            'payment_voucher_id' => 'required',
            'description' => 'required',
            'image' => 'required',
            'total_price' => 'required',
            'value_added' => 'required',
            'total_value' => 'required',
        ], [
            'items.required' => 'Vui lòng chọn các sản phẩm.',
            'inventories.required' => 'Vui lòng nhập phân loại sản phẩm.',
            'supplier_id.required' => 'Vui lòng chọn nhà cung cấp.',
            'payment_voucher_id.required' => 'Vui lòng chọn phiếu chi.',
            'description.required' => 'Vui lòng nhập mô tả.',
            'image.required' => 'Vui lòng chọn chứng từ.',
            'total_price.required' => 'Vui lòng nhập tổng tiền.',
            'value_added.required' => 'Vui lòng nhập giá trị gia tăng.',
            'total_value.required' => 'Vui lòng nhập tổng giá trị.',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => 'warning',
                'message' => $validator->errors()
            ], 422);

        } else {
            $payment_voucher = PaymentVoucher::find($request->payment_voucher_id);
            $stock_received_docket = StockReceivedDocket::where('payment_voucher_id', $request->payment_voucher_id)->get();
            $importTotal = $request->total_value;
            foreach($stock_received_docket as $import) {
                $importTotal += $import['total_value'];
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
                    
                    $importProductId = $importProduct->id;

                    foreach ($request->inventories as $inventory) {
                        if ($inventory['product_id'] == $item['product_id']) {
                            $importProductDetail = new StockReceivedDocketProductDetail();
                            $importProductDetail->stock_received_docket_product_id = $importProductId;
                            $importProductDetail->product_id = $inventory['product_id'];
                            $importProductDetail->color_id = $inventory['color_id'];
                            $importProductDetail->size_id = $inventory['size_id'];
                            $importProductDetail->quantity = $inventory['quantity'];
                            $importProductDetail->save();
                        }
                    }
                }
                
                $sizesNew = [];
                $sizesOld = [];
                $firstInventory = null;
                
                $inputDate = $request->date;
                $carbonDate = Carbon::parse($inputDate);
                $currentMonthYear = $carbonDate->format('Ym');
                
                foreach($request->inventories as $inventory) {
                    $existingInventoryCurrent = Inventory::where(['month_year' => $currentMonthYear, 
                        'product_id' => $inventory['product_id'], 'color_id' => $inventory['color_id'], 
                        'size_id' => $inventory['size_id']])->first();
        
                    // Tính tháng và năm của cuối tháng cũ
                    $existingInventory = Inventory::where([
                        'product_id' => $inventory['product_id'],
                        'color_id' => $inventory['color_id'],
                        'size_id' => $inventory['size_id']
                    ])
                    ->where('month_year', '<', $currentMonthYear) // Select months earlier than the current month
                    ->orderBy('month_year', 'desc') // Order by month_year in descending order
                    ->first();

                    $sizesNew[] = $existingInventory;


                    if ($firstInventory === null) {
                        $firstInventory = $inventory;
                        // Truy vấn dữ liệu từ mảng kết hợp $sizesOld
                        $existingMonthYear =  Inventory::where([
                            'product_id' => $inventory['product_id'],
                            'color_id' => $inventory['color_id']
                        ])
                        ->where('month_year', '<', $currentMonthYear)
                        ->orderBy('month_year', 'desc')
                        ->first();

                        $existingInventorySizeOld = [];
                        if ($existingMonthYear) {
                            $monthYearToMatch = $existingMonthYear->month_year;
                        
                            $existingInventorySizeOld = Inventory::where([
                                'product_id' => $inventory['product_id'],
                                'color_id' => $inventory['color_id'],
                                'month_year' => $monthYearToMatch
                            ])->get();
                        }

                        if($existingInventorySizeOld) {
                            foreach($existingInventorySizeOld as $size) {
                                if($size->total_final>0) {
                                    $sizesOld[] = $size; 
                                }
                            }
                        }
                    }
                    

                    if(!$existingInventoryCurrent) {
                        $importInventory = new Inventory();
                        $importInventory->month_year = $currentMonthYear;
                        $importInventory->product_id = $inventory['product_id'];
                        $importInventory->color_id = $inventory['color_id'];
                        $importInventory->size_id = $inventory['size_id'];
                        if($existingInventory) {
                            $importInventory->total_initial = $existingInventory['total_final'];
                            
                            $importInventory->total_import = $inventory['quantity'];
                            $importInventory->total_export = 0;
                            $importInventory->total_final = $existingInventory['total_final'] 
                                                            + $inventory['quantity'];
                        } else {
                            $importInventory->total_initial = 0;
                            $importInventory->total_import = $inventory['quantity'];
                            $importInventory->total_export = 0;
                            $importInventory->total_final = $inventory['quantity'];
                        }
                                                        
                        $importInventory->save();
                    } else {
                        Inventory::where(['month_year' => $currentMonthYear, 
                            'product_id' => $inventory['product_id'], 'color_id' => $inventory['color_id'], 
                            'size_id' => $inventory['size_id']])->update([
                                'total_import' => $existingInventoryCurrent['total_import'] + $inventory['quantity'],
                                'total_final' => $existingInventoryCurrent['total_final'] + $inventory['quantity'],
                            ]);
                    }

                }
        
                $itemsOnlyInOld = array_diff($sizesOld, $sizesNew);

                if($itemsOnlyInOld) {
                    foreach($itemsOnlyInOld as $inventory) {
                        $existingInventory =  Inventory::where([
                            'product_id' => $inventory['product_id'],
                            'color_id' => $inventory['color_id'],
                            'size_id' => $inventory['size_id']
                        ])
                        ->where('month_year', '<', $currentMonthYear) // Select months earlier than the current month
                        ->orderBy('month_year', 'desc') // Order by month_year in descending order
                        ->first();
                        
                        $existingInventoryCurrent = Inventory::where(['month_year' => $currentMonthYear, 
                        'product_id' => $inventory['product_id'], 'color_id' => $inventory['color_id'], 
                        'size_id' => $inventory['size_id']])->first();

                        if(!$existingInventoryCurrent) {
                            $importInventory = new Inventory();
                            $importInventory->month_year = $currentMonthYear;
                            $importInventory->product_id = $inventory['product_id'];
                            $importInventory->color_id = $inventory['color_id'];
                            $importInventory->size_id = $inventory['size_id'];
                            $importInventory->total_initial = $existingInventory['total_final'];
                            $importInventory->total_import = $inventory['total_import'];
                            $importInventory->total_export = 0;
                            $importInventory->total_final = $existingInventory['total_final'] + $inventory['total_import'];
                                                            
                            $importInventory->save();
                        }
                    }

                }

                return response()->json([
                    'success' => 'success',
                    'message' => "Phiếu nhập được lập thành công"
                ]); 
            }
        }
    }

    public function update(Request $request, $id) {
        $import = StockReceivedDocket::find($id);
        $payment_voucher = PaymentVoucher::find($request->payment_voucher_id);

        $check = false;
        $orderIds = [];

        $importTotal = $request->total_value;
        $relatedStockReceivedDockets = StockReceivedDocket::where('payment_voucher_id', $request->payment_voucher_id)->get();
        foreach ($relatedStockReceivedDockets as $relatedImport) {
            if ($relatedImport['id'] != $id) {
                $importTotal += $relatedImport->total_value;
            }
        }

        if ($payment_voucher['total_price'] < $importTotal) {
            return response()->json([
                'success' => 'warning',
                'message' => "Tổng giá trị nhập vượt quá giá trị phiếu chi.",
            ]); 
        } else {
            
            if($request->image != $import['image']) {
                $base64Image = $request->image;
                $imageLink = $import['image'];
                DeleteImportFromGoogleDrive::dispatch($imageLink);
                UploadImportToGoogleDrive::dispatch($import, $base64Image);
            }

            // Danh sach chi tiet phieu nhap hang (stock_received_docket_product)
            $existingImportProduct = StockReceivedDocketProduct::where('stock_received_docket_id', $id)->get();
            
            
            foreach($existingImportProduct as $importProduct) {
                $existingImportProductDetail = 
                    StockReceivedDocketProductDetail::where('stock_received_docket_product_id', $importProduct['id'])->get();
                    
                foreach($existingImportProductDetail as $importProductDetail) {
                    // Update Inventories        
                    $inputDate = $request->date;
                    $carbonDate = Carbon::parse($inputDate);
                    $currentMonthYear = $carbonDate->format('Ym');

                    $existingInventory = Inventory::where(['month_year' => $currentMonthYear, 
                        'product_id' => $importProductDetail['product_id'], 
                        'color_id' => $importProductDetail['color_id'], 
                        'size_id' => $importProductDetail['size_id']])->first();
                    
                    if($existingInventory) {
                        if($existingInventory['total_final'] < $importProductDetail['quantity']) {
                            $check = true;
                            $findOrder = OrderProduct::where([
                                    'product_id' => $importProductDetail['product_id']
                                ])
                                ->orderBy('order_id', 'DESC')
                                ->get();

                            $totalCheck = 0;
                            foreach($findOrder as $order) {
                                $totalCheck = $totalCheck + $order->quantity;
                                if($totalCheck >= $importProductDetail['quantity']) {
                                    $orderIds[] = $order->order_id;
                                    break;
                                }
                            }
                            
                        }
                    }
                }
            }

            if(!$check) {
                foreach($existingImportProduct as $importProduct) {
                    $existingImportProductDetail = 
                        StockReceivedDocketProductDetail::where('stock_received_docket_product_id', $importProduct['id'])->get();
                        
                    foreach($existingImportProductDetail as $importProductDetail) {
                        // Update Inventories        
                        $inputDate = $request->date;
                        $carbonDate = Carbon::parse($inputDate);
                        $currentMonthYear = $carbonDate->format('Ym');
    
                        $existingInventory = Inventory::where(['month_year' => $currentMonthYear, 
                            'product_id' => $importProductDetail['product_id'], 
                            'color_id' => $importProductDetail['color_id'], 
                            'size_id' => $importProductDetail['size_id']])->first();
                        
                        if($existingInventory) {
                            if($existingInventory['total_final'] >= $importProductDetail['quantity']) {
                                    Inventory::where(['month_year' => $currentMonthYear, 
                                    'product_id' => $importProductDetail['product_id'], 
                                    'color_id' => $importProductDetail['color_id'], 
                                    'size_id' => $importProductDetail['size_id']])
                                    ->update([
                                        'total_import' => $existingInventory['total_import'] - $importProductDetail['quantity'],
                                        'total_final' => $existingInventory['total_final'] - $importProductDetail['quantity']
                                    ]);
                            } 
                        }
    
                        $importProductDetail->delete();
                    }
    
                    $importProduct->delete();
    
                    $import->staff_id = $request->staff_id;
                    $import->supplier_id = $request->supplier_id;
                    $import->payment_voucher_id = $request->payment_voucher_id;
                    $import->date = $request->date;
                    $import->total_price = $request->total_price;
                    $import->value_added = $request->value_added;
                    $import->total_value = $request->total_value;
                    $import->description = $request->description;
    
                    $import->save(); 
                }
    
                foreach($request->items as $item) {
                    $product = Product::find($item['product_id']);
                    if($product['price'] != $item['price']) {
                        $product->price = $item['price'];
                        $product->price_final = ($item['price']*(100-$product['discount_percent']))/100;
                        $product->save();
                    }
                    $importProduct = new StockReceivedDocketProduct();
                    $importProduct->stock_received_docket_id = $id;
                    $importProduct->product_id = $item['product_id'];
                    $importProduct->quantity = $item['quantity'];
                    $importProduct->price = $item['price_purchase'];
                    $importProduct->save();
                    
                    $importProductId = $importProduct->id;
    
                    foreach ($request->inventories as $inventory) {
                        if ($inventory['product_id'] == $item['product_id']) {
                            $importProductDetail = new StockReceivedDocketProductDetail();
                            $importProductDetail->stock_received_docket_product_id = $importProductId;
                            $importProductDetail->product_id = $inventory['product_id'];
                            $importProductDetail->color_id = $inventory['color_id'];
                            $importProductDetail->size_id = $inventory['size_id'];
                            $importProductDetail->quantity = $inventory['quantity'];
                            $importProductDetail->save();
                        }
                    }
                }
    
                foreach($request->inventories as $inventory) {
                    $inputDate = $request->date;
                    $carbonDate = Carbon::parse($inputDate);
                    $currentMonthYear = $carbonDate->format('Ym');
    
                    $existingInventory = Inventory::where(['month_year' => $currentMonthYear, 
                            'product_id' => $inventory['product_id'], 'color_id' => $inventory['color_id'], 
                            'size_id' => $inventory['size_id']])->first();
    
                    if(!$existingInventory) {
                        $importInventory = new Inventory();
                        $importInventory->month_year = $currentMonthYear;
                        $importInventory->product_id = $inventory['product_id'];
                        $importInventory->color_id = $inventory['color_id'];
                        $importInventory->size_id = $inventory['size_id'];
                        $importInventory->total_initial = $existingInventory['total_initial'];
                        $importInventory->total_import = $inventory['quantity'];
                        $importInventory->total_export = 0;
                        $importInventory->total_final = $inventory['quantity'];
                        $importInventory->save();
                    } 
                    else {
                        Inventory::where(['month_year' => $currentMonthYear, 
                            'product_id' => $inventory['product_id'], 'color_id' => $inventory['color_id'], 
                            'size_id' => $inventory['size_id']])->update([
                                'total_import' => $existingInventory['total_import'] + $inventory['quantity'],
                                'total_final' => $existingInventory['total_final'] + $inventory['quantity'],
                            ]);
                    }
                }
                return response()->json([
                    'success' => 'success',
                    'message' => "Phiếu nhập được cập nhật thành công",
                ]); 
            } else {
                $orderIdsString = implode(', ', $orderIds);
                return response()->json([
                    'success' => 'warning',
                    'message' => "Không thể sửa phiếu nhập hàng, do khách hàng đã đặt. Bạn cần hủy đơn hàng " .$orderIdsString.".",
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
