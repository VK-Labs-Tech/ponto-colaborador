<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ponto Colaborador</title>

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Manrope', sans-serif;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container-fluid">
        <div class="row min-vh-100">

            <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-center align-items-center bg-primary text-white p-5">
                <div class="text-center">
                    <h1 class="fw-bold display-5">Ponto Colaborador</h1>
                    <p class="mt-3 opacity-75">
                        Gerencie jornadas, controle horas e aumente a produtividade da sua equipe.
                    </p>

                    <div class="mt-4">
                        <i class="bi bi-graph-up-arrow fs-1"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-4">

                <div class="w-100" style="max-width: 420px;">

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-md-5">

                            <div class="text-center mb-4">
                                <span class="badge bg-primary-subtle text-primary mb-3">
                                    <i class="bi bi-shield-lock"></i> Acesso Seguro
                                </span>

                                <h2 class="fw-bold">Entrar</h2>
                                <p class="text-muted small">
                                    Acesse sua conta para continuar
                                </p>
                            </div>

                            <x-flash />

                            <form action="{{ route('company.login.submit') }}" method="POST" class="d-grid gap-3">
                                @csrf

                                <div class="form-floating">
                                    <input type="text" name="login" class="form-control" id="login"
                                        placeholder="usuario@email.com" value="{{ old('login') }}" required>
                                    <label for="login">
                                        <i class="bi bi-person"></i> Usuário ou e-mail
                                    </label>
                                </div>

                                <div class="form-floating">
                                    <input type="password" name="password" class="form-control" id="password"
                                        placeholder="Senha" required>
                                    <label for="password">
                                        <i class="bi bi-lock"></i> Senha
                                    </label>
                                </div>

                                <button class="btn btn-primary fw-bold py-2">
                                    <i class="bi bi-box-arrow-in-right"></i> Entrar
                                </button>
                            </form>

                            <div class="text-center mt-4">
                                <small class="text-muted">
                                    © {{ date('Y') }} Ponto Colaborador
                                </small>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <script>
        window.addEventListener('pageshow', function (event) {
            const nav = performance.getEntriesByType('navigation')[0];
            if (event.persisted || nav?.type === 'back_forward') {
                window.location.reload();
            }
        });
    </script>

</body>

</html>