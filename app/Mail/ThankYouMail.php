<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class ThankYouMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(private $name, private $email, private $price, private $date_order, private $quantity, private $ticket_name, private $string_to_qr)
    {
        $this->price = number_format($this->price, 0, ',', '.');
        $this->date_order = date('d/m/Y', strtotime($this->date_order));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Bạn đã đặt vé thành công',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.content',
            with: ['name' => $this->name, 'email' => $this->email, 'price' => $this->price, 'date_order' => $this->date_order, 'quantity' => $this->quantity, 'ticket_name' => $this->ticket_name, 'string_to_qr' => $this->string_to_qr],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
