<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::all();
        return view('index', compact('tickets'));
    }

    public function beforepay(Request $request)
    {
        $date = $request->date_order;
        $selectedDate = Carbon::createFromFormat('Y-m-d', $date);
        $today = Carbon::today();
        if ($request->quantity == '' || $request->quantity <= 0 || $selectedDate->lessThan($today)) {
            return redirect()->route('index');
        }


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
            'success_url' => route('checkout.success', [], true) . "?session_id={CHECKOUT_SESSION_ID}",
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
    // End Method

    public function success(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        try {
            $session_id = $request->get('session_id');
            $session = $stripe->checkout->sessions->retrieve($session_id);
            if (!$session) {
                throw new NotFoundHttpException();
            }

            $order = Order::where('session_id', $session_id)->first();
            if (!$order) {
                throw new NotFoundHttpException();
            }
            if ($order->status === 'unpaid') {
                $order->status = 'paid';
                $order->save();
                //TODO  send mail
            }
            
            return view('checkout-success');
        } catch (Exception $e) {
            throw new NotFoundHttpException();
        }
    }
    // End Method

    public function cancel()
    {
    }
    // End Method

    public function webhook()
    {

        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        // This is your Stripe CLI webhook secret for testing your endpoint locally.
        $endpoint_secret = env('STRIPE_SECRET_WEBHOOK');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload

            return response('', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature

            return response('', 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $session_id = $session->id;

                $order = Order::where('session_id', $session_id)->first();
                if ($order && $order->status === 'unpaid') {
                    $order->status = 'paid';
                    $order->save();
                    //TODO  send mail
                }

            default:
                echo 'Received unknown event type ' . $event->type;
        }

        return response('');
    }
    // End Method
}
