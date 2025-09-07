<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class UserSession extends Model
{
    protected $fillable = [
        'user_id',
        'token_id',
        'ip_address',
        'user_agent',
        'device',
        'platform',
        'browser',
        'is_current',
        'last_activity',
        'expires_at',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'last_activity' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function createSession($user, $tokenId, $request)
    {
        $userAgent = $request->userAgent();
        $platform = self::getPlatform($userAgent);
        $browser = self::getBrowser($userAgent);
        $device = self::getDevice($userAgent);
        
        // Mark all other sessions as not current
        self::where('user_id', $user->id)
            ->where('is_current', true)
            ->update(['is_current' => false]);

        return self::create([
            'user_id' => $user->id,
            'token_id' => hash('sha256', $tokenId),
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'device' => $device,
            'platform' => $platform,
            'browser' => $browser,
            'is_current' => true,
            'last_activity' => now(),
            'expires_at' => now()->addMinutes(config('session.lifetime')),
        ]);
    }

    protected static function getPlatform($userAgent)
    {
        if (Str::contains($userAgent, 'Windows')) {
            return 'Windows';
        } elseif (Str::contains($userAgent, 'Macintosh') || Str::contains($userAgent, 'Mac OS X')) {
            return 'Mac OS X';
        } elseif (Str::contains($userAgent, 'Linux')) {
            return 'Linux';
        } elseif (Str::contains($userAgent, 'Android')) {
            return 'Android';
        } elseif (Str::contains($userAgent, 'iPhone') || Str::contains($userAgent, 'iPad') || Str::contains($userAgent, 'iPod')) {
            return 'iOS';
        }
        return 'Unknown';
    }

    protected static function getBrowser($userAgent)
    {
        if (Str::contains($userAgent, 'OPR') || Str::contains($userAgent, 'Opera')) {
            return 'Opera';
        } elseif (Str::contains($userAgent, 'Edg')) {
            return 'Edge';
        } elseif (Str::contains($userAgent, 'Chrome')) {
            return 'Chrome';
        } elseif (Str::contains($userAgent, 'Safari')) {
            return 'Safari';
        } elseif (Str::contains($userAgent, 'Firefox')) {
            return 'Firefox';
        }
        return 'Unknown';
    }

    protected static function getDevice($userAgent)
    {
        if (Str::contains($userAgent, 'Mobile')) {
            return 'Mobile';
        } elseif (Str::contains($userAgent, 'Tablet')) {
            return 'Tablet';
        } elseif (Str::contains($userAgent, 'Windows') || Str::contains($userAgent, 'Macintosh') || Str::contains($userAgent, 'Linux')) {
            return 'Desktop';
        }
        return 'Unknown';
    }
}
