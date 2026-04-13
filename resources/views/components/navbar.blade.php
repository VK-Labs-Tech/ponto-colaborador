<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top rounded-4 mt-2 mx-2 px-3">
    <div class="container">

        <a class="navbar-brand d-flex align-items-center gap-2 fw-semibold" href="{{ route('dashboard.index') }}">
            <div class="brand-icon">
                <i class="bi bi-clock-history"></i>
            </div>
            <span>Ponto</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuApp">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuApp">

            @auth
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    @foreach(config('menu')[auth()->user()->role] ?? [] as $item)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs($item['route']) ? 'active fw-semibold text-white bg-primary rounded-3 px-3' : '' }}"
                                href="{{ route($item['route']) }}">

                                <i class="bi {{ $item['icon'] }} me-1"></i>
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach

                </ul>

                {{-- Usuário --}}
                <div class="d-flex align-items-center gap-3">
                    <div class="d-none d-md-flex flex-column text-start">
                        <small class="fw-semibold">{{ auth()->user()->name }}</small>
                        <small class="text-muted">{{ auth()->user()->role }}</small>
                    </div>
                    <form method="POST" action="{{ route('company.logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger btn:hover:bg-red-500" type="submit">
                            <i class="bi bi-box-arrow-right me-2"></i> Sair
                        </button>
                    </form>
                </div>
            @endauth

        </div>
    </div>
</nav>