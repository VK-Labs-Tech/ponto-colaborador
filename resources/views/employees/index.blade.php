<x-layouts.app title="Funcionarios">
    <x-page-header title="Funcionarios" subtitle="Cadastre e organize o quadro de colaboradores da empresa" />

    <div class="row g-4">
        <div class="col-12 col-lg-5">
            <div class="container-fluid p-4 shadow-lg border rounded-4 bg-light">
                <section class="p-4">
                    <h2 class="h5 mb-3 text-dark">Cadastrar funcionario</h2>
                    @include('employees.components.form')
                </section>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <section class="surface-card p-4">
                <h2 class="h5 mb-3 text-dark">Lista de funcionarios</h2>
                @include('employees.components.table', ['employees' => $employees])
            </section>
        </div>
    </div>
</x-layouts.app>