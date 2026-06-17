<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\DailySalesReportController;
use App\Http\Controllers\Api\LoadDistributionController;
use App\Http\Controllers\Api\DistributedLockDemoController;

// Public API Routes
Route::prefix('v1')->group(function () {
    // Product Routes (Public)
    Route::get('/products', [ProductController::class, 'apiIndex']);
    Route::get('/products/{product}', [ProductController::class, 'apiShow']);

    Route::get('/load-distribution/simulate', [LoadDistributionController::class, 'simulate']);

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

        // Concurrency Control
        // method 1
        Route::post('/concurrency/optimistic-checkout', [OrderController::class, 'apiCheckoutOptimistic']);
        // method 2
        Route::prefix('concurrency')->group(function () {
            Route::post('/distributed-lock-checkout', [DistributedLockDemoController::class, 'distributedLockCheckout']);
        });

        // Transaction Integrity / ACID
        Route::prefix('acid')->group(function () {
            Route::post('/naive-checkout', [\App\Http\Controllers\Api\AcidDemoController::class, 'naiveCheckout']);
            Route::post('/transaction-checkout', [\App\Http\Controllers\Api\AcidDemoController::class, 'transactionCheckout']);
        });
    });

    Route::post('/concurrency/lock-probe', [DistributedLockDemoController::class, 'distributedLockProbe']);

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

        Route::get('/load-distribution/stats', [LoadDistributionController::class, 'stats']);
        Route::post('/load-distribution/reset', [LoadDistributionController::class, 'reset']);
    });
    Route::get('/lb-test', function (Request $request) {
        return response()->json([
            'message' => 'Load balancer test',
            'served_by_port' => $request->server('SERVER_PORT'),
            'served_by_addr' => $request->server('SERVER_ADDR'),
            'time' => now()->toDateTimeString(),
        ]);
    });
});
