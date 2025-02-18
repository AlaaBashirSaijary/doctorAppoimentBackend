<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;


class ContactReceived extends Mailable
{
    use Queueable, SerializesModels;
    public $contact; //

    public function __construct($contact) // أضف الباراميتر
    {
        $this->contact = $contact; // خزّن البيانات في الخاصية
    }

    // public function build()
    // {
    //     return $this->subject('تأكيد استلام طلبك')
    //                 ->view('emails.contact_received')
    //                 ->with(['contact' => $this->contact]);

    // }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@example.com', 'اسم التطبيق'), // أضف هذا
            subject: 'تم استلام طلبك',
            // subject: 'Contact Received',
     //       from: new Address('mailtrap@gmail.com','john') // تحديد المرسل
        );
    }
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact_received',
            with: ['contact' => $this->contact],
            // with: ['name' => $this->name],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
