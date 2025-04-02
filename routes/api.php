<?php

use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->controller(GoogleAuthController::class)->group(function () {
    Route::get('/generate-token', 'generateToken')->name('google.auth.generate-token');
});


Route::prefix('users')->controller(UserController::class)->group(function () {
    Route::get('/search', 'search')->name('user.search');
    Route::post('{user}/complete-registration', 'completeRegistration')->name('user.complete-registration');
});
