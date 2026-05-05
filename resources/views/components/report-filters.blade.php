@props([
    'employees',
    'filters',
])

<div class="container-fluid p-4 shadow-lg border rounded-4 bg-light mb-4">
    <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end">
        <div class="col-12 col-md-3">
            <label class="form-label">De</label>
            <input type="date" name="from" value="{{ $filters['from'] }}" class="form-control" required>
        </div>

        <div class="col-12 col-md-3">
            <label class="form-label">Ate</label>
            <input type="date" name="to" value="{{ $filters['to'] }}" class="form-control" required>
        </div>

        <div class="col-12 col-md-4">
            <label class="form-label">Colaborador</label>
            <select class="form-select" name="employee_id">
                <option value="">Todos</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" @selected((string) $filters['employee_id'] === (string) $employee->id)>
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-2 d-grid">
            <button class="btn btn-success" type="submit">Filtrar</button>
        </div>
    </form>
</div>
