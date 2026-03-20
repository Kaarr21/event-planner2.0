<x-mail::message>
# Event Cancelled

Hi,

We are writing to inform you that the event **{{ $event->title }}** has been cancelled by **{{ $cancelledBy->name }}**.

@if($reason)
**Reason for cancellation:**
{{ $reason }}
@endif

We apologize for any inconvenience this may cause.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
