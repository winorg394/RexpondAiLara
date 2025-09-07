<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\WelcomeEmail;
use Illuminate\Support\Facades\Notification;
use App\Models\UserSession; // Add this line
use Illuminate\Http\Request; // Add this line

class OAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {  // Return the redirect URL instead of redirecting
        $url = Socialite::driver('google')
            ->stateless()
            ->redirect()
            ->getTargetUrl();

       return $this->reply(true, 'redirect user to this url', [
            'url' => $url,
        ]);
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback(Request $request) // Update this line
    {
        try {
            $googleUser = Socialite::driver('google')->with(['verify' => false])->stateless()->user();
            
            $googleName = $googleUser->getName();
            $nameParts = explode(' ', $googleName, 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';
            
            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(uniqid()),
                    'email_verified_at' => now(),
                ]
            );
            
            // Send welcome email
            Notification::send($user, new WelcomeEmail());
    
            // Create token with device name and default abilities
            $token = $user->createToken(
                'google-oauth-token',
                ['*']
            );

            // Track the session
            UserSession::createSession($user, $token->accessToken->id, $request);
    
            return $this->reply(true, 'Google authentication successful', [
                'user' => $user->only(['id', 'first_name', 'last_name', 'email']),
                'access_token' => $token->plainTextToken,
                'token_type' => 'Bearer',
            ]);
    
        } catch (\Throwable $e) {
            return $this->reply(false, 'Google authentication failed: ' . $e->getMessage(), [], 401);
        }
    }
}
