<?php

namespace App\Http\Controllers\Api\Accounts;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Get the authenticated user's profile.
     */
    public function show(Request $request)
    {
        return $this->reply(true, 'Profile retrieved successfully', [
            'user' => $request->user()->only([
                'id',
                'name',
                'email',
                'first_name',
                'last_name',
                'profile_picture',
                'job_title',
                'show_welcome_screen',
                'email_verified_at',
                'created_at',
                'updated_at',
            ])
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        
        $user = $request->user();
    
        $validated = $request->validate([
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'profile_picture' => ['nullable', 'string'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'show_welcome_screen' => ['sometimes', 'boolean'],
        ]);

        
    
        try {
            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture if it exists
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                
                // Store the new file
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $validated['profile_picture'] = $path;
            }
        
            $user->update($validated);
        
            return $this->reply(true, 'Profile updated successfully', [
                'user' => $user->only([
                    'id',
                    'name',
                    'email',
                    'first_name',
                    'last_name',
                    'profile_picture',
                    'job_title',
                    'show_welcome_screen',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ])
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Profile update failed: ' . $e->getMessage());
            return $this->reply(false, 'Failed to update profile: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Update the user's welcome screen preference.
     */
    public function updateWelcomeScreen(Request $request)
    {
        $validated = $request->validate([
            'show_welcome_screen' => ['required', 'boolean'],
        ]);

        try {
            $user = $request->user();
            $user->update([
                'show_welcome_screen' => $validated['show_welcome_screen']
            ]);

            return $this->reply(true, 'Welcome screen preference updated successfully', [
                'show_welcome_screen' => $user->show_welcome_screen
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Welcome screen update failed: ' . $e->getMessage());
            return $this->reply(false, 'Failed to update welcome screen preference', [], 500);
        }
    }
}