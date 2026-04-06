<x-layouts.app title="Dashboard">
    <x-page-header
        title="Dashboard"
        subtitle="Resumo de produtividade, faltas e alertas em tempo real"
    />

    <div class="row g-3 mb-4">
        <x-stat-card title="Horas trabalhadas (mes)" :value="$metrics['worked_hours']" />
        <x-stat-card title="Atrasos (periodo)" :value="$metrics['late_count']" />
        <x-stat-card title="Faltas (ate hoje)" :value="$metrics['absence_count']" />
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <x-alert-list
                title="Alerta: funcionario nao bateu ponto hoje"
                :items="$metrics['alerts_missing_punch']"
                emptyText="Todos bateram ponto hoje"
            />
        </div>

        <div class="col-12 col-lg-6">
            <x-alert-list
                title="Alerta: atrasos de hoje"
                :items="$metrics['alerts_late']"
                emptyText="Nenhum atraso hoje"
            />
        </div>
    </div>
</x-layouts.app>
