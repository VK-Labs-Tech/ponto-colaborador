
<x-layouts.app title="Empresas">
    @php
        $subscriptionLabels = [
            'trial' => 'Trial',
            'active' => 'Ativa',
            'overdue' => 'Inadimplente',
            'canceled' => 'Cancelada',
        ];

        $subscriptionBadges = [
            'trial' => 'text-bg-info',
            'active' => 'text-bg-success',
            'overdue' => 'text-bg-warning',
            'canceled' => 'text-bg-secondary',
        ];
    @endphp

    <x-page-header
        title="Cadastro de empresas"
        subtitle="Area do dono do SaaS para criar empresas e liberar acesso EMPRESA"
    />

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="surface-card p-3">
                <p class="small text-secondary mb-1">MRR estimado</p>
                <h4 class="mb-0">R$ {{ $metrics['mrr_brl'] }}</h4>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="surface-card p-3">
                <p class="small text-secondary mb-1">Ativas</p>
                <h4 class="mb-0">{{ $metrics['active_count'] }}</h4>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="surface-card p-3">
                <p class="small text-secondary mb-1">Trial</p>
                <h4 class="mb-0">{{ $metrics['trial_count'] }}</h4>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="surface-card p-3">
                <p class="small text-secondary mb-1">Inadimplentes</p>
                <h4 class="mb-0">{{ $metrics['overdue_count'] }}</h4>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-5">
            <section class="surface-card p-4" style="background:linear-gradient(180deg,#fff 0%,#ecfeff 100%);">
                <h2 class="h5 mb-3">Nova empresa</h2>

                <form method="POST" action="{{ route('admin.companies.store') }}" class="row g-3">
                    @csrf

                    <div class="col-12">
                        <label class="form-label">Nome da empresa</label>
                        <input class="form-control" type="text" name="name" value="{{ old('name') }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Login da empresa (kiosk)</label>
                        <input class="form-control" type="text" name="company_login" value="{{ old('company_login') }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Senha da empresa (kiosk)</label>
                        <input class="form-control" type="password" name="company_password" required>
                    </div>

                    <hr class="my-2">

                    <div class="col-12">
                        <label class="form-label">Nome do admin da empresa</label>
                        <input class="form-control" type="text" name="admin_name" value="{{ old('admin_name') }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">E-mail do admin da empresa</label>
                        <input class="form-control" type="email" name="admin_email" value="{{ old('admin_email') }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Senha do admin da empresa</label>
                        <input class="form-control" type="password" name="admin_password" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Plano inicial</label>
                        <select class="form-select" name="plan_code" required>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->code }}" @selected(old('plan_code', 'starter') === $plan->code)>
                                    {{ $plan->name }} ({{ $plan->employee_limit }} colaboradores / {{ $plan->user_limit }} usuarios)
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Toda empresa inicia com 7 dias de teste gratis.</small>
                    </div>

                    <div class="col-12 d-grid">
                        <button class="btn btn-brand" type="submit">Cadastrar empresa</button>
                    </div>
                </form>
            </section>
        </div>

        <div class="col-12 col-lg-7">
            <section class="surface-card p-4">
                <h2 class="h5 mb-3">Empresas cadastradas</h2>

                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Empresa</th>
                                <th>Login</th>
                                <th>Plano</th>
                                <th>Assinatura</th>
                                <th>Ciclo / trial</th>
                                <th>Status</th>
                                <th class="text-end">Acoes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($companies as $company)
                                <tr>
                                    <td>{{ $company->name }}</td>
                                    <td>{{ $company->login }}</td>
                                    <td>{{ $company->subscription?->plan?->name ?? '-' }}</td>
                                    <td>
                                        @php
                                            $status = $company->subscription?->status;
                                        @endphp
                                        @if($status)
                                            <span class="badge {{ $subscriptionBadges[$status] ?? 'text-bg-light' }}">
                                                {{ $subscriptionLabels[$status] ?? strtoupper($status) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($company->subscription?->status === 'trial')
                                            Trial ate {{ optional($company->subscription?->trial_ends_at)->format('d/m/Y') ?? '-' }}
                                        @else
                                            Ate {{ optional($company->subscription?->current_period_ends_at)->format('d/m/Y') ?? '-' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($company->is_active)
                                            <span class="badge text-bg-success">Ativa</span>
                                        @else
                                            <span class="badge text-bg-secondary">Inativa</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.companies.show', $company->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                            Detalhes
                                        </a>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-dark"
                                            data-bs-toggle="modal"
                                            data-bs-target="#manageSubscriptionModal{{ $company->id }}"
                                        >
                                            Gerenciar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Nenhuma empresa cadastrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            @foreach($companies as $company)
                <div class="modal fade" id="manageSubscriptionModal{{ $company->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header">
                                <h5 class="modal-title">Gerenciar assinatura - {{ $company->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                            </div>

                            <div class="modal-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-md-4">
                                        <div class="p-3 rounded-3 border">
                                            <small class="text-secondary d-block">Plano atual</small>
                                            <strong>{{ $company->subscription?->plan?->name ?? '-' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="p-3 rounded-3 border">
                                            <small class="text-secondary d-block">Status</small>
                                            <strong>{{ $subscriptionLabels[$company->subscription?->status] ?? '-' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="p-3 rounded-3 border">
                                            <small class="text-secondary d-block">Ciclo</small>
                                            <strong>{{ optional($company->subscription?->current_period_ends_at)->format('d/m/Y') ?? '-' }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2 mb-4">
                                    <form method="POST" action="{{ route('admin.companies.subscription.update', $company->id) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="activate">
                                        <input type="hidden" name="months" value="1">
                                        <button class="btn btn-success" type="submit">Ativar por 1 mes</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.companies.subscription.update', $company->id) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="overdue">
                                        <button class="btn btn-warning" type="submit">Marcar inadimplente</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.companies.subscription.update', $company->id) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="cancel">
                                        <button class="btn btn-outline-danger" type="submit">Cancelar assinatura</button>
                                    </form>
                                </div>

                                <form method="POST" action="{{ route('admin.companies.subscription.update', $company->id) }}" class="row g-2 align-items-end">
                                    @csrf
                                    <input type="hidden" name="action" value="change_plan">
                                    <div class="col-12 col-md-8">
                                        <label class="form-label">Trocar plano</label>
                                        <select class="form-select" name="plan_code" required>
                                            @foreach($plans as $plan)
                                                <option value="{{ $plan->code }}" @selected($company->subscription?->plan?->code === $plan->code)>
                                                    {{ $plan->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4 d-grid">
                                        <button class="btn btn-outline-primary" type="submit">Salvar plano</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
