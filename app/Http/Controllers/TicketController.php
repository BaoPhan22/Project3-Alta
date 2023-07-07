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
use Ramsey\Uuid\Type\Decimal;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TicketController extends Controller
{
    protected function getFirstCapitalLetter(string $str)
    {
        $strFinal = '';

        $str = strtolower($str);
        $arr = explode(' ', $str);
        foreach ($arr as $i) {
            $strFinal .= strtoupper($i[0]);
        }

        return $strFinal;
    }
    protected function getQrCode(string $ticket_name, int $orderId, int $orderDetailId, string $date_order)
    {
        return $this->getFirstCapitalLetter($ticket_name) . $orderId  . $orderDetailId . date('Ymd', strtotime($date_order));
    }
    protected function changeStatus(object $row, string $status)
    {
        $row->status = $status;
        $row->save();
    }
    protected function checkUserExistByPhone(string $phone, string $email, string $name)
    {
        $userId = User::where('phone', $phone)->first();
        if (empty($userId)) {
            $newUser = new User;
            $newUser->email = $email;
            $newUser->phone = $phone;
            $newUser->name = $name;
            $newUser->save();
            return $newUser->id;
        }
        return $userId->id;
    }
    protected function insertOrderDetail(int $orderId, int $id_ticket, int $quantity)
    {
        $od = new OrderDetail();
        $od->id_order = $orderId;
        $od->id_ticket = $id_ticket;
        $od->quantity = $quantity;
        $od->save();
        return $od->id;
    }
    protected function createUnpaidOrder(string $total_price, string $date_order, string $checkout_session_id, int $id_user)
    {
        $order = new Order();
        $order->status = 'unpaid';
        $order->total_price = $total_price;
        $order->date_order = $date_order;
        $order->session_id = $checkout_session_id;
        $order->id_users = $id_user;
        $order->save();
    }
    protected function soldTicket(int $id, int $quantity)
    {
        $ticket = Ticket::find($id);
        $ticket->remain -= $quantity;
        $ticket->save();
    }
    public function beforepay(Request $request)
    {
        $ticket = Ticket::where('id', $request->ticket)->first();

        //* check remaining tickets
        $tickets_remaining = $ticket->remain;
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
        $userId = $this->checkUserExistByPhone($request->phone, $request->email, $request->name);
        //* if user (phone) is not existed -> create 

        //* get total price for order
        $total = $ticket->price * $request->quantity;
        //* get total price for order

        //* prepare data
        $data = [
            'ticket' => ['name' => $ticket->name, 'id' => $ticket->id, 'remain' => $ticket->remain],
            'quantity' => $request->quantity,
            'date_order' => $request->date_order,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'total' => $total,
            'id_user' => (int) $userId
        ];

        return view('beforepay')->with('data', $data);
    }

    public function checkout(Request $request)
    {
        //* code from stripe
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        //* create a checkout session
        $checkout_session = $stripe->checkout->sessions->create([
            'invoice_creation' => ['enabled' => true],
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
        $this->createUnpaidOrder($request->total_price, $request->date_order, $checkout_session->id, $request->id_user);
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

        $payload = file_get_contents('php://input');
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

                if ($order) {
                    //* change order status
                    if ($order->status === 'unpaid')
                        $this->changeStatus($order, 'paid');
                    //* change order status

                    //* metadata variable
                    $id_ticket = $session->metadata['id_ticket'];
                    $quantity = $session->metadata['quantity'];
                    $date_order = $session->metadata['date_order'];
                    $remain = $session->metadata['remain'];
                    $userEmail = $session->customer_details['email'];
                    $userName = $session->customer_details['name'];
                    //* metadata variable

                    //* insert OrderDetail
                    $orderDetail = OrderDetail::where('id_order', $order->id)->first();

                    if (empty($orderDetail))
                        $orderDetailId = $this->insertOrderDetail($order->id, $id_ticket, $quantity);
                    else
                        $orderDetailId = $orderDetail->id;
                    //* insert OrderDetail

                    //* decrease quantity of ticket (sold ticket)
                    $ticket = Ticket::where('id', $id_ticket)->first();
                    if ($ticket) {
                        $ticket_name = $ticket->name;
                        if ($ticket->remain == $remain) {
                            $this->soldTicket($ticket->id, $quantity);
                        }
                    }
                    //* decrease quantity of ticket (sold ticket)

                    //* make a string to create QR code

                    $qrCodeString = $this->getQrCode($ticket_name, $order->id, $orderDetailId, $date_order);
                    //* make a string to create QR code

                    //* send mail
                    Mail::to($userEmail)->send(new ThankYouMail($userName, $userEmail, $order->total_price, $order->date_order, $quantity, $ticket_name, $qrCodeString));
                    //* send mail
                }

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
            $remain = $session->metadata['remain'];
            $quantity = $session->metadata['quantity'];
            $date_order = $session->metadata['date_order'];

            if (!$session) {
                throw new NotFoundHttpException();
            }

            $order = Order::where('session_id', $session_id)->first();

            if (!$order) {
                throw new NotFoundHttpException();
            }
            $orderId = $order->id;

            if ($order->status === 'unpaid')
                $this->changeStatus($order, 'paid');

            //* insert OrderDetail
            $orderDetail = OrderDetail::where('id_order', $orderId)->first();
            if (empty($orderDetail))
                $orderDetailId = $this->insertOrderDetail($orderId, $id_ticket, $quantity);
            else
                $orderDetailId = $orderDetail->id;
            //* insert OrderDetail

            //* decrease quantity of ticket (sold ticket)
            $ticket = Ticket::where('id', $id_ticket)->first();
            if ($ticket) {
                $ticket_name = $ticket->name;
                if ($ticket->remain == $remain)
                    $this->soldTicket($ticket->id, $quantity);
            }
            //* decrease quantity of ticket (sold ticket)

            //* make a string to create QR code
            $qrCodeString = $this->getQrCode($ticket_name, $orderId, $orderDetailId, $date_order);
            //* make a string to create QR code

            //* prepare data to view
            $data = [
                'string_to_qr' => $qrCodeString,
                'quantity' => $quantity,
                'date_order' => date('d/m/Y', strtotime($date_order)),
                'ticket_name' => $ticket_name,
            ];
            //* prepare data to view
            return view('checkout-success')->with('data', $data);
        } catch (Exception $e) {
            throw new NotFoundHttpException();
        }
    }
    // End Method

    public function cancel(Request $request)
    {
        $order = Order::where('session_id', $request->get('session_id'))->first();
        $this->changeStatus($order, 'cancel');
        return redirect()->route('index');
    }
    // End Method


}
