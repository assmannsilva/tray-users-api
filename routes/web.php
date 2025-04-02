<?php

use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/google-callback',  [GoogleAuthController::class, 'googleCallback'])
    ->name('google.auth.google-callback');
