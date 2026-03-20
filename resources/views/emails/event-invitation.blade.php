<x-mail::message>
# You've been invited!

**{{ $inviter->name }}** has invited you to the event: **{{ $event->title }}**.

@if($message)
They included this message:
> {{ $message }}
@endif

<x-mail::button :url="route('events.show', $event->id)">
View Event
</x-mail::button>

## Sync to your Calendar
Add this event to your calendar to get notifications:

<x-mail::button :url="$googleUrl" color="primary">
Add to Google Calendar
</x-mail::button>

<x-mail::button :url="$outlookUrl" color="success">
Add to Outlook
</x-mail::button>

*You can also find an `.ics` file attached to this email for other calendar apps.*

If you don't have an account, please [register here]({{ route('register') }}) to see your invitation and notifications.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
