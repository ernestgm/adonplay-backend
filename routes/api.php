<?php

// routes/api.php

use App\Http\Controllers\LoginCodeController;
use App\Http\Controllers\UserController;
//use App\Http\Controllers\QrAuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\SlideController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\DeviceInfoController;
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
    // DeviceInfo public create
    Route::post('device-infos', [DeviceInfoController::class, 'store']);
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
            Route::delete('/users', 'destroy');
        });
        // Login code routes
        Route::controller(LoginCodeController::class)->group(function () {
            Route::post('login-code/confirm', 'confirmCode');
        });
        // Business management routes
        Route::controller(BusinessController::class)->group(function () {
            Route::get('businesses', 'index');
            Route::post('businesses', 'store');
            Route::get('businesses/{id}', 'show');
            Route::put('businesses/{id}', 'update');
            Route::delete('businesses', 'destroy');
        });
        // Slide management routes
        Route::controller(SlideController::class)->group(function () {
            Route::get('businesses/{businessId}/slides', 'index');
            Route::get('/slides', 'all');
            Route::post('/slides', 'store');
            Route::get('/slides/{id}', 'show');
            Route::put('/slides/{id}', 'update');
            Route::delete('/slides', 'destroy');
        });
        // Media management routes
        Route::controller(MediaController::class)->group(function () {
            Route::get('slides/{slideId}/media', 'index');
            Route::post('slides/{slideId}/media', 'store');
            Route::get('slides/{slideId}/media/{id}', 'show');
            Route::post('slides/{slideId}/media/{id}', 'update');
            Route::delete('slides/{slideId}/media', 'destroy');
        });
        // Marquee management routes
        Route::controller(\App\Http\Controllers\MarqueeController::class)->group(function () {
            Route::get('marquees', 'index');
            Route::post('marquees', 'store');
            Route::get('marquees/{id}', 'show');
            Route::put('marquees/{id}', 'update');
            Route::delete('marquees', 'destroy');
        });
        // Qr management routes
        Route::controller(\App\Http\Controllers\QrController::class)->group(function () {
            Route::get('qrs', 'index');
            Route::post('qrs', 'store');
            Route::get('qrs/{id}', 'show');
            Route::put('qrs/{id}', 'update');
            Route::delete('qrs', 'destroy');
        });
        // Device management routes
        Route::controller(\App\Http\Controllers\DeviceController::class)->group(function () {
            Route::get('devices', 'index');
            Route::post('devices', 'store');
            Route::get('devices/{id}', 'show');
            Route::put('devices/{id}', 'update');
            Route::delete('devices/{id}', 'destroy');
        });
        // DeviceInfo private routes
        Route::controller(DeviceInfoController::class)->group(function () {
            Route::get('device-infos', 'index');
            Route::get('device-infos/{id}', 'show');
            Route::put('device-infos/{id}', 'update');
            Route::delete('device-infos/{id}', 'destroy');
        });
    });
});
