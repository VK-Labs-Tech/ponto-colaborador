<x-layouts.app title="Ajustar Batidas">
    <x-page-header
        title="Ajustar batidas"
        subtitle="Edite as 4 batidas do dia para corrigir esquecimentos ou erros"
    />

    <section class="surface-card p-4">
        <h2 class="h5 mb-3">{{ $employee->name }} - {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h2>

        <form method="POST" action="{{ route('reports.adjust.update') }}" class="row g-3">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
            <input type="hidden" name="date" value="{{ $date }}">

            <div class="col-12 col-md-3">
                <label class="form-label">Entrada 1</label>
                <input type="time" name="entry_1" class="form-control" value="{{ old('entry_1', $values['entry_1']) }}">
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label">Saida 1</label>
                <input type="time" name="exit_1" class="form-control" value="{{ old('exit_1', $values['exit_1']) }}">
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label">Entrada 2</label>
                <input type="time" name="entry_2" class="form-control" value="{{ old('entry_2', $values['entry_2']) }}">
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label">Saida 2</label>
                <input type="time" name="exit_2" class="form-control" value="{{ old('exit_2', $values['exit_2']) }}">
            </div>

            <div class="col-12">
                <label class="form-label">Motivo do ajuste</label>
                <input type="text" name="reason" class="form-control" value="{{ old('reason') }}" required placeholder="Ex.: colaborador esqueceu de bater retorno do almoco">
            </div>

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-brand" type="submit">Salvar ajuste</button>
                <a class="btn btn-outline-secondary" href="{{ route('reports.index', ['from' => $date, 'to' => $date, 'employee_id' => $employee->id]) }}">Voltar</a>
            </div>
        </form>
    </section>
</x-layouts.app>
