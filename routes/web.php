<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Security architecture:
|
|  [CSRF]         Laravel's VerifyCsrfToken runs on all web routes automatically.
|                 Every POST/PATCH/DELETE form includes @csrf in the Blade view.
|
|  [DDoS]         Upload route uses throttle:uploads (10/min per user).
|                 Auth routes use throttle:auth (10/min per IP) via auth.php.
|                 Global web rate limiter (120/min per IP) in AppServiceProvider.
|
|  [Auth]         All sensitive routes inside 'auth' middleware group.
|                 Unauthenticated access redirects to /login (no 401 leakage).
|
|  [IDOR]         File ownership verified inside FileController before any action.
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');

Route::middleware(['auth', 'throttle:web'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // File management
    Route::post('/files', [FileController::class, 'store'])
        ->name('files.store')
        ->middleware('throttle:uploads'); // 10 uploads/min per user

    Route::get('/files/{file}/download', [FileController::class, 'download'])
        ->name('files.download');

    Route::delete('/files/{file}', [FileController::class, 'destroy'])
        ->name('files.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
