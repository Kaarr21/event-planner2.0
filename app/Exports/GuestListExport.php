<?php

namespace App\Exports;

use App\Models\RSVP;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class GuestListExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $eventId;

    public function __construct(int $eventId)
    {
        $this->eventId = $eventId;
    }

    public function query()
    {
        return RSVP::query()
            ->where('event_id', $this->eventId)
            ->whereIn('status', ['attending', 'maybe'])
            ->with('user');
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Status',
            'Registered At',
        ];
    }

    public function map($rsvp): array
    {
        return [
            $rsvp->user->name,
            $rsvp->user->email,
            ucfirst($rsvp->status),
            $rsvp->created_at->format('Y-m-d H:i'),
        ];
    }
}
