<div>
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
        <button class="btn btn-primary" type="submit">Cadastrar usuario</button>
      </div>
    </form>
  @else
    <p class="text-muted mb-0">Apenas administradores da empresa podem criar novos usuarios.</p>
  @endif
</div>