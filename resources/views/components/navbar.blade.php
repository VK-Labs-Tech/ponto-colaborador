<nav class="navbar navbar-expand-lg border-bottom sticky-top surface-glass">
    <div class="container">
        @php
            $homeRoute = 'dashboard.index';
            if (auth()->check() && auth()->user()->role === 'company_operator') {
                $homeRoute = 'kiosk.index';
            }
            if (auth()->check() && auth()->user()->role === 'saas_admin') {
                $homeRoute = 'admin.companies.index';
            }
        @endphp

        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route($homeRoute) }}">
            <span class="px-2 py-1 rounded-2" style="background:#fff7ed;color:#9a3412;"><i class="bi bi-clock-history"></i></span>
            <span>Ponto Colaborador</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuApp" aria-controls="menuApp" aria-expanded="false" aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuApp">
            @if(auth()->check())
                @if(auth()->user()->role === 'saas_admin')
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1">
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.companies.index') }}">Empresas</a></li>
                    </ul>

                    <div class="d-flex align-items-center gap-3">
                        <span class="chip-soft"><i class="bi bi-person-badge"></i> ADM SaaS</span>
                        <form method="POST" action="{{ route('company.logout') }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger rounded-3" type="submit">Sair</button>
                        </form>
                    </div>
                @elseif(auth()->user()->role === 'company_admin')
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1">
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard.index') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('kiosk.index') }}">Bater ponto</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('employees.index') }}">Funcionarios</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('company-users.index') }}">Usuarios</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('reports.index') }}">Relatorios</a></li>
                    </ul>

                    <div class="d-flex align-items-center gap-3">
                        <span class="chip-soft"><i class="bi bi-buildings"></i> {{ session('company_name') }}</span>
                        <form method="POST" action="{{ route('company.logout') }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger rounded-3" type="submit">Sair</button>
                        </form>
                    </div>
                @elseif(auth()->user()->role === 'company_editor')
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1">
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard.index') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('reports.index') }}">Relatorios</a></li>
                    </ul>

                    <div class="d-flex align-items-center gap-3">
                        <span class="chip-soft"><i class="bi bi-pencil-square"></i> Editor de ponto</span>
                        <form method="POST" action="{{ route('company.logout') }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger rounded-3" type="submit">Sair</button>
                        </form>
                    </div>
                @elseif(auth()->user()->role === 'company_operator')
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1">
                        <li class="nav-item"><a class="nav-link" href="{{ route('kiosk.index') }}">Bater ponto</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('reports.index') }}">Relatorios</a></li>
                    </ul>

                    <div class="d-flex align-items-center gap-3">
                        <span class="chip-soft"><i class="bi bi-person-workspace"></i> Operador</span>
                        <form method="POST" action="{{ route('company.logout') }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger rounded-3" type="submit">Sair</button>
                        </form>
                    </div>
                @endif
            @endif
        </div>
    </div>
</nav>
