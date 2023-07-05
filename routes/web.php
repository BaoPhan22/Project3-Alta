<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ThankYouMail;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
// use PDF;


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

Route::get('/', function () {
    $tickets = DB::table('tickets')->select('id', 'name')->get();
    return view('index', compact('tickets'));
})->name('index');

Route::controller(TicketController::class)->group(function () {
    Route::get('/success', 'success')->name('checkout.success');
    Route::get('/cancel', 'cancel')->name('checkout.cancel');
    Route::post('/beforepay', 'beforepay')->name('beforepay');
    Route::post('/checkout', 'checkout')->name('checkout');
    Route::post('/webhook', 'webhook')->name('webhook');
});

Route::post('/save', function (Request $request) {
    $order = DB::table('orders')->where('session_id', $request->session_id)->first();
    $user = DB::table('users')->find($order->id_users);
    $orderDetail = DB::table('order_details')->where('id_order', $order->id)->first();
    $ticket_name = DB::table('tickets')->where('id', $orderDetail->id_ticket)->first()->name;

    //* button mail clicked
    if (isset($_POST['mail'])) {
        Mail::to($user->email)->send(new ThankYouMail($user->name, $user->email, $order->total_price, $order->date_order, $orderDetail->quantity, $ticket_name, $request->string_to_qr));
        return redirect()->route('index');
    }
    //* button mail clicked

    //* button save clicked
    if (isset($_POST['save'])) {
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'price' => number_format($order->total_price, 0, ',', '.'),
            'date_order' => date('d/m/Y', strtotime($order->date_order)),
            'quantity' => $orderDetail->quantity,
            'ticket_name' => $ticket_name,
            'string_to_qr' => $request->string_to_qr
        ];
        $pdf = FacadePdf::loadView('email.content', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream();
    }
    //* button save clicked
})->name('save');

Route::controller(EventController::class)->group(function () {
    Route::get('/event', 'event')->name('event');
    Route::get('/event/{id}', 'EventDetail')->name('event.detail');
});

Route::get('/contact', function () {
    return view('contact.contact');
})->name('contact');
