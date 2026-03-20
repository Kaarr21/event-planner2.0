<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

class GuestTemplateExport implements WithHeadings
{
    use Exportable;

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Phone Number',
        ];
    }
}
