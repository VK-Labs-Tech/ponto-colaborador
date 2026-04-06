@props([
    'title',
    'items' => [],
    'emptyText' => 'Sem alertas',
])

<div class="surface-card p-3 h-100">
    <h5 class="d-flex align-items-center gap-2"><i class="bi bi-bell"></i> {{ $title }}</h5>

    @if(count($items) === 0)
        <p class="text-muted mb-0 d-flex align-items-center gap-2"><i class="bi bi-check2-circle"></i> {{ $emptyText }}</p>
    @else
        <ul class="list-group list-group-flush">
            @foreach($items as $item)
                <li class="list-group-item px-0 d-flex align-items-center gap-2"><i class="bi bi-exclamation-circle text-warning"></i> {{ $item }}</li>
            @endforeach
        </ul>
    @endif
</div>
