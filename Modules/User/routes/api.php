<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\V1\Controllers\Auth\Admin\AdminController;

Route::prefix('v1/admin')->group(function () {
    Route::post('login', [AdminController::class, 'login']);

    Route::middleware('auth:admin')->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard']);

        // CRUD operations
        Route::get('admins', [AdminController::class, 'index']);
        Route::post('admins', [AdminController::class, 'store']);
        Route::get('admins/{id}', [AdminController::class, 'show']);
        Route::put('admins/{id}', [AdminController::class, 'update']);
        Route::delete('admins/{id}', [AdminController::class, 'destroy']);
        Route::post('admins/logout', [AdminController::class, 'logout']);
        
    });
});
