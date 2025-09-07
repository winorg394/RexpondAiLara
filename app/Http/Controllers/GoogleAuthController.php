<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_Gmail;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
  
        $client = new Google_Client();
        $client->setClientId("406055892745-lmbl0g3f0k4m3ihnnd74md1avltv00ge.apps.googleusercontent.com");
        $client->setClientSecret("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");
        $client->setRedirectUri("http://127.0.0.1:8000/api/auth/google/import/callback");
        $client->addScope("email");
        $client->addScope("profile");
        $client->addScope("https://www.googleapis.com/auth/userinfo.email");
        $client->addScope(Google_Service_Gmail::GMAIL_READONLY);
        $client->setAccessType("offline");
        $client->setPrompt("consent");
        $client->setIncludeGrantedScopes(true);
        
        return $this->reply(true, 'Redirecting to Google', [
            'url' => $client->createAuthUrl(),
        ]);
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $client = new Google_Client();
            $client->setClientId("406055892745-lmbl0g3f0k4m3ihnnd74md1avltv00ge.apps.googleusercontent.com");
            $client->setClientSecret("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");
            $client->setRedirectUri("http://127.0.0.1:8000/api/auth/google/import/callback");

            $token = $client->fetchAccessTokenWithAuthCode($request->code);

            if (isset($token['error'])) {
                return $this->reply(false, 'Authentication failed: ' . ($token['error_description'] ?? 'Unknown error'));
            }

            $client->setAccessToken($token);
            $service = new Google_Service_Gmail($client);

            // Get user info from Google
            $oauth2 = new \Google_Service_Oauth2($client);
            $userInfo = $oauth2->userinfo->get();
            $googleEmail = $userInfo->email;

            // Find user by email
            $user = User::where('email', $googleEmail)->first();
            
            if (!$user) {
                // Create new user or handle unregistered user
                return $this->reply(false, 'No account found with this Google email. Please register first.', [], 404);
            }

            // Store the token in the database for the authenticated user
            $user->google_access_token = $token;
            $user->save(); 

            // Fetch emails
            $messages = $service->users_messages->listUsersMessages('me', ['maxResults' => 10]);

            $emails = [];
            foreach ($messages->getMessages() as $msg) {
                $message = $service->users_messages->get('me', $msg->getId(), ['format' => 'metadata']);
                $headers = $message->getPayload()->getHeaders();

                $email = [
                    'id' => $msg->getId(),
                    'snippet' => $message->getSnippet(),
                    'from' => '',
                    'to' => '',
                    'subject' => '',
                    'message_id' => '',
                    'date' => ''
                ];

                // Extract specific headers we need
                foreach ($headers as $header) {
                    $name = strtolower($header->getName());
                    if (in_array($name, ['from', 'to', 'subject', 'message-id', 'date'])) {
                        $key = $name === 'message-id' ? 'message_id' : $name;
                        $email[$key] = $header->getValue();
                    }
                }

                $emails[] = $email;
            }

            return $this->reply(true, 'Emails retrieved successfully', [
                'emails' => $emails,
                'token' => $token
            ]);

        } catch (\Exception $e) {
            return $this->reply(false, 'Error: ' . $e->getMessage(), [], 500);
        }
    }

    public function getEmails(Request $request)
    {
        $user =Auth::user();

        
        if (!$user->google_access_token) {
            return $this->reply(false, 'Google account not connected');
        }

        try {
            $client = new Google_Client();
            $client->setAccessToken($user->google_access_token);

            if ($client->isAccessTokenExpired()) {
                if ($refreshToken = $client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($refreshToken);
                    $user->google_access_token = $client->getAccessToken();
                    $user->save();
                } else {
                    return $this->reply(false, 'Session expired. Please reconnect your Google account.');
                }
            }

            $service = new Google_Service_Gmail($client);
            $messages = $service->users_messages->listUsersMessages('me', [
                'maxResults' => $request->get('limit', 50),
                'q' => $request->get('q', '')
            ]);

            $emails = [];
            foreach ($messages->getMessages() as $msg) {
                $message = $service->users_messages->get('me', $msg->getId(), ['format' => 'metadata']);
                $headers = $message->getPayload()->getHeaders();

                $email = [
                    'id' => $msg->getId(),
                    'snippet' => $message->getSnippet(),
                    'from' => '',
                    'to' => '',
                    'subject' => '',
                    'message_id' => '',
                    'date' => ''
                ];

                // Extract specific headers we need
                foreach ($headers as $header) {
                    $name = strtolower($header->getName());
                    if (in_array($name, ['from', 'to', 'subject', 'message-id', 'date'])) {
                        $key = $name === 'message-id' ? 'message_id' : $name;
                        $email[$key] = $header->getValue();
                    }
                }

                $emails[] = $email;
            }

            return $this->reply(true, 'Emails retrieved successfully', [
                'emails' => $emails
            ]);

        } catch (\Exception $e) {
            return $this->reply(false, 'Error fetching emails: ' . $e->getMessage(), [], 500);
        }
    }

    public function getUnreadEmails(Request $request)
    {
        $user =Auth::user();
        
        if (!$user->google_access_token) {
            return $this->reply(false, 'Google account not connected');
        }

        try {
            $client = new Google_Client();
            $client->setAccessToken($user->google_access_token);

            if ($client->isAccessTokenExpired()) {
                if ($refreshToken = $client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($refreshToken);
                    $user->google_access_token = $client->getAccessToken();
                    $user->save();
                } else {
                    return $this->reply(false, 'Session expired. Please reconnect your Google account.');
                }
            }

            $service = new Google_Service_Gmail($client);
            $messages = $service->users_messages->listUsersMessages('me', [
                'maxResults' => $request->get('limit', 100),
                'q' => 'is:unread',
                'labelIds' => ['INBOX' ] // Include both INBOX and SPAM
            ]);

            $emails = [];
            foreach ($messages->getMessages() as $msg) {
                $message = $service->users_messages->get('me', $msg->getId(), ['format' => 'metadata']);
                $headers = $message->getPayload()->getHeaders();

                $email = [
                    'id' => $msg->getId(),
                    'snippet' => $message->getSnippet(),
                    'from' => '',
                    'to' => '',
                    'subject' => '',
                    'message_id' => '',
                    'date' => ''
                ];

                // Extract specific headers we need
                foreach ($headers as $header) {
                    $name = strtolower($header->getName());
                    if (in_array($name, ['from', 'to', 'subject', 'message-id', 'date'])) {
                        $key = $name === 'message-id' ? 'message_id' : $name;
                        $email[$key] = $header->getValue();
                    }
                }

                $emails[] = $email;
            }

            return $this->reply(true, 'Unread emails retrieved successfully', [
                'emails' => $emails,
                'total_unread' => count($emails)
            ]);

        } catch (\Exception $e) {
            return $this->reply(false, 'Error fetching unread emails: ' . $e->getMessage(), [], 500);
        }
    }
}
