<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina nao encontrada</title>
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
            justify-content: center;
            align-items: center;
            color: #1b2430;
        }

        .error-card {
            width: min(620px, 92vw);
            background: #fff;
            border-radius: 1.25rem;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 16px 34px rgba(15, 23, 42, 0.10);
        }

        .code-badge {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            background: #fff7ed;
            color: #9a3412;
            border-radius: 999px;
            padding: .35rem .75rem;
            font-weight: 800;
            font-size: .85rem;
        }

        .btn-brand {
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 60%, #f97316 100%);
            color: #fff;
            border: none;
            font-weight: 700;
            border-radius: .75rem;
            padding: .62rem 1rem;
        }

        .btn-brand:hover,
        .btn-brand:focus {
            color: #fff;
            background: linear-gradient(135deg, #b45309 0%, #d97706 60%, #ea580c 100%);
        }
    </style>
</head>
<body>
    <section class="error-card p-4 p-md-5 text-center">
        <span class="code-badge mb-3"><i class="bi bi-compass"></i> Erro 404</span>
        <h1 class="h2 fw-bold mb-2">Pagina nao encontrada</h1>
        <p class="text-muted mb-4">
            A URL que voce tentou acessar nao existe ou foi movida.
        </p>

        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="{{ route('company.login') }}" class="btn btn-brand">Ir para login</a>
            <a href="javascript:history.back()" class="btn btn-outline-secondary">Voltar</a>
        </div>
    </section>
</body>
</html>
