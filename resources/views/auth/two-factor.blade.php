<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificacao 2FA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Manrope', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 15% 20%, #fde68a 0%, transparent 38%),
                        radial-gradient(circle at 82% 14%, #7dd3fc 0%, transparent 36%),
                        linear-gradient(145deg, #111827 0%, #1f2937 35%, #374151 100%);
        }

        .auth-card {
            width: min(500px, 92vw);
            border-radius: 1.25rem;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 18px 46px rgba(0, 0, 0, .32);
            border: 1px solid rgba(255, 255, 255, .28);
            backdrop-filter: blur(8px);
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: #fff7ed;
            color: #9a3412;
            font-weight: 700;
            font-size: .8rem;
            border-radius: 999px;
            padding: .35rem .7rem;
        }
    </style>
</head>
<body>
    <section class="auth-card p-4 p-md-5">
        <span class="pill mb-3"><i class="bi bi-shield-check"></i> Verificacao em duas etapas</span>
        <h1 class="h4 mb-1 fw-bold">Confirme seu acesso</h1>
        <p class="text-muted mb-4">Digite o codigo de 6 digitos enviado para o e-mail do administrador.</p>

        <x-flash />

        <form action="{{ route('company.2fa.verify') }}" method="POST" class="d-grid gap-3 mb-3">
            @csrf
            <div>
                <label class="form-label">Codigo 2FA</label>
                <input class="form-control" type="text" name="code" inputmode="numeric" maxlength="6" placeholder="000000" required>
            </div>

            <button class="btn btn-warning fw-bold" type="submit">Validar codigo</button>
        </form>

        <form action="{{ route('company.2fa.resend') }}" method="POST" class="d-grid">
            @csrf
            <button class="btn btn-outline-secondary" type="submit">Reenviar codigo</button>
        </form>
    </section>

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
