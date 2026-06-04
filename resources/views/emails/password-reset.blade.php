<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #f5576c;
            text-align: center;
            padding: 20px;
            background: #f0f0f0;
            border-radius: 5px;
            letter-spacing: 5px;
            margin: 20px 0;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset Request</h1>
        </div>
        <div class="content">
            <h2>Hello {{ $name }}!</h2>
            <p>We received a request to reset your password for your <strong>{{ ucfirst($role ?? '') }}</strong> account. Please use the verification code below to proceed:</p>
            <div class="code">
                {{ $code }}
            </div>
            <div class="warning">
                <strong>⚠️ Security Notice:</strong>
                <ul style="margin-top: 10px; margin-bottom: 0;">
                    <li>This code will expire in <strong>10 minutes</strong>.</li>
                    <li>Never share this code with anyone.</li>
                    <li>If you did not request this password reset, please ignore this email.</li>
                    <li>Make sure you select the correct role ({{ ucfirst($role ?? '') }}) when resetting your password.</li>
                </ul>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Clinic Management System. All rights reserved.</p>
            <p>This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>