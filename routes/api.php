<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\BrandsController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\StatisticalsController;
use App\Http\Controllers\Admin\StockReceivedDocketsController;

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

Route::prefix('/admin/import/stock-received-docket')->group( function() {
    Route::post('/add/{id}', [StockReceivedDocketsController::class, 'store']);
});
Route::apiResource('/admin/import/stock-received-docket', App\Http\Controllers\Admin\StockReceivedDocketsController::class);

Route::apiResource('/admin/permissions', App\Http\Controllers\Admin\PermissionsController::class);

Route::apiResource('/admin/staffs', App\Http\Controllers\Admin\StaffsController::class);
Route::delete('/admin/staffs', [App\Http\Controllers\Admin\StaffsController::class, 'destroyIds']);
Route::put('admin/staffs/{id}/{status}', [App\Http\Controllers\Admin\StaffsController::class, 'updateStaffStatus']);

Route::prefix('/admin/categories')->group( function() {
    Route::get('/add',[CategoriesController::class, 'create']);   
    Route::put('{id}/{status}', [CategoriesController::class, 'updateCategoryStatus']);
    Route::delete('/', [CategoriesController::class, 'destroyIds']);
});
Route::apiResource('/admin/categories', CategoriesController::class);

Route::apiResource('/admin/brands', BrandsController::class);
Route::delete('/admin/brands', [BrandsController::class, 'destroyIds']);

Route::prefix('/admin/products')->group( function() {
    Route::get('/',[ProductsController::class, 'index']); 
    Route::get('/all',[ProductsController::class, 'listingAll']);
    Route::get('/type',[ProductsController::class, 'type']); 
    Route::get('/sizes',[ProductsController::class, 'sizeAll']);
    Route::get('/colors',[ProductsController::class, 'colorAll']);
    Route::get('/{url}',[ProductsController::class, 'listing']);
    Route::get('/detail/{id}',[ProductsController::class, 'detail']);
    Route::get('/get-inventory/product-{product}/size-{size}',[ProductsController::class, 'getInventory']); 
    Route::post('/add-image',[ProductsController::class, 'addImage']);
    Route::post('/add-size',[ProductsController::class, 'addSize']);
    Route::put('{id}/{status}', [ProductsController::class, 'updateProductStatus']);
    Route::delete('/delete-image/{id}',[ProductsController::class, 'deleteImage']);
    Route::delete('/delete-size/{id}',[ProductsController::class, 'deleteSize']);

});

Route::apiResource('/admin/products', ProductsController::class);

Route::apiResource('/admin/orders', OrdersController::class);
Route::put('admin/orders/{staff}/{id}/{status}', [OrdersController::class, 'updateOrderStatus']);

Route::apiResource('/admin/statisticals', StatisticalsController::class);

Route::apiResource('/admin/invoices', App\Http\Controllers\Admin\InvoicesController::class);

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
    Route::post('/add-to-cart/{id}',[App\Http\Controllers\User\FavoritesController::class, 'addToCart']); 
    Route::get('/{id}',[App\Http\Controllers\User\FavoritesController::class, 'index']); 
    Route::put('{id}/{quantity}', [App\Http\Controllers\User\FavoritesController::class, 'updateQuantity']);
    Route::delete('/{id}',[App\Http\Controllers\User\FavoritesController::class, 'destroy']); 
    Route::delete('/delete-by-user/{user}/{product}',[App\Http\Controllers\User\FavoritesController::class, 'destroyByUser']); 
});

Route::get('/addresses/cities',[App\Http\Controllers\User\AddressesController::class, 'getCities']);
Route::apiResource('/admin/payment-methods', App\Http\Controllers\Admin\PaymentMethodsController::class);

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
Route::put('orders/purchase/cancle-{id}', [App\Http\Controllers\User\OrdersController::class, 'cancleOrder']);
Route::put('orders/purchase/receipt-{id}', [App\Http\Controllers\User\OrdersController::class, 'receiptOrder']);

Route::put('user/update-profile/{id}', [App\Http\Controllers\User\UsersController::class, 'updateProfile']);
Route::put('user/update-password/{id}', [App\Http\Controllers\User\UsersController::class, 'updatePassword']);
Route::post('reviews/add',[App\Http\Controllers\User\ReviewsController::class, 'store']);

Route::get('admin/reviews',[App\Http\Controllers\Admin\ReviewsController::class, 'index']);
Route::put('admin/reviews/{id}/{status}', [App\Http\Controllers\Admin\ReviewsController::class, 'updateReviewStatus']);

Route::post('register', [App\Http\Controllers\User\AuthController::class, 'register']);
Route::post('admin/login', [App\Http\Controllers\Admin\AuthController::class, 'login']);
Route::post('admin/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('login', [App\Http\Controllers\User\AuthController::class, 'login']);
Route::post('logout', [App\Http\Controllers\User\AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
