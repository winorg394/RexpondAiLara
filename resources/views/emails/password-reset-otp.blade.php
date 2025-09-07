<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP • {{ config('app.name') }}</title>
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
            border-bottom: 1px solid #e5e7eb;
        }
        
        .logo {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
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
        }
        
        .otp-code {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #111827;
            background: #f3f4f6;
            padding: 16px 24px;
            border-radius: 6px;
            display: inline-block;
            margin: 16px 0 24px;
        }
        
        .footer {
            padding: 24px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body style="padding: 24px 0;">
    <div class="email-container">
        <div class="card">
            <!-- Header -->
            <div class="header">
                <a href="{{ config('app.url') }}" class="logo">
                    {{ config('app.name') }}
                </a>
            </div>
            
            <!-- Content -->
            <div class="content">
                <h1>Password Reset Request</h1>
                
                <p>We received a request to reset your password. Please use the following OTP to proceed:</p>
                
                <div class="otp-code">{{ $otp }}</div>
                
                <p>This OTP is valid for 10 minutes. If you didn't request this, you can safely ignore this email.</p>
                
                <p>Thanks,<br>{{ config('app.name') }} Team</p>
            </div>
            
            <!-- Footer -->
            <div class="footer">
                <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                <p>If you're having trouble with the button above, copy and paste the OTP code into the application.</p>
            </div>
        </div>
    </div>
</body>
</html>
