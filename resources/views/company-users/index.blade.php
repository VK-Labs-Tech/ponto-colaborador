<x-layouts.app title="Usuarios da Empresa">
    <x-page-header
        title="Usuarios da empresa"
        subtitle="Crie usuarios com perfis de administrador, editor e operador"
    />

    <div class="row g-4">
        <div class="col-12 col-lg-5">
            <section class="surface-card p-4">
                <h2 class="h5 mb-3">Novo usuario</h2>

                @if(auth()->user()->role === 'company_admin')
                    <form method="POST" action="{{ route('company-users.store') }}" class="row g-3">
                        @csrf

                        <div class="col-12">
                            <label class="form-label">Nome</label>
                            <input class="form-control" type="text" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">E-mail</label>
                            <input class="form-control" type="email" name="email" value="{{ old('email') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Senha</label>
                            <input class="form-control" type="password" name="password" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Perfil</label>
                            <select class="form-select" name="role" required>
                                <option value="company_operator">Operador (Bater ponto + Relatorios)</option>
                                <option value="company_editor">Editor de ponto</option>
                                <option value="company_admin">Administrador da empresa</option>
                            </select>
                        </div>

                        <div class="col-12 d-grid">
                            <button class="btn btn-brand" type="submit">Cadastrar usuario</button>
                        </div>
                    </form>
                @else
                    <p class="text-muted mb-0">Apenas administradores da empresa podem criar novos usuarios.</p>
                @endif
            </section>
        </div>

        <div class="col-12 col-lg-7">
            <section class="surface-card p-4">
                <h2 class="h5 mb-3">Usuarios cadastrados</h2>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Perfil</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->role === 'company_admin')
                                            <span class="badge text-bg-primary">Administrador</span>
                                        @elseif($user->role === 'company_operator')
                                            <span class="badge text-bg-success">Operador</span>
                                        @else
                                            <span class="badge text-bg-warning">Editor de ponto</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Nenhum usuario cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>
