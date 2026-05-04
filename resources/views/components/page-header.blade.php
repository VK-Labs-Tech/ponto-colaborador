@props([
    'title',
    'subtitle' => null,
])

<section class="mb-4">
    <div class="container-fluid p-4 shadow-lg border rounded-4 bg-light p-3 p-md-4 d-flex flex-wrap justify-content-between align-items-end gap-2 rou">
        <div>
            <span class="chip-soft mb-2">Painel operacional</span>
            <h1 class="display-6 fw-bold mb-1" style="letter-spacing:-.03em;">{{ $title }}</h1>
            @if($subtitle)
                <p class="text-secondary mb-0">{{ $subtitle }}</p>
            @endif
        </div>

        <div>
            {{ $actions ?? '' }}
        </div>
    </div>
</section>
