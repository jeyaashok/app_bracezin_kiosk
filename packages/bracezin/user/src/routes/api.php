<?php

use Illuminate\Support\Facades\Route;
use User\Http\Controllers\AdminController;
use User\Http\Controllers\AgentController;
use User\Http\Controllers\AllUserController;
use User\Http\Controllers\AuthController;
use User\Http\Controllers\CompanyController;
use User\Http\Controllers\CustomerController;
use User\Http\Controllers\StaffController;

// For UnAuthorized Access Routes
Route::prefix('api')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register'])->name('api-register');
    Route::post('/login', [AuthController::class, 'login'])->name('api-login');
    Route::post('/forget-password', [AuthController::class, 'forgetPassword'])->name('forget-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
    Route::get('/unauthorized', [AuthController::class, 'unauthorized'])->name('api-unauthorized');
});

Route::middleware(['auth:admin', 'auth.user'])->prefix('api')->group(function (): void {
    Route::get('/me', [AuthController::class, 'authMe']);
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('api-change-password');
    Route::post('/update-password', [AuthController::class, 'updatePassword'])->name('api-update-password');
    Route::get('/logout', [AuthController::class, 'logout'])->name('api-logout');
    Route::get('/forcelogout/{userId}', [AuthController::class, 'forceLogout'])->name('force-logout');
    Route::get('/forcelogoutall', [AuthController::class, 'forceLogoutAllUser'])->name('force-logout-all');
    Route::get('/delete-my-account', [AuthController::class, 'deleteMyAccount']);
});

Route::middleware(['auth:admin', 'auth.user'])->prefix('api')->group(function (): void {
    Route::resource('/user', AllUserController::class);
    Route::resource('/admin', AdminController::class);
    Route::resource('/agent', AgentController::class);
    Route::resource('/company', CompanyController::class);
    Route::resource('/customer', CustomerController::class);
    Route::resource('/staff', StaffController::class);

    Route::post('/all-user-store-media', [AllUserController::class, 'storeMedia']);
    Route::post('user-map-permission', [AllUserController::class, 'mapPermissionByUser']);
});
