<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Callback</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 500px;
            text-align: center;
        }
        h1 {
            color: #333;
            margin-top: 0;
        }
        .status {
            font-size: 48px;
            margin: 20px 0;
        }
        .message {
            color: #666;
            margin: 20px 0;
            line-height: 1.6;
        }
        .user-info {
            background: #f5f5f5;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
            border-radius: 4px;
        }
        .user-info p {
            margin: 8px 0;
            color: #555;
        }
        .label {
            font-weight: bold;
            color: #333;
        }
        .token-info {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            word-break: break-all;
            color: #666;
            margin: 10px 0;
        }
        .redirect-notice {
            color: #999;
            font-size: 14px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Authentication Successful</h1>
        
        <div class="status">✓</div>
        
        <div class="message">
            <p>You have been successfully authenticated!</p>
            <p>This window will close and your application will receive the authentication token.</p>
        </div>

        @if(isset($user))
        <div class="user-info">
            <p><span class="label">Name:</span> {{ $user['name'] ?? 'N/A' }}</p>
            <p><span class="label">Email:</span> {{ $user['email'] ?? 'N/A' }}</p>
            <p><span class="label">Username:</span> {{ $user['username'] ?? 'N/A' }}</p>
        </div>
        @endif

        @if(isset($token))
        <div class="token-info">
            <strong>Access Token:</strong><br>
            {{ substr($token, 0, 20) }}...{{ substr($token, -20) }}
        </div>
        @endif

        <div class="redirect-notice">
            Redirecting to application in 3 seconds...
        </div>
    </div>

    <script>
        // Send data to parent window (opener)
        const authData = {
            success: true,
            state: '{{ $state ?? null }}',
            token: '{{ $token ?? null }}',
            token_type: '{{ $token_type ?? 'Bearer' }}',
            expires_in: {{ $expires_in ?? 3600 }},
            user: {!! json_encode($user ?? []) !!}
        };

        // If opened in popup, send to opener
        if (window.opener && !window.opener.closed) {
            window.opener.postMessage(authData, '{{ $frontend_url ?? '*' }}');
            
            // Close popup after brief delay
            setTimeout(() => {
                window.close();
            }, 1000);
        } 
        // If opened in same window, redirect to app
        else if ('{{ $frontend_url }}') {
            // Build redirect URL with auth params
            const params = new URLSearchParams({
                token: authData.token,
                state: authData.state || ''
            });
            
            setTimeout(() => {
                window.location.href = '{{ $frontend_url }}?auth=' + btoa(JSON.stringify(authData));
            }, 3000);
        }
    </script>
</body>
</html>
