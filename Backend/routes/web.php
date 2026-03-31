<?php
use Illuminate\Support\Facades\Route;

// ============================================================
// AUTH WEB
// ============================================================
use App\Http\Controllers\Web\AuthWebController;
Route::get('/login',    [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthWebController::class, 'login'])->name('login.post');
Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthWebController::class, 'register'])->name('register.post');
Route::post('/logout',  [AuthWebController::class, 'logout'])->name('logout');

// ============================================================
// AREA PUBLIK
// ============================================================
use App\Http\Controllers\Web\HomeController;
Route::get('/',              [HomeController::class, 'index'])->name('home');
Route::get('/mobil',         [HomeController::class, 'cars'])->name('cars.index');
Route::get('/mobil/{id}',    [HomeController::class, 'carDetail'])->name('cars.show');

// ============================================================
// AREA USER (penyewa)
// ============================================================
use App\Http\Controllers\Web\UserController;
Route::middleware(['auth', 'web.role:user'])
    ->prefix('user')->name('user.')->group(function () {

    Route::get('/dashboard',                [UserController::class, 'dashboard'])
        ->name('dashboard');
    Route::get('/mobil/{carId}/booking',    [UserController::class, 'bookingForm'])
        ->name('booking.form');
    Route::post('/booking',                 [UserController::class, 'storeBooking'])
        ->name('booking.store');
    Route::get('/bookings',                 [UserController::class, 'myBookings'])
        ->name('bookings');
    Route::get('/bookings/{id}',            [UserController::class, 'bookingDetail'])
        ->name('booking.detail');
    Route::post('/bookings/{id}/cancel',    [UserController::class, 'cancelBooking'])
        ->name('booking.cancel');
    Route::get('/notifikasi',               [UserController::class, 'notifications'])
        ->name('notifications');
    Route::post('/reviews',                 [UserController::class, 'storeReview'])
        ->name('reviews.store');
    Route::get('/profil',                   [UserController::class, 'profile'])
        ->name('profile');
    Route::put('/profil',                   [UserController::class, 'updateProfile'])
        ->name('profile.update');
});

// ============================================================
// AREA OWNER (penyedia rental)
// ============================================================
use App\Http\Controllers\Web\OwnerWebController;
Route::middleware(['auth', 'web.role:owner'])
    ->prefix('owner')->name('owner.')->group(function () {

    Route::get('/dashboard',                    [OwnerWebController::class, 'dashboard'])
        ->name('dashboard');
    Route::get('/mobil',                        [OwnerWebController::class, 'cars'])
        ->name('cars');
    Route::get('/mobil/tambah',                 [OwnerWebController::class, 'createCar'])
        ->name('cars.create');
    Route::post('/mobil',                       [OwnerWebController::class, 'storeCar'])
        ->name('cars.store');
    Route::get('/mobil/{id}/edit',              [OwnerWebController::class, 'editCar'])
        ->name('cars.edit');
    Route::put('/mobil/{id}',                   [OwnerWebController::class, 'updateCar'])
        ->name('cars.update');
    Route::delete('/mobil/{id}',                [OwnerWebController::class, 'destroyCar'])
        ->name('cars.destroy');
    Route::get('/booking',                      [OwnerWebController::class, 'bookings'])
        ->name('bookings');
    Route::post('/booking/{id}/terima',         [OwnerWebController::class, 'approve'])
        ->name('booking.approve');
    Route::post('/booking/{id}/tolak',          [OwnerWebController::class, 'reject'])
        ->name('booking.reject');
    Route::get('/profil',                       [OwnerWebController::class, 'profile'])
        ->name('profile');
    Route::put('/profil',                       [OwnerWebController::class, 'updateProfile'])
        ->name('profile.update');
});

// ============================================================
// AREA ADMIN
// ============================================================
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\ProviderAdminController;
use App\Http\Controllers\Admin\CarAdminController;
use App\Http\Controllers\Admin\BookingAdminController;
use App\Http\Controllers\Admin\CityAdminController;
use App\Http\Controllers\Admin\ReviewAdminController;

Route::get('/admin/login',  [AdminController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');

Route::middleware(['auth', 'web.role:admin'])
    ->prefix('admin')->name('admin.')->group(function () {

    Route::post('/logout',                  [AdminController::class, 'logout'])
        ->name('logout');
    Route::get('/dashboard',                [AdminController::class, 'dashboard'])
        ->name('dashboard');
    Route::get('/users',                    [UserAdminController::class, 'index'])
        ->name('users');
    Route::post('/users/{id}/block',        [UserAdminController::class, 'block'])
        ->name('users.block');
    Route::get('/providers',                [ProviderAdminController::class, 'index'])
        ->name('providers');
    Route::post('/providers/{id}/approve',  [ProviderAdminController::class, 'approve'])
        ->name('providers.approve');
    Route::post('/providers/{id}/reject',   [ProviderAdminController::class, 'reject'])
        ->name('providers.reject');
    Route::get('/cars',                     [CarAdminController::class, 'index'])
        ->name('cars');
    Route::delete('/cars/{id}',             [CarAdminController::class, 'destroy'])
        ->name('cars.destroy');
    Route::get('/bookings',                 [BookingAdminController::class, 'index'])
        ->name('bookings');
    Route::get('/cities',                   [CityAdminController::class, 'index'])
        ->name('cities');
    Route::post('/cities',                  [CityAdminController::class, 'store'])
        ->name('cities.store');
    Route::put('/cities/{id}',              [CityAdminController::class, 'update'])
        ->name('cities.update');
    Route::delete('/cities/{id}',           [CityAdminController::class, 'destroy'])
        ->name('cities.destroy');
    Route::get('/reviews',                  [ReviewAdminController::class, 'index'])
        ->name('reviews');
    Route::delete('/reviews/{id}',          [ReviewAdminController::class, 'destroy'])
        ->name('reviews.destroy');
});