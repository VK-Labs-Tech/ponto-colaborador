<?php

use App\Http\Controllers\Auth\CompanyAuthController;
use App\Http\Controllers\Admin\CompanyManagementController;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\DashboardController;
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
    Route::post('/login', [CompanyAuthController::class, 'login'])->name('company.login.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [CompanyAuthController::class, 'logout'])->name('company.logout');
});

Route::middleware(['auth', 'role:saas_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/companies', [CompanyManagementController::class, 'index'])->name('companies.index');
    Route::post('/companies', [CompanyManagementController::class, 'store'])->name('companies.store');
});

Route::middleware(['auth', 'company.auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::get('/kiosk', [KioskController::class, 'index'])->name('kiosk.index');
    Route::post('/kiosk/punch', [KioskController::class, 'store'])->name('kiosk.punch');

    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');

    Route::get('/company-users', [CompanyUserController::class, 'index'])->name('company-users.index');
    Route::post('/company-users', [CompanyUserController::class, 'store'])
        ->middleware('role:company_admin')
        ->name('company-users.store');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/adjust', [TimePunchAdjustmentController::class, 'edit'])
        ->middleware('role:company_editor')
        ->name('reports.adjust.edit');
    Route::post('/reports/adjust', [TimePunchAdjustmentController::class, 'update'])
        ->middleware('role:company_editor')
        ->name('reports.adjust.update');

    Route::post('/monthly-closures', [MonthlyClosureController::class, 'store'])->name('monthly-closures.store');
});
