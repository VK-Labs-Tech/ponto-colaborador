<x-layouts.app title="Funcionarios">
    <x-page-header
        title="Funcionarios"
        subtitle="Cadastre e organize o quadro de colaboradores da empresa"
    />

    <div class="row g-4">
        <div class="col-12 col-lg-5">
            <section class="surface-card p-4" style="background:linear-gradient(180deg,#fff 0%,#fff7ed 100%);">
                <h2 class="h5 mb-3">Cadastrar funcionario</h2>

                <form method="POST" action="{{ route('employees.store') }}" class="row g-3">
                    @csrf

                    <div class="col-12">
                        <label class="form-label">Nome</label>
                        <input class="form-control" type="text" name="name" value="{{ old('name') }}" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Matricula</label>
                        <input class="form-control" type="text" name="registration" value="{{ old('registration') }}" placeholder="Opcional">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">PIN</label>
                        <input class="form-control" type="password" name="pin" inputmode="numeric" required>
                    </div>

                    <div class="col-12 col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Ativo</label>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Inicio do expediente</label>
                        <input class="form-control" type="time" name="shift_start" value="{{ old('shift_start', '08:00') }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Fim do expediente</label>
                        <input class="form-control" type="time" name="shift_end" value="{{ old('shift_end', '17:00') }}" required>
                    </div>

                    <div class="col-12 d-grid">
                        <button class="btn btn-brand" type="submit">Salvar funcionario</button>
                    </div>
                </form>
            </section>
        </div>

        <div class="col-12 col-lg-7">
            <section class="surface-card p-4">
                <h2 class="h5 mb-3">Lista de funcionarios</h2>
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Matricula</th>
                                <th>Jornada</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                <tr>
                                    <td>{{ $employee->name }}</td>
                                    <td>{{ $employee->registration ?: '-' }}</td>
                                    <td>{{ $employee->shift_start->format('H:i') }} - {{ $employee->shift_end->format('H:i') }}</td>
                                    <td>
                                        @if($employee->is_active)
                                            <span class="badge text-bg-success">Ativo</span>
                                        @else
                                            <span class="badge text-bg-secondary">Inativo</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Nenhum funcionario cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>
