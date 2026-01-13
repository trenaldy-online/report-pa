<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ImportController; 
use App\Http\Controllers\PatientController; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FollowUpController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
    
    // Route Filter Dashboard (POST) - TAMBAHKAN INI
    Route::post('/dashboard/filter', [DashboardController::class, 'setFilter'])->name('dashboard.filter');

// --- AREA KHUSUS USER LOGIN ---
Route::middleware('auth')->group(function () {
    
    // 1. Route untuk Import Excel
    Route::get('/import', [ImportController::class, 'showForm'])->name('import.form');
    Route::post('/import', [ImportController::class, 'process'])->name('import.process');

    // 2. Route untuk Manajemen Pasien (List & Manual Input)
    // Resource route otomatis membuat jalur untuk index, create, store, edit, dll.
    Route::resource('patients', PatientController::class);

    // 3. Route untuk Laporan Bi-Weekly
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');

    // Route Khusus Filter (POST) - Tambahkan ini
    Route::post('/report/filter', [ReportController::class, 'setFilter'])->name('report.filter');
    
    // Route untuk mengupdate catatan pasien via AJAX
    Route::post('/report/update-note', [ReportController::class, 'updateNote'])->name('report.update-note');

    // Bawaan Breeze (Jangan dihapus)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route Baru untuk Fitur Review
    Route::get('/import/review', [ImportController::class, 'review'])->name('import.review');
    Route::post('/import/commit', [ImportController::class, 'commit'])->name('import.commit');
    Route::delete('/import/temp/{id}', [ImportController::class, 'destroyTemp'])->name('import.destroyTemp');

    
    // Route Baru untuk Fitur Follow Up
    Route::get('/followup', [FollowUpController::class, 'index'])->name('followup.index')->middleware('auth');
});

require __DIR__.'/auth.php';