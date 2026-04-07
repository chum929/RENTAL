<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\ProviderAdminController;
use App\Http\Controllers\Admin\CarAdminController;
use App\Http\Controllers\Admin\BookingAdminController;
use App\Http\Controllers\Admin\CityAdminController;
use App\Http\Controllers\Admin\ReviewAdminController;

// Redirect root langsung ke admin login
Route::get('/', fn() => redirect('/admin/login'));

// Admin login
Route::get('/admin/login',  [AdminController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');

// Semua halaman admin (perlu login)
Route::middleware(['auth', 'web.role:admin'])
    ->prefix('admin')->name('admin.')->group(function () {

    Route::post('/logout',                  [AdminController::class, 'logout'])->name('logout');
    Route::get('/dashboard',                [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users',                    [UserAdminController::class, 'index'])->name('users');
    Route::post('/users/{id}/block',        [UserAdminController::class, 'block'])->name('users.block');
    Route::get('/providers',                [ProviderAdminController::class, 'index'])->name('providers');
    Route::post('/providers/{id}/approve',  [ProviderAdminController::class, 'approve'])->name('providers.approve');
    Route::post('/providers/{id}/reject',   [ProviderAdminController::class, 'reject'])->name('providers.reject');
    Route::get('/cars',                     [CarAdminController::class, 'index'])->name('cars');
    Route::delete('/cars/{id}',             [CarAdminController::class, 'destroy'])->name('cars.destroy');
    Route::get('/bookings',                 [BookingAdminController::class, 'index'])->name('bookings');
    Route::get('/cities',                   [CityAdminController::class, 'index'])->name('cities');
    Route::post('/cities',                  [CityAdminController::class, 'store'])->name('cities.store');
    Route::put('/cities/{id}',              [CityAdminController::class, 'update'])->name('cities.update');
    Route::delete('/cities/{id}',           [CityAdminController::class, 'destroy'])->name('cities.destroy');
    Route::get('/reviews',                  [ReviewAdminController::class, 'index'])->name('reviews');
    Route::delete('/reviews/{id}',          [ReviewAdminController::class, 'destroy'])->name('reviews.destroy');
});