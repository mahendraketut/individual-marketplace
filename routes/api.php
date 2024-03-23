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

    Route::get('/', function () {
        return response()->json(['message' => 'Welcome to the API']);
    });


    Route::post('/register', 'App\Http\Controllers\Auth\RegisterController@register');
    Route::post('/login', 'App\Http\Controllers\Auth\LoginController@login');


    Route::middleware('auth:sanctum')->group(function () {
        Route::put('/update/{id}', 'App\Http\Controllers\Auth\UpdateController@update');
        Route::post('/logout', 'App\Http\Controllers\Auth\LogoutController@logout');
        Route::get('/brands/trashed', 'App\Http\Controllers\BrandController@trashed')->name('brands.trashed');
        Route::post('/brands/restore/{brand}', 'App\Http\Controllers\BrandController@restore')->name('brands.restore');
        Route::resource('brands', 'App\Http\Controllers\BrandController');
        Route::get('/categories/trashed', 'App\Http\Controllers\CategoryController@trashed')->name('categories.trashed');
        Route::post('/categories/restore/{category}', 'App\Http\Controllers\CategoryController@restore')->name('categories.restore');
        Route::resource('categories', 'App\Http\Controllers\CategoryController');
    });
});
