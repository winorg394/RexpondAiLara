<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP Code • {{ config('app.name') }}</title>
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
        
        /* Layout */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 32px 0;
            text-align: center;
        }
        
        .logo {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
            text-decoration: none;
        }
        
        .content {
            padding: 40px;
            color: #374151;
        }
        
        .otp-container {
            margin: 32px 0;
            text-align: center;
        }
        
        .otp-label {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .otp-box {
            display: inline-block;
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px 32px;
            margin: 16px 0;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 4px;
            color: #111827;
            font-family: 'Courier New', monospace;
        }
        
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 24px 0;
        }
        
        .footer {
            padding: 24px 40px;
            text-align: center;
            font-size: 13px;
            color: #9ca3af;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }
        
        .social-links {
            margin: 24px 0 16px;
        }
        
        .social-link {
            display: inline-block;
            margin: 0 8px;
            color: #6b7280;
            text-decoration: none;
        }
        
        .social-link:hover {
            color: #4f46e5;
        }
        
        /* Typography */
        h1 {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 16px;
        }
        
        p {
            margin-bottom: 16px;
            color: #4b5563;
        }
        
        .text-muted {
            color: #6b7280;
            font-size: 14px;
        }
        
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .content {
                padding: 32px 24px;
            }
            .otp-box {
                font-size: 28px;
                padding: 14px 24px;
            }
        }
    </style>
</head>
<body style="padding: 24px 0;">
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <a href="{{ config('app.url') }}" class="logo">
                {{ config('app.name') }}
            </a>
        </div>
        
        <!-- Content -->
        <div class="content">
            <h1>Your Verification Code</h1>
            <p>Hello,</p>
            <p>We received a request to sign in to your account. Please use the following verification code:</p>
            
            <div class="otp-container">
                <div class="otp-label">Verification Code</div>
                <div class="otp-box">{{ $otp }}</div>
                <div class="text-muted">This code expires in 10 minutes</div>
            </div>
            
            <div class="divider"></div>
            
            <p>If you didn't request this code, you can safely ignore this email. Someone else might have typed your email address by mistake.</p>
            
            <p>Thanks,<br>The {{ config('app.name') }} Team</p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="social-links">
                <a href="#" class="social-link">Help Center</a> • 
                <a href="#" class="social-link">Privacy Policy</a> • 
                <a href="#" class="social-link">Terms</a>
            </div>
            <div class="text-muted">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                {{ config('app.address') ?? '123 Business Street, City, Country' }}
            </div>
        </div>
    </div>
</body>
</html>
