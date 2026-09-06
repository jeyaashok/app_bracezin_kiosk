<?php

use Illuminate\Support\Facades\Route;
use Notification\Http\Controllers\NotifyController;

Route::middleware(['auth:admin', 'auth.user'])->prefix('api')->group(function (): void {
    Route::resource('/notify', NotifyController::class);
});
