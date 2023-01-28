<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::get('/',[App\Http\Controllers\RolesController::class, 'index']);
Route::prefix('/admin/roles')->group( function() {
    Route::get('/',[App\Http\Controllers\RolesController::class, 'index']);
    Route::post('/',[App\Http\Controllers\RolesController::class, 'store']);
    Route::get('/{id}',[App\Http\Controllers\RolesController::class, 'show']);
    Route::put('/{id}',[App\Http\Controllers\RolesController::class, 'update']);
    Route::delete('/{id}',[App\Http\Controllers\RolesController::class, 'destroy']);
    Route::delete('/',[App\Http\Controllers\RolesController::class, 'destroyAll']);    
});

Route::prefix('/admin/categories')->group( function() {
    Route::get('/',[App\Http\Controllers\CategoriesController::class, 'index']);
    Route::get('/add',[App\Http\Controllers\CategoriesController::class, 'create']);
    Route::post('/',[App\Http\Controllers\CategoriesController::class, 'store']);
    Route::get('/{id}',[App\Http\Controllers\CategoriesController::class, 'show']);
    Route::put('/{id}',[App\Http\Controllers\CategoriesController::class, 'update']);
    Route::delete('/{id}',[App\Http\Controllers\CategoriesController::class, 'destroy']);
    Route::delete('/',[App\Http\Controllers\CategoriesController::class, 'destroyAll']);    
    Route::put('{id}/{status}', [App\Http\Controllers\CategoriesController::class, 'updateCategoryStatus']);
});

Route::prefix('/admin/products')->group( function() {
    Route::get('/',[App\Http\Controllers\ProductsController::class, 'index']);
    Route::post('/',[App\Http\Controllers\ProductsController::class, 'store']);
    Route::get('/add',[App\Http\Controllers\ProductsController::class, 'create']);
    Route::get('/{id}',[App\Http\Controllers\ProductsController::class, 'show']);
    Route::put('/{id}',[App\Http\Controllers\ProductsController::class, 'update']);
    Route::delete('/{id}',[App\Http\Controllers\ProductsController::class, 'destroy']);
    Route::get('/view/{id}',[App\Http\Controllers\ProductsController::class, 'view']);
    Route::post('/add-image',[App\Http\Controllers\ProductsController::class, 'addImage']);
    Route::delete('/delete-image/{id}',[App\Http\Controllers\ProductsController::class, 'deleteImage']);
    Route::post('/add-size',[App\Http\Controllers\ProductsController::class, 'addSize']);
    Route::delete('/delete-size/{id}',[App\Http\Controllers\ProductsController::class, 'deleteSize']);
    Route::put('{id}/{status}', [App\Http\Controllers\ProductsController::class, 'updateProductStatus']);
    // Route::delete('/',[App\Http\Controllers\ProductsController::class, 'destroyAll']);    
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
