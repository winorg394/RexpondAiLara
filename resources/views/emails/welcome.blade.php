<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $appName }}!</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Modern Reset */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        /* Base Styles */
        body, html {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            background-color: #f9fafb;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 0 16px;
        }
        
        .card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            padding: 24px;
            text-align: center;
            background: #4f46e5;
            color: white;
        }
        
        .logo {
            font-size: 24px;
            font-weight: 700;
            color: white;
            text-decoration: none;
        }
        
        .content {
            padding: 32px 24px;
        }
        
        h1 {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 16px;
        }
        
        p {
            margin-bottom: 24px;
            color: #4b5563;
            line-height: 1.7;
        }
        
        .button {
            display: inline-block;
            background: #4f46e5;
            color: white !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            margin: 16px 0;
        }
        
        .footer {
            padding: 24px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
        }
        
        .welcome-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 16px 0;
        }
    </style>
</head>
<body style="padding: 24px 0;">
    <div class="email-container">
        <div class="card">
            <!-- Header -->
            <div class="header">
                <a href="{{ $appUrl }}" class="logo">
                    {{ $appName }}
                </a>
            </div>
            
            <!-- Content -->
            <div class="content">
                <h1>Welcome to {{ $appName }}, {{ $user->first_name }}!</h1>
                
                <p>We're thrilled to have you on board. Thank you for joining our community! 🎉</p>
                
                <p>With {{ $appName }}, you can now access all our amazing features and start your journey with us.</p>
                
                <img src="https://source.unsplash.com/random/800x400/?welcome,technology" alt="Welcome to {{ $appName }}" class="welcome-image">
                
                <p>Here are a few things you can do to get started:</p>
                
                <ul style="margin-bottom: 24px; padding-left: 20px; color: #4b5563;">
                    <li style="margin-bottom: 8px;">Complete your profile</li>
                    <li style="margin-bottom: 8px;">Explore our features</li>
                    <li style="margin-bottom: 8px;">Invite your friends</li>
                </ul>
                
                <a href="{{ $appUrl }}/dashboard" class="button">Go to Dashboard</a>
                
                <p>If you have any questions, feel free to reply to this email. We're here to help!</p>
                
                <p>Best regards,<br>The {{ $appName }} Team</p>
            </div>
            
            <!-- Footer -->
            <div class="footer">
                <p>© {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
                <p>You're receiving this email because you signed up for a {{ $appName }} account.</p>
                <p style="margin-top: 8px; font-size: 12px; color: #9ca3af;">
                    <a href="{{ $appUrl }}/unsubscribe" style="color: #9ca3af; text-decoration: underline;">Unsubscribe</a> | 
                    <a href="{{ $appUrl }}/privacy" style="color: #9ca3af; text-decoration: underline;">Privacy Policy</a> | 
                    <a href="{{ $appUrl }}/terms" style="color: #9ca3af; text-decoration: underline;">Terms of Service</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
