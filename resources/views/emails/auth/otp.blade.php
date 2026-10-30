<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>IDFR Email Verification Code</title>
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
      max-width: 540px;
      margin: 0 auto;
      background: #18181b;
      border: 1px solid rgba(212, 175, 55, 0.25);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }
    .header {
      background: linear-gradient(180deg, #27272a 0%, #18181b 100%);
      padding: 32px 24px;
      text-align: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .brand {
      color: #d4af37;
      font-size: 20px;
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
      margin: 0 0 24px 0;
    }
    .otp-box {
      background: #09090b;
      border: 2px dashed #d4af37;
      border-radius: 8px;
      padding: 20px 24px;
      margin: 28px 0;
      display: inline-block;
    }
    .otp-code {
      font-family: 'Courier New', Courier, monospace;
      font-size: 36px;
      font-weight: 700;
      letter-spacing: 10px;
      color: #facc15;
      display: block;
      margin-left: 10px;
    }
    .badge {
      display: inline-block;
      margin-top: 8px;
      color: #fbbf24;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 1px;
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
      line-height: 1.5;
      margin: 0;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="container">
      <div class="header">
        <h2 class="brand">IDFR</h2>
        <div class="subbrand">Indian Designer Fashion Runway</div>
      </div>
      <div class="content">
        <h1>Verify Your Email Address</h1>
        <p>Hello {{ $userName }},</p>
        <p>Thank you for registering with IDFR. Use the single-use six-digit verification code below to activate your account:</p>
        <div class="otp-box">
          <span class="otp-code">{{ $otp }}</span>
          <span class="badge">Valid for 5 minutes</span>
        </div>
        <p style="font-size: 13px; color: #71717a;">
          Do not share this code with anyone. If you did not create an IDFR account, you can safely disregard this email.
        </p>
      </div>
      <div class="footer">
        <p>&copy; {{ date('Y') }} IDFR &middot; Indian Designer Fashion Runway. All rights reserved.</p>
      </div>
    </div>
  </div>
</body>
</html>
