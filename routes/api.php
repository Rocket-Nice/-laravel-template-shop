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
Route::any('getCart/', [\App\Http\Controllers\Api\OrderController::class, 'getCart']);

Route::prefix('webhook')->group(function () {
  Route::post('/cdek/status', [App\Http\Controllers\Shipping\CdekController::class, 'status']);
});

Route::middleware('auth:sanctum')->group(function () {
  Route::get('/user', [\App\Http\Controllers\Api\UserController::class, 'user']);
  // пользователи
  Route::post('/login', [\App\Http\Controllers\Api\UserController::class, 'login']);
  Route::get('/orders', [\App\Http\Controllers\Api\OrderController::class, 'by_user']);

  // продукты
  Route::get('/products', [\App\Http\Controllers\Api\ProductConrtoller::class, 'all']);
  Route::get('/products/find', [\App\Http\Controllers\Api\ProductConrtoller::class, 'getName']);
});

Route::get('/products', [\App\Http\Controllers\ProductController::class, 'get'])->name('products.get');

