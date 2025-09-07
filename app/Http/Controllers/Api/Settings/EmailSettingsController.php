<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Models\EmailSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailSettingsController extends Controller
{
    /**
     * Get the authenticated user's email settings.
     */
    public function show()
    {
        $settings = Auth::user()->emailSettings;
        
        return $this->reply(true, 'Email settings retrieved successfully', [
            'auto_archive_low_priority' => $settings->auto_archive_low_priority ?? false,
            'newsletter_auto_categorize' => $settings->newsletter_auto_categorize ?? true,
            'email_retention_days' => $settings->email_retention_days ?? 7,
        ]);
    }

    /**
     * Update the authenticated user's email settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'auto_archive_low_priority' => ['sometimes', 'boolean'],
            'newsletter_auto_categorize' => ['sometimes', 'boolean'],
            'email_retention_days' => ['sometimes', 'integer', 'min:1', 'max:3650'], // Max 10 years
        ]);

        $user = $request->user();
        
        // Update or create the settings
        $user->emailSettings()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        // Refresh the settings to get the updated values
        $settings = $user->fresh()->emailSettings;

        return $this->reply(true, 'Email settings updated successfully', [
            'auto_archive_low_priority' => $settings->auto_archive_low_priority,
            'newsletter_auto_categorize' => $settings->newsletter_auto_categorize,
            'email_retention_days' => $settings->email_retention_days,
        ]);
    }
}
