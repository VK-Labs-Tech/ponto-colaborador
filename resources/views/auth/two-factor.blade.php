<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação 2FA</title>

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

            {{-- LADO ESQUERDO --}}
            <div
                class="col-lg-6 d-none d-lg-flex flex-column justify-content-center align-items-center bg-primary text-white p-5">
                <div class="text-center">
                    <h1 class="fw-bold display-5">Segurança Avançada</h1>
                    <p class="mt-3 opacity-75">
                        Protegemos o acesso ao sistema com autenticação em duas etapas.
                    </p>

                    <div class="mt-4">
                        <i class="bi bi-shield-lock fs-1"></i>
                    </div>
                </div>
            </div>

            {{-- LADO DIREITO --}}
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-4">

                <div class="w-100" style="max-width: 420px;">

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-md-5">

                            <div class="text-center mb-4">
                                <span class="badge bg-primary-subtle text-primary mb-3">
                                    <i class="bi bi-shield-check"></i> Verificação 2FA
                                </span>

                                <h2 class="fw-bold">Confirme seu acesso</h2>
                                <p class="text-muted small">
                                    Digite o código de 6 dígitos enviado ao e-mail
                                </p>
                            </div>

                            <x-flash />

                            <form action="{{ route('company.2fa.verify') }}" method="POST" class="d-grid gap-3 mb-3">
                                @csrf

                                <div class="form-floating text-center">
                                    <input type="text" name="code"
                                        class="form-control text-center fs-4 fw-bold tracking-wide" id="code"
                                        placeholder="000000" inputmode="numeric" maxlength="6" required>
                                    <label for="code">
                                        <i class="bi bi-key"></i> Código 2FA
                                    </label>
                                </div>

                                <button class="btn btn-primary fw-bold py-2">
                                    <i class="bi bi-check-circle"></i> Validar código
                                </button>
                            </form>

                            <form action="{{ route('company.2fa.resend') }}" method="POST" class="d-grid">
                                @csrf
                                <button class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-repeat"></i> Reenviar código
                                </button>
                            </form>

                            <div class="text-center mt-4">
                                <small class="text-muted">
                                    Não recebeu? Verifique o spam ou aguarde alguns segundos.
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