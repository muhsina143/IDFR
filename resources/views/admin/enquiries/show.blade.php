@extends('admin.layouts.app')

@section('title', 'Enquiry Details: ' . $enquiry->name)
@section('header_title', 'Inbound Enquiries')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Enquiry from <em>{{ $enquiry->name }}</em></h2>
    <p class="module-subtitle">Received on {{ $enquiry->created_at->format('l, F j, Y \a\t g:i A') }}</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.enquiries') }}" class="btn-subtle">
      ← Back to Enquiries
    </a>
  </div>
</div>

<div class="enquiry-layout">
  <!-- Left Column: Message Content -->
  <div class="enquiry-col-main">
    <div class="admin-card">
      <div class="message-head">
        <div>
          <span class="message-label">Enquiry Topic</span>
          <h3 class="message-subject">{{ $enquiry->subject ?: 'General Inbound Enquiry' }}</h3>
        </div>
        <div>
          @if($enquiry->status === 'unread')
            <span class="status-badge status-badge--pending">Unread</span>
          @elseif($enquiry->status === 'read')
            <span class="status-badge status-badge--active">Read</span>
          @elseif($enquiry->status === 'responded')
            <span class="status-badge status-badge--active" style="color: #4ade80; border-color: rgba(74, 222, 128, 0.4);">Responded</span>
          @else
            <span class="status-badge status-badge--archived">Archived</span>
          @endif
        </div>
      </div>

      <div class="message-body">
        <label class="form-label" style="text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; color: var(--gold);">Message Content</label>
        <div class="message-text">
          {!! nl2br(e($enquiry->message ?: 'No message body provided.')) !!}
        </div>
      </div>

      <div class="message-telemetry">
        <span><strong>IP Address:</strong> {{ $enquiry->ip_address ?: 'Unknown' }}</span>
        <span><strong>Client:</strong> {{ \Illuminate\Support\Str::limit($enquiry->user_agent ?: 'Standard Web Browser', 60) }}</span>
      </div>
    </div>
  </div>

  <!-- Right Column: Sender Profile & Management -->
  <div class="enquiry-col-side">
    <!-- Sender Card -->
    <div class="admin-card">
      <h3 class="card-section-title">Sender Information</h3>

      <div class="sender-detail-list">
        <div class="sender-detail-item">
          <span class="detail-label">Full Name</span>
          <span class="detail-val">{{ $enquiry->name }}</span>
        </div>

        <div class="sender-detail-item">
          <span class="detail-label">Phone Number</span>
          <div class="detail-val-action">
            <span class="detail-val">{{ $enquiry->phone }}</span>
            <a href="tel:{{ $enquiry->phone }}" class="btn-contact-action" title="Call Sender">
              <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
              <span>Call</span>
            </a>
          </div>
        </div>

        @if($enquiry->email)
          <div class="sender-detail-item">
            <span class="detail-label">Email Address</span>
            <div class="detail-val-action">
              <span class="detail-val">{{ $enquiry->email }}</span>
              <a href="mailto:{{ $enquiry->email }}?subject=Re:%20{{ rawurlencode($enquiry->subject ?: 'IDFR Fashion Runway Enquiry') }}" class="btn-contact-action" title="Send Email">
                <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                <span>Email</span>
              </a>
            </div>
          </div>
        @endif
      </div>
    </div>

    <!-- Management Card -->
    <div class="admin-card" style="margin-top: 1.5rem;">
      <h3 class="card-section-title">Status &amp; Internal Notes</h3>

      <form action="{{ route('admin.enquiries.update', $enquiry->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
          <label for="status" class="form-label">Update Status</label>
          <select id="status" name="status" class="form-control">
            <option value="unread" {{ $enquiry->status === 'unread' ? 'selected' : '' }}>Unread</option>
            <option value="read" {{ $enquiry->status === 'read' ? 'selected' : '' }}>Read</option>
            <option value="responded" {{ $enquiry->status === 'responded' ? 'selected' : '' }}>Responded (Action Taken)</option>
            <option value="archived" {{ $enquiry->status === 'archived' ? 'selected' : '' }}>Archived</option>
          </select>
        </div>

        <div class="form-group">
          <label for="admin_notes" class="form-label">Internal Administrator Notes</label>
          <textarea id="admin_notes" name="admin_notes" rows="4" class="form-control" placeholder="Add private notes regarding phone calls, designer agreements, or follow-up status...">{{ old('admin_notes', $enquiry->admin_notes) }}</textarea>
        </div>

        <button type="submit" class="btn-gold btn-block">
          Save Status &amp; Notes
        </button>
      </form>

      <div class="danger-zone" style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.08);">
        <form action="{{ route('admin.enquiries.destroy', $enquiry->id) }}" method="POST" onsubmit="return confirm('Permanently delete this enquiry record?');">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn-delete-full">
            Delete Enquiry
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
  .module-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
  }
  .module-title {
    font-size: 1.75rem;
    font-weight: 300;
    color: var(--text-primary);
  }
  .module-title em {
    font-style: normal;
    color: var(--gold);
    font-weight: 600;
  }
  .module-subtitle {
    font-size: 0.9rem;
    color: var(--text-muted);
    margin-top: 0.25rem;
  }
  .enquiry-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
  }
  .card-section-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--gold);
    letter-spacing: 0.05em;
    text-transform: uppercase;
    margin-bottom: 1.25rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid rgba(229, 184, 61, 0.2);
  }
  .message-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding-bottom: 1.25rem;
    border-bottom: 1px solid var(--border-subtle);
    margin-bottom: 1.5rem;
  }
  .message-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-muted);
  }
  .message-subject {
    font-size: 1.35rem;
    font-weight: 500;
    color: var(--text-primary);
    margin-top: 0.25rem;
  }
  .message-body {
    margin-bottom: 2rem;
  }
  .message-text {
    margin-top: 0.5rem;
    padding: 1.5rem;
    background: #000;
    border: 1px solid var(--border-subtle);
    border-radius: 8px;
    color: #e5e5e5;
    font-size: 0.95rem;
    line-height: 1.7;
    white-space: pre-wrap;
  }
  .message-telemetry {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    font-size: 0.78rem;
    color: var(--text-muted);
  }
  .sender-detail-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
  .sender-detail-item {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    padding-bottom: 0.85rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.04);
  }
  .detail-label {
    font-size: 0.75rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .detail-val {
    font-size: 0.95rem;
    color: var(--text-primary);
    font-weight: 500;
  }
  .detail-val-action {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .btn-contact-action {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.3rem 0.65rem;
    background: #1a1a1a;
    border: 1px solid rgba(229, 184, 61, 0.3);
    border-radius: 4px;
    color: var(--gold);
    font-size: 0.75rem;
    text-decoration: none;
    transition: all 0.2s;
  }
  .btn-contact-action:hover {
    background: rgba(229, 184, 61, 0.15);
    transform: translateY(-1px);
  }
  .btn-contact-action svg {
    width: 12px;
    height: 12px;
    stroke: currentColor;
    stroke-width: 2;
    fill: none;
  }
  .form-group {
    margin-bottom: 1.25rem;
  }
  .form-label {
    display: block;
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin-bottom: 0.4rem;
  }
  .form-control {
    width: 100%;
    padding: 0.65rem 0.85rem;
    background: #000;
    border: 1px solid var(--border-subtle);
    border-radius: 6px;
    color: var(--text-primary);
    font-size: 0.9rem;
  }
  .form-control:focus {
    border-color: var(--gold);
    outline: none;
  }
  .btn-block {
    width: 100%;
    padding: 0.75rem;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .btn-delete-full {
    width: 100%;
    padding: 0.6rem;
    background: transparent;
    border: 1px solid rgba(239, 68, 68, 0.3);
    border-radius: 6px;
    color: #f87171;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s;
  }
  .btn-delete-full:hover {
    background: rgba(239, 68, 68, 0.15);
    border-color: #f87171;
  }
  @media (max-width: 900px) {
    .enquiry-layout {
      grid-template-columns: 1fr;
    }
  }
</style>
@endsection
