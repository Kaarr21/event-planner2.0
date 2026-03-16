<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class TasksExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $eventId;

    public function __construct(int $eventId)
    {
        $this->eventId = $eventId;
    }

    public function query()
    {
        return Task::query()
            ->where('event_id', $this->eventId)
            ->with('assignee');
    }

    public function headings(): array
    {
        return [
            'Task Title',
            'Description',
            'Status',
            'Assigned To',
            'Due Date',
            'Completed',
        ];
    }

    public function map($task): array
    {
        return [
            $task->title,
            $task->description ?? 'N/A',
            $task->assignment_status ?? 'Pending',
            $task->assignee ? $task->assignee->name : 'Unassigned',
            $task->due_date ? $task->due_date->format('Y-m-d') : 'No Date',
            $task->completed ? 'Yes' : 'No',
        ];
    }
}
