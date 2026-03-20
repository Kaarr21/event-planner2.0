<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $googleUrl;
    public $outlookUrl;

    public function __construct(
        public Event $event,
        public User $inviter,
        public ?string $message = null
    ) {
        $calendarService = new \App\Services\CalendarService();
        $this->googleUrl = $calendarService->generateGoogleUrl($event);
        $this->outlookUrl = $calendarService->generateOutlookUrl($event);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You have been invited to ' . $this->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.event-invitation',
            with: [
                'googleUrl' => $this->googleUrl,
                'outlookUrl' => $this->outlookUrl,
            ],
        );
    }

    public function attachments(): array
    {
        $calendarService = new \App\Services\CalendarService();
        $icsContent = $calendarService->generateIcsContent($this->event);

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $icsContent,
                'invite.ics'
            )->withMime('text/calendar; charset=UTF-8; method=REQUEST'),
        ];
    }
}
