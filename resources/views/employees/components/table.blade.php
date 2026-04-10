@props(['employees'])
<div>
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
                        <td>{{ $employee->shift_start->format('H:i') }} - {{ $employee->lunch_start->format('H:i') }} - {{ $employee->lunch_end->format('H:i') }} - {{ $employee->shift_end->format('H:i') }}</td>
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
</div>