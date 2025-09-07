<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'first_name',
        'last_name',
        'google_access_token',
        'profile_picture',
        'job_title',
        'show_welcome_screen',
        'two_factor_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google_access_token',
    ];

    /**
     * The attributes that should be eager loaded.
     *
     * @var list<string>
     */
    protected $with = ['emailSettings', 'aiSettings', 'emailNotificationSettings', 'sessions'];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($user) {
            $user->name = trim($user->first_name . ' ' . $user->last_name);
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'google_access_token' => 'array',
            'show_welcome_screen' => 'boolean',
            'two_factor_enabled' => 'boolean',
        ];
    }

    /**
     * Get the email settings for the user.
     */
    public function emailSettings(): HasOne
    {
        return $this->hasOne(EmailSetting::class)->withDefault();
    }

    /**
     * Get the AI settings for the user.
     */
    public function aiSettings(): HasOne
    {
        return $this->hasOne(AISetting::class)->withDefault();
    }

    /**
     * Get the email notification settings for the user.
     */
    public function emailNotificationSettings(): HasOne
    {
        return $this->hasOne(EmailNotificationSetting::class)->withDefault();
    }

    /**
     * Get all sessions for the user.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }
}
