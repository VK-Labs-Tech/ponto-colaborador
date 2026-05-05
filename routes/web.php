<?php

use App\Http\Controllers\Auth\CompanyAuthController;
use App\Http\Controllers\Admin\CompanyManagementController;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EditEmployeesController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\MonthlyClosureController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TimePunchAdjustmentController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [CompanyAuthController::class, 'showLogin'])->name('company.login');
    Route::get('/auth/login', fn () => redirect()->route('company.login'))->name('login');
    Route::post('/login', [CompanyAuthController::class, 'login'])->middleware('throttle:login')->name('company.login.submit');
    Route::get('/2fa', [CompanyAuthController::class, 'showTwoFactor'])->name('company.2fa.form');
    Route::post('/2fa', [CompanyAuthController::class, 'verifyTwoFactor'])
        ->middleware('throttle:2fa-verify')
        ->name('company.2fa.verify');
    Route::post('/2fa/resend', [CompanyAuthController::class, 'resendTwoFactor'])
        ->middleware('throttle:2fa-resend')
        ->name('company.2fa.resend');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [CompanyAuthController::class, 'logout'])->name('company.logout');
});

Route::middleware(['auth', 'role:saas_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/companies', [CompanyManagementController::class, 'index'])->name('companies.index');
    Route::get('/companies/{companyId}', [CompanyManagementController::class, 'show'])->name('companies.show');
    Route::post('/companies', [CompanyManagementController::class, 'store'])->name('companies.store');
    Route::post('/companies/{companyId}/subscription', [CompanyManagementController::class, 'updateSubscription'])
        ->name('companies.subscription.update');
});

Route::middleware(['auth', 'company.auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:admin,gestor')
        ->name('dashboard.index');

    Route::get('/kiosk', [KioskController::class, 'index'])
        ->middleware('role:admin,colaborador')
        ->name('kiosk.index');
    Route::post('/kiosk/punch', [KioskController::class, 'store'])
        ->middleware('throttle:kiosk-punch')
        ->middleware('role:admin,colaborador')
        ->name('kiosk.punch');

    Route::get('/employees', [EmployeeController::class, 'index'])
        ->middleware('role:company_admin')
        ->name('employees.index');
    Route::post('/employees', [EmployeeController::class, 'store'])
        ->middleware('role:company_admin')
        ->name('employees.store');

    Route::get('/employees/{employee}/edit', [EditEmployeesController::class, 'edit'])
        ->middleware('role:company_admin')
        ->name('employees.edit');
    Route::put('/employees/{employee}', [EditEmployeesController::class, 'update'])
        ->middleware('role:company_admin')
        ->name('employees.update');

    Route::get('/company-users', [CompanyUserController::class, 'index'])
        ->middleware('role:company_admin')
        ->name('company-users.index');
    Route::post('/company-users', [CompanyUserController::class, 'store'])
        ->middleware('role:company_admin')
        ->name('company-users.store');
    Route::get('/company-users/{user}/edit', [CompanyUserController::class, 'edit'])
        ->middleware('role:company_admin')
        ->name('company-users.edit');
    Route::put('/company-users/{user}', [CompanyUserController::class, 'update'])
        ->middleware('role:company_admin')
        ->name('company-users.update');

    Route::get('/reports', [ReportController::class, 'index'])
        ->middleware('role:admin,gestor,colaborador')
        ->name('reports.index');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])
        ->middleware('role:admin,gestor,colaborador')
        ->name('reports.export.pdf');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])
        ->middleware('role:admin,gestor,colaborador')
        ->name('reports.export.excel');
    Route::post('/reports/acknowledge', [ReportController::class, 'acknowledgeMirror'])
        ->middleware('role:admin,gestor,colaborador')
        ->name('reports.acknowledge');
    Route::get('/reports/adjust', [TimePunchAdjustmentController::class, 'edit'])
        ->middleware('role:gestor')
        ->name('reports.adjust.edit');
    Route::post('/reports/adjust', [TimePunchAdjustmentController::class, 'update'])
        ->middleware('role:gestor')
        ->name('reports.adjust.update');

    Route::post('/monthly-closures', [MonthlyClosureController::class, 'store'])
        ->middleware('role:company_admin')
        ->name('monthly-closures.store');
});
