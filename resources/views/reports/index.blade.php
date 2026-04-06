<x-layouts.app title="Relatorios">
    @php
        $canAdjustPunch = auth()->check() && auth()->user()->role === 'company_editor';
        $canCloseMonth = auth()->check() && auth()->user()->role === 'company_admin';
    @endphp

    <x-page-header
        title="Relatorios"
        subtitle="Espelho de ponto, totais, exportacoes e fechamento mensal"
    />

    <x-report-filters :employees="$employees" :filters="$filters" />

    <section class="surface-card p-3 p-md-4 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <h2 class="h5 mb-0">Espelho de ponto</h2>

            <div class="d-flex gap-2">
                <a class="btn btn-outline-brand btn-sm" href="{{ route('reports.export.pdf', request()->query()) }}"><i class="bi bi-file-earmark-pdf"></i> Exportar PDF</a>
                <a class="btn btn-outline-success btn-sm rounded-3" href="{{ route('reports.export.excel', request()->query()) }}"><i class="bi bi-file-earmark-excel"></i> Exportar Excel</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Colaborador</th>
                        <th>Data</th>
                        <th>Entrada 1</th>
                        <th>Saida 1</th>
                        <th>Entrada 2</th>
                        <th>Saida 2</th>
                        <th>Total de horas</th>
                        <th>Horas extras</th>
                        <th>Atraso</th>
                        @if($canAdjustPunch)
                            <th>Acoes</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($report['rows'] as $row)
                        <tr>
                            <td>{{ $row['employee_name'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}</td>
                            <td>{{ $row['entry_1'] }}</td>
                            <td>{{ $row['exit_1'] }}</td>
                            <td>{{ $row['entry_2'] }}</td>
                            <td>{{ $row['exit_2'] }}</td>
                            <td>{{ $row['worked_hours'] }}</td>
                            <td>{{ $row['overtime_hours'] }}</td>
                            <td>
                                @if($row['is_late'])
                                    <span class="badge text-bg-warning">Sim</span>
                                @else
                                    <span class="badge text-bg-success">Nao</span>
                                @endif
                            </td>
                            @if($canAdjustPunch)
                                <td>
                                    <a class="btn btn-sm btn-outline-brand" href="{{ route('reports.adjust.edit', ['employee_id' => $row['employee_id'], 'date' => $row['date']]) }}">
                                        Ajustar
                                    </a>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canAdjustPunch ? 10 : 9 }}" class="text-center text-muted">Nenhum registro encontrado para o filtro informado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex flex-wrap gap-3">
            <div class="chip-soft">Total horas: {{ $report['totals']['worked_hours'] }}</div>
            <div class="chip-soft">Total extras: {{ $report['totals']['overtime_hours'] }}</div>
        </div>
    </section>

    @if($canCloseMonth)
        <section class="surface-card p-3 p-md-4">
            <h2 class="h5">Fechamento mensal</h2>
            <form method="POST" action="{{ route('monthly-closures.store') }}" class="row g-3 align-items-end">
                @csrf

                <div class="col-12 col-md-4">
                    <label class="form-label">Ano</label>
                    <input type="number" name="year" class="form-control" min="2000" max="2100" value="{{ now()->year }}" required>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Mes</label>
                    <input type="number" name="month" class="form-control" min="1" max="12" value="{{ now()->month }}" required>
                </div>

                <div class="col-12 col-md-4 d-grid">
                    <button class="btn btn-danger" type="submit">Fechar mes</button>
                </div>
            </form>
            <small class="text-muted d-block mt-2">Apos fechar, nao sera permitido registrar ou ajustar ponto para este mes.</small>
        </section>
    @endif
</x-layouts.app>
