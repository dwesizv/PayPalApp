<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\PaypalController::class, 'main'])->name('main');
Route::get('about', [App\Http\Controllers\PaypalController::class, 'about'])->name('about');
Route::get('add/{id}', [App\Http\Controllers\PaypalController::class, 'add'])->name('add');
Route::get('checkout', [App\Http\Controllers\PaypalController::class, 'checkout'])->name('checkout');
Route::get('home', [App\Http\Controllers\PaypalController::class, 'home'])->name('home');
Route::get('login', [App\Http\Controllers\PaypalController::class, 'login'])->name('login');
Route::post('logout', [App\Http\Controllers\PaypalController::class, 'logout'])->name('logout');
Route::post('pay', [App\Http\Controllers\PaypalController::class, 'pay'])->name('pay');
Route::get('substract/{id}', [App\Http\Controllers\PaypalController::class, 'substract'])->name('substract');

Route::prefix('paypal')
    ->name('paypal.')
    ->group(function () {
        Route::post('/approve', [App\Http\Controllers\PaypalController::class, 'paypalApprove'])->name('approve');
        Route::get('/approved', [App\Http\Controllers\PaypalController::class, 'paypalApproved'])->name('approved');
        Route::post('/cancel', [App\Http\Controllers\PaypalController::class, 'paypalCancel'])->name('cancel');
        Route::get('/canceled', [App\Http\Controllers\PaypalController::class, 'paypalCanceled'])->name('canceled');
        Route::get('/error', [App\Http\Controllers\PaypalController::class, 'paypalError'])->name('error');
        Route::get('/notapproved', [App\Http\Controllers\PaypalController::class, 'paypalNotApproved'])->name('notapproved');
        Route::post('/pay', [App\Http\Controllers\PaypalController::class, 'paypalPay'])->name('pay');
});