<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
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

Route::controller(TicketController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/success', 'success')->name('checkout.success');
    Route::get('/cancel', 'cancel')->name('checkout.cancel');
    Route::post('/beforepay', 'beforepay')->name('beforepay');
    Route::post('/checkout', 'checkout')->name('checkout');
    Route::post('/save', 'save')->name('save');
    Route::post('/webhook', 'webhook')->name('webhook');
});


Route::controller(EventController::class)->group(function () {
    Route::get('/event', 'event')->name('event');
    Route::get('/event/detail/{id}', 'EventDetail')->name('event.detail');
});

Route::get('/contact', function () {
    return view('contact.contact');
})->name('contact');
