<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::all();
        return view('index', compact('tickets'));
    }

    public function beforepay(Request $request)
    {
        $checkUser = User::select('id')->where('phone', $request->phone)->get();
        if (count($checkUser) == 0) {
            $newUser = new User;
            $newUser->email = $request->email;
            $newUser->phone = $request->phone;
            $newUser->name = $request->name;
            $newUser->save();
        }
        // echo $checkUser;

        $total = json_decode(Ticket::find($request->ticket)->first('price'))->price * $request->quantity;
        $data = [
            'ticket' => Ticket::select('name')->where('id', $request->ticket)->get()[0]->name,
            'quantity' => $request->quantity,
            'date_order' => $request->date_order,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'total' => $total,
            'id_user' => User::select('id')->where('phone', $request->phone)->get()[0]->id
        ];
        return view('beforepay')->with('data', $data);
    }

    public function checkout(Request $request)
    {

        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'vnd',
                    'product_data' => [
                        'name' => $request->name,
                    ],
                    'unit_amount' => $request->total_price / $request->quantity,
                ],
                'quantity' => $request->quantity,
            ]],
            'mode' => 'payment',
            'success_url' => route('checkout.success', [], true),
            'cancel_url' =>  route('checkout.cancel', [], true),
        ]);

        $order = new Order();
        $order->status = 'unpaid';
        $order->total_price = $request->total_price;
        $order->date_order = $request->date_order;
        $order->session_id = $checkout_session->id;
        $order->id_users = $request->id_user;
        $order->save();
        return redirect($checkout_session->url);
    }

    public function success() {
        return view('checkout-success'); 
    }
    public function cancel() {}
}
