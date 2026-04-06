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
            $row['entry_1'],
            $row['exit_1'],
            $row['entry_2'],
            $row['exit_2'],
            $row['worked_hours'],
            $row['overtime_hours'],
            $row['is_late'] ? 'Sim' : 'Nao',
        ], $this->rows);
    }

    public function headings(): array
    {
        return [
            'Colaborador',
            'Data',
            'Entrada 1',
            'Saida 1',
            'Entrada 2',
            'Saida 2',
            'Horas Trabalhadas',
            'Horas Extras',
            'Atraso',
        ];
    }
}
