@props([
    'title',
    'subtitle' => null,
])

<section class="mb-4">
    <div class="surface-card p-3 p-md-4 d-flex flex-wrap justify-content-between align-items-end gap-2" style="background:linear-gradient(130deg,#ffffff 0%,#fff7ed 50%,#eff6ff 100%);">
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
