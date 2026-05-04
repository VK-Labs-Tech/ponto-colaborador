<x-layouts.app title="Usuarios da Empresa">
    <x-page-header title="Usuarios da empresa"
        subtitle="Crie usuarios com perfis de administrador, editor e operador" />

    <div class="row g-4">
        <div class="col-12 col-lg-5">
            <section class="surface-card p-4">
                <h2 class="h5 mb-3">Novo usuario</h2>
                @include('company-users.components.form')
            </section>
        </div>

        <div class="col-12 col-lg-7">
            <section class="surface-card p-4">
                <h2 class="h5 mb-3">Usuarios cadastrados</h2>
                @include('company-users.components.table', ['users' => $users])
            </section>
        </div>
    </div>
</x-layouts.app>