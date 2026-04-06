<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login da Empresa</title>
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
            width: min(490px, 92vw);
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
        <span class="pill mb-3"><i class="bi bi-shield-lock"></i> Acesso ADM/EMPRESA</span>
        <h1 class="h3 mb-1 fw-bold">Ponto Colaborador</h1>
        <p class="text-muted mb-4">Use seu usuario de administrador SaaS ou administrador da empresa</p>

        <x-flash />

        <form action="{{ route('company.login.submit') }}" method="POST" class="d-grid gap-3">
            @csrf
            <div>
                <label class="form-label">E-mail ou nome do usuario</label>
                <input class="form-control" type="text" name="login" value="{{ old('login') }}" required>
            </div>

            <div>
                <label class="form-label">Senha</label>
                <input class="form-control" type="password" name="password" required>
            </div>

            <button class="btn btn-warning fw-bold" type="submit">Entrar</button>
        </form>

        <hr>

        <small class="text-muted">
            ADM SaaS demo: <strong>owner@ponto.com</strong> / <strong>123456</strong>.
        </small>
    </section>
</body>
</html>
