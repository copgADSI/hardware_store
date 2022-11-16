<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\product\ProductController;
use App\Http\Controllers\ShoppingCartController;
use App\Http\Controllers\User\AddressController;
use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PHPUnit\TextUI\XmlConfiguration\Group;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('user-register', [UserController::class, 'store'])->name('user.register');
Route::post('user-login', [UserController::class, 'login'])->name('user.login');
Route::middleware('auth:sanctum')->controller(UserController::class)->group(function () {
    Route::get('user-data', function (Request $request) {
        return $request->user();
    })
        ->name('user.update');
    Route::put('updateUser', 'update')
        ->name('user.update');
});

Route::middleware('auth:sanctum')->controller(AddressController::class)->group(function () {
    Route::get('get-addresses-by-user', 'index')
        ->name('addresses.index');
    Route::post('add-addresses-by-user', 'store')
        ->name('addresses.store');
    Route::put('update-addresses-by-user', 'update')
        ->name('addresses.update');
});

Route::controller(ShoppingCartController::class)->group(function () {
    Route::get('get-shopping-cart', 'getShoppingCart')
        ->name('user.shopping_cart')->middleware(['auth:sanctum']);
    Route::post('add-to-cart', 'addToCart')
        ->name('user.addToCart')->middleware('auth:sanctum');
});


Route::controller(ProductController::class)->group(function () {
    Route::get('search', 'searchMatches')->name('product.match');
    Route::get('list-product', 'index')->name('product.list');
    Route::get('show-product', 'show')->name('product.show');
    Route::post('create-product', 'store')->name('product.store')
        ->middleware(['auth:sanctum', 'validateIsAdminRole']);
    Route::delete('delete-product', 'destroy')->name('product.destroy')
        ->middleware(['auth:sanctum', 'validateIsAdminRole:user']);
    Route::put('update-product', 'update')->name('product.update')
        ->middleware(['auth:sanctum', 'validateIsAdminRole:user']);
    Route::get('get-laptops', 'getLaptops')->name('product.laptops');

    Route::post('add-favorites-product', 'addFavoritesProduct')->name('product.favorite')
        ->middleware(['auth:sanctum']);
    Route::get('get-favorites', 'getFavorites')->name('products.getFavorites')
        ->middleware(['auth:sanctum']);
});

Route::controller(CategoryController::class)->group(function () {
    Route::get('categories', 'index')->name('categories.index');
});

Route::controller(BrandController::class)->group(function () {
    Route::get('brands', 'index')->name('brands.index');
});
