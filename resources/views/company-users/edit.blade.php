<x-layouts.app title="Editar Usuario">

  <div>
    <x-page-header title="Editar usuario" subtitle="Atualize as informações do usuario" />

    <div class="row g-4">
      <div class="col-12 col-lg-6">
        <form action="{{ route('company-users.update', $user->id) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="mb-3">
            <label>Nome</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
          </div>
          <div class="mb-3">
            <label>E-mail</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
          </div>
          <div class="mb-3">
            <label>Perfil</label>
            <select name="role" class="form-select" required>
              <option value="company_admin" {{ old('role', $user->role) === 'company_admin' ? 'selected' : '' }}>
                Administrador</option>
              <option value="company_operator" {{ old('role', $user->role) === 'company_operator' ? 'selected' : '' }}>
                Operador</option>
              <option value="company_editor" {{ old('role', $user->role) === 'company_editor' ? 'selected' : '' }}>Editor
                de ponto</option>
            </select>
            @error('role') <span class="text-danger">{{ $message }}</span> @enderror
          </div>
          <button type="submit" class="btn btn-success">Salvar</button>
          <a href="{{ route('company-users.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>

      </div>
    </div>
</x-layouts.app>
</div>