<x-layouts.app title="Detalhe da Empresa">
    @php
        $statusLabels = [
            'trial' => 'Trial',
            'active' => 'Ativa',
            'overdue' => 'Inadimplente',
            'canceled' => 'Cancelada',
        ];
    @endphp

    <x-page-header
        title="{{ $company->name }}"
        subtitle="Visao detalhada de assinatura, uso de limites e historico financeiro"
    >
        <x-slot:actions>
            <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-secondary">Voltar</a>
        </x-slot:actions>
    </x-page-header>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="surface-card p-3">
                <p class="small text-secondary mb-1">Plano</p>
                <h5 class="mb-0">{{ $plan?->name ?? '-' }}</h5>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="surface-card p-3">
                <p class="small text-secondary mb-1">Status assinatura</p>
                <h5 class="mb-0">{{ $statusLabels[$subscription?->status] ?? '-' }}</h5>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="surface-card p-3">
                <p class="small text-secondary mb-1">Vencimento ciclo</p>
                <h5 class="mb-0">{{ optional($subscription?->current_period_ends_at)->format('d/m/Y') ?? '-' }}</h5>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-4">
            <section class="surface-card p-4 h-100">
                <h2 class="h5 mb-3">Uso de limites</h2>

                <div class="mb-3">
                    <p class="mb-1">Colaboradores</p>
                    <div class="progress" role="progressbar" aria-valuenow="{{ $limits['employees']['used'] }}" aria-valuemin="0" aria-valuemax="{{ $limits['employees']['limit'] ?? 1 }}">
                        <div class="progress-bar" style="width: {{ $limits['employees']['limit'] ? min(100, ($limits['employees']['used'] / $limits['employees']['limit']) * 100) : 0 }}%"></div>
                    </div>
                    <small class="text-muted">{{ $limits['employees']['used'] }} / {{ $limits['employees']['limit'] ?? '-' }}</small>
                </div>

                <div class="mb-3">
                    <p class="mb-1">Usuarios</p>
                    <div class="progress" role="progressbar" aria-valuenow="{{ $limits['users']['used'] }}" aria-valuemin="0" aria-valuemax="{{ $limits['users']['limit'] ?? 1 }}">
                        <div class="progress-bar bg-info" style="width: {{ $limits['users']['limit'] ? min(100, ($limits['users']['used'] / $limits['users']['limit']) * 100) : 0 }}%"></div>
                    </div>
                    <small class="text-muted">{{ $limits['users']['used'] }} / {{ $limits['users']['limit'] ?? '-' }}</small>
                </div>

                <div>
                    <p class="mb-1">Exportacoes mensais</p>
                    <div class="progress" role="progressbar" aria-valuenow="{{ $limits['exports']['used'] }}" aria-valuemin="0" aria-valuemax="{{ $limits['exports']['limit'] ?? 1 }}">
                        <div class="progress-bar bg-warning" style="width: {{ $limits['exports']['limit'] ? min(100, ($limits['exports']['used'] / $limits['exports']['limit']) * 100) : 0 }}%"></div>
                    </div>
                    <small class="text-muted">{{ $limits['exports']['used'] }} / {{ $limits['exports']['limit'] ?? '-' }}</small>
                </div>
            </section>
        </div>

        <div class="col-12 col-lg-8">
            <section class="surface-card p-4 h-100">
                <h2 class="h5 mb-3">Timeline da assinatura</h2>

                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Evento</th>
                                <th>Ator</th>
                                <th>Detalhes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                                <tr>
                                    <td>{{ $event->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $event->event }}</td>
                                    <td>{{ $event->actor }}</td>
                                    <td>{{ $event->payload ? json_encode($event->payload, JSON_UNESCAPED_UNICODE) : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Sem eventos de assinatura.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <section class="surface-card p-4">
        <h2 class="h5 mb-3">Historico financeiro</h2>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Referencia</th>
                        <th>Plano</th>
                        <th>Valor</th>
                        <th>Vencimento</th>
                        <th>Pagamento</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->reference_month->format('m/Y') }}</td>
                            <td>{{ $invoice->plan?->name ?? '-' }}</td>
                            <td>R$ {{ number_format($invoice->amount_cents / 100, 2, ',', '.') }}</td>
                            <td>{{ optional($invoice->due_at)->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ optional($invoice->paid_at)->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ $invoice->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Sem faturas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-layouts.app>
