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
    <p class="meta">Empresa: {{ $companyName }} | Periodo: {{ $from->format('d/m/Y') }} a {{ $to->format('d/m/Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Colaborador</th>
                <th>Data</th>
                <th>Entrada 1</th>
                <th>Saida 1</th>
                <th>Entrada 2</th>
                <th>Saida 2</th>
                <th>Total</th>
                <th>Extras</th>
                <th>Atraso</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td>{{ $row['employee_name'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}</td>
                    <td>{{ $row['entry_1'] }}</td>
                    <td>{{ $row['exit_1'] }}</td>
                    <td>{{ $row['entry_2'] }}</td>
                    <td>{{ $row['exit_2'] }}</td>
                    <td>{{ $row['worked_hours'] }}</td>
                    <td>{{ $row['overtime_hours'] }}</td>
                    <td>{{ $row['is_late'] ? 'Sim' : 'Nao' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Total horas:</strong> {{ $totals['worked_hours'] }} | <strong>Total extras:</strong> {{ $totals['overtime_hours'] }}</p>
</body>
</html>
