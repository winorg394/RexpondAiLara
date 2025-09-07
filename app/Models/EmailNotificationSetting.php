<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailNotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'high_priority_email_alerts',
        'all_new_emails',
        'desktop_badges',
        'enable_quiet_time',
        'quiet_time_start',
        'quiet_time_end',
    ];

    protected $casts = [
        'high_priority_email_alerts' => 'boolean',
        'all_new_emails' => 'boolean',
        'desktop_badges' => 'boolean',
        'enable_quiet_time' => 'boolean',
        'quiet_time_start' => 'datetime:H:i',
        'quiet_time_end' => 'datetime:H:i',
    ];

    protected $attributes = [
        'high_priority_email_alerts' => true,
        'all_new_emails' => true,
        'desktop_badges' => true,
        'enable_quiet_time' => false,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
