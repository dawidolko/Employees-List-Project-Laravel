<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;

Route::get('/', function () {
    return view('home', ['pageTitle' => 'Home']);
});

Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::get('/employees/export/pdf', [EmployeeController::class, 'exportPdf'])->name('employees.export.pdf');
Route::get('/employees/export/csv', [EmployeeController::class, 'exportCsv'])->name('employees.export.csv');
Route::get('/employees/export/pdf/check', [EmployeeController::class, 'exportPdfCheck'])->name('employees.export.pdf.check');

Route::get('/employees/{emp_no}', [EmployeeController::class, 'show'])->name('employees.show');
Route::get('/employees/{emp_no}/export/pdf', [EmployeeController::class, 'exportEmployeePdf'])->name('employees.export.employee.pdf');
