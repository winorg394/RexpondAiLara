<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\Api\Accounts\ProfileController;
use App\Http\Controllers\Api\Settings\EmailSettingsController;
use App\Http\Controllers\Api\Settings\AISettingsController;
use App\Http\Controllers\Api\Settings\EmailNotificationSettingsController;
use App\Http\Controllers\Api\Accounts\SessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Api\PriorityRuleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-two-factor', [AuthController::class, 'verifyTwoFactor']);

// OTP Authentication Routes
Route::post('/auth/otp/send', [AuthController::class, 'sendOtp']);
Route::post('/auth/otp/verify', [AuthController::class, 'verifyOtp']);
Route::post('/auth/register-with-otp', [AuthController::class, 'registerWithOtp']);
Route::post('/auth/login-with-otp', [AuthController::class, 'loginWithOtp']);

// Password Reset Routes
Route::post('/auth/password/otp', [AuthController::class, 'sendPasswordResetOtp']);
Route::post('/auth/password/reset', [AuthController::class, 'resetPasswordWithOtp']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Profile routes

        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.user.show');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.user.update');
        Route::put('/profile/welcome-screen', [ProfileController::class, 'updateWelcomeScreen'])->name('profile.user.update-welcome-screen');
    
    // 2FA Toggle
    Route::post('/toggle-two-factor', [AuthController::class, 'toggleTwoFactor']);

    // Session management routes
    Route::apiResource('sessions', SessionController::class)->only(['index', 'destroy']);
    Route::post('sessions/revoke-others', [SessionController::class, 'revokeOtherSessions'])->name('sessions.revoke-others');
        Route::delete('/{id}', [SessionController::class, 'destroy']);

    // Email Settings routes
    Route::prefix('settings/email')->group(function () {
        Route::get('/', [EmailSettingsController::class, 'show']);
        Route::put('/', [EmailSettingsController::class, 'update']);
    });

    // Email Notification Settings routes
    Route::prefix('settings/email-notifications')->group(function () {
        Route::get('/', [EmailNotificationSettingsController::class, 'show']);
        Route::put('/', [EmailNotificationSettingsController::class, 'update']);
    });

    // AI Settings routes
    Route::prefix('settings/ai')->group(function () {
        Route::get('/', [AISettingsController::class, 'show']);
        Route::put('/', [AISettingsController::class, 'update']);
    });

    // Priority Rules API Routes
    Route::apiResource('priority-rules', \App\Http\Controllers\Api\PriorityRuleController::class);
    
    // Additional routes for priority rule actions
    Route::get('priority-rules/actions', [\App\Http\Controllers\Api\PriorityRuleController::class, 'getActions']);
    Route::get('priority-rules/priority-types', [\App\Http\Controllers\Api\PriorityRuleController::class, 'getPriorityTypes']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);
});

 
// Google OAuth Routes
Route::get('/auth/redirect', [OAuthController::class, 'redirectToGoogle']);

// Google Gmail Import Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/google/import', [GoogleAuthController::class, 'redirectToGoogle']);
    Route::get('/emails', [GoogleAuthController::class, 'getEmails']);
    Route::get('/emails/unread', [GoogleAuthController::class, 'getUnreadEmails']);
});
Route::get('/auth/google/import/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
