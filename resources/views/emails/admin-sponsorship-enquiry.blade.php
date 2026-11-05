<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New IDFR Sponsorship Enquiry</title>
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
      font-size: 12px;
      font-weight: 600;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      margin-bottom: 16px;
    }
    .title {
      font-size: 20px;
      color: #ffffff;
      font-weight: 600;
      margin: 0 0 12px 0;
    }
    .description {
      font-size: 14px;
      line-height: 1.6;
      color: #a1a1aa;
      margin: 0 0 24px 0;
    }
    .details-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 24px;
      background: #121214;
      border-radius: 8px;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.06);
    }
    .details-table td {
      padding: 12px 16px;
      font-size: 14px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .details-table tr:last-child td {
      border-bottom: none;
    }
    .label-col {
      color: #71717a;
      width: 150px;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 600;
    }
    .value-col {
      color: #ffffff;
      font-weight: 500;
    }
    .message-box {
      background: #121214;
      border-left: 3px solid #d4af37;
      padding: 16px;
      border-radius: 0 8px 8px 0;
      margin-bottom: 28px;
    }
    .message-title {
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #d4af37;
      margin: 0 0 8px 0;
      font-weight: 600;
    }
    .message-body {
      color: #e4e4e7;
      font-size: 14px;
      line-height: 1.6;
      margin: 0;
      white-space: pre-wrap;
    }
    .btn-container {
      text-align: center;
      margin: 28px 0 12px 0;
    }
    .btn {
      display: inline-block;
      padding: 14px 28px;
      background: linear-gradient(135deg, #d4af37 0%, #aa8c2c 100%);
      color: #000000 !important;
      text-decoration: none;
      font-weight: 600;
      font-size: 14px;
      border-radius: 6px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }
    .footer {
      background: #121214;
      padding: 20px;
      text-align: center;
      font-size: 12px;
      color: #71717a;
      border-top: 1px solid rgba(255, 255, 255, 0.05);
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="container">
      <div class="header">
        <h1 class="brand">IDFR</h1>
        <div class="subbrand">International Direct Fashion Runway</div>
      </div>
      <div class="content">
        <span class="badge">New Sponsorship Enquiry</span>
        <h2 class="title">{{ $enquiry->name }} has enquired about {{ $enquiry->sponsorship_type }}</h2>
        <p class="description">
          A new sponsorship enquiry has been submitted on the IDFR portal for 
          <strong>{{ $enquiry->season_title ?: 'Active Season' }}</strong>.
        </p>

        <table class="details-table">
          <tr>
            <td class="label-col">Company / Name</td>
            <td class="value-col">{{ $enquiry->name }}</td>
          </tr>
          <tr>
            <td class="label-col">Sponsorship Tier</td>
            <td class="value-col" style="color: #d4af37; font-weight: 600;">{{ $enquiry->sponsorship_type }}</td>
          </tr>
          <tr>
            <td class="label-col">Runway Season</td>
            <td class="value-col">{{ $enquiry->season_title ?: 'Season 8' }}</td>
          </tr>
          <tr>
            <td class="label-col">Email Address</td>
            <td class="value-col"><a href="mailto:{{ $enquiry->email }}" style="color: #60a5fa; text-decoration: none;">{{ $enquiry->email }}</a></td>
          </tr>
          <tr>
            <td class="label-col">Phone Number</td>
            <td class="value-col"><a href="tel:{{ $enquiry->phone }}" style="color: #d4af37; text-decoration: none;">{{ $enquiry->phone }}</a></td>
          </tr>
          <tr>
            <td class="label-col">Received At</td>
            <td class="value-col">{{ $enquiry->created_at ? $enquiry->created_at->format('M d, Y h:i A') : now()->format('M d, Y h:i A') }}</td>
          </tr>
        </table>

        @if($enquiry->message)
          <div class="message-box">
            <div class="message-title">Enquiry Message / Requirements</div>
            <p class="message-body">{{ $enquiry->message }}</p>
          </div>
        @endif

        <div class="btn-container">
          <a href="{{ route('admin.sponsorship-enquiries.show', $enquiry->id) }}" class="btn">View Enquiry in Admin</a>
        </div>
      </div>
      <div class="footer">
        &copy; {{ date('Y') }} IDFR Runway. This is an automated notification from your website.
      </div>
    </div>
  </div>
</body>
</html>

