<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;

// ─── Public Routes ────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::get('/events',    [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);

// ─── Protected Routes ─────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // ─── Organizer / Admin Only ───────────────────────────
    Route::middleware('role:admin,organizer')->group(function () {
        Route::post('/events',                    [EventController::class,  'store']);
        Route::put('/events/{id}',                [EventController::class,  'update']);
        Route::delete('/events/{id}',             [EventController::class,  'destroy']);
        Route::post('/events/{event_id}/tickets', [TicketController::class, 'store']);
        Route::put('/tickets/{id}',               [TicketController::class, 'update']);
        Route::delete('/tickets/{id}',            [TicketController::class, 'destroy']);
    });

    // ─── Customer Only ────────────────────────────────────
    Route::middleware('role:customer')->group(function () {
        Route::post('/tickets/{id}/bookings', [BookingController::class, 'store']); // ← removed middleware here
        Route::get('/bookings',               [BookingController::class, 'index']);
        Route::put('/bookings/{id}/cancel',   [BookingController::class, 'cancel']);
        Route::post('/bookings/{id}/payment', [PaymentController::class, 'store']);
        Route::get('/payments/{id}',          [PaymentController::class, 'show']);
    });
});
