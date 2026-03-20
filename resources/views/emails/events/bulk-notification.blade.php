<x-mail::message>
# Update for {{ $event->title }} 🥂

Hello,

The event organizers have a new message for you regarding **{{ $event->title }}**:

<x-mail::panel>
{{ $messageContent }}
</x-mail::panel>

### Event Details
*   **When**: {{ $event->date->format('M d, Y') }} at {{ $event->date->format('H:i') }}
*   **Where**: {{ $event->location }}

<x-mail::button :url="route('events.show', $event)" color="primary">
View Event Details
</x-mail::button>

If you have any questions, feel free to reach out.

Warm regards,<br>
The {{ config('app.name') }} Team
</x-mail::message>
