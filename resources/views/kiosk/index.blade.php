<x-layouts.app title="Bater ponto">
    <x-page-header
        title="Bater ponto"
        subtitle="Selecione o colaborador, informe o PIN e registre a batida"
    />

    <section class="container-fluid p-4 shadow-lg border rounded-4 bg-light">
        <h2 class="h4 mb-3">Registro de ponto por PIN</h2>

        <form method="POST" action="{{ route('kiosk.punch') }}" class="row g-3 align-items-end">
            @csrf

            <div class="col-12 col-md-5">
                <label class="form-label">Colaborador</label>
                <select class="form-select" name="employee_id" required>
                    <option value="">Selecione</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label">PIN</label>
                <input class="form-control" type="password" name="pin" inputmode="numeric" required>
            </div>

            <div class="col-12 col-md-2 d-grid">
                <button class="btn btn-primary" type="submit">Registrar</button>
            </div>
        </form>
    </section>
</x-layouts.app>
