<x-mail::message>
# Event Updated: {{ $event->title }}

Hello,

@if(isset($customMessage) && $customMessage)
<x-mail::panel>
**Message from the organizer:**
{{ $customMessage }}
</x-mail::panel>
@endif

The details for the event **{{ $event->title }}** have been updated by **{{ $updatedBy->name }}**.

@if(count($changes) > 0)
<x-mail::panel>
### Updated Details:
@foreach($changes as $field => $data)
- **{{ ucfirst($field) }}**: {{ $data['new'] }}
@endforeach
</x-mail::panel>
@else
<x-mail::panel>
### Event Details:
- **Date**: {{ $event->date->format('M d, Y H:i') }}
- **Location**: {{ $event->location ?: 'Not specified' }}
</x-mail::panel>
@endif

@if($event->description)
<x-mail::button :url="route('events.show', $event)">
View Event Details
</x-mail::button>
@endif

Thank you for being part of this event!

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
