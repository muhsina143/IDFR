<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New IDFR Booking Enquiry</title>
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
      max-width: 600px;
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
    .badge {
      display: inline-block;
      padding: 4px 12px;
      background: rgba(212, 175, 55, 0.15);
      border: 1px solid rgba(212, 175, 55, 0.4);
      color: #d4af37;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 12px;
    }
    h1 {
      font-size: 20px;
      font-weight: 600;
      color: #ffffff;
      margin: 0 0 20px 0;
    }
    .data-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 24px;
    }
    .data-table td {
      padding: 10px 12px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
      font-size: 13.5px;
      vertical-align: top;
    }
    .data-table td.label {
      color: #a1a1aa;
      width: 35%;
      font-weight: 500;
    }
    .data-table td.value {
      color: #ffffff;
      font-weight: 600;
    }
    .brief-box {
      background: #111113;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 8px;
      padding: 16px;
      font-size: 13.5px;
      line-height: 1.6;
      color: #e4e4e7;
      margin-bottom: 24px;
      white-space: pre-wrap;
    }
    .btn {
      display: inline-block;
      background: #d4af37;
      color: #0c0c0e !important;
      text-decoration: none;
      font-weight: 700;
      font-size: 13px;
      letter-spacing: 1px;
      text-transform: uppercase;
      padding: 12px 28px;
      border-radius: 6px;
      text-align: center;
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
        <div class="subbrand">Executive Portal Notification</div>
      </div>
      <div class="content">
        <span class="badge">New Inbound Booking Request</span>
        <h1>Enquiry for {{ $enquiry->professional_name }}</h1>

        <table class="data-table">
          <tr>
            <td class="label">Target Talent / Pro</td>
            <td class="value">{{ $enquiry->professional_name }} ({{ $enquiry->professional_category }})</td>
          </tr>
          <tr>
            <td class="label">Client / Enquirer</td>
            <td class="value">{{ $enquiry->name }}</td>
          </tr>
          <tr>
            <td class="label">Client Email</td>
            <td class="value"><a href="mailto:{{ $enquiry->email }}" style="color: #d4af37; text-decoration: none;">{{ $enquiry->email }}</a> (Verified ✓)</td>
          </tr>
          <tr>
            <td class="label">Phone / WhatsApp</td>
            <td class="value">{{ $enquiry->phone }}</td>
          </tr>
          <tr>
            <td class="label">Assignment Type</td>
            <td class="value">{{ $enquiry->assignment_type }}</td>
          </tr>
          <tr>
            <td class="label">Preferred Date</td>
            <td class="value">{{ $enquiry->preferred_date ? $enquiry->preferred_date->format('F d, Y') : 'Not specified' }}</td>
          </tr>
          <tr>
            <td class="label">Location</td>
            <td class="value">{{ $enquiry->location }}</td>
          </tr>
          <tr>
            <td class="label">Submission Date</td>
            <td class="value">{{ $enquiry->created_at->format('M d, Y h:i A') }}</td>
          </tr>
        </table>

        <div style="font-weight: 600; font-size: 13px; color: #d4af37; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
          Assignment Brief / Description:
        </div>
        <div class="brief-box">{{ $enquiry->brief }}</div>

        <div style="text-align: center; margin-top: 24px;">
          <a href="{{ url('/admin/profile-enquiries/' . $enquiry->id) }}" class="btn">View &amp; Manage in Admin Portal</a>
        </div>
      </div>
      <div class="footer">
        &copy; {{ date('Y') }} Indian Designer Fashion Runway. Confidential Executive Portal.
      </div>
    </div>
  </div>
</body>
</html>

