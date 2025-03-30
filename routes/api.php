<?php

use App\Http\Controllers\GoogleAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->middleware(['web'])->group(function () {
    Route::controller(GoogleAuthController::class)->group(function () {
        Route::get('/generate-token', 'generateToken')->name('google.auth.generate-token');
        Route::get('/google-callback', 'googleCallback')->name('google.auth.google-callback');
    });
});
