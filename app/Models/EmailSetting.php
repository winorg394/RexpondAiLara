<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailSetting extends Model
{
    protected $fillable = [
        'user_id',
        'auto_archive_low_priority',
        'newsletter_auto_categorize',
        'email_retention_days',
    ];

    protected $casts = [
        'auto_archive_low_priority' => 'boolean',
        'newsletter_auto_categorize' => 'boolean',
        'email_retention_days' => 'integer',
    ];

    protected $attributes = [
        'auto_archive_low_priority' => false,
        'newsletter_auto_categorize' => true,
        'email_retention_days' => 7,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
