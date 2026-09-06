<?php

use Illuminate\Support\Facades\Route;
use Role\Http\Controllers\PermissionController;
use Role\Http\Controllers\RoleController;

Route::middleware(['auth:admin'])->prefix('api')->group(function (): void {
    Route::resource('/role', RoleController::class);
    Route::get('/role/{id}', [RoleController::class, 'show']);
    Route::resource('/permission', PermissionController::class);
    
    Route::post('role-map-permission', [RoleController::class, 'mapPermissionByRole']);
    Route::post('/sync-role-permissions', [RoleController::class, 'syncRolePermissions']);
    
    Route::post('user-sync-role', [RoleController::class, 'userSyncRole']);
    Route::post('user-sync-permission', [PermissionController::class, 'userSyncPermission']);
});
