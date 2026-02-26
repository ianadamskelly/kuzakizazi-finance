<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Settings\StudentCategoryController; // Add this
use App\Http\Controllers\Settings\FeeStructureController;   // Add this
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\InvoiceController; // Add this
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController; // Add this
use App\Http\Controllers\Settings\EmployeeCategoryController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\Settings\CampusController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\Settings\AppSettingsController;




Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/switch-campus', [DashboardController::class, 'switchCampus'])->name('dashboard.switch-campus');
    Route::resource('students', StudentController::class);
    Route::get('students/{student}/statement', [StudentController::class, 'statement'])->name('students.statement');
    // --- INVOICES & PAYMENTS ROUTES ---
    Route::resource('invoices', InvoiceController::class)->only(['index', 'create', 'store', 'show']);
    Route::delete('invoices/bulk-destroy', [InvoiceController::class, 'bulkDestroy'])->name('invoices.bulk-destroy');

    // --- PAYMENTS ROUTES ---
    Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    // --- EXPENSES ROUTE ---
    Route::resource('expenses', ExpenseController::class);
    // --- EMPLOYEES ROUTE ---
    Route::resource('employees', EmployeeController::class);
    // --- DONATIONS ROUTE ---
    // We only need index, create, store, and destroy for this simple module.
    Route::resource('donations', DonationController::class)->only(['index', 'create', 'store', 'destroy']);
    // --- REPORTS ROUTES ---
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/fee-defaulters', [ReportController::class, 'feeDefaulters'])->name('reports.fee-defaulters');
    // --- SEARCH ROUTE ---
    Route::get('/search', SearchController::class)->name('search');
    // --- REPORTS ROUTES ---
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/fee-defaulters', [ReportController::class, 'feeDefaulters'])->name('reports.fee-defaulters');
    Route::get('/reports/income-statement', [ReportController::class, 'incomeStatement'])->name('reports.income-statement');
    Route::get('/reports/expense-report', [ReportController::class, 'expenseReport'])->name('reports.expense-report');
    Route::get('/reports/fund-balance', [ReportController::class, 'fundBalance'])->name('reports.fund-balance');
    // --- PAYROLL ROUTES ---
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/generate', [PayrollController::class, 'create'])->name('payroll.create');
    Route::post('/payroll/generate', [PayrollController::class, 'store'])->name('payroll.store');
    Route::get('/payroll/{payslip}', [PayrollController::class, 'show'])->name('payroll.show');
    Route::get('/payroll/{payslip}/pdf', [PayrollController::class, 'downloadPdf'])->name('payroll.pdf');
    Route::post('/payroll/{payslip}/items', [PayrollController::class, 'storeItem'])->name('payroll.items.store');
    Route::delete('/payroll/items/{item}', [PayrollController::class, 'destroyItem'])->name('payroll.items.destroy');
    Route::patch('/payroll/{payslip}/pay', [PayrollController::class, 'markAsPaid'])->name('payroll.pay');
    Route::post('/payroll/bulk-pay', [PayrollController::class, 'bulkMarkAsPaid'])->name('payroll.bulk-pay');
    // --- HELP & SUPPORT ROUTES ---
    Route::get('/help', [HelpController::class, 'index'])->name('help.index');
    Route::post('/help/send', [HelpController::class, 'send'])->name('help.send');


    // --- SETTINGS ROUTES (Refactored) ---
    Route::middleware(['can:manage settings'])->prefix('settings')->name('settings.')->group(function () {
        // The main settings page now has its own controller
        Route::get('/', [SettingsController::class, 'index'])->name('index');

        // Student Category Routes
        Route::resource('student-categories', StudentCategoryController::class)->only(['store', 'destroy']);
        Route::post('fee-structure/update', [FeeStructureController::class, 'bulkUpdate'])->name('fee-structure.update'); // New route
        Route::post('student-categories/{studentCategory}/fee-structures', [FeeStructureController::class, 'store'])->name('fee-structures.store');
        Route::delete('fee-structures/{feeStructure}', [FeeStructureController::class, 'destroy'])->name('fee-structures.destroy');

        // Employee Category Routes
        Route::resource('employee-categories', EmployeeCategoryController::class)->only(['store', 'destroy']);
        // Campus Routes
        Route::resource('campuses', CampusController::class)->except(['index', 'show', 'create']);
        // Grade Routes
        Route::resource('grades', \App\Http\Controllers\Settings\GradeController::class)->except(['create', 'edit', 'show']);

        // Academic Year Routes
        Route::post('academic-year/update', [\App\Http\Controllers\Settings\AcademicYearController::class, 'update'])->name('academic-year.update');
        Route::post('academic-year/graduate', [\App\Http\Controllers\Settings\AcademicYearController::class, 'graduate'])->name('academic-year.graduate');

        // App Settings Route (for Super Admin)
        Route::post('/app', [AppSettingsController::class, 'update'])->name('app.update');
    });
});
require __DIR__ . '/auth.php';
