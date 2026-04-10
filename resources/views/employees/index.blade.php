<x-layouts.app title="Funcionarios">
    <x-page-header
        title="Funcionarios"
        subtitle="Cadastre e organize o quadro de colaboradores da empresa"
    />

    <div class="row g-4">
        <div class="col-12 col-lg-5">
            <section class="surface-card p-4" style="background:linear-gradient(180deg,#fff 0%,#fff7ed 100%);">
                <h2 class="h5 mb-3">Cadastrar funcionario</h2>

                @include('employees.components.form')
            </section>
        </div>

        <div class="col-12 col-lg-7">
            <section class="surface-card p-4">
                <h2 class="h5 mb-3">Lista de funcionarios</h2>
                @include('employees.components.table', ['employees' => $employees]) 
            </section>
        </div>
    </div>
</x-layouts.app>
