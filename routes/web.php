<?php

use Illuminate\Support\Facades\Route;
use App\Models\Category;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__.'/auth.php';

Route::get('{any}', function () {
    return view('app');
})->where('any', '.*');

Route::prefix('/admin')->namespace('App\Http\Controllers\Admin')->group(function() {

    // Admin login route
    Route::match(['get','post'],'login', 'AdminController@login');
    
    Route::group(['middleware'=>['admin']], function() {
        // Admin dashboard route
        Route::get('dashboard', 'AdminController@dashboard')->name('dashboard');

        // Update admin password
        Route::match(['get','post'],'update-admin-password', 'AdminController@updateAdminPassword');

        // Check admin password
        Route::post('check-admin-password', 'AdminController@checkAdminPassword');

        // Update admin details
        Route::match(['get','post'],'update-admin-details', 'AdminController@updateAdminDetails');

        // Admin logout
        Route::post('logout', 'AdminController@logout');

        // Banners
        Route::get('banners', 'BannerController@index');
        Route::get('update-banner/{id}', 'BannerController@show');
        Route::post('update-banner/{id}', 'BannerController@update');
        Route::get('delete-banner/{id}', 'BannerController@destroy');

        // Categories
        Route::get('categories', 'CategoriesController@index');
        Route::get('add-category', 'CategoriesController@create');
        Route::post('add-category', 'CategoriesController@store');
        Route::get('update-category/{id}', 'CategoriesController@show');
        Route::post('update-category/{id}', 'CategoriesController@update');
        Route::post('update-category-status', 'CategoriesController@updateCategoryStatus');
        Route::get('delete-category/{id}', 'CategoriesController@destroy');

        // Products
        Route::get('products', 'ProductsController@index');
        Route::get('add-product', 'ProductsController@create');
        Route::post('add-product', 'ProductsController@store');
        Route::get('update-product/{id}', 'ProductsController@show');
        Route::post('update-product/{id}', 'ProductsController@update');
        Route::post('update-product-status', 'ProductsController@updateProductStatus');
        Route::get('delete-product/{id}', 'ProductsController@destroy');

        // Images
        Route::match(['get','post'], 'add-images/{id}', 'ProductsController@addImages');
        Route::get('delete-image/{id}', 'ProductsController@deleteImage');

        // Sizes
        Route::match(['get','post'], 'add-sizes/{id}', 'ProductsController@addSizes');
        Route::post('/update-size/{id}', 'ProductsController@updateSize');
        Route::get('delete-size/{id}', 'ProductsController@deleteSize');

        // Staffs
        Route::get('staffs', 'StaffsController@index');
        Route::get('add-staff', 'StaffsController@create');
        Route::post('add-staff', 'StaffsController@store');
        Route::get('update-staff/{id}', 'StaffsController@show');
        Route::post('update-staff/{id}', 'StaffsController@update');
        Route::post('update-staff-status', 'StaffsController@updateStaffStatus');
        Route::get('delete-staff/{id}', 'StaffsController@destroy');
        Route::get('/update-staff-role/{id}', 'StaffsController@showRole');
        Route::post('/update-staff-role/{id}', 'StaffsController@updateRole');

        
    });
});


Route::namespace('App\Http\Controllers\Front')->group(function() {
    
    Route::get('/', 'HomeController@index');

    // Listing/Categories Routes
    $catUrls = Category::select('url')->where('status',1)->get()->pluck('url')->toArray();
    // dd($catUrls); die;
    foreach($catUrls as $key => $url) {
        Route::match(['get','post'], '/'.$url, 'ProductsController@listing');
    }
    Route::match(['get','post'], '/all', 'ProductsController@all');
    
    Route::get('/product/{id}', 'ProductsController@detail');

    Route::group(['middleware'=>['auth']], function() {
        Route::get('/profile', 'ProfilesController@index');
        Route::match(['get','post'],'/update-profile', 'ProfilesController@updateProfileDetails');

        // Check user password
        Route::post('/check-user-password', 'ProfilesController@checkUserPassword');

        Route::match(['get','post'],'/update-password', 'ProfilesController@updatePassword');
        Route::match(['get','post'],'/update-email', 'ProfilesController@updateEmail');
        
        Route::match(['get','post'],'/addresses', 'ProfilesController@addresses');
        Route::post('/address/add', 'ProfilesController@addAddress');
        Route::post('/address/get-districts/{id}', 'ProfilesController@getDistricts');
        Route::post('/address/get-wards/{id}', 'ProfilesController@getWards');
        Route::post('/address/upadate/{id}', 'ProfilesController@updateAdress');
        Route::get('delete-address/{id}', 'ProfilesController@deleteAddress');
        Route::post('/address-default', 'ProfilesController@addressDefault');

    });

    // Search
    Route::get('/search', 'ProductsController@search');

    
    // Cart
    Route::post('cart/add', 'CartsController@cartAdd');
    Route::get('carts', 'CartsController@index');
    Route::post('cart/update/{id}', 'CartsController@update');
    Route::get('delete-item/{id}', 'CartsController@destroy');

    // Checkout
    Route::get('checkout', 'CartsController@checkout');


});
