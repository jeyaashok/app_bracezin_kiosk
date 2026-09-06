<?php

$namespace = 'Security\Http\Controllers';

// For Testing Route
Route::middleware(['auth:admin', 'auth.user'])->prefix('api')->group(function (): void {
	Route::resource('/qr', 'QrController');
	Route::resource('/otp', 'OtpController');

	Route::get('/qr-read', 'QrController@readQrcode');
});