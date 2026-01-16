<?php

use Illuminate\Support\Facades\Route;
Route::middleware(['maintenance'])->group(function(){
  Route::get('/order', [\App\Http\Controllers\OrderController::class, 'index'])->name('order.index');
  Route::get('/order/voucher/{voucher}', [\App\Http\Controllers\VoucherController::class, 'order'])->name('order.voucher');
  Route::post('/order/voucher/{voucher}', [\App\Http\Controllers\VoucherController::class, 'submit'])->name('order.voucher.submit');
  Route::post('/order', [\App\Http\Controllers\OrderController::class, 'submit'])->name('order.submit');
  Route::get('/meeting/order', [\App\Http\Controllers\CustomOrderController::class, 'order'])->name('order.meeting');
  Route::post('/meeting/order', [\App\Http\Controllers\CustomOrderController::class, 'submit'])->name('order.meeting.submit');

});

Route::get('/cart', [\App\Http\Controllers\CartController::class, 'init'])->name('cart.init');
Route::get('/cart/clear', [\App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/update', [\App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');

Route::get('/cdek/regions', [\App\Http\Controllers\ShippingController::class, 'getCdekRegions'])->name('getCdekRegions');
Route::get('/cdek/cities', [\App\Http\Controllers\ShippingController::class, 'getCdekCities'])->name('getCdekCities');
Route::get('/cdek/pvz', [\App\Http\Controllers\ShippingController::class, 'getCdekPvz'])->name('getCdekPvz');
Route::get('/cdek/calculate', [\App\Http\Controllers\ShippingController::class, 'calculateCdek'])->name('calculateCdek');

Route::get('/cdek_courier/regions', [\App\Http\Controllers\ShippingController::class, 'getCdekCourierRegions'])->name('getCdekCourierRegions');
Route::get('/cdek_courier/cities', [\App\Http\Controllers\ShippingController::class, 'getCdekCourierCities'])->name('getCdekCourierCities');

Route::get('/boxberry/regions', [\App\Http\Controllers\ShippingController::class, 'getBoxberryRegions'])->name('getBoxberryRegions');
Route::get('/boxberry/cities', [\App\Http\Controllers\ShippingController::class, 'getBoxberryCities'])->name('getBoxberryCities');
Route::get('/boxberry/pvz', [\App\Http\Controllers\ShippingController::class, 'getBoxberryPvz'])->name('getBoxberryPvz');
Route::get('/boxberry/calculate', [\App\Http\Controllers\ShippingController::class, 'calculateBoxberry'])->name('calculateBoxberry');

Route::get('/5post/regions', [\App\Http\Controllers\ShippingController::class, 'getX5PostRegions'])->name('getX5PostRegions');
Route::get('/5post/cities', [\App\Http\Controllers\ShippingController::class, 'getX5PostCities'])->name('getX5PostCities');
Route::get('/5post/pvz', [\App\Http\Controllers\ShippingController::class, 'getX5PostPvz'])->name('getX5PostPvz');
Route::get('/5post/calculate', [\App\Http\Controllers\ShippingController::class, 'calculateX5Post'])->name('calculateX5Post');


Route::post('/russianpost/calculate', [\App\Http\Controllers\ShippingController::class, 'calculatePochta'])->name('calculatePochta');

Route::post('/voucher/check', [\App\Http\Controllers\OrderController::class, 'checkVoucher'])->name('checkVoucher');
Route::post('/promocode/check', [\App\Http\Controllers\OrderController::class, 'checkPromocode'])->name('checkPromocode');

Route::get('/payment/robokassa/{order}', [\App\Http\Controllers\Payment\RobokassaController::class, 'index'])->name('order.robokassa');
Route::post('/order/payment/robokassa/check', [\App\Http\Controllers\Payment\RobokassaController::class, 'check']);

Route::get('/order/cloudpayments/{order}', [\App\Http\Controllers\Payment\CloudpaymentsController::class, 'index'])->name('order.cloudpayments');
Route::post('/order/cloudpayments/check', [\App\Http\Controllers\Payment\CloudpaymentsController::class, 'check']);
Route::post('/order/cloudpayments/refund', [\App\Http\Controllers\Payment\CloudpaymentsController::class, 'refund']);
Route::post('/order/cloudpayments/receipt', [\App\Http\Controllers\Payment\CloudpaymentsController::class, 'receipt']);

Route::get('/order/success', [\App\Http\Controllers\OrderController::class, 'success_page'])->name('order.success');
Route::get('/order/fail', [\App\Http\Controllers\OrderController::class, 'fail_page']);
