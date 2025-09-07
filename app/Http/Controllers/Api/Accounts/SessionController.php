<?php

namespace App\Http\Controllers\Api\Accounts;

use App\Http\Controllers\Controller;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    /**
     * Get all active sessions for the authenticated user.
     */
    public function index(Request $request)
    {
        $sessions = $request->user()->sessions()
            ->orderBy('last_activity', 'desc')
            ->get();

        return $this->reply(true, 'Sessions retrieved successfully', [
            'sessions' => $sessions->map(function ($session) use ($request) {
                return [
                    'id' => $session->id,
                    'device' => $session->device,
                    'platform' => $session->platform,
                    'browser' => $session->browser,
                    'ip_address' => $session->ip_address,
                    'is_current' => $session->is_current,
                    'last_activity' => $session->last_activity,
                    'expires_at' => $session->expires_at,
                ];
            })
        ]);
    }

    /**
     * Revoke a specific session.
     */
    public function destroy(Request $request, $id)
    {
        $session = $request->user()->sessions()->findOrFail($id);
        
        // Delete the session
        $session->delete();

        // If it's the current session, log the user out
        if ($session->is_current) {
            $request->user()->currentAccessToken()->delete();
            
            return $this->reply(true, 'Session revoked. You have been logged out.', [], 200);
        }

        return $this->reply(true, 'Session has been revoked.', [], 200);
    }

    /**
     * Revoke all other sessions except the current one.
     */
    public function revokeOtherSessions(Request $request)
    {
        $request->user()->sessions()
            ->where('is_current', false)
            ->delete();

        return $this->reply(true, 'All other sessions have been revoked.', [], 200);
    }
}
