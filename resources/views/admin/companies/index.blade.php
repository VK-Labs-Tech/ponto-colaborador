<x-layouts.app title="Empresas">
    <x-page-header
        title="Cadastro de empresas"
        subtitle="Area do dono do SaaS para criar empresas e liberar acesso EMPRESA"
    />

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
                                <th>Slug</th>
                                <th>Login</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($companies as $company)
                                <tr>
                                    <td>{{ $company->name }}</td>
                                    <td>{{ $company->slug }}</td>
                                    <td>{{ $company->login }}</td>
                                    <td>
                                        @if($company->is_active)
                                            <span class="badge text-bg-success">Ativa</span>
                                        @else
                                            <span class="badge text-bg-secondary">Inativa</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Nenhuma empresa cadastrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>
