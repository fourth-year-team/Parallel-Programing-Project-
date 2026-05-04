<?php

use Illuminate\Support\Facades\Route;

// API-only project - Frontend routes removed
Route::get('/', function () {
    return response()->json([
        'message' => 'E-Commerce API',
        'version' => '1.0',
        'documentation' => 'API endpoints available at /api/v1'
    ]);
});

Route::get('/home', function () {
    return response()->json([
        'message' => 'Welcome home',
        'status' => 'ok'
    ]);
})->name('home');

