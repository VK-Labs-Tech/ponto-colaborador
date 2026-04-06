@props([
    'title',
    'value',
    'subtitle' => null,
])

<div class="col-12 col-md-4">
    <div class="surface-card p-3 h-100 position-relative overflow-hidden">
        <div style="position:absolute;right:-18px;top:-24px;width:90px;height:90px;border-radius:50%;background:radial-gradient(circle,#fed7aa 0%,transparent 70%);"></div>
        <p class="text-secondary mb-1 small text-uppercase position-relative">{{ $title }}</p>
        <h3 class="mb-1 fw-bold position-relative" style="font-size:2rem;">{{ $value }}</h3>
        @if($subtitle)
            <small class="text-muted">{{ $subtitle }}</small>
        @endif
    </div>
</div>
