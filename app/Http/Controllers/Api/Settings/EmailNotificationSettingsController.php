<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailNotificationSettingsController extends Controller
{
    /**
     * Get the authenticated user's email notification settings.
     */
    public function show(Request $request)
    {
        $settings = $request->user()->emailNotificationSettings;
        
        return $this->reply(true, 'Email notification settings retrieved successfully', [
            'high_priority_email_alerts' => $settings->high_priority_email_alerts,
            'all_new_emails' => $settings->all_new_emails,
            'desktop_badges' => $settings->desktop_badges,
            'enable_quiet_time' => $settings->enable_quiet_time,
            'quiet_time_start' => $settings->quiet_time_start?->format('H:i'),
            'quiet_time_end' => $settings->quiet_time_end?->format('H:i'),
        ]);
    }

    /**
     * Update the authenticated user's email notification settings.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'high_priority_email_alerts' => ['sometimes', 'boolean'],
            'all_new_emails' => ['sometimes', 'boolean'],
            'desktop_badges' => ['sometimes', 'boolean'],
            'enable_quiet_time' => ['sometimes', 'boolean'],
            'quiet_time_start' => ['required_if:enable_quiet_time,true', 'nullable', 'date_format:H:i'],
            'quiet_time_end' => ['required_if:enable_quiet_time,true', 'nullable', 'date_format:H:i', 'after:quiet_time_start'],
        ], [
            'quiet_time_end.after' => 'The end time must be after the start time.',
        ]);

        if ($validator->fails()) {
            return $this->reply(false, 'Validation failed', $validator->errors()->toArray(), 422);
        }

        try {
            $user = $request->user();
            $validated = $validator->validated();
            
            // If quiet time is disabled, set the times to null
            if (isset($validated['enable_quiet_time']) && !$validated['enable_quiet_time']) {
                $validated['quiet_time_start'] = null;
                $validated['quiet_time_end'] = null;
            }
            
            // Update or create the settings
            $user->emailNotificationSettings()->updateOrCreate(
                ['user_id' => $user->id],
                $validated
            );

            // Refresh to get the updated settings
            $settings = $user->fresh()->emailNotificationSettings;

            return $this->reply(true, 'Email notification settings updated successfully', [
                'high_priority_email_alerts' => $settings->high_priority_email_alerts,
                'all_new_emails' => $settings->all_new_emails,
                'desktop_badges' => $settings->desktop_badges,
                'enable_quiet_time' => $settings->enable_quiet_time,
                'quiet_time_start' => $settings->quiet_time_start?->format('H:i'),
                'quiet_time_end' => $settings->quiet_time_end?->format('H:i'),
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to update email notification settings: ' . $e->getMessage());
            return $this->reply(false, 'Failed to update email notification settings', [], 500);
        }
    }
}
