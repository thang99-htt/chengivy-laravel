<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\BrandsController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\StatisticalsController;
use App\Http\Controllers\Admin\StockReceivedDocketsController;
use App\Http\Controllers\Admin\VouchersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResource('/admin/roles', App\Http\Controllers\Admin\RolesController::class);
Route::delete('admin/roles/', [App\Http\Controllers\Admin\RolesController::class, 'destroyIds']);

Route::apiResource('/admin/suppliers', App\Http\Controllers\Admin\SuppliersController::class);
Route::apiResource('/admin/payment-vouchers', App\Http\Controllers\Admin\PaymentVouchersController::class);

Route::apiResource('/admin/import/stock-received-docket', App\Http\Controllers\Admin\StockReceivedDocketsController::class);

Route::apiResource('/admin/permissions', App\Http\Controllers\Admin\PermissionsController::class);

Route::apiResource('/admin/staffs', App\Http\Controllers\Admin\StaffsController::class);
Route::delete('/admin/staffs', [App\Http\Controllers\Admin\StaffsController::class, 'destroyIds']);
Route::put('admin/staffs/{id}/{status}', [App\Http\Controllers\Admin\StaffsController::class, 'updateStaffStatus']);

Route::get('/admin/customers/filter-ghost',[App\Http\Controllers\Admin\CustomersController::class, 'filterGhost']);   
Route::apiResource('/admin/customers', App\Http\Controllers\Admin\CustomersController::class);

Route::prefix('/admin/categories')->group( function() {
    Route::get('/add',[CategoriesController::class, 'create']);   
    Route::put('{id}/{status}', [CategoriesController::class, 'updateCategoryStatus']);
    Route::delete('/', [CategoriesController::class, 'destroyIds']);
});
Route::apiResource('/admin/categories', CategoriesController::class);

Route::apiResource('/admin/brands', BrandsController::class);
Route::delete('/admin/brands', [BrandsController::class, 'destroyIds']);
Route::get('/admin/vouchers/voucher-by-user-{id}', [VouchersController::class, 'voucherByUser']);
Route::apiResource('/admin/vouchers', VouchersController::class);
Route::delete('admin/vouchers/', [VouchersController::class, 'destroyIds']);

Route::prefix('/admin/products')->group( function() {
    Route::get('/',[ProductsController::class, 'index']); 
    Route::get('/get-products',[ProductsController::class, 'getProducts']); 
    Route::get('/sales',[ProductsController::class, 'getSales']); 
    Route::get('/hiddens',[ProductsController::class, 'getHiddens']); 
    Route::get('/type',[ProductsController::class, 'type']); 
    Route::get('/sizes',[ProductsController::class, 'sizeAll']);
    Route::get('/colors',[ProductsController::class, 'colorAll']);
    Route::get('/inventories',[ProductsController::class, 'getInventories']); 
    Route::get('/detail/{id}',[ProductsController::class, 'detail']);
    Route::post('/add-image',[ProductsController::class, 'addImage']);
    Route::post('/add-size',[ProductsController::class, 'addSize']);
    Route::put('update-product-sale', [ProductsController::class, 'updateProductsSale']);
    Route::put('{id}/{status}', [ProductsController::class, 'updateProductStatus']);
    Route::put('/hidden',[ProductsController::class, 'hiddenProduct']);
    Route::put('/delete',[ProductsController::class, 'deleteProduct']);
    Route::delete('/delete-image/{id}',[ProductsController::class, 'deleteImage']);
    Route::delete('/delete-size/{id}',[ProductsController::class, 'deleteSize']);
});

Route::apiResource('/admin/products', ProductsController::class);

// Route::apiResource('/admin/orders', OrdersController::class);
Route::get('admin/orders/{id}', [OrdersController::class, 'show']);
Route::post('admin/orders/', [OrdersController::class, 'index']);
Route::post('admin/orders/sold-at-store', [OrdersController::class, 'soldAtStore']);
Route::put('admin/orders/update-status', [OrdersController::class, 'updateOrderStatus']);
Route::put('admin/orders/cancel', [OrdersController::class, 'cancelOrder']);

Route::get('/admin/statisticals/get-range-date',[StatisticalsController::class, 'getRangeDate']);

Route::get('/admin/statisticals/get-notification',[StatisticalsController::class, 'getNotification']);
Route::get('/admin/statisticals/get-top-products',[StatisticalsController::class, 'getTopProducts']);
Route::get('/admin/statisticals/get-inventories',[StatisticalsController::class, 'getInventories']);
Route::apiResource('/admin/statisticals', StatisticalsController::class);
Route::post('/admin/statisticals/get-sales',[StatisticalsController::class, 'getSales']);
Route::post('/admin/statisticals/get-orders',[StatisticalsController::class, 'getOrders']);
Route::post('/admin/statisticals/get-products',[StatisticalsController::class, 'getProducts']);
Route::post('/admin/statisticals/send-notification',[StatisticalsController::class, 'sendNotification']);

Route::apiResource('/admin/invoices', App\Http\Controllers\Admin\InvoicesController::class);

Route::apiResource('/admin/statuses', App\Http\Controllers\Admin\StatusesController::class);

Route::prefix('/categories')->group( function() {
    Route::get('/category',[App\Http\Controllers\User\CategoriesController::class, 'category']);
    Route::post('/add/{id}',[App\Http\Controllers\User\CartsController::class, 'store']); 
});

