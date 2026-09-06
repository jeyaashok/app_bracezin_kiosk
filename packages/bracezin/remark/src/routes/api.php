<?php

use Illuminate\Support\Facades\Route;
use Remark\Http\Controllers\CommentController;
use Remark\Http\Controllers\FeedbackController;

// For Testing Route
Route::middleware(['auth:admin', 'auth.user'])->prefix('api')->group(function (): void {
    Route::resource('/comment', CommentController::class);
    Route::resource('/feedback', FeedbackController::class);
});
