<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PriorityRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_name',
        'condition',
        'keywords',
        'action',
        'priority_type'
    ];

    protected $casts = [
        'keywords' => 'array',
    ];

    // Constants for action field
    const ACTION_SET_PRIORITY = 'set_priority';
    const ACTION_MARK_AS_SPAN = 'mark_as_span';

    // Constants for priority_type field
    const PRIORITY_HIGH = 'high_priority';
    const PRIORITY_MID = 'mid_priority';
    const PRIORITY_LOW = 'low_priority';
    const PRIORITY_SPAN = 'span';

    /**
     * Get the available actions
     */
    public static function getActions(): array
    {
        return [
            self::ACTION_SET_PRIORITY => 'Set Priority',
            self::ACTION_MARK_AS_SPAN => 'Mark as Span',
        ];
    }

    /**
     * Get the available priority types
     */
    public static function getPriorityTypes(): array
    {
        return [
            self::PRIORITY_HIGH => 'High Priority',
            self::PRIORITY_MID => 'Mid Priority',
            self::PRIORITY_LOW => 'Low Priority',
            self::PRIORITY_SPAN => 'Span',
        ];
    }
}
