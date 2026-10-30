<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>IDFR Booking Enquiry Confirmation</title>
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
      max-width: 580px;
      margin: 0 auto;
      background: #18181b;
      border: 1px solid rgba(212, 175, 55, 0.3);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }
    .header {
      background: linear-gradient(180deg, #27272a 0%, #18181b 100%);
      padding: 28px 24px;
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
      padding: 32px 28px;
    }
    .check-circle {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: rgba(34, 197, 94, 0.15);
      border: 2px solid #22c55e;
      color: #22c55e;
      font-size: 24px;
      line-height: 44px;
      text-align: center;
      margin: 0 auto 16px auto;
    }
    h1 {
      font-size: 20px;
      font-weight: 600;
      color: #ffffff;
      text-align: center;
      margin: 0 0 12px 0;
    }
    p.lead {
      text-align: center;
      color: #a1a1aa;
      font-size: 14px;
      line-height: 1.6;
      margin-bottom: 24px;
    }
    .summary-card {
      background: #111113;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 24px;
    }
    .summary-row {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      font-size: 13.5px;
    }
    .summary-row:last-child {
      border-bottom: none;
    }
    .label {
      color: #a1a1aa;
    }
    .val {
      color: #ffffff;
      font-weight: 600;
      text-align: right;
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
        <div class="check-circle">&check;</div>
        <h1>Enquiry Received Successfully</h1>
        <p class="lead">
          Dear {{ $enquiry->name }}, thank you for your booking enquiry. Our management team and {{ $enquiry->professional_name }} will review your request and get in touch with you shortly.
        </p>

        <div class="summary-card">
          <div class="summary-row">
            <span class="label">Talent / Professional</span>
            <span class="val">{{ $enquiry->professional_name }} ({{ $enquiry->professional_category }})</span>
          </div>
          <div class="summary-row">
            <span class="label">Assignment Type</span>
            <span class="val">{{ $enquiry->assignment_type }}</span>
          </div>
          @if($enquiry->preferred_date)
          <div class="summary-row">
            <span class="label">Preferred Date</span>
            <span class="val">{{ $enquiry->preferred_date->format('F d, Y') }}</span>
          </div>
          @endif
          <div class="summary-row">
            <span class="label">Location</span>
            <span class="val">{{ $enquiry->location }}</span>
          </div>
        </div>

        <p style="font-size: 13px; color: #a1a1aa; line-height: 1.6; text-align: center; margin: 0;">
          If you have any immediate questions or need to revise your brief, you can reply directly to this email or contact us at <a href="mailto:azmiyashiri22@gmail.com" style="color: #d4af37; text-decoration: none;">azmiyashiri22@gmail.com</a>.
        </p>
      </div>
      <div class="footer">
        &copy; {{ date('Y') }} Indian Designer Fashion Runway (IDFR). All rights reserved.
      </div>
    </div>
  </div>
</body>
</html>

