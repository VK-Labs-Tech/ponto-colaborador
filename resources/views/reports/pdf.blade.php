<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Espelho de Ponto</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { margin-bottom: 0; }
        .meta { margin-bottom: 14px; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f3f3f3; }
    </style>
</head>
<body>
    <h1>Espelho de ponto</h1>
    <p class="meta">
        Empresa: {{ $companyName }} | Periodo: {{ $from->format('d/m/Y') }} a {{ $to->format('d/m/Y') }}<br>
        Snapshot: #{{ $snapshot->id }} | Hash: {{ $snapshot->content_hash }} | Versao: {{ $snapshot->version }}<br>
        Ciencia do colaborador: {{ $snapshot->signed_at ? $snapshot->signed_at->format('d/m/Y H:i') : 'Pendente' }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Colaborador</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Acao</th>
                <th>Atraso</th>
                <th>Ajuste</th>
                <th>Justificativa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td>{{ $row['employee_name'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}</td>
                    <td>{{ $row['time'] }}</td>
                    <td>{{ $row['action_label'] }}</td>
                    <td>{{ $row['is_late'] ? 'Sim' : 'Nao' }}</td>
                    <td>{{ $row['is_adjustment'] ? 'Sim' : 'Nao' }}</td>
                    <td>{{ $row['note'] ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Total horas:</strong> {{ $totals['worked_hours'] }} | <strong>Total extras:</strong> {{ $totals['overtime_hours'] }}</p>
</body>
</html>
