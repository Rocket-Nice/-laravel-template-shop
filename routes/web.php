<?php

use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\ImageUploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// тест кота
Route::get('/test-cat-popup', function () {
  return view('test-cat-popup');
});

//Route::get('/test-memcached', function() {
//  $key = 'test_key';
//  $value = 'Hello, Memcached!';
//
//  // Попробуем сохранить значение в кэш
//  Cache::put($key, $value, 60); // Сохраняем на 60 секунд
//
//  // Получим значение из кэша
//  $cachedValue = Cache::get($key);
//
//  // Вернем значение
//  return $cachedValue;
//});

$tg_notification = \App\Models\Setting::query()->where('key', 'tg_notifications_bot')->first();
Route::post('/tg_webhook/' . ($tg_notification->value ?? ''), [\App\Http\Controllers\Admin\TelegramController::class, 'webhook'])->name('tg_webhook');
//Route::post('/tg_webhook/'.($tg_notification->value ?? ''), function () {
//  echo 1;
//  Log::debug(print_r($_POST, true));
//});
Route::post('/upload', [ImageUploadController::class, 'upload']);
Route::get('/happy_coupon/{order}', [\App\Http\Controllers\HappyCouponController::class, 'index'])->name('happy_coupon');
Route::post('/happy-coupon/{order:slug}/open', [\App\Http\Controllers\HappyCouponController::class, 'open'])->name('happy_coupon.open');
Route::post('/happy-coupon/{order:slug}/opened', [\App\Http\Controllers\HappyCouponController::class, 'opened'])->name('happy_coupon.opened');

Route::post('/happy-coupon/store', [\App\Http\Controllers\HappyCouponController::class, 'store'])->name('happy_coupon.store');
Route::get('/happy-coupon/store/user', [\App\Http\Controllers\HappyCouponController::class, 'user'])->name('happy_coupon.user');

Route::get('s/{short_link}', [App\Http\Controllers\ShortLinkController::class, 'redirect'])->name('redirect');

Route::get('r/{partner}', [App\Http\Controllers\ShortLinkController::class, 'partner'])->name('partner');


Route::get('/link', [\App\Http\Controllers\HomeController::class, 'link']);
Route::get('/api-documentation', [\App\Http\Controllers\HomeController::class, 'apiDocumentation']);
Route::middleware(['maintenance'])->group(function () {
  Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('page.index');
  Route::get('/about-us', [\App\Http\Controllers\HomeController::class, 'index'])->name('page.about');
  Route::get('/celebrities', [\App\Http\Controllers\HomeController::class, 'index'])->name('page.guests');
  Route::get('/awards', [\App\Http\Controllers\HomeController::class, 'index'])->name('page.awards');
  Route::get('/ambassadors', [\App\Http\Controllers\HomeController::class, 'index'])->name('page.ambassadors');
  Route::get('/certificates', [\App\Http\Controllers\HomeController::class, 'index'])->name('page.certificates');
  Route::get('/delivery_and_payment', [\App\Http\Controllers\HomeController::class, 'index'])->name('page.delivery_and_payment');
  Route::get('/contacts', [\App\Http\Controllers\HomeController::class, 'index'])->name('page.contacts');
  Route::get('/xenon', [\App\Http\Controllers\HomeController::class, 'index'])->name('page.xenon');
  Route::get('/puzzles', [\App\Http\Controllers\HomeController::class, 'puzzles'])->name('page.puzzles');
  Route::get('/dermatologists', [\App\Http\Controllers\HomeController::class, 'index'])->name('page.dermatologists');
  Route::get('/dermatologists/all', [\App\Http\Controllers\HomeController::class, 'dermatologists'])->name('page.dermatologists.all');
  Route::get('/news', [\App\Http\Controllers\HomeController::class, 'blog'])->name('blog.index');
  Route::get('/news/c/{category}', [\App\Http\Controllers\HomeController::class, 'blog_category'])->name('blog.category');
  Route::get('/news/{article}', [\App\Http\Controllers\HomeController::class, 'blog_article'])->name('blog.article');
  // Route::get('/product', [\App\Http\Controllers\ProductController::class, 'index'])->name('product.index');
  Route::get('/product/{product}', [\App\Http\Controllers\ProductController::class, 'index'])->name('product.index');
  Route::get('/product/{product}/reviews', [\App\Http\Controllers\ProductController::class, 'reviews'])->name('product.reviews');
  Route::get('/present/{product}', [\App\Http\Controllers\ProductController::class, 'present'])->name('product.present');
  Route::get('/vouchers', [\App\Http\Controllers\VoucherController::class, 'catalog'])->name('product.vouchers');
  Route::get('/meeting', [\App\Http\Controllers\CustomOrderController::class, 'page'])->name('product.meeting');
  Route::get('/catalog', [\App\Http\Controllers\ProductController::class, 'catalog'])->name('product.catalog');
  Route::get('/catalog/load', [\App\Http\Controllers\ProductController::class, 'loadProducts'])->name('product.loadProducts');
  Route::get('/catalog/{category}', [App\Http\Controllers\ProductController::class, 'category'])->name('catalog.category');
  Route::get('/our-presents', [\App\Http\Controllers\ProductController::class, 'presents'])->name('product.presents');
});

Route::middleware(['auth'])->group(function () {
  Route::post('/product/set-notification', [\App\Http\Controllers\Cabinet\HomeController::class, 'product_notification'])->name('product.notification');
});
Route::middleware('auth')->post('/product/{product}/review', [\App\Http\Controllers\Cabinet\HomeController::class, 'storeProductReview'])->name('product.review');

Route::middleware(['auth', 'permission:Доступ в партнерский кабинет'])->group(function () {
  Route::get('/partner', [\App\Http\Controllers\Partner\HomeController::class, 'index'])->name('partner.cabinet.index');
});

require __DIR__ . '/order.php';

Route::middleware(['maintenance'])->get('/{page}', [\App\Http\Controllers\PageController::class, 'page'])->name('page');
