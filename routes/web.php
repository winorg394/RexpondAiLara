<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\OAuthController;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});




Route::get('auth/google', [OAuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('auth/google/callback', [OAuthController::class, 'handleGoogleCallback'])->name('google.callback');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
