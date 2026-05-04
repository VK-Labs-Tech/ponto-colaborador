<div>
    <div class="row g-3">
        <div class="col">
            <form method="POST" action="{{ route('employees.store') }}" class="row g-3">
                @csrf

                <div class="col-12">
                    <label class="form-label">Nome</label>
                    <input class="form-control" type="text" name="name" value="{{ old('name') }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Matricula</label>
                    <input class="form-control" type="text" name="registration" value="{{ old('registration') }}"
                        placeholder="Opcional">
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">PIN</label>
                    <input class="form-control" type="password" name="pin" inputmode="numeric" required>
                </div>

                <div class="col-12 col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active"
                            value="1" checked>
                        <label class="form-check-label" for="is_active">Ativo</label>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Inicio do expediente</label>
                    <input class="form-control" type="time" name="shift_start" value="{{ old('shift_start') }}"
                        required>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Saida para almoço</label>
                    <input class="form-control" type="time" name="lunch_start" value="{{ old('lunch_start') }}"
                        required>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Retorno do almoço</label>
                    <input class="form-control" type="time" name="lunch_end" value="{{ old('lunch_end') }}" required>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Fim do expediente</label>
                    <input class="form-control" type="time" name="shift_end" value="{{ old('shift_end') }}" required>
                </div>

                <div class="col-12 d-grid">
                    <button class="btn btn-brand" type="submit">Salvar funcionario</button>
                </div>
            </form>
        </div>
    </div>
</div>