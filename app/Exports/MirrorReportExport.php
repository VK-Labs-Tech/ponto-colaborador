<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MirrorReportExport implements FromArray, WithHeadings
{
    public function __construct(private readonly array $rows)
    {
    }

    public function array(): array
    {
        return array_map(fn (array $row) => [
            $row['employee_name'],
            $row['date'],
            $row['time'],
            $row['action_label'],
            $row['is_late'] ? 'Sim' : 'Nao',
        ], $this->rows);
    }

    public function headings(): array
    {
        return [
            'Colaborador',
            'Data',
            'Hora',
            'Acao',
            'Atraso',
        ];
    }
}
