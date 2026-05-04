<x-layouts.app>

<div class="container-fluid p-4 shadow-lg border rounded-4 bg-light">
    
    <h4 class="text-dark mt-2">Editar Funcionário</h4>

   <div class="row g-3">
     <form action="{{ route('employees.update', $employee->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nome</label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name', $employee->name) }}" required>
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label>Matrícula</label>
            <input type="text" name="registration" class="form-control"
                   value="{{ old('registration', $employee->registration) }}">
        </div>

        <div class="row">
            <div class="col-md-3 mb-3">
                <label>Entrada</label>
                <input type="time" name="shift_start" class="form-control"
                       value="{{ old('shift_start', $employee->shift_start?->format('H:i')) }}">
            </div>
            <div class="col-md-3 mb-3">
                <label>Início Almoço</label>
                <input type="time" name="lunch_start" class="form-control"
                       value="{{ old('lunch_start', $employee->lunch_start?->format('H:i')) }}">
            </div>
            <div class="col-md-3 mb-3">
                <label>Fim Almoço</label>
                <input type="time" name="lunch_end" class="form-control"
                       value="{{ old('lunch_end', $employee->lunch_end?->format('H:i')) }}">
            </div>
            <div class="col-md-3 mb-3">
                <label>Saída</label>
                <input type="time" name="shift_end" class="form-control"
                       value="{{ old('shift_end', $employee->shift_end?->format('H:i')) }}">
            </div>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="is_active" class="form-check-input" value="1"
                   {{ old('is_active', $employee->is_active) ? 'checked' : '' }}>
            <label class="form-check-label">Ativo</label>
        </div>

        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
   </div>
</div>
</x-layouts.app>
