<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use User\Http\Controllers\SocialAuthController;

// OAuth redirect & callback routes (web flow)
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirectToProvider'])
    ->where('provider', 'google|facebook|github');

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])
    ->where('provider', 'google|facebook|github');

// REST API token authentication routes (direct token submission)
Route::post('/auth/google', [SocialAuthController::class, 'googleAuth']);
Route::post('/auth/facebook', [SocialAuthController::class, 'facebookAuth']);
Route::post('/auth/github', [SocialAuthController::class, 'githubAuth']);

// Protected routes
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
