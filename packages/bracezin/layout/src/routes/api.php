<?php

use Illuminate\Support\Facades\Route;
use Layout\Http\Controllers\MenuController;
use Layout\Http\Controllers\MenuGroupController;
use Layout\Http\Controllers\SubMenuController;


Route::middleware(['auth:admin'])->prefix('api')->group(function (): void {
	Route::resource('/menu-group', MenuGroupController::class);
	Route::resource('/menu', MenuController::class);
	Route::resource('/sub-menu', SubMenuController::class);
});
