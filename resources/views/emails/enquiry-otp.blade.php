<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>IDFR Booking Verification Code</title>
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
      font-size: 14px;
      line-height: 1.6;
      margin: 0 0 24px 0;
    }
    .otp-card {
      background: #111113;
      border: 1px dashed rgba(212, 175, 55, 0.4);
      border-radius: 8px;
      padding: 24px 16px;
      margin: 28px 0;
    }
    .otp-code {
      font-family: 'Courier New', Courier, monospace;
      font-size: 38px;
      font-weight: 800;
      letter-spacing: 8px;
      color: #d4af37;
      margin: 0;
      display: inline-block;
    }
    .otp-meta {
      margin-top: 10px;
      font-size: 12px;
      color: #71717a;
    }
    .footer {
      padding: 20px;
      background: #111113;
      text-align: center;
      border-top: 1px solid rgba(255, 255, 255, 0.05);
      font-size: 11px;
      color: #71717a;
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
        <h1>Booking Verification Code</h1>
        <p>Hello {{ $userName }}, please use the 6-digit verification code below to confirm your booking enquiry on IDFR.</p>
        <div class="otp-card">
          <div class="otp-code">{{ $otp }}</div>
          <div class="otp-meta">This code will expire in 5 minutes. Do not share this code with anyone.</div>
        </div>
        <p style="font-size: 12px; color: #71717a; margin-bottom: 0;">If you did not initiate this booking request, you can safely ignore this email.</p>
      </div>
      <div class="footer">
        &copy; {{ date('Y') }} Indian Designer Fashion Runway (IDFR). All rights reserved.
      </div>
    </div>
  </div>
</body>
</html>

