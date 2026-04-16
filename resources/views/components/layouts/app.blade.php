<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Ponto Colaborador' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --brand-bg: #f8f5f1;
            --brand-text: #1b2430;
            --brand-accent: #d97706;
            --brand-accent-2: #0f766e;
            --brand-soft: #fff7ed;
            --brand-card: #ffffff;
            --brand-muted: #667085;
        }

        body {
            font-family: 'Manrope', sans-serif;
            min-height: 100vh;
            color: var(--brand-text);
        }

        .surface-card {
            background: var(--brand-card);
            border-radius: 1.2rem;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
        }

        .surface-glass {
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.7);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .btn-brand {
            background: linear-gradient(135deg, var(--brand-accent) 0%, #f59e0b 60%, #f97316 100%);
            color: #fff;
            border: none;
            font-weight: 700;
            border-radius: .75rem;
            padding: .62rem 1rem;
            box-shadow: 0 8px 20px rgba(217, 119, 6, .28);
        }

        .btn-brand:hover,
        .btn-brand:focus {
            background: linear-gradient(135deg, #b45309 0%, #d97706 60%, #ea580c 100%);
            color: #fff;
        }

        .btn-outline-brand {
            border-color: #f59e0b;
            color: #b45309;
            border-radius: .75rem;
            font-weight: 700;
        }

        .btn-outline-brand:hover {
            background: #fff7ed;
            color: #9a3412;
            border-color: #d97706;
        }

        .chip-soft {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .35rem .65rem;
            border-radius: 999px;
            background: var(--brand-soft);
            color: #9a3412;
            font-weight: 700;
            font-size: .8rem;
        }

        .table thead th {
            color: var(--brand-muted);
            font-size: .82rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .table tbody tr {
            border-radius: .75rem;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .table tbody tr:hover {
            transform: translateY(-2px);
        }

        .form-control,
        .form-select {
            border-radius: .75rem;
            border-color: #d0d5dd;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 .25rem rgba(245, 158, 11, .17);
        }

        .page-enter {
            animation: rise-in .4s ease;
        }

        @keyframes rise-in {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            main.container {
                padding-top: 1.25rem;
            }
        }
    </style>
</head>

<body>
    @php
        $introVideos = config('app.intro_videos', []);
        $showIntroVideos = session('show_intro_videos') && count($introVideos) > 0;
    @endphp

    <x-navbar />

    <main class="container py-4 page-enter">
        <x-flash />
        {{ $slot }}
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @if($showIntroVideos)
        <div class="modal fade" id="introVideosModal" tabindex="-1" aria-labelledby="introVideosModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content" style="border-radius: 1rem;">
                    <div class="modal-header">
                        <h5 class="modal-title" id="introVideosModalLabel">Bem-vindo! Veja esta introducao rapida</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            @foreach($introVideos as $videoUrl)
                                <div class="col-12 col-lg-6">
                                    @if(\Illuminate\Support\Str::endsWith(\Illuminate\Support\Str::lower($videoUrl), ['.mp4', '.webm', '.ogg', '.m4v', '.mov']))
                                        <video controls preload="metadata" class="w-100 rounded-3 border" style="background:#000;">
                                            <source src="{{ $videoUrl }}">
                                            Seu navegador nao suporta video HTML5.
                                        </video>
                                    @else
                                        <div class="ratio ratio-16x9">
                                            <iframe class="rounded-3 border" src="{{ $videoUrl }}" title="Video de introducao"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                                            </iframe>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <script>
        window.addEventListener('pageshow', function (event) {
            const nav = performance.getEntriesByType('navigation')[0];
            if (event.persisted || nav?.type === 'back_forward') {
                window.location.reload();
            }
        });

        @if($showIntroVideos)
            window.addEventListener('DOMContentLoaded', function () {
                const modalEl = document.getElementById('introVideosModal');
                if (!modalEl) return;

                const modal = new bootstrap.Modal(modalEl, {
                    backdrop: 'static',
                    keyboard: true,
                });

                modal.show();
            });
        @endif
    </script>
</body>

</html>