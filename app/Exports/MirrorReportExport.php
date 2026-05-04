<?php

namespace App\Exports;

use App\Models\MirrorSnapshot;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MirrorReportExport implements FromArray, WithHeadings
{
    public function __construct(
        private readonly array $rows,
        private readonly ?MirrorSnapshot $snapshot = null
    )
    {
    }

    public function array(): array
    {
        $dataRows = array_map(fn (array $row) => [
            $row['employee_name'],
            $row['date'],
            $row['time'],
            $row['action_label'],
            $row['is_late'] ? 'Sim' : 'Nao',
            $row['is_adjustment'] ? 'Sim' : 'Nao',
            $row['note'] ?? '',
        ], $this->rows);

        if (! $this->snapshot) {
            return $dataRows;
        }

        array_unshift($dataRows, [
            'Snapshot #'.$this->snapshot->id,
            'Hash '.$this->snapshot->content_hash,
            'Versao '.$this->snapshot->version,
            'Assinado '.($this->snapshot->signed_at?->format('d/m/Y H:i:s') ?? 'Nao'),
            '',
        ]);

        return $dataRows;
    }

    public function headings(): array
    {
        return [
            'Colaborador',
            'Data',
            'Hora',
            'Acao',
            'Atraso',
            'Ajuste',
            'Justificativa',
        ];
    }
}
