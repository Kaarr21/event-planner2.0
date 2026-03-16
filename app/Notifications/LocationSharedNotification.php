<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LocationSharedNotification extends Notification
{
    use Queueable;

    protected $event;

    /**
     * Create a new notification instance.
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mapUrl = "https://www.google.com/maps/search/?api=1&query={$this->event->latitude},{$this->event->longitude}&query_place_id={$this->event->google_place_id}";

        return (new MailMessage)
                    ->subject("Location Shared: {$this->event->title}")
                    ->line("The organizer of **{$this->event->title}** has shared the exact location pin with you.")
                    ->line("Location: **{$this->event->location}**")
                    ->action('Open in Google Maps', $mapUrl)
                    ->line('We look forward to seeing you there!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'location' => $this->event->location,
            'latitude' => $this->event->latitude,
            'longitude' => $this->event->longitude,
            'google_place_id' => $this->event->google_place_id,
            'message' => "Location shared for {$this->event->title}",
        ];
    }
}
