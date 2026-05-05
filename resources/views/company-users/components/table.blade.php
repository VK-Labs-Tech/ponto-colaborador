@props(['users'])
<div class="container-fluid p-0">
  <div class="row">
    <div class="col">
      <div class="table-responsive" style="max-height: 400px; overflow-y: auto; overflow-x: auto;">
        <table class="table table-striped table-hover align-middle text-center bg-white rounded-3 shadow-sm">
          <thead>
            <tr>
              <th>Nome</th>
              <th>E-mail</th>
              <th>Perfil</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users as $user)
              <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                  @if($user->role === 'company_admin')
                    <span class="badge text-bg-primary">Administrador</span>
                  @elseif($user->role === 'company_operator')
                    <span class="badge text-bg-success">Operador</span>
                  @else
                    <span class="badge text-bg-secondary">Editor de ponto</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('company-users.edit', $user->id) }}" class="btn btn-sm btn-warning">
                    Editar
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted">Nenhum usuario cadastrado.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>