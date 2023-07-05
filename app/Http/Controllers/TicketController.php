<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Ticket;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Mail\ThankYouMail;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

function checkUserExistByPhone(string $phone, string $email, string $name)
{
    $checkUser = User::select('id')->where('phone', $phone)->get();
    if (count($checkUser) == 0) {
        $newUser = new User;
        $newUser->email = $email;
        $newUser->phone = $phone;
        $newUser->name = $name;
        $newUser->save();
    }
}
function getFirstCapitalLetter(string $str)
{
    $strFinal = '';

    $str = strtolower($str);
    $arr = explode(' ', $str);
    foreach ($arr as $i) {
        $strFinal .= strtoupper($i[0]);
    }

    return $strFinal;
}
function changeStatus(object $row, string $status)
{
    $row->status = $status;
    $row->save();
}

class TicketController extends Controller
{
    public function beforepay(Request $request)
    {
        //* check remaining tickets
        $tickets_remaining = Ticket::where('id', $request->ticket)->first()->remain;
        //* check remaining tickets

        //* validate input 
        $request->validate([
            "quantity" => 'bail|required|integer|min:1|max:' . $tickets_remaining,
            "date_order" => 'required|date|date_format:Y-m-d|after_or_equal:' . date('Y-m-d'),
            "phone" => ['required', 'regex:/(0[3|5|7|8|9])+([0-9]{8})/', 'size:10']
        ], [
            'quantity.max' => 'Không đủ vé',
            'quantity.min' => 'Số vé phải > 0',
            'date_order.after_or_equal' => 'Ngày đặt phải từ hôm nay trở đi',
            'phone.regex' => 'Số điện thoại không đúng định dạng',
            'phone.size' => 'Số điện thoại phải có 10 số'
        ]);
        //* validate input 

        //* if user (phone) is not existed -> create 
        checkUserExistByPhone($request->phone, $request->email, $request->name);
        //* if user (phone) is not existed -> create 

        //* get total price for order
        $total = json_decode(Ticket::find($request->ticket)->first('price'))->price * $request->quantity;
        //* get total price for order

        //* prepare data
        $data = [
            'ticket' => Ticket::select('name', 'id', 'remain')->where('id', $request->ticket)->first(),
            'quantity' => $request->quantity,
            'date_order' => $request->date_order,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'total' => $total,
            'id_user' => User::select('id')->where('phone', $request->phone)->first()->id
        ];

        return view('beforepay')->with('data', $data);
    }

    public function checkout(Request $request)
    {
        //* code from stripe
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        //* create a checkout session
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
            'cancel_url' =>  route('checkout.cancel', [], true) . "?session_id={CHECKOUT_SESSION_ID}",
            'metadata' => [
                'id_ticket' => $request->id_ticket,
                'date_order' => $request->date_order,
                'remain' => $request->remain,
                'quantity' => $request->quantity,
            ],
        ]);

        //* create an unpaid order
        $order = new Order();
        $order->status = 'unpaid';
        $order->total_price = $request->total_price;
        $order->date_order = $request->date_order;
        $order->session_id = $checkout_session->id;
        $order->id_users = $request->id_user;
        $order->save();
        //* create an unpaid order

        return redirect($checkout_session->url);
    }
    // End Method

    public function webhook()
    {
        //* code from stripe
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

                //* metadata variable
                $id_ticket = $session->metadata['id_ticket'];
                $quantity = $session->metadata['quantity'];
                $date_order = $session->metadata['date_order'];
                $remain = $session->metadata['remain'];
                $userEmail = $session->customer_details['email'];
                $userName = $session->customer_details['name'];
                dd($userEmail);
                //* metadata variable

                $order = Order::where('session_id', $session_id)->first();
                $orderDetail = OrderDetail::where('id_order', $order->id)->first();
                $tickets = Ticket::where('id', $id_ticket->first());

                //* change order status
                if ($order && $order->status === 'unpaid') {
                    changeStatus($order, 'paid');
                }
                //* change order status

                //* insert OrderDetail
                $orderDetail->id_order = $order->id;
                $orderDetail->id_ticket = $id_ticket;
                $orderDetail->quantity = $quantity;
                $orderDetail->save();
                //* insert OrderDetail

                //* decrease quantity of ticket (sold ticket)
                $tickets->remain -= $quantity;
                $tickets->save();
                //* decrease quantity of ticket (sold ticket)

                //* make a string to create QR code
                $qrCodeString = getFirstCapitalLetter($tickets->name) . $orderDetail->id_order  . $orderDetail->id . date('Ymd', strtotime($date_order));
                //* make a string to create QR code

                //* send mail
                // Mail::to($userEmail)->send(new ThankYouMail($userName, $userEmail, $order->total_price, $order->date_order, $orderDetail->quantity, $tickets->name, $qrCodeString));
                //* send mail

            default:
                echo 'Received unknown event type ' . $event->type;
        }

        return response('');
    }
    // End Method

    public function success(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        try {
            //* get id from url
            $session_id = $request->get('session_id');
            $session = $stripe->checkout->sessions->retrieve($session_id);
            $id_ticket = $session->metadata['id_ticket'];
            $quantity = $session->metadata['quantity'];
            $date_order = $session->metadata['date_order'];
            // $userEmail = $session->customer_details['email'];
            // $userName = $session->customer_details['name'];
            // dd($userName);

            if (!$session) {
                throw new NotFoundHttpException();
            }

            $order = Order::where('session_id', $session_id)->first();
            if (!$order) {
                throw new NotFoundHttpException();
            }
            if ($order->status === 'unpaid') {
                //* change order status
                changeStatus($order, 'paid');
                //* change order status

                //* insert OrderDetail
                $orderDetail = new OrderDetail();
                $orderDetail->id_order = $order->id;
                $orderDetail->id_ticket = $id_ticket;
                $orderDetail->quantity = $quantity;
                $orderDetail->save();
                //* insert OrderDetail

                //* decrease quantity of ticket (sold ticket)
                $tickets = Ticket::where('id', $id_ticket)->first();
                $tickets->remain -= $quantity;
                $tickets->save();
                //* decrease quantity of ticket (sold ticket)

                //* make a string to create QR code
                $qrCodeString = getFirstCapitalLetter($tickets->name) . $orderDetail->id_order  . $orderDetail->id . date('Ymd', strtotime($date_order));
                //* make a string to create QR code

                //* prepare data to view
                $data = [
                    'string_to_qr' => $qrCodeString,
                    'quantity' => $orderDetail->quantity,
                    'date_order' => date('d/m/Y', strtotime($request->date_order)),
                    'ticket_name' => Ticket::find($orderDetail->id_ticket)->name,
                ];
                //* prepare data to view
            }
            return view('checkout-success')->with('data', $data);
        } catch (Exception $e) {
            throw new NotFoundHttpException();
        }
    }
    // End Method

    public function cancel(Request $request)
    {
        $order = Order::where('session_id', $request->get('session_id'))->first();
        changeStatus($order, 'cancel');
        return redirect()->route('index');
    }
    // End Method


}
