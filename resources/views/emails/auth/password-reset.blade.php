<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Your IDFR Password</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #0c0c0e;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      color: #e4e4e7;
    }
    .wrapper {
      width: 100%;
      background-color: #0c0c0e;
      padding: 40px 16px;
      box-sizing: border-box;
    }
    .container {
      max-width: 560px;
      margin: 0 auto;
      background: #18181b;
      border: 1px solid rgba(212, 175, 55, 0.3);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
    }
    .header {
      background: linear-gradient(180deg, #27272a 0%, #18181b 100%);
      padding: 32px 24px;
      text-align: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .brand {
      color: #d4af37;
      font-size: 22px;
      font-weight: 700;
      letter-spacing: 3px;
      text-transform: uppercase;
      margin: 0;
    }
    .subbrand {
      color: #a1a1aa;
      font-size: 11px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      margin-top: 4px;
    }
    .content {
      padding: 36px 32px;
      text-align: center;
    }
    h1 {
      font-size: 22px;
      font-weight: 600;
      color: #ffffff;
      margin: 0 0 16px 0;
    }
    p {
      color: #a1a1aa;
      font-size: 15px;
      line-height: 1.6;
      margin: 0 0 20px 0;
      text-align: left;
    }
    .btn-container {
      text-align: center;
      margin: 32px 0;
    }
    .btn {
      display: inline-block;
      background: #d4af37;
      color: #0c0c0e !important;
      font-weight: 700;
      font-size: 14px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      text-decoration: none;
      padding: 14px 32px;
      border-radius: 4px;
      box-shadow: 0 4px 15px rgba(212, 175, 55, 0.25);
    }
    .notice {
      background: #09090b;
      border-left: 3px solid #d4af37;
      padding: 14px 18px;
      border-radius: 4px;
      margin: 24px 0;
      text-align: left;
    }
    .notice p {
      margin: 0;
      font-size: 13.5px;
      color: #d4d4d8;
    }
    .fallback {
      font-size: 12px;
      color: #71717a;
      line-height: 1.5;
      word-break: break-all;
      text-align: left;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      padding-top: 20px;
      margin-top: 24px;
    }
    .fallback a {
      color: #d4af37;
      text-decoration: underline;
    }
    .footer {
      background: #09090b;
      padding: 20px 24px;
      text-align: center;
      border-top: 1px solid rgba(255, 255, 255, 0.06);
    }
    .footer p {
      color: #71717a;
      font-size: 12px;
      margin: 0;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="container">
      <div class="header">
        <div class="brand">IDFR</div>
        <div class="subbrand">Indian Designer Fashion Runway</div>
      </div>
      <div class="content">
        <h1>Password Reset Request</h1>
        <p>Hello {{ $user->name ?: 'Member' }},</p>
        <p>You are receiving this email because we received a password reset request for your IDFR account. Click the button below to choose a new password:</p>

        <div class="btn-container">
          <a href="{{ $resetUrl }}" class="btn" target="_blank">Reset Password</a>
        </div>

        <div class="notice">
          <p><strong>Security notice:</strong> This password reset link will expire in 60 minutes. If you did not request a password reset, no further action is required and your account remains completely secure.</p>
        </div>

        <div class="fallback">
          If you are having trouble clicking the "Reset Password" button, copy and paste the following URL into your web browser:<br>
          <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
        </div>
      </div>
      <div class="footer">
        <p>&copy; {{ date('Y') }} IDFR - Indian Designer Fashion Runway. All rights reserved.</p>
      </div>
    </div>
  </div>
</body>
</html>

