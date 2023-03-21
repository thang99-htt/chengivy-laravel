<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Back\CategoriesController;
use App\Http\Controllers\Back\ProductsController;
use App\Http\Controllers\Back\OrdersController;


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

Route::apiResource('/admin/roles', App\Http\Controllers\Back\RolesController::class);
Route::delete('admin/roles/', [App\Http\Controllers\Back\RolesController::class, 'destroyAll']);

// Route::get('/admin/categories/add',  [App\Http\Controllers\Back\CategoriesController::class, 'create']);
Route::prefix('/admin/categories')->group( function() {
    Route::get('/add',[CategoriesController::class, 'create']);   
    Route::put('{id}/{status}', [CategoriesController::class, 'updateCategoryStatus']);
});
Route::apiResource('/admin/categories', CategoriesController::class);

Route::prefix('/admin/products')->group( function() {
    Route::get('/add',[ProductsController::class, 'create']);
    Route::get('/view/{id}',[ProductsController::class, 'view']);
    Route::post('/add-image',[ProductsController::class, 'addImage']);
    Route::delete('/delete-image/{id}',[ProductsController::class, 'deleteImage']);
    Route::post('/add-size',[ProductsController::class, 'addSize']);
    Route::delete('/delete-size/{id}',[ProductsController::class, 'deleteSize']);
    Route::put('{id}/{status}', [ProductsController::class, 'updateProductStatus']);
});
Route::apiResource('/admin/products', ProductsController::class);

Route::apiResource('/admin/orders', OrdersController::class);
Route::put('admin/orders/{id}/{status}', [OrdersController::class, 'updateOrderStatus']);

Route::prefix('/categories')->group( function() {
    Route::get('/category',[App\Http\Controllers\Front\CategoriesController::class, 'category']);
});

Route::prefix('/products')->group( function() {
    Route::get('/',[App\Http\Controllers\Front\ProductsController::class, 'index']); 
    Route::get('/all',[App\Http\Controllers\Front\ProductsController::class, 'listingAll']);
    Route::get('/type',[App\Http\Controllers\Front\ProductsController::class, 'type']); 
    Route::get('/{url}',[App\Http\Controllers\Front\ProductsController::class, 'listing']);
    Route::get('/detail/{id}',[App\Http\Controllers\Front\ProductsController::class, 'detail']);
    Route::get('/get-stock/product-{product}/size-{size}',[App\Http\Controllers\Front\ProductsController::class, 'getStock']); 
});

Route::prefix('/cart')->group( function() {
    Route::post('/add/{id}',[App\Http\Controllers\Front\CartsController::class, 'store']); 
    Route::get('/{id}',[App\Http\Controllers\Front\CartsController::class, 'index']); 
    Route::put('{id}/{quantity}', [App\Http\Controllers\Front\CartsController::class, 'updateQuantity']);
    Route::delete('/{id}',[App\Http\Controllers\Front\CartsController::class, 'destroy']); 
});

Route::apiResource('/admin/payment-methods', App\Http\Controllers\Back\PaymentMethodsController::class);

Route::prefix('/addresses')->group( function() {
    Route::get('/cities',[App\Http\Controllers\Front\AddressesController::class, 'getCities']);
    Route::post('/get-districts/{id}', [App\Http\Controllers\Front\AddressesController::class, 'getDistricts']);
    Route::post('/get-wards/{id}', [App\Http\Controllers\Front\AddressesController::class, 'getWards']); 
    Route::get('/{id}',[App\Http\Controllers\Front\AddressesController::class, 'addresses']);  // Find all address by user_id
    Route::get('/address-order/{id}',[App\Http\Controllers\Front\AddressesController::class, 'addressOrder']); 
    Route::post('/address-add/{id}', [App\Http\Controllers\Front\AddressesController::class, 'store']);
});


Route::apiResource('/orders', App\Http\Controllers\Front\OrdersController::class);
Route::post('orders/add/{id}', [App\Http\Controllers\Front\OrdersController::class, 'store']);
Route::get('orders/purchases/user-{id}', [App\Http\Controllers\Front\OrdersController::class, 'purchaseAll']);
Route::get('orders/purchase/order-{id}', [App\Http\Controllers\Front\OrdersController::class, 'purchaseShow']);
Route::put('orders/purchase/cancle-{id}', [App\Http\Controllers\Front\OrdersController::class, 'cancleOrder']);
Route::put('orders/purchase/receipt-{id}', [App\Http\Controllers\Front\OrdersController::class, 'receiptOrder']);

Route::post('admin/login', [App\Http\Controllers\Back\AuthController::class, 'login']);
Route::post('admin/logout', [App\Http\Controllers\Back\AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('register', [App\Http\Controllers\Front\AuthController::class, 'register']);
Route::post('login', [App\Http\Controllers\Front\AuthController::class, 'login']);
Route::post('logout', [App\Http\Controllers\Front\AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
