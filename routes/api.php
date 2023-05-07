<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\StatisticalsController;


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
Route::delete('admin/roles/', [App\Http\Controllers\Admin\RolesController::class, 'destroyAll']);

Route::apiResource('/admin/suppliers', App\Http\Controllers\Admin\SuppliersController::class);
Route::apiResource('/admin/payment-vouchers', App\Http\Controllers\Admin\PaymentVouchersController::class);
Route::apiResource('/admin/import-coupons', App\Http\Controllers\Admin\ImportCouponsController::class);
Route::post('admin/import-coupons/add/{id}', [App\Http\Controllers\Admin\ImportCouponsController::class, 'store']);

Route::apiResource('/admin/permissions', App\Http\Controllers\Admin\PermissionsController::class);

Route::apiResource('/admin/staffs', App\Http\Controllers\Admin\StaffsController::class);
Route::put('admin/staffs/{id}/{status}', [App\Http\Controllers\Admin\StaffsController::class, 'updateStaffStatus']);

Route::prefix('admin/authorization')->group( function() {
    Route::get('/by-role', [App\Http\Controllers\Admin\AuthorizationController::class, 'authorizationByRole']);
    Route::get('/by-staff', [App\Http\Controllers\Admin\AuthorizationController::class, 'authorizationByStaff']);
    Route::get('/by-staff/{id}', [App\Http\Controllers\Admin\AuthorizationController::class, 'getStaff']);
    Route::get('/role-staff/{id}', [App\Http\Controllers\Admin\AuthorizationController::class, 'getRoleStaff']);
    Route::post('/role-staff', [App\Http\Controllers\Admin\AuthorizationController::class, 'storeRoleStaff']);
    Route::get('/permission-role/{id}', [App\Http\Controllers\Admin\AuthorizationController::class, 'getPermssionRole']);
    Route::post('/permission-role', [App\Http\Controllers\Admin\AuthorizationController::class, 'storePermssionRole']);
});

Route::prefix('/admin/categories')->group( function() {
    Route::get('/add',[CategoriesController::class, 'create']);   
    Route::put('{id}/{status}', [CategoriesController::class, 'updateCategoryStatus']);
});
Route::apiResource('/admin/categories', CategoriesController::class);

Route::prefix('/admin/products')->group( function() {
    Route::get('/add',[ProductsController::class, 'create']);
    Route::get('/view/{id}',[ProductsController::class, 'view']);
    Route::get('/sizes',[ProductsController::class, 'sizeAll']);
    Route::post('/add-image',[ProductsController::class, 'addImage']);
    Route::delete('/delete-image/{id}',[ProductsController::class, 'deleteImage']);
    Route::post('/add-size',[ProductsController::class, 'addSize']);
    Route::delete('/delete-size/{id}',[ProductsController::class, 'deleteSize']);
    Route::put('{id}/{status}', [ProductsController::class, 'updateProductStatus']);
});

Route::apiResource('/admin/products', ProductsController::class);

Route::apiResource('/admin/orders', OrdersController::class);
Route::put('admin/orders/{staff}/{id}/{status}', [OrdersController::class, 'updateOrderStatus']);

Route::apiResource('/admin/statisticals', StatisticalsController::class);

Route::apiResource('/admin/invoices', App\Http\Controllers\Admin\InvoicesController::class);

Route::prefix('/categories')->group( function() {
    Route::get('/category',[App\Http\Controllers\User\CategoriesController::class, 'category']);
});

Route::prefix('/products')->group( function() {
    Route::get('/',[App\Http\Controllers\User\ProductsController::class, 'index']); 
    Route::get('/all',[App\Http\Controllers\User\ProductsController::class, 'listingAll']);
    Route::get('/type',[App\Http\Controllers\User\ProductsController::class, 'type']); 
    Route::get('/{url}',[App\Http\Controllers\User\ProductsController::class, 'listing']);
    Route::get('/detail/{id}',[App\Http\Controllers\User\ProductsController::class, 'detail']);
    Route::get('/get-stock/product-{product}/size-{size}',[App\Http\Controllers\User\ProductsController::class, 'getStock']); 
});

Route::prefix('/cart')->group( function() {
    Route::post('/add/{id}',[App\Http\Controllers\User\CartsController::class, 'store']); 
    Route::get('/{id}',[App\Http\Controllers\User\CartsController::class, 'index']); 
    Route::put('{id}/{quantity}', [App\Http\Controllers\User\CartsController::class, 'updateQuantity']);
    Route::delete('/{id}',[App\Http\Controllers\User\CartsController::class, 'destroy']); 
});

Route::apiResource('/admin/payment-methods', App\Http\Controllers\Admin\PaymentMethodsController::class);

Route::prefix('/addresses')->group( function() {
    Route::get('/cities',[App\Http\Controllers\User\AddressesController::class, 'getCities']);
    Route::post('/get-districts/{id}', [App\Http\Controllers\User\AddressesController::class, 'getDistricts']);
    Route::post('/get-wards/{id}', [App\Http\Controllers\User\AddressesController::class, 'getWards']); 
    Route::get('/{id}',[App\Http\Controllers\User\AddressesController::class, 'addresses']);  // Find all address by user_id
    Route::get('/address-order/{id}',[App\Http\Controllers\User\AddressesController::class, 'addressOrder']); 
    Route::post('/address-add/{id}', [App\Http\Controllers\User\AddressesController::class, 'store']);
});


Route::apiResource('/orders', App\Http\Controllers\User\OrdersController::class);
Route::post('orders/add/{id}', [App\Http\Controllers\User\OrdersController::class, 'store']);
Route::get('orders/purchases/user-{id}', [App\Http\Controllers\User\OrdersController::class, 'purchaseAll']);
Route::get('orders/purchase/order-{id}', [App\Http\Controllers\User\OrdersController::class, 'purchaseShow']);
Route::put('orders/purchase/cancle-{id}', [App\Http\Controllers\User\OrdersController::class, 'cancleOrder']);
Route::put('orders/purchase/receipt-{id}', [App\Http\Controllers\User\OrdersController::class, 'receiptOrder']);

Route::get('user/{id}', [App\Http\Controllers\User\UsersController::class, 'infoAccount']);
Route::put('user/update-profile/{id}', [App\Http\Controllers\User\UsersController::class, 'updateProfile']);

Route::get('admin/reviews',[App\Http\Controllers\Admin\ReviewsController::class, 'index']);
Route::put('admin/reviews/{id}/{status}', [App\Http\Controllers\Admin\ReviewsController::class, 'updateReviewStatus']);
Route::post('reviews/add',[App\Http\Controllers\User\ReviewsController::class, 'store']);

Route::post('admin/login', [App\Http\Controllers\Admin\AuthController::class, 'login']);
Route::post('admin/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('register', [App\Http\Controllers\User\AuthController::class, 'register']);
Route::post('login', [App\Http\Controllers\User\AuthController::class, 'login']);
Route::post('logout', [App\Http\Controllers\User\AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
