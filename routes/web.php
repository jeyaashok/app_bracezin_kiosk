<?php

use Illuminate\Support\Facades\Route;

// Route::get('/reset-password/{token}', function (string $token) {
//     return view('email.resetPassword', ['token' => $token]);
// })->middleware('guest')->name('password.reset');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/upload', function () {
    return view('layout');
});
