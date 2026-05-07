<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\DailySalesReportController;

// Public API Routes
Route::prefix('v1')->group(function () {
    // Product Routes (Public)
    Route::get('/products', [ProductController::class, 'apiIndex']);
    Route::get('/products/{product}', [ProductController::class, 'apiShow']);

    // Authentication Routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Protected Routes (Require Authentication)
    Route::middleware(['auth.api'])->group(function () {
        // User Routes
        Route::get('/user', [AuthController::class, 'currentUser']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Cart Routes
        Route::get('/cart', [CartController::class, 'apiIndex']);
        Route::post('/cart/add', [CartController::class, 'apiAdd']);
        Route::patch('/cart/{cartItem}', [CartController::class, 'apiUpdate']);
        Route::delete('/cart/{cartItem}', [CartController::class, 'apiRemove']);
        Route::post('/cart/clear', [CartController::class, 'apiClear']);

        // Order Routes (Customer)
        Route::get('/orders', [OrderController::class, 'apiIndex']);
        Route::get('/orders/{order}', [OrderController::class, 'apiShow']);
        Route::post('/orders/checkout', [OrderController::class, 'apiCheckout']);
    });

    // Admin Routes - Protected by AdminMiddleware
    Route::middleware(['auth.api', 'admin'])->prefix('admin')->group(function () {
        // Product Management
        Route::get('/products', [ProductController::class, 'apiAdminIndex']);
        Route::post('/products', [ProductController::class, 'apiStore']);
        Route::patch('/products/{product}', [ProductController::class, 'apiUpdate']);
        Route::delete('/products/{product}', [ProductController::class, 'apiDestroy']);

        // Order Management
        Route::get('/orders', [OrderController::class, 'apiAdminIndex']);
        Route::patch('/orders/{order}/status', [OrderController::class, 'apiUpdateStatus']);

        Route::post('/reports/daily-sales', [DailySalesReportController::class, 'store']);
    });
});
