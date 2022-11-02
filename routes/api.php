<?php

use App\Http\Controllers\product\ProductController;
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


Route::controller(ProductController::class)->group(function () {
    Route::get('list-product', 'index')->name('product.list');
    Route::get('show-product', 'show')->name('product.show');
    Route::post('create-product', 'store')->name('product.store')
        ->middleware(['auth:sanctum', 'validateIsAdminRole']);
    Route::delete('delete-product', 'destroy')->name('product.destroy')
        ->middleware(['auth:sanctum', 'validateIsAdminRole:user']);
    Route::put('update-product', 'update')->name('product.update')
        ->middleware(['auth:sanctum', 'validateIsAdminRole:user']);
});
