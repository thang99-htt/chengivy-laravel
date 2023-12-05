<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Size;
use App\Models\ProductImage;
use App\Models\Inventory;
use App\Models\Color;
use Intervention\Image\Facades\Image;
use App\Http\Resources\ProductResource;
use Carbon\Carbon;

use App\Models\User;
use App\Jobs\SendMailProductsWithDiscount;

use Illuminate\Support\Facades\Validator;
use App\Jobs\UploadToGoogleDrive;
use App\Jobs\DeleteFromGoogleDrive;

class ProductsController extends Controller
{
    public function index()
    {
        $products = Product::with('category','brand', 'product_image', 'inventories.size', 
            'reviews.review_image')->orderBy('deleted_at', 'ASC')->orderBy('created_at', 'DESC')->get();
        return response()->json(ProductResource::collection($products));
    }

    public function getProducts()
    {
        // Fetch products with related data using eager loading
        $products = Product::with(
            'category',
            'brand',
            'product_image',
            'inventories.size',
            'reviews.review_image'
        )
        ->whereHas('inventories', function ($query) {
            // Filter products that have non-empty inventories
            $query->where('total_final', '>', 0);
        })
        ->where('status', 1) // Thêm điều kiện status là 1
        ->whereNull('deleted_at') // Thêm điều kiện deleted_at là null
        ->orderBy('created_at', 'DESC')
        ->get();

        // Return the fetched products as a JSON response using the ProductResource collection
        return response()->json(ProductResource::collection($products));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'images' => 'required',
            'price' => 'required',
            'discount_percent' => 'max:2',
            'description' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'description.required' => 'Vui lòng nhập mô tả.',
            'price.required' => 'Vui lòng nhập giá.',
            'discount_percent.max' => 'Phần trăm giảm giá có nhiều nhất 2 chữ số.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'brand_id.required' => 'Vui lòng chọn thương hiệu.',
            'images.required' => 'Vui lòng thêm các hình ảnh.',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);

        } else {
            $product = new Product;
            $product->category_id = $request['category_id'];
            $product->brand_id = $request['brand_id'];
            $product->name = $request['name'];
            $product->description = $request['description'];
            $product->price = $request['price'];
    
            if ($request['discount_percent']) {
                $product->discount_percent = $request['discount_percent'];
                $product->price_final = $request['price'] - ($request['price'] * $request['discount_percent']) / 100;
            } else {
                $product->discount_percent = 0;
                $product->price_final = $request['price'];
            }
    
            $product->save();
            $productId = $product->id;
            $productCat = $product->category_id;
            $productBra = $product->brand_id;
    
            $imagesData = $request['images'];
    
            if ($imagesData) {
                foreach ($imagesData as $colorItem) {
                    $colorId = $colorItem['color_id'];
    
                    foreach ($colorItem['items'] as $imageItem) {
                        $base64Image = $imageItem['image'];
                        $colorId = $colorItem['color_id'];
    
                        $strpos = strpos($base64Image, ';');
                        $sub = substr($base64Image, 0, $strpos);
                        $ex = explode("/", $sub)[1];
                        $imageName = $productId.$productCat.$productBra.$colorId.uniqid().".".$ex;
                        $img = Image::make($base64Image);
                        $upload_path = public_path()."/storage/uploads/products/";
                        $img->save($upload_path.$imageName);

                        $productImage = new ProductImage();
                        $productImage->product_id = $productId;
                        $productImage->color_id = $colorId;
                        $productImage->image = "http://localhost:8000/storage/uploads/products/".$imageName;
                        $productImage->save();
                        
                    }
                }
            }
            return response()->json($product);
        }
    }

    public function show($id)
    {
        $product = Product::find($id);
        return response()->json($product);
    }

    public function update($id, Request $request)
    {
        $product = Product::find($id);
        $product->category_id = $request['category_id'];
        $product->brand_id = $request['brand_id'];
        $product->name = $request['name'];
        $product->description = $request['description'];
        $product->price = $request['price'];

        if ($request['discount_percent']) {
            $product->discount_percent = $request['discount_percent'];
            $product->price_final = $request['price'] - ($request['price'] * $request['discount_percent']) / 100;
        } else {
            $product->discount_percent = 0;
            $product->price_final = $request['price'];
        }

        $product->save();
        $productId = $product->id;
        $productCat = $product->category_id;
        $productBra = $product->brand_id;

        $existingImages = [];
        $existingColorIds = [];
        $newImages = [];  
        $removedImages = [];

        $imagesData = $request['images'];
        if ($imagesData) {
            // Assuming you already have $productId defined
            $existingImages = ProductImage::where(['product_id' => $productId])->get();
            
            // Extract existing color_ids from $existingImages
            $existingColorIds = $existingImages->pluck('color_id')->toArray();

            // Filter imagesData to include only images with color_ids not in existingColorIds
            $newImages = array_filter($imagesData, function ($colorItem) use ($existingColorIds) {
                return !in_array($colorItem['color_id'], $existingColorIds);
            });

            // Find images in existingImages that are not in newImages, excluding common ones
            $removedImages = $existingImages->filter(function ($existingImage) use ($imagesData) {
                return !in_array($existingImage->color_id, array_column($imagesData, 'color_id'));
            });

            if ($newImages) { 
                foreach ($newImages as $colorItem) {
                    $colorId = $colorItem['color_id'];
                    foreach ($colorItem['items'] as $imageItem) {
                        $base64Image = $imageItem['image'];    
                        // Enqueue job to upload image
                        $strpos = strpos($base64Image, ';');
                        $sub = substr($base64Image, 0, $strpos);
                        $ex = explode("/", $sub)[1];
                        $imageName = $productId.$productCat.$productBra.$colorId.uniqid().".".$ex;
                        $img = Image::make($base64Image);
                        $upload_path = public_path()."/storage/uploads/products/";
                        $img->save($upload_path.$imageName);

                        $productImage = new ProductImage();
                        $productImage->product_id = $productId;
                        $productImage->color_id = $colorId;
                        $productImage->image = "http://localhost:8000/storage/uploads/products/".$imageName;
                        $productImage->save();
                    }
                }
            }

            if ($removedImages) {
                foreach ($removedImages as $image) {
                    $productImage = ProductImage::where('image', $image->image)->first();
                    $imageLink = $image->image;
                    // Remove the base URL from the image link
                    $baseURL = "http://localhost:8000/storage/uploads/products/";
                    $relativePath = str_replace($baseURL, '', $imageLink);
                    
                    $storagePath = public_path("/storage/uploads/products/");
                    $imageName = basename($imageLink);
                    $imagePath = $storagePath . $relativePath;

                    // Check if the file exists before attempting to delete
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                    $productImage->delete();
                }
            }
        }

        return response()->json([
            'success' => 'success',
            'message' => "Cập nhật thành công",
        ]);
    }

    public function updateProductStatus($id, Request $request) {
        $product = Product::find($id);
        if($request->status == 0) {
            $product->status = 1;
        } else {
            $product->status = 0;
        }
        $product->save();
        
        return response()->json([
            'success' => true,
            'product' => $product,
        ]);
    }

    public function updateHiddens(Request $request)
    {
        $selectedIds = $request->all(); // Lấy danh sách selectedIds từ request
        $products = Product::whereIn('id', $selectedIds['data'])->get(); // Sử dụng whereIn để lấy các bản ghi tương ứng với selectedIds
        foreach($products as $product) {
            if($product['status'] == 1)
                $product->status = 0;
            else $product->status = 1;
            $product->save();
        }      
        return response()->json($selectedIds, 200);
    }

    public function deleteProduct(Request $request)
    {
        $selectedIds = $request->all(); // Lấy danh sách selectedIds từ request
        $products = Product::whereIn('id', $selectedIds['data'])->get(); // Sử dụng whereIn để lấy các bản ghi tương ứng với selectedIds
        foreach($products as $product) {
            $product->deleted_at = Carbon::now('Asia/Ho_Chi_Minh');
            $product->save();
        }      
        return response()->json(['success'=>'true'], 200);
    }

    public function sizeAll()
    {
        $sizes = Size::get();
        return response()->json($sizes);
    }
    
    public function colorAll()
    {
        $colors = Color::get();
        return response()->json($colors);
    }

    public function type()
    {        
        $newProducts = Product::with('category', 'product_image', 'brand')->orderBy('created_at','DESC')->limit(4)->get();

        // Special Products
        $specialNewProduct = Product::with('category', 'brand')->where('brand_id', 3)->orderBy('created_at','asc')->limit(1)->get();
        $specialHighestPriceProduct = Product::with('category')->where('brand_id', 3)->orderBy('price','desc')->limit(1)->get();
        $bestSellerProducts = Product::with('category', 'brand')->orderBy('created_at','desc')->limit(8)->inRandomOrder()->get();

        return response()->json([
            'newProducts' => ProductResource::collection($newProducts),
            'specialNewProduct' => ProductResource::collection($specialNewProduct),
            'specialHighestPriceProduct' => ProductResource::collection($specialHighestPriceProduct),
            'bestSellerProducts' => ProductResource::collection($bestSellerProducts),
        ]);
    }

    public function detail($id) {
        $maxMonthYear = Inventory::where('product_id', $id)->orderBy('month_year', 'desc')->first();

        if ($maxMonthYear) {
            $maxMonthYear = $maxMonthYear->month_year;
    
            $product = Product::with(['category','brand', 'product_image', 
                'inventories' => function ($query) use ($maxMonthYear) {
                    $query->where('month_year', $maxMonthYear);
                }, 'reviews.review_image'])
                ->where('status', 1)->find($id);
        } else {
            $product = Product::with(['category','brand', 'product_image', 'reviews.review_image'])
            ->where('status', 1)->find($id);
        }
        return response()->json(new ProductResource($product));
    }

    
    public function getInventories()
    {
        $products = Product::with(['product_image'])
            ->orderBy('created_at', 'DESC')
            ->get();

        $filteredProducts = [];
        foreach ($products as $product) {
            $totalImport = $product->inventories->sum('total_import');
            $product->total_import = $totalImport;

            $totalExport = $product->inventories->sum('total_export');
            $product->total_export = $totalExport;
            
            $firstImage = $product->product_image->first();
            if ($firstImage) {
                $product->image = $firstImage->image;
            }

            $inventoryEntries = Inventory::with('size', 'color')
                ->where('product_id', $product->id)
                ->orderBy('color_id')
                ->orderBy('size_id')
                ->get();

            // Tạo mảng để lưu trữ tổng cộng total_import và total_export
            $inventoryTotals = [];

            // Khởi tạo biến để theo dõi tháng lớn nhất và total_final tương ứng
            $maxMonthYear = '';
            $maxTotalFinal = 0;
            $totalFinal = 0;

            foreach ($inventoryEntries as $inventoryEntry) {
                $colorId = $inventoryEntry->color->id;
                $sizeId = $inventoryEntry->size->id;
                $monthYear = $inventoryEntry->month_year;

                // Tạo khóa duy nhất cho mỗi cặp color_id và size_id
                $key = $colorId . '-' . $sizeId;

                if (!isset($inventoryTotals[$key])) {
                    $inventoryTotals[$key] = [
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'color_id' => $colorId,
                        'size_id' => $sizeId,
                        'month_year' => $monthYear,
                        'total_import' => 0,
                        'total_export' => 0,
                        'total_final' => 0,
                        'color' => $inventoryEntry->color,
                        'size' => $inventoryEntry->size,
                    ];
                }

                // Cộng dữ liệu từ các bản ghi cùng color_id và size_id
                $inventoryTotals[$key]['total_import'] += $inventoryEntry->total_import;
                $inventoryTotals[$key]['total_export'] += $inventoryEntry->total_export;

                // Cập nhật total_final nếu month_year lớn hơn
                if ($monthYear > $inventoryTotals[$key]['month_year']) {
                    $inventoryTotals[$key]['month_year'] = $monthYear;
                    $inventoryTotals[$key]['total_final'] = $inventoryEntry->total_final;
                }

                // Cập nhật tháng lớn nhất và total_final tương ứng
                if ($monthYear > $maxMonthYear) {
                    $maxMonthYear = $monthYear;
                    $maxTotalFinal = $inventoryEntry->total_final;
                    $totalFinal += $inventoryEntry->total_final;
                }
            }

            // Chuyển mảng kết quả về dạng dấu vết
            $inventoryTotals = array_values($inventoryTotals);

            // Gán kết quả cho thuộc tính filtered_inventories
            $product->filtered_inventories = $inventoryTotals;

            // Gán tháng lớn nhất và total_final tương ứng
            $product->maxMonthYear = $maxMonthYear;
            $product->maxTotalFinal = $maxTotalFinal;
            
            $product->total_final = $totalFinal;

            if (count($inventoryTotals) > 0) {
                $filteredProducts[] = $product;
            }
        }

        return response()->json($filteredProducts);
    }

    public function addSize(Request $request) {
        $size = Inventory::where(['product_id' => $request['id'], 'size_id' => $request['size']])->first();
        if(!$size) {
            $productsize = new Inventory; 
            $productsize->product_id = $request['id'];
            $productsize->size_id =  $request['size'];
            $productsize->quantity =  $request['quantity'];
            $productsize->stock =  $request['stock'];
            $productsize->save();
            return response()->json(true);
        }
        return response()->json(false);

    }

    public function deleteSize(Request $request)
    {
        Inventory::where('id', $request->id)->delete();
        return response()->json(['success'=>'true'], 200);
    }


    public function getSales()
    {
        $product = Product::with('category', 'brand', 'product_image', 
            'inventories.size', 'reviews.review_image')
            ->where('discount_percent', '>', 0)
            ->orderBy('created_at', 'DESC')
            ->get();
        return response()->json(ProductResource::collection($product));
    }

    public function updateProductsSale(Request $request)
    {
        $products = $request->all();

        // Lấy danh sách sản phẩm có khuyến mãi
        $productsWithDiscount = [];
        foreach ($products as $item) {
            $product = Product::find($item['id']);
            $product->price = $item['price'];
            $product->discount_percent = $item['discount_percent'];
            $product->price_final = $item['price_final'];
            $product->save();

            
            // Kiểm tra nếu sản phẩm này có trong bảng favorite của người dùng
            $users = User::whereHas('favorites', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })->get();
            
            foreach ($users as $user) {
                // Thêm sản phẩm vào danh sách sản phẩm khuyến mãi nếu chưa có trong danh sách
                if (!in_array($product, $productsWithDiscount)) {
                    $imageProduct = $product->product_image->first();
                    $product->image = $imageProduct ? $imageProduct->image : null;
                    if($product->discount_percent>0) {
                        $productsWithDiscount[] = $product;
                    }
                }
            }
        }

        // Tạo một mảng để lưu thông tin sản phẩm yêu thích của từng người dùng
    $usersFavoriteProducts = [];

    // Tìm sản phẩm yêu thích của từng người dùng
    foreach ($productsWithDiscount as $productWithDiscount) {
        $usersWithFavoriteProduct = User::whereHas('favorites', function ($query) use ($productWithDiscount) {
            $query->where('product_id', $productWithDiscount->id);
        })->get();

        // Lưu thông tin sản phẩm vào mảng $usersFavoriteProducts
        foreach ($usersWithFavoriteProduct as $user) {
            if (!isset($usersFavoriteProducts[$user->id])) {
                $usersFavoriteProducts[$user->id] = [
                    'user' => $user,
                    'products' => [],
                ];
            }
            $usersFavoriteProducts[$user->id]['products'][] = $productWithDiscount;
        }
    }

    // Gửi email cho từng người dùng với danh sách sản phẩm yêu thích của họ
    foreach ($usersFavoriteProducts as $userData) {
        $user = $userData['user'];
        $favoriteProducts = $userData['products'];

        SendMailProductsWithDiscount::dispatch($user->name, $user->email, $favoriteProducts);
    }

        return response()->json(['success' => true], 200);
    }


    public function getHiddens()
    {
        $product = Product::with('category', 'brand', 'product_image', 
            'inventories.size', 'reviews.review_image')
            ->where('status', '=', 0)
            ->where('deleted_at', '=', null)
            ->orderBy('created_at', 'DESC')
            ->get();
        return response()->json(ProductResource::collection($product));
    }
}
