@extends('admin.layouts.app')

@section('title', 'Booking Enquiry #' . $enquiry->id . ' — ' . $enquiry->professional_name)
@section('header_title', 'Booking Enquiry Details')

@section('styles')
<style>
  .enquiry-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 1.5rem;
    align-items: start;
  }
  @media (max-width: 1024px) {
    .enquiry-layout {
      grid-template-columns: 1fr;
    }
  }
  .card-section-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--gold);
    letter-spacing: 0.05em;
    text-transform: uppercase;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .detail-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
  .detail-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    padding-bottom: 0.85rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  }
  .detail-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }
  .detail-label {
    font-size: 0.72rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--text-muted);
    font-weight: 600;
  }
  .detail-val {
    font-size: 0.95rem;
    color: var(--text-primary);
    font-weight: 500;
  }
  .brief-box {
    background: rgba(0, 0, 0, 0.35);
    border: 1px solid var(--border-subtle);
    border-radius: 8px;
    padding: 1.25rem;
    font-size: 0.95rem;
    line-height: 1.6;
    color: var(--text-secondary);
    white-space: pre-wrap;
    margin-top: 0.5rem;
  }
  .btn-contact-action {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    background: rgba(229, 184, 61, 0.1);
    border: 1px solid rgba(229, 184, 61, 0.3);
    color: var(--gold);
    transition: all 0.2s ease;
  }
  .btn-contact-action:hover {
    background: rgba(229, 184, 61, 0.2);
    border-color: var(--gold);
    color: var(--gold);
  }
  .btn-contact-action svg {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    stroke-width: 2;
    fill: none;
  }
  .badge-status {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.85rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .badge-status--new { background: rgba(229, 184, 61, 0.15); border: 1px solid rgba(229, 184, 61, 0.4); color: var(--gold); }
  .badge-status--contacted { background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.4); color: #60a5fa; }
  .badge-status--in_progress { background: rgba(234, 179, 8, 0.15); border: 1px solid rgba(234, 179, 8, 0.4); color: #facc15; }
  .badge-status--completed { background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.4); color: #4ade80; }
  .badge-status--cancelled { background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #f87171; }
</style>
@endsection

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Booking Enquiry <em>#{{ $enquiry->id }}</em></h2>
    <p class="module-subtitle">Received on {{ $enquiry->created_at->format('l, F j, Y \a\t g:i A') }}</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.profile-enquiries.index') }}" class="btn-subtle">
      &larr; Back to Enquiries
    </a>
  </div>
</div>

<div class="enquiry-layout">
  <!-- Left Column: Talent & Assignment Details -->
  <div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Talent Card -->
    <div class="admin-card">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
        <div>
          <span style="font-size: 0.72rem; letter-spacing: 0.1em; text-transform: uppercase; color: var(--gold); font-weight: 600;">Selected Professional / Talent</span>
          <h3 style="font-family: var(--font-serif); font-size: 1.6rem; color: var(--text-primary); margin-top: 0.25rem;">
            {{ $enquiry->professional_name }}
          </h3>
          <span style="display: inline-block; padding: 0.2rem 0.6rem; background: rgba(255,255,255,0.06); border-radius: 4px; font-size: 0.78rem; color: var(--gold); text-transform: capitalize; margin-top: 0.35rem;">
            {{ $enquiry->professional_category }} ({{ ucfirst($enquiry->professional_type) }})
          </span>
        </div>
        <div>
          <span class="badge-status badge-status--{{ $enquiry->status }}">
            {{ $enquiry->statusLabel() }}
          </span>
        </div>
      </div>

      <div style="border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1.25rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
        <div>
          <span class="detail-label">Assignment Type</span>
          <div class="detail-val" style="color: var(--gold);">{{ $enquiry->assignment_type }}</div>
        </div>
        <div>
          <span class="detail-label">Preferred Date</span>
          <div class="detail-val">{{ $enquiry->preferred_date ? $enquiry->preferred_date->format('F d, Y') : 'Flexible / To be decided' }}</div>
        </div>
        <div>
          <span class="detail-label">Target Location</span>
          <div class="detail-val">{{ $enquiry->location }}</div>
        </div>
      </div>
    </div>

    <!-- Brief / Message Card -->
    <div class="admin-card">
      <h3 class="card-section-title">Assignment Brief / Requirements</h3>
      <div class="brief-box">{{ $enquiry->brief }}</div>

      <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.05); display: flex; flex-wrap: wrap; gap: 1.5rem; font-size: 0.78rem; color: var(--text-muted);">
        <span><strong>Email Verified:</strong> {{ $enquiry->is_email_verified ? 'Yes (via 6-digit OTP ✓)' : 'Pending' }}</span>
        @if($enquiry->verified_at)
          <span><strong>Verified At:</strong> {{ $enquiry->verified_at->format('M d, Y h:i A') }}</span>
        @endif
        <span><strong>Client IP:</strong> {{ $enquiry->ip_address ?: 'Unknown' }}</span>
      </div>
    </div>
  </div>

  <!-- Right Column: Client & Status Management -->
  <div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Client Card -->
    <div class="admin-card">
      <h3 class="card-section-title">Client Information</h3>
      <div class="detail-list">
        <div class="detail-item">
          <span class="detail-label">Client Name</span>
          <span class="detail-val">{{ $enquiry->name }}</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Email Address</span>
          <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-top: 0.2rem;">
            <span class="detail-val" style="word-break: break-all;">{{ $enquiry->email }}</span>
            <a href="mailto:{{ $enquiry->email }}?subject=Re:%20IDFR%20Booking%20Enquiry%20for%20{{ rawurlencode($enquiry->professional_name) }}" class="btn-contact-action" title="Send Email">
              <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
              <span>Email</span>
            </a>
          </div>
        </div>
        <div class="detail-item">
          <span class="detail-label">Phone / WhatsApp</span>
          <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-top: 0.2rem;">
            <span class="detail-val">{{ $enquiry->phone }}</span>
            <a href="tel:{{ $enquiry->phone }}" class="btn-contact-action" title="Call Client">
              <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
              <span>Call</span>
            </a>
          </div>
        </div>
        <div class="detail-item">
          <span class="detail-label">Location</span>
          <span class="detail-val">{{ $enquiry->location }}</span>
        </div>
      </div>
    </div>

    <!-- Status & Notes Management Card -->
    <div class="admin-card">
      <h3 class="card-section-title">Status &amp; Notes</h3>

      <form action="{{ route('admin.profile-enquiries.update', $enquiry) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 1.25rem;">
          <label class="form-label" for="status" style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Status</label>
          <select id="status" name="status" class="filter-select" style="width: 100%; margin-top: 0.35rem;">
            <option value="new" {{ $enquiry->status === 'new' ? 'selected' : '' }}>New</option>
            <option value="contacted" {{ $enquiry->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
            <option value="in_progress" {{ $enquiry->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="completed" {{ $enquiry->status === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ $enquiry->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label class="form-label" for="admin_notes" style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Internal Admin Notes</label>
          <textarea id="admin_notes" name="admin_notes" rows="4" style="width: 100%; background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle); border-radius: 6px; padding: 0.75rem; color: var(--text-primary); font-size: 0.85rem; margin-top: 0.35rem; resize: vertical;" placeholder="Add internal notes about communication with client or talent...">{{ old('admin_notes', $enquiry->admin_notes) }}</textarea>
        </div>

        <button type="submit" class="btn-subtle" style="width: 100%; justify-content: center; background: var(--gold); color: #000; font-weight: 600;">
          Save Changes
        </button>
      </form>

      <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid rgba(255,255,255,0.05); text-align: center;">
        <form action="{{ route('admin.profile-enquiries.destroy', $enquiry) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this booking enquiry?');">
          @csrf
          @method('DELETE')
          <button type="submit" style="background: none; border: none; color: #f87171; font-size: 0.8rem; cursor: pointer; text-decoration: underline;">
            Delete this enquiry permanently
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

