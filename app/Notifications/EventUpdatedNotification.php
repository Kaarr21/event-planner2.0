<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventUpdatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $event;
    protected $changes;
    protected $updatedBy;
    protected $customMessage;

    public function __construct($event, $changes, $updatedBy, $customMessage = null)
    {
        $this->event = $event;
        $this->changes = $changes;
        $this->updatedBy = $updatedBy;
        $this->customMessage = $customMessage;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject("Event Updated: {$this->event->title}")
                    ->markdown('emails.events.updated', [
                        'event' => $this->event,
                        'changes' => $this->changes,
                        'updatedBy' => $this->updatedBy,
                        'customMessage' => $this->customMessage,
                        'notifiable' => $notifiable
                    ]);
    }

    public function toArray(object $notifiable): array
    {
        $changeList = implode(', ', array_keys($this->changes));
        return [
            'event_id' => $this->event->id,
            'title' => 'Event Details Updated',
            'message' => "The event '{$this->event->title}' has been updated ({$changeList}) by {$this->updatedBy->name}.",
            'type' => 'event_updated',
            'related_id' => $this->event->id
        ];
    }
}
