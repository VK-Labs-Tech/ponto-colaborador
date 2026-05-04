@props(['employees'])
<div class="container-fluid p-0">
    <div class="row">
        <div class="col">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto; overflow-x: auto;">
                <table class="table table-striped table-hover align-middle text-center bg-white rounded-3 shadow-sm">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Matricula</th>
                            <th>Jornada</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->registration ?: '-' }}</td>
                                <td>
                                    {{ $employee->shift_start?->format('H:i') ?? '--:--' }} -
                                    {{ $employee->lunch_start?->format('H:i') ?? '--:--' }} -
                                    {{ $employee->lunch_end?->format('H:i') ?? '--:--' }} -
                                    {{ $employee->shift_end?->format('H:i') ?? '--:--' }}
                                </td>
                                <td>
                                    @if($employee->is_active)
                                        <span class="badge text-bg-success">Ativo</span>
                                    @else
                                        <span class="badge text-bg-secondary">Inativo</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-primary">
                                        Editar
                                    </a>
                                    <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este funcionário?')">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Nenhum funcionario cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>