<?php

namespace App\Mail;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class TicketConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Access Pass: ' . $this->order->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tickets.confirmation',
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdfs.ticket', ['order' => $this->order]);
        
        return [
            Attachment::fromData(fn () => $pdf->output(), 'Tickets-' . $this->order->id . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
