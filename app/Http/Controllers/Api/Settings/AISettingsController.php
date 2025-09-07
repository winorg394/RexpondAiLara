<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Models\AISetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AISettingsController extends Controller
{
    /**
     * Get the authenticated user's AI settings.
     */
    public function show()
    {
        $settings = Auth::user()->aiSettings;
        
        return $this->reply(true, 'AI settings retrieved successfully', [
            'default_summary_format' => $settings->default_summary_format ?? 'paragraph',
            'priority_learning' => $settings->priority_learning ?? true,
        ]);
    }

    /**
     * Update the authenticated user's AI settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'default_summary_format' => ['sometimes', 'in:paragraph,bullet'],
            'priority_learning' => ['sometimes', 'boolean'],
        ]);

        $user = $request->user();
        
        // Update or create the settings
        $user->aiSettings()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        // Refresh the settings to get the updated values
        $settings = $user->fresh()->aiSettings;

        return $this->reply(true, 'AI settings updated successfully', [
            'default_summary_format' => $settings->default_summary_format,
            'priority_learning' => $settings->priority_learning,
        ]);
    }
}