Route::prefix('/cart')->group( function() {
    Route::get('/{id}',[App\Http\Controllers\User\CartsController::class, 'index']); 
    Route::post('/add/{id}',[App\Http\Controllers\User\CartsController::class, 'store']); 
    Route::put('/quantity', [App\Http\Controllers\User\CartsController::class, 'updateQuantity']);
    Route::put('/size-and-color', [App\Http\Controllers\User\CartsController::class, 'updateColorAndSize']);
    Route::delete('/{user}/{product}/{color}/{size}',[App\Http\Controllers\User\CartsController::class, 'destroy']); 
});

Route::prefix('/favorite')->group( function() {
    Route::post('/add/{id}',[App\Http\Controllers\User\FavoritesController::class, 'store']); 
    Route::post('/add-to-cart/{id}',[App\Http\Controllers\User\FavoritesController::class, 'addToCart']); 
    Route::get('/{id}',[App\Http\Controllers\User\FavoritesController::class, 'index']); 
    Route::delete('/{user}/{product}',[App\Http\Controllers\User\FavoritesController::class, 'destroy']); 
});

Route::get('/addresses/cities',[App\Http\Controllers\User\AddressesController::class, 'getCities']);

Route::prefix('/addresses')->group( function() {
    Route::get('/{id}',[App\Http\Controllers\User\AddressesController::class, 'index']);
    Route::get('/getAll/{id}',[App\Http\Controllers\User\AddressesController::class, 'addresses']);
    Route::get('/get-districts/{id}', [App\Http\Controllers\User\AddressesController::class, 'getDistricts']);
    Route::get('/get-wards/{id}', [App\Http\Controllers\User\AddressesController::class, 'getWards']); 
    Route::get('/address-order/{id}',[App\Http\Controllers\User\AddressesController::class, 'addressOrder']); 
    Route::post('/address-add/{id}', [App\Http\Controllers\User\AddressesController::class, 'store']);
    Route::put('/{id}', [App\Http\Controllers\User\AddressesController::class, 'update']);
    Route::put('/set-default/{id}', [App\Http\Controllers\User\AddressesController::class, 'setDefault']);
    Route::delete('/{id}',[App\Http\Controllers\User\AddressesController::class, 'destroy']); 
    Route::apiResource('/orders', App\Http\Controllers\User\OrdersController::class);
});


Route::post('orders/add/{id}', [App\Http\Controllers\User\OrdersController::class, 'store']);
Route::post('orders/add-buy-now/{id}', [App\Http\Controllers\User\OrdersController::class, 'addBuyNow']);
Route::get('orders/purchases/user-{id}', [App\Http\Controllers\User\OrdersController::class, 'purchaseAll']);
Route::get('orders/purchase/order-{id}', [App\Http\Controllers\User\OrdersController::class, 'purchaseShow']);
Route::get('user/{id}', [App\Http\Controllers\User\UsersController::class, 'infoAccount']);
Route::put('orders/purchase/cancel-{id}', [App\Http\Controllers\User\OrdersController::class, 'cancelOrder']);
Route::put('orders/purchase/receipt-{id}', [App\Http\Controllers\User\OrdersController::class, 'receiptOrder']);

Route::put('user/update-profile/{id}', [App\Http\Controllers\User\UsersController::class, 'updateProfile']);
Route::put('user/update-password/{id}', [App\Http\Controllers\User\UsersController::class, 'updatePassword']);

Route::get('admin/reviews/{id}',[App\Http\Controllers\Admin\ReviewsController::class, 'show']);
Route::post('admin/reviews/all',[App\Http\Controllers\Admin\ReviewsController::class, 'index']);
Route::post('admin/reviews/',[App\Http\Controllers\Admin\ReviewsController::class, 'store']);
Route::put('admin/reviews/user-{id}', [App\Http\Controllers\Admin\ReviewsController::class, 'updateReviewStatus']);
Route::put('admin/reviews/hidden', [App\Http\Controllers\Admin\ReviewsController::class, 'hiddenIds']);
Route::put('admin/reviews/{id}', [App\Http\Controllers\Admin\ReviewsController::class, 'update']);

Route::put('returns/{id}', [App\Http\Controllers\User\ReturnsController::class, 'cancelReturn']);
Route::apiResource('/returns', App\Http\Controllers\User\ReturnsController::class);

Route::get('admin/returns/{id}',[App\Http\Controllers\Admin\ReturnsController::class, 'show']);
Route::post('admin/returns/all',[App\Http\Controllers\Admin\ReturnsController::class, 'index']);
Route::put('admin/returns/update-status', [App\Http\Controllers\Admin\ReturnsController::class, 'updateReturnStatus']);

Route::post('admin/notifications/reviews/{id}', [App\Http\Controllers\Admin\NotificationsController::class, 'storeReview']);

Route::post('/upload-image',[App\Http\Controllers\User\UploadImageController::class, 'store']);

Route::post('register', [App\Http\Controllers\User\AuthController::class, 'register']);
Route::post('admin/login', [App\Http\Controllers\Admin\AuthController::class, 'login']);
Route::post('admin/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('login', [App\Http\Controllers\User\AuthController::class, 'login']);
Route::post('logout', [App\Http\Controllers\User\AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

