<?php

// routes/api.php

use App\Http\Controllers\LoginCodeController;
use App\Http\Controllers\UserController;
//use App\Http\Controllers\QrAuthController;
use Illuminate\Support\Facades\Route;


//Public Routes
Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::controller(UserController::class)->group(function () {
        Route::post('/login', 'login');
    });
    Route::controller(LoginCodeController::class)->group(function () {
        Route::post('login-code/generate', 'generate');
        Route::post('login-code/login', 'login');
    });
});

//Private Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('v1')->group(function () {
        // User management routes
        Route::controller(UserController::class)->group(function () {
            Route::post('/logout', 'logout');
            Route::get('/users', 'index');
            Route::get('/user/{id}', 'show');
            Route::post('/users', 'store');
            Route::put('/user/{id}', 'update');
            Route::delete('/user/{id}', 'destroy');
        });
        // Login code routes
        Route::controller(LoginCodeController::class)->group(function () {
            Route::post('login-code/confirm', 'confirmCode');
        });
    });
});

