<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\NotificationController;

// ===== PUBLIC =====
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::get('/cities',    [CityController::class, 'index']);
Route::get('/cars',      [CarController::class, 'index']);
Route::get('/cars/{id}', [CarController::class, 'show']);

// ===== PERLU LOGIN =====
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout',   [AuthController::class, 'logout']);
    Route::get('/me',        [AuthController::class, 'me']);
    Route::put('/me',        [AuthController::class, 'updateProfile']);
    Route::post('/me/photo', [AuthController::class, 'uploadPhoto']);

    // Notifikasi
    Route::get('/notifications',              [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read',    [NotificationController::class, 'markRead']);

    // USER
    Route::middleware('role:user')->group(function () {
        Route::post('/bookings',           [BookingController::class, 'store']);
        Route::get('/bookings',            [BookingController::class, 'myBookings']);
        Route::get('/bookings/{id}',       [BookingController::class, 'show']);
        Route::delete('/bookings/{id}',    [BookingController::class, 'cancel']);
        Route::post('/reviews',            [ReviewController::class, 'store']);
    });

    // OWNER
    Route::middleware('role:owner')->group(function () {
        Route::get('/owner/cars',              [CarController::class, 'ownerCars']);
        Route::post('/owner/cars',             [CarController::class, 'store']);
        Route::put('/owner/cars/{id}',         [CarController::class, 'update']);
        Route::delete('/owner/cars/{id}',      [CarController::class, 'destroy']);
        Route::get('/owner/bookings',                     [BookingController::class, 'ownerBookings']);
        Route::put('/owner/bookings/{id}/approve',        [BookingController::class, 'approve']);
        Route::put('/owner/bookings/{id}/reject',         [BookingController::class, 'reject']);
    });
});