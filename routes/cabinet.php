<?php

use Illuminate\Support\Facades\Route;

Route::name('cabinet.')->group(function () {
  Route::get('/orders', [\App\Http\Controllers\Cabinet\OrderController::class, 'index'])->name('order.index');
  Route::get('/discounts', [\App\Http\Controllers\Cabinet\HomeController::class, 'discounts'])->name('discounts');
  Route::get('/orders/{order}', [\App\Http\Controllers\Cabinet\OrderController::class, 'show'])->name('order.show');
  Route::get('/profile', [\App\Http\Controllers\Cabinet\ProfileController::class, 'index'])->name('profile.index');
  Route::put('/profile', [\App\Http\Controllers\Cabinet\ProfileController::class, 'update'])->name('profile.update');

  Route::get('/nps/{survey}', [\App\Http\Controllers\Cabinet\SurveyController::class, 'index'])->name('survey.index');
  Route::post('/nps/{survey}', [\App\Http\Controllers\Cabinet\SurveyController::class, 'save'])->name('survey.save');

  Route::get('/form/{form}', [\App\Http\Controllers\Cabinet\CustomFormController::class, 'index'])->name('form.index');
  Route::post('/form/{form}', [\App\Http\Controllers\Cabinet\CustomFormController::class, 'save'])->name('form.save');

  Route::get('/puzzle', [\App\Http\Controllers\Cabinet\HomeController::class, 'puzzle'])->name('page.puzzle');
  Route::post('/puzzle', [\App\Http\Controllers\Cabinet\HomeController::class, 'puzzle_upload'])->name('page.puzzle_upload');

  Route::post('/settings/{id}', [\App\Http\Controllers\Cabinet\OrderController::class, 'hideWindow'])->name('settings.hide');
  Route::post('/document/{page_id}/accept', [\App\Http\Controllers\Cabinet\HomeController::class, 'documentAccept'])->name('document.accept');
});



