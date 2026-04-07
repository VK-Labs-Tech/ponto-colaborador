<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assinatura bloqueada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh;">
    <main class="container">
        <div class="card shadow-sm border-0 mx-auto" style="max-width:700px;">
            <div class="card-body p-4 p-md-5">
                <h1 class="h3 mb-2">Acesso temporariamente bloqueado</h1>
                <p class="text-muted mb-4">
                    A empresa {{ $companyName }} esta com assinatura no status <strong>{{ $status }}</strong>.
                </p>

                <div class="alert alert-warning mb-4">
                    Renove a assinatura ou troque para um plano ativo para liberar o acesso aos modulos da empresa.
                </div>

                <a href="{{ route('company.logout') }}" class="btn btn-outline-secondary" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Sair
                </a>

                <form id="logout-form" method="POST" action="{{ route('company.logout') }}" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </main>
</body>
</html>
