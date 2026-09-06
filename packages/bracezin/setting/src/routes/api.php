<?php

use Illuminate\Support\Facades\Route;
use Setting\Http\Controllers\SettingController;

Route::middleware(['auth:admin'])->prefix('api')->group(function (): void {
    Route::resource('/setting', SettingController::class);
});
