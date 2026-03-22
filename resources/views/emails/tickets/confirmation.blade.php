<x-mail::message>
# You're In!

Hi {{ $order->user->name }},

Your registration for **{{ $order->event->title }}** is confirmed. We've attached your official tickets to this email as a PDF.

**Experience Details:**
- **Date:** {{ $order->event->start_date->format('l, F j, Y') }}
- **Time:** {{ $order->event->start_date->format('g:i A') }}
- **Venue:** {{ $order->event->location ?? 'To be announced' }}

Please keep the attached PDF handy. You will need to present the QR code(s) at the entrance for verification.

<x-mail::button :url="route('events.show', $order->event)">
View Event Details
</x-mail::button>

If you have any questions, feel free to reach out to the organizers.

See you there,<br>
The {{ config('app.name') }} Team
</x-mail::message>
