<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Carbon;

class CalendarService
{
    /**
     * Generate Google Calendar URL.
     */
    public function generateGoogleUrl(Event $event): string
    {
        $start = $event->date->format('Ymd\THis\Z');
        $end = $event->date->copy()->addHour()->format('Ymd\THis\Z'); // Default to 1 hour duration
        
        $params = [
            'action' => 'TEMPLATE',
            'text' => $event->title,
            'dates' => "{$start}/{$end}",
            'details' => $event->description,
            'location' => $event->location,
        ];

        return 'https://calendar.google.com/calendar/render?' . http_build_query($params);
    }

    /**
     * Generate Outlook.com / Office 365 Calendar URL.
     */
    public function generateOutlookUrl(Event $event): string
    {
        $start = $event->date->format('Y-m-d\TH:i:s\Z');
        $end = $event->date->copy()->addHour()->format('Y-m-d\TH:i:s\Z');
        
        $params = [
            'path' => '/calendar/action/compose',
            'rru' => 'addevent',
            'subject' => $event->title,
            'startdt' => $start,
            'enddt' => $end,
            'body' => $event->description,
            'location' => $event->location,
        ];

        return 'https://outlook.live.com/calendar/0/deeplink/compose?' . http_build_query($params);
    }

    /**
     * Generate ICS file content.
     */
    public function generateIcsContent(Event $event): string
    {
        $start = $event->date->format('Ymd\THis\Z');
        $end = $event->date->copy()->addHour()->format('Ymd\THis\Z');
        $stamp = Carbon::now()->format('Ymd\THis\Z');
        $uid = uniqid() . '@' . request()->getHost();

        $description = str_replace(["\r", "\n"], "\\n", $event->description);
        
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PROID:-//EventPlanner//EN',
            'METHOD:REQUEST', // Crucial for auto-prompting calendar apps
            'BEGIN:VEVENT',
            "UID:{$uid}",
            "DTSTAMP:{$stamp}",
            "DTSTART:{$start}",
            "DTEND:{$end}",
            "SUMMARY:{$event->title}",
            "DESCRIPTION:{$description}",
            "LOCATION:{$event->location}",
            'STATUS:CONFIRMED',
            'SEQUENCE:0',
            'BEGIN:VALARM',
            'TRIGGER:-PT15M',
            'ACTION:DISPLAY',
            'DESCRIPTION:Reminder',
            'END:VALARM',
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        return implode("\r\n", $lines);
    }
}
