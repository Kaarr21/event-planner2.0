<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Your Two-Factor Authentication Code</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            background-color: #f4f4f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #257bf4;
            padding: 24px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
        }

        .content {
            padding: 32px 24px;
            text-align: center;
        }

        .content p {
            margin: 0 0 16px;
            color: #3f3f46;
            font-size: 16px;
            line-height: 1.5;
        }

        .code-box {
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 16px;
            margin: 24px 0;
            font-size: 32px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.25em;
            text-align: center;
        }

        .footer {
            padding: 24px;
            background-color: #f8fafc;
            text-align: center;
            border-top: 1px solid #f1f5f9;
        }

        .footer p {
            margin: 0;
            color: #64748b;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Event Planner</h1>
        </div>
        <div class="content">
            <h2>Your Security Code</h2>
            <p>Please use the following 6-digit code to complete your sign-in process. This code will expire in 10
                minutes.</p>
            <div class="code-box">
                {{ $code }}
            </div>
            <p>If you didn't request this code, you can safely ignore this email.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Event Planner. All rights reserved.</p>
        </div>
    </div>
</body>

</html>