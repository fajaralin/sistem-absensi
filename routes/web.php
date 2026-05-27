<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicAbsensiController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\StudentCRUDController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;

// Halaman utama Kios Absensi Publik Siswa
Route::get('/', [PublicAbsensiController::class, 'index'])->name('kios-absensi');
Route::post('/absensi/search', [PublicAbsensiController::class, 'searchSiswa'])->name('absensi.search');
Route::get('/absensi/suggest', [PublicAbsensiController::class, 'suggestSiswa'])->name('absensi.suggest');
Route::post('/absensi/scan', [PublicAbsensiController::class, 'scan'])->name('absensi.scan');

// Guest Routes (Authentication Admin)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Shared Auth Routes (Logout)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // CRUD Siswa (Students)
    Route::resource('/students', StudentCRUDController::class);
    Route::post('/students/{id}/register-face', [StudentCRUDController::class, 'registerFace'])->name('students.register-face');
    
    // Master Face Data Logs
    Route::get('/face-data', [StudentCRUDController::class, 'faceDataList'])->name('face-data.index');
    Route::delete('/face-data/{id}', [StudentCRUDController::class, 'deleteFaceData'])->name('face-data.destroy');

    // Kehadiran (Attendance) Management & Filters
    Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/manual', [AdminAttendanceController::class, 'storeManual'])->name('attendance.manual');
    Route::delete('/attendance/{id}', [AdminAttendanceController::class, 'destroy'])->name('attendance.destroy');
    Route::get('/attendance/export/csv', [AdminAttendanceController::class, 'exportCsv'])->name('attendance.export.csv');
    Route::get('/attendance/export/pdf', [AdminAttendanceController::class, 'exportPdf'])->name('attendance.export.pdf');

    // Logs aktivitas sistem
    Route::get('/logs', [AdminDashboardController::class, 'logs'])->name('logs');
});
