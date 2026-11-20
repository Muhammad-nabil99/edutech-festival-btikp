<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\JudgeDashboardController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Publik (Pendaftaran EduTech Festival BTIKP)
|--------------------------------------------------------------------------
*/
Route::get('/', [RegistrationController::class, 'create'])->name('registration.create');
Route::post('/register', [RegistrationController::class, 'store'])->name('registration.store');
Route::post('/upload-photo-temp', [RegistrationController::class, 'uploadTempPhoto'])->name('registration.upload-temp');
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

// Direct public asset fallback route untuk menjamin logo & favicon selalu dapat diakses
Route::get('/{file}', function (string $file) {
    $filePath = public_path($file);
    if (!file_exists($filePath) || !is_file($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
})->where('file', '.*\.(png|jpg|jpeg|ico|svg|webp)');

/*
|--------------------------------------------------------------------------
| Rute Otentikasi Admin & Dewan Juri
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Rute Dashboard & Manajemen Admin (Khusus Role: admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/registrations/export', [AdminDashboardController::class, 'export'])->name('registrations.export');
    Route::post('/registrations/{registration}/toggle-top10', [AdminDashboardController::class, 'toggleTop10'])->name('registrations.toggle-top10');
    Route::post('/settings/registration', [AdminDashboardController::class, 'updateRegistrationSetting'])->name('settings.registration');
    Route::put('/judges/{judge}', [AdminDashboardController::class, 'updateJudge'])->name('judges.update');
    Route::delete('/registrations/{registration}', [AdminDashboardController::class, 'destroy'])->name('registrations.destroy');
});

/*
|--------------------------------------------------------------------------
| Rute Penilaian & Dashboard Dewan Juri (Khusus Role: judge)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'judge'])->prefix('judge')->name('judge.')->group(function () {
    Route::get('/dashboard', [JudgeDashboardController::class, 'index'])->name('dashboard');
    Route::post('/works/{registration}/score', [JudgeDashboardController::class, 'saveScore'])->name('works.score');
    Route::put('/profile', [JudgeDashboardController::class, 'updateProfile'])->name('profile.update');
});
