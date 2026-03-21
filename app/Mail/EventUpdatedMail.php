<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $changes;
    public $updatedBy;
    public $customMessage;

    public function __construct($event, $changes, $updatedBy, $customMessage = null)
    {
        $this->event = $event;
        $this->changes = $changes;
        $this->updatedBy = $updatedBy;
        $this->customMessage = $customMessage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Updates for the event: {$this->event->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.events.updated',
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
