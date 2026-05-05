<x-layouts.app title="Relatorios">
    @php
        $canAdjustPunch = auth()->check() && in_array(auth()->user()->role, ['company_editor', 'company_admin'], true);
        $canCloseMonth = auth()->check() && auth()->user()->role === 'company_admin';
    @endphp

    <x-page-header
        title="Relatorios"
        subtitle="Espelho de ponto, totais, exportacoes e fechamento mensal"
    />

    <x-report-filters :employees="$employees" :filters="$filters" />

    <section class="container-fluid p-4 shadow-lg border rounded-4 bg-light p-md-4 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <h2 class="h5 mb-0">Espelho de ponto</h2>

            <div class="d-flex gap-2">
                <a class="btn btn-outline-brand btn-sm" href="{{ route('reports.export.pdf', array_merge(request()->query(), ['snapshot_id' => $snapshot->id])) }}"><i class="bi bi-file-earmark-pdf"></i> Exportar PDF</a>
                <a class="btn btn-outline-success btn-sm rounded-3" href="{{ route('reports.export.excel', array_merge(request()->query(), ['snapshot_id' => $snapshot->id])) }}"><i class="bi bi-file-earmark-excel"></i> Exportar Excel</a>
            </div>
        </div>

        <div class="alert alert-light border mb-3">
            <strong>Snapshot oficial:</strong> #{{ $snapshot->id }} |
            <strong>Hash:</strong> <code>{{ $snapshot->content_hash }}</code> |
            <strong>Versao:</strong> {{ $snapshot->version }} |
            <strong>Ciencia:</strong> {{ $snapshot->signed_at ? $snapshot->signed_at->format('d/m/Y H:i') : 'Pendente' }}
            <form method="POST" action="{{ route('reports.acknowledge') }}" class="d-inline ms-2">
                @csrf
                <input type="hidden" name="snapshot_id" value="{{ $snapshot->id }}">
                <button class="btn btn-sm btn-outline-dark" type="submit">Registrar ciencia</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Colaborador</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Acao</th>
                        <th>Atraso</th>
                        <th>Ajuste</th>
                        @if($canAdjustPunch)
                            <th>Acoes</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($report['punch_rows'] as $row)
                        <tr>
                            <td>{{ $row['employee_name'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}</td>
                            <td>{{ $row['time'] }}</td>
                            <td>{{ $row['action_label'] }}</td>
                            <td>
                                @if($row['is_late'])
                                    <span class="badge text-bg-warning">Sim</span>
                                @else
                                    <span class="badge text-bg-success">Nao</span>
                                @endif
                            </td>
                            <td>
                                @if($row['is_adjustment'])
                                    <span class="badge text-bg-info">Manual</span>
                                @else
                                    <span class="badge text-bg-secondary">Original</span>
                                @endif
                            </td>
                            @if($canAdjustPunch)
                                <td>
                                    <a class="btn btn-sm btn-outline-brand" href="{{ route('reports.adjust.edit', ['employee_id' => $row['employee_id'], 'date' => $row['date']]) }}">
                                        Ajustar dia
                                    </a>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canAdjustPunch ? 7 : 6 }}" class="text-center text-muted">Nenhum registro encontrado para o filtro informado.</td>
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
