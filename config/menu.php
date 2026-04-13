<?php 

return [
    'saas_admin' => [
        ['label' => 'Empresas', 'route' => 'admin.companies.index', 'icon' => 'bi-buildings'],
    ],

    'company_admin' => [
        ['label' => 'Dashboard', 'route' => 'dashboard.index', 'icon' => 'bi-speedometer2'],
        ['label' => 'Bater ponto', 'route' => 'kiosk.index', 'icon' => 'bi-clock'],
        ['label' => 'Funcionários', 'route' => 'employees.index', 'icon' => 'bi-people'],
        ['label' => 'Usuários', 'route' => 'company-users.index', 'icon' => 'bi-person-gear'],
        ['label' => 'Relatórios', 'route' => 'reports.index', 'icon' => 'bi-bar-chart'],
    ],

    'company_editor' => [
        ['label' => 'Dashboard', 'route' => 'dashboard.index', 'icon' => 'bi-speedometer2'],
        ['label' => 'Relatórios', 'route' => 'reports.index', 'icon' => 'bi-bar-chart'],
    ],

    'company_operator' => [
        ['label' => 'Bater ponto', 'route' => 'kiosk.index', 'icon' => 'bi-clock'],
        ['label' => 'Relatórios', 'route' => 'reports.index', 'icon' => 'bi-bar-chart'],
    ],
];