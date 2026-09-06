<?php

use Illuminate\Support\Facades\Route;
use Tji\Http\Controllers\DbController;
use Tji\Http\Controllers\QueueController;
use Tji\Http\Controllers\SysController;
use Tji\Http\Controllers\WidgetController;

Route::group([
    // 'domain' => config('app.sub_domain'),
    'middleware' => ['auth:admin'],
    'prefix' => 'api',
], function () {

    Route::get('/month-widget', [WidgetController::class, 'month']);
    Route::get('/add-db-column', [DbController::class, 'addColumn']);
    Route::get('/add-db-clients-table', [DbController::class, 'addColumnOnClientsTables']);

    // Sample Api Response for ITRS APIS
    Route::get('/test-api', [WidgetController::class, 'testGetApi']);
    Route::post('/test-api', [WidgetController::class, 'testPostApi']);
    Route::get('/clear-cache', [WidgetController::class, 'cacheClear']);
    Route::get('/clear-log', [WidgetController::class, 'clearLogFiles']);

    Route::get('/run-queue', [QueueController::class, 'executeRegularJob']);

    Route::get('/websockets-start', [SysController::class, 'websocketsStart']);
    Route::get('/websockets-restart', [SysController::class, 'websocketsRestart']);
});
