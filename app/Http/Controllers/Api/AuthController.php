<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OtpVerification;
use App\Models\User;
use App\Models\UserSession;
use App\Notifications\SendOtpNotification;
use App\Notifications\WelcomeEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Notification;

class AuthController extends Controller
{
    /**
     * Register a new user and issue an API token
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'device_name' => 'required|string',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
 
        // Send welcome email
        Notification::send($user, new WelcomeEmail());

        // Create token with device name and default abilities
        $token = $user->createToken(
            $validated['device_name'],
            ['*']
        );

        // Track the session
        UserSession::createSession($user, $token->accessToken->id, $request);

        return $this->reply(true, 'User registered successfully', [
            'user' => $user->only(['id', 'first_name', 'last_name', 'email']),
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->reply(false, 'The provided credentials are incorrect.', ['email' => ['The provided credentials are incorrect.']], 401);
        }

        // If 2FA is enabled, send OTP instead of logging in directly
        if ($user->two_factor_enabled) {
            $otp = OtpVerification::generateForEmail($user->email);
            
            // Send OTP via email
            Mail::send('emails.otp', ['otp' => $otp->otp], function($message) use ($user) {
                $message->to($user->email)
                        ->subject('Your Two-Factor Authentication Code');
            });

            return $this->reply(true, 'Two-factor authentication required', [
                'two_factor_required' => true,
                'email' => $user->email,
                'otp' => $otp->otp // For testing only - remove in production
            ], 202);
        }

        // Create token with device name and default abilities
        $token = $user->createToken(
            $request->device_name,
            ['*']
        );

        // Track the session
        UserSession::createSession($user, $token->accessToken->id, $request);

        return $this->reply(true, 'Login successful', [
            'user' => $user->only(['id', 'first_name', 'last_name', 'email', 'two_factor_enabled']),
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
            'device_name' => 'required|string',
        ]);

        // Verify OTP
        $isValid = OtpVerification::verify($request->email, $request->otp);

        if (!$isValid) {
            return $this->reply(false, 'Invalid or expired OTP', ['otp' => ['Invalid or expired OTP']], 422);
        }

        // Get the user
        $user = User::where('email', $request->email)->firstOrFail();

        // Create token with device name and default abilities
        $token = $user->createToken(
            $request->device_name,
            ['*']
        );

        // Track the session
        UserSession::createSession($user, $token->accessToken->id, $request);

        return $this->reply(true, 'Two-factor authentication successful', [
            'user' => $user->only(['id', 'first_name', 'last_name', 'email', 'two_factor_enabled']),
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Refresh the current token
     */
    public function refresh(Request $request)
    {
        $request->validate([
            'device_name' => 'required|string',
        ]);

        // Revoke the current token
        $request->user()->currentAccessToken()->delete();

        // Create a new token
        $token = $request->user()->createToken(
            $request->device_name,
            ['*'] // All abilities
        )->plainTextToken;

        return $this->reply(true, 'Token refreshed successfully', [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Logout user (revoke token and remove session)
     */
    public function logout(Request $request)
    {
        // Delete the current token
        $token = $request->user()->currentAccessToken();
        
        // Delete the corresponding session
        UserSession::where('token_id', hash('sha256', $token->token->id))->delete();
        
        // Delete the token
        $token->delete();

        return $this->reply(true, 'Successfully logged out');
    }

    /**
     * Get the authenticated user
     */
    public function me(Request $request)
    {
        return $this->reply(true, 'User retrieved successfully', 
            $request->user()->only(['id', 'first_name', 'last_name', 'email'])
        );
    }

    /**
     * Send OTP to user's email
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'is_login' => 'boolean',
            'is_register' => 'boolean'
        ]);
  
        if($request->is_login && !User::where('email', $request->email)->exists()){
            return $this->reply(false, 'No account found with this email address.', ['email' => ['No account found with this email address.']], 404);
        }

        if($request->is_register && User::where('email', $request->email)->exists()){
            return $this->reply(false, 'An account with this email already exists.', ['email' => ['An account with this email already exists.']], 422);
        }
        
        $otp = OtpVerification::generateForEmail($request->email);

        // Send email using the view
        Mail::send('emails.otp', ['otp' => $otp->otp], function($message) use ($request) {
            $message->to($request->email)
                    ->subject('Your OTP Code');
        });

        return $this->reply(true, 'OTP has been sent to your email address.', [
            'otp' => $otp->otp // For testing only - remove in production
        ]);
    }

    /**
     * Verify OTP for registration, login, or general verification
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'is_login' => 'sometimes|boolean',
            'is_verification_only' => 'sometimes|boolean',
            'device_name' => 'required_if:is_login,true|string|nullable'
        ]);

        // If it's a verification-only request, skip the user existence checks
        if (!$request->boolean('is_verification_only')) {
            // Additional check for login flow
            if ($request->is_login && !User::where('email', $request->email)->exists()) {
                return $this->reply(false, 'No account found with this email address.', ['email' => ['No account found with this email address.']], 404);
            }
            // Additional check for registration flow
            elseif (!$request->is_login && User::where('email', $request->email)->exists()) {
                return $this->reply(false, 'An account with this email already exists.', ['email' => ['An account with this email already exists.']], 422);
            }
        }

        $isValid = OtpVerification::verify($request->email, $request->otp);

        if (!$isValid) {
            return $this->reply(false, 'Invalid or expired OTP', ['otp' => ['Invalid or expired OTP']], 422);
        }

        // For verification-only requests, just return success without generating tokens
        if ($request->boolean('is_verification_only')) {
            return $this->reply(true, 'OTP verified successfully');
        }

        // Handle login flow
        if ($request->is_login) {
            $user = User::where('email', $request->email)->firstOrFail();
            
            // Create token with device name and default abilities
            $token = $user->createToken(
                $request->device_name,
                ['*']
            );

            // Track the session
            UserSession::createSession($user, $token->accessToken->id, $request);

            return $this->reply(true, 'Login successful', [
                'user' => $user->only(['id', 'first_name', 'last_name', 'email', 'two_factor_enabled']),
                'access_token' => $token->plainTextToken,
                'token_type' => 'Bearer',
            ]);
        }

        return $this->reply(true, 'OTP verified successfully', [
            'expires_in' => 600, // 10 minutes in seconds
            'is_login' => false
        ]);
    }

    /**
     * Register a new user with OTP verification
     */
    public function registerWithOtp(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email',
            'password' => 'required|string|min:8|confirmed',
            'otp' => 'required|string|size:6',
            'device_name' => 'required|string',
        ]);

        // Check if email already exists
        if (User::where('email', $request->email)->exists()) {
            return $this->reply(false, 'The email has already been taken.', ['email' => ['The email has already been taken.']], 422);
        }

        // Verify the verification token
        $otpRecord = OtpVerification::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->whereNull('verified_at')
            ->first();

        if (!$otpRecord) {
            return $this->reply(false, 'Invalid or expired verification', 422);
        }

        // Mark OTP as used
        $otpRecord->update(['verified_at' => now()]);

        // Create the user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);
        
        // Send welcome email
        Notification::send($user, new WelcomeEmail());

        // Create token with device name and default abilities
        $token = $user->createToken(
            $request->device_name,
            ['*']
        );

        // Track the session
        UserSession::createSession($user, $token->accessToken->id, $request);

        return $this->reply(true, 'Registration successful', [
            'user' => $user->only(['id', 'first_name', 'last_name', 'email', 'two_factor_enabled']),
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Login with OTP
     */
    public function loginWithOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
            'device_name' => 'required|string',
        ]);

        // Verify OTP
        $isValid = OtpVerification::verify($request->email, $request->otp);

        if (!$isValid) {
            return $this->reply(false, 'Invalid or expired OTP', ['otp' => ['Invalid or expired OTP']], 422);
        }

        // Get the user
        $user = User::where('email', $request->email)->firstOrFail();

        // Create token with device name and default abilities
        $token = $user->createToken(
            $request->device_name,
            ['*']
        );

        // Track the session
        UserSession::createSession($user, $token->accessToken->id, $request);

        return $this->reply(true, 'Login successful', [
            'user' => $user->only(['id', 'first_name', 'last_name', 'email', 'two_factor_enabled']),
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Send OTP for password reset
     */
    public function sendPasswordResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        // Generate and save OTP
        $otp = OtpVerification::generateForEmail($request->email);

        // Send email with OTP
        Mail::send('emails.password-reset-otp', ['otp' => $otp->otp], function($message) use ($request) {
            $message->to($request->email)
                    ->subject('Your Password Reset OTP');
        });

        return $this->reply(true, 'OTP has been sent to your email for password reset.', [
            'otp' => $otp->otp // For testing only - remove in production
        ]);
    }

    /**
     * Reset password with OTP verification
     */
    public function resetPasswordWithOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verify OTP
        $otpRecord = OtpVerification::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->whereNull('verified_at')
            ->first();

        if (!$otpRecord) {
            return $this->reply(false, 'Invalid or expired OTP', ['otp' => ['Invalid or expired OTP']], 422);
        }

        // Mark OTP as used
        $otpRecord->update(['verified_at' => now()]);

        // Update user's password
        $user = User::where('email', $request->email)->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->save();

        // Revoke all user's tokens (optional: force logout from all devices)
        $user->tokens()->delete();

        return $this->reply(true, 'Password has been reset successfully');
    }

    /**
     * Toggle two-factor authentication for the authenticated user.
     */
    public function toggleTwoFactor(Request $request)
    {
        $user = $request->user();
        
        $user->update([
            'two_factor_enabled' => !$user->two_factor_enabled
        ]);

        $status = $user->two_factor_enabled ? 'enabled' : 'disabled';
        
        return $this->reply(true, "Two-factor authentication has been {$status}", [
            'two_factor_enabled' => $user->two_factor_enabled
        ]);
    }
}
