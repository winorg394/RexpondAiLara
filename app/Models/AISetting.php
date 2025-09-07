<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AISetting extends Model
{

    protected $table = 'ai_settings';
    protected $fillable = [
        'user_id',
        'default_summary_format',
        'priority_learning',
    ];

    protected $casts = [
        'priority_learning' => 'boolean',
    ];

    protected $attributes = [
        'default_summary_format' => 'paragraph',
        'priority_learning' => true,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
