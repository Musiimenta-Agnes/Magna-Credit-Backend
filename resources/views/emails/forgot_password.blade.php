<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f8fa;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 480px;
      margin: 40px auto;
      background: #ffffff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .header {
      background: linear-gradient(135deg, #007BFF, #28a745);
      padding: 32px 24px;
      text-align: center;
    }
    .header h1 {
      color: #ffffff;
      margin: 0;
      font-size: 22px;
      font-weight: 700;
      letter-spacing: 0.3px;
    }
    .header p {
      color: rgba(255,255,255,0.85);
      margin: 6px 0 0;
      font-size: 13px;
    }
    .body {
      padding: 36px 32px;
      text-align: center;
    }
    .greeting {
      font-size: 16px;
      color: #333;
      margin-bottom: 10px;
    }
    .message {
      font-size: 14px;
      color: #666;
      line-height: 1.6;
      margin-bottom: 30px;
    }
    .code-box {
      display: inline-block;
      background: #f0f6ff;
      border: 2px dashed #007BFF;
      border-radius: 14px;
      padding: 18px 40px;
      margin-bottom: 28px;
    }
    .code {
      font-size: 38px;
      font-weight: 800;
      color: #007BFF;
      letter-spacing: 10px;
    }
    .expiry {
      font-size: 13px;
      color: #999;
      margin-bottom: 24px;
    }
    .expiry span {
      color: #e53935;
      font-weight: 600;
    }
    .warning {
      font-size: 12px;
      color: #aaa;
      border-top: 1px solid #f0f0f0;
      padding-top: 20px;
      line-height: 1.6;
    }
    .footer {
      background: #f9f9f9;
      padding: 16px;
      text-align: center;
      font-size: 12px;
      color: #bbb;
    }
  </style>
</head>
<body>
  <div class="container">

    <div class="header">
      <h1>{{ config('app.name') }}</h1>
      <p>Password Reset Request</p>
    </div>

    <div class="body">
      <p class="greeting">Hello, <strong>{{ $user->name }}</strong> 👋</p>
      <p class="message">
        We received a request to reset your password.<br>
        Use the code below to continue. Do not share it with anyone.
      </p>

      <div class="code-box">
        <div class="code">{{ $code }}</div>
      </div>

      <p class="expiry">
        This code expires in <span>10 minutes</span>.
      </p>

      <p class="warning">
        If you did not request a password reset, please ignore this email.<br>
        Your account remains safe.
      </p>
    </div>

    <div class="footer">
      &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>

  </div>
</body>
</html>

