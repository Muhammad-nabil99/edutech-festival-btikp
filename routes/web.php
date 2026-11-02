<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Publik (Pendaftaran EduTech Festival BTIKP)
|--------------------------------------------------------------------------
*/
Route::get('/', [RegistrationController::class, 'create'])->name('registration.create');
Route::post('/register', [RegistrationController::class, 'store'])->name('registration.store');
Route::get('/register/success/{registration_number}', [RegistrationController::class, 'success'])->name('registration.success');
Route::get('/registrations/{registration}/photo', [RegistrationController::class, 'photo'])->name('registration.photo');

// Direct storage fallback route untuk menjamin file gambar statis selalu dapat dibuka
Route::get('/storage/{path}', function (string $path) {
    $filePath = storage_path('app/public/' . ltrim($path, '/'));
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*');

/*
|--------------------------------------------------------------------------
| Rute Otentikasi Admin Panitia BTIKP
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Rute Dashboard & Manajemen Admin (Perlu Autentikasi)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/registrations/export', [AdminDashboardController::class, 'export'])->name('registrations.export');
    Route::delete('/registrations/{registration}', [AdminDashboardController::class, 'destroy'])->name('registrations.destroy');
});
