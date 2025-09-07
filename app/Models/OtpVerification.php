<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class OtpVerification extends Model
{
    protected $fillable = [
        'email',
        'otp',
        'expires_at',
        'verified_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Generate a new OTP for the given email
     */
    public static function generateForEmail(string $email): self
    {
        // Invalidate any existing OTPs for this email
        self::where('email', $email)->delete();

        return self::create([
            'email' => $email,
            'otp' => strtoupper(Str::random(6)), // 6-digit alphanumeric OTP
            'expires_at' => now()->addMinutes(10), // 10 minutes expiration
        ]);
    }

    /**
     * Verify if the OTP is valid
     */
    public static function verify(string $email, string $otp): bool
    {
        $otpRecord = self::where('email', $email)
            ->where('otp', $otp)
            ->where('expires_at', '>', now())
            ->whereNull('verified_at')
            ->first();

        if ($otpRecord) {
            $otpRecord->update(['verified_at' => now()]);
            
            // Update user's email_verified_at if the user exists
            $user = User::where('email', $email)->first();
            if ($user && is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
                $user->save();
            }
            
            return true;
        }

        return false;
    }

    /**
     * Check if OTP is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if OTP is verified
     */
    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }
}
