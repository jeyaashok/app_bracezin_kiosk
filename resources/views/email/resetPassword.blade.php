@php
    $email = $email ?? request('email');
    $token = $token ?? '';
    $resetUrl = $email
        ? url('/reset-password/' . $token . '?email=' . urlencode($email))
        : url('/reset-password/' . $token);
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} Password Reset</title>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f7fa;
            font-family: Arial, Helvetica, sans-serif;
            color: #1f2937;
        }

        table {
            border-collapse: collapse;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f4f7fa;
            padding: 24px 12px;
        }

        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #dbe3ea;
            border-radius: 10px;
            overflow: hidden;
        }

        .email-header {
            background: linear-gradient(135deg, #2c9d72, #236f56);
            padding: 22px 28px;
            color: #ffffff;
        }

        .brand-name {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .header-subtitle {
            margin: 8px 0 0;
            font-size: 14px;
            opacity: 0.95;
        }

        .email-body {
            padding: 28px;
        }

        .greeting {
            margin: 0 0 14px;
            font-size: 16px;
            font-weight: 600;
            color: #111827;
        }

        .message {
            margin: 0 0 16px;
            font-size: 15px;
            line-height: 1.6;
            color: #374151;
        }

        .cta-box {
            margin: 22px 0 18px;
            padding: 18px 16px;
            text-align: center;
            background-color: #eef7f3;
            border: 1px solid #cfe7dc;
            border-radius: 8px;
        }

        .reset-button {
            display: inline-block;
            padding: 14px 22px;
            background-color: #2c9d72;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 15px;
        }

        .secondary-link {
            margin-top: 12px;
            display: block;
            font-size: 13px;
            color: #2563eb;
            word-break: break-all;
        }

        .note {
            margin: 0;
            font-size: 14px;
            line-height: 1.6;
            color: #4b5563;
        }

        .email-footer {
            padding: 20px 28px 24px;
            border-top: 1px solid #e5e7eb;
            background-color: #fbfcfd;
        }

        .footer-text {
            margin: 0;
            font-size: 13px;
            line-height: 1.6;
            color: #6b7280;
        }

        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 12px 8px;
            }

            .email-body,
            .email-header,
            .email-footer {
                padding-left: 18px;
                padding-right: 18px;
            }
        }
    </style>
</head>
<body>
    <table class="email-wrapper" role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table class="email-container" role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="email-header">
                            <p class="brand-name">{{ config('app.name') }}</p>
                            <p class="header-subtitle">Password Reset Request</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-body">
                            <p class="greeting">Hello,</p>
                            <p class="message">
                                We received a request to reset your password for your {{ config('app.name') }} account. Click the button below to create a new password.
                            </p>

                            <div class="cta-box">
                                <a href="{{ $resetUrl }}" class="reset-button">Reset Password</a>
                                <a href="{{ $resetUrl }}" class="secondary-link">{{ $resetUrl }}</a>
                            </div>

                            <p class="note">
                                If you did not request a password reset, you can safely ignore this email. This link will expire shortly for security reasons.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-footer">
                            <p class="footer-text">
                                Regards,<br>
                                {{ config('app.name') }} Team
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
