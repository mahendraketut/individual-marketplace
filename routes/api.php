<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    // test API
    Route::get('/', function () {
        return response()->json(['message' => 'Welcome to the API']);
    });

    // Auth routes
    Route::post('/register', 'App\Http\Controllers\Auth\RegisterController@register');
    Route::post('/login', 'App\Http\Controllers\Auth\LoginController@login');

    // Routes for public users
    Route::get('/products', 'App\Http\Controllers\ProductController@index')->name('products.index');  // get all products

    // Routes for public data
    Route::get('/images/{image}', 'App\Http\Controllers\ImageController@show')->name('images.show');

    Route::middleware('auth:sanctum')->group(function () {
        // Route for auth user
        Route::put('/update/{id}', 'App\Http\Controllers\Auth\UpdateController@update');
        Route::post('/logout', 'App\Http\Controllers\Auth\LogoutController@logout');
        // Routes for brand
        Route::get('/brands/trashed', 'App\Http\Controllers\BrandController@trashed')->name('brands.trashed');
        Route::post('/brands/restore/{brand}', 'App\Http\Controllers\BrandController@restore')->name('brands.restore');
        Route::resource('brands', 'App\Http\Controllers\BrandController');
        // Route for category
        Route::get('/categories/trashed', 'App\Http\Controllers\CategoryController@trashed')->name('categories.trashed');
        Route::post('/categories/restore/{category}', 'App\Http\Controllers\CategoryController@restore')->name('categories.restore');
        Route::resource('categories', 'App\Http\Controllers\CategoryController');
        // Route for product
        Route::get('/products/trashed', 'App\Http\Controllers\ProductController@trashed')->name('products.trashed');
        Route::post('/products/restore/{product}', 'App\Http\Controllers\ProductController@restore')->name('products.restore');
        Route::resource('products', 'App\Http\Controllers\ProductController')->except("index");

        // Route for wishlist
        Route::get('/wishlist', 'App\Http\Controllers\WishlistController@index')->name('wishlist.index');
        Route::post('/wishlist', 'App\Http\Controllers\WishlistController@store')->name('wishlist.store');
        Route::delete('/wishlist/{product}', 'App\Http\Controllers\WishlistController@destroy')->name('wishlist.destroy');
    });
});
