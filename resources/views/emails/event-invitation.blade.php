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

If you don't have an account, please [register here]({{ route('register') }}) to see your invitation and notifications.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
