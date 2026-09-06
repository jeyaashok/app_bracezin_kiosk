<?php

use Directory\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:admin'])->prefix('api')->group(function (): void {
    Route::resource('/media', MediaController::class);
    Route::get('/show-media/{id}', [MediaController::class, 'showMedia'])->name('show.media');
    // Route::post('/upload-media', [MediaController::class, 'uploadMedia'])->name('upload.media');
    // Route::get('/upload-media/progress', [MediaController::class, 'uploadProgress'])->name('upload.media.progress');
    Route::delete('/delete-media/{id}', [MediaController::class, 'deleteMedia'])->name('delete.media');
});

Route::prefix('api')->group(function (): void {
    Route::post('/upload-media', [MediaController::class, 'uploadMedia'])->name('upload.media');
    Route::get('/upload-media/progress', [MediaController::class, 'uploadProgress'])->name('upload.media.progress');
    Route::get('/media-url/{id}', [MediaController::class, 'showMedia'])->name('show.media');
});
