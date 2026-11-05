@extends('admin.layouts.app')

@section('title', 'Sponsorship Enquiry #' . $enquiry->id . ' — ' . $enquiry->name)
@section('header_title', 'Sponsorship Enquiry Details')

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
  .message-box {
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
    padding: 0.5rem 0.85rem;
    border-radius: 6px;
    font-size: 0.8rem;
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
    width: 15px;
    height: 15px;
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
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .badge-status--new {
    background: rgba(229, 184, 61, 0.15);
    border: 1px solid rgba(229, 184, 61, 0.5);
    color: var(--gold);
    box-shadow: 0 0 12px rgba(229, 184, 61, 0.2);
  }
  .badge-status--contacted {
    background: rgba(59, 130, 246, 0.15);
    border: 1px solid rgba(59, 130, 246, 0.4);
    color: #60a5fa;
  }
  .badge-status--completed {
    background: rgba(34, 197, 94, 0.15);
    border: 1px solid rgba(34, 197, 94, 0.4);
    color: #4ade80;
  }
  .status-radio-group {
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
    margin-bottom: 1.25rem;
  }
  .status-radio-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.65rem 0.85rem;
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid var(--border-subtle);
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.85rem;
    color: var(--text-primary);
    transition: all 0.2s ease;
  }
  .status-radio-label:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.2);
  }
  .status-radio-label input[type="radio"]:checked + span {
    font-weight: 600;
    color: var(--gold);
  }
</style>
@endsection

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.sponsorship-enquiries.index') }}" class="back-link">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
      <span>Back to Sponsorship Enquiries</span>
    </a>
    <h2 class="module-title" style="margin-top: 0.35rem;">
      Enquiry #{{ $enquiry->id }} &mdash; <em>{{ $enquiry->name }}</em>
    </h2>
    <p class="module-subtitle">Received {{ $enquiry->created_at ? $enquiry->created_at->format('l, F j, Y \a\t g:i A') : 'N/A' }} ({{ $enquiry->created_at ? $enquiry->created_at->diffForHumans() : '' }})</p>
  </div>

  <div class="module-header__actions">
    <span class="badge-status {{ $enquiry->statusBadgeClass() }}">
      {{ $enquiry->statusLabel() }}
    </span>
  </div>
</div>

<div class="enquiry-layout">
  <!-- Left Column: Details & Message -->
  <div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Company & Contact Details -->
    <div class="admin-card">
      <h3 class="card-section-title">
        <span>Sponsor Information</span>
        <span style="font-size: 0.75rem; color: var(--gold); text-transform: none; letter-spacing: normal;">IDFR Runway Partnership</span>
      </h3>

      <div class="detail-list">
        <div class="detail-item">
          <span class="detail-label">Name / Company Name</span>
          <span class="detail-val" style="font-size: 1.15rem; color: #fff;">{{ $enquiry->name }}</span>
        </div>

        <div class="detail-item">
          <span class="detail-label">Sponsorship Tier / Package</span>
          <span class="detail-val" style="color: var(--gold); font-size: 1rem; font-weight: 600;">
            {{ $enquiry->sponsorship_type }}
          </span>
        </div>

        <div class="detail-item">
          <span class="detail-label">Target Runway Season</span>
          <span class="detail-val">
            {{ $enquiry->season_title ?: ($enquiry->season?->title ?? 'Season 8') }}
            @if($enquiry->season && $enquiry->season->is_current)
              <span style="display: inline-block; margin-left: 0.35rem; font-size: 0.7rem; color: var(--gold); padding: 0.1rem 0.4rem; border: 1px solid rgba(229,184,61,0.4); border-radius: 3px;">Active Season</span>
            @endif
          </span>
        </div>

        <div class="detail-item">
          <span class="detail-label">Email Address</span>
          <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
            <span class="detail-val">{{ $enquiry->email }}</span>
            <a href="mailto:{{ $enquiry->email }}?subject={{ urlencode('Re: IDFR Sponsorship Enquiry - ' . $enquiry->sponsorship_type) }}" class="btn-contact-action">
              <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
              <span>Email Sponsor</span>
            </a>
          </div>
        </div>

        <div class="detail-item">
          <span class="detail-label">Phone / WhatsApp Number</span>
          <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
            <span class="detail-val">{{ $enquiry->phone }}</span>
            <div style="display: flex; gap: 0.5rem;">
              <a href="tel:{{ $enquiry->phone }}" class="btn-contact-action">
                <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                <span>Call</span>
              </a>
              <a href="https://wa.me/{{ preg_replace('/\D/', '', $enquiry->phone) }}" target="_blank" rel="noopener noreferrer" class="btn-contact-action">
                <span>WhatsApp</span>
              </a>
            </div>
          </div>
        </div>

        <div class="detail-item">
          <span class="detail-label">Submission Context</span>
          <span class="detail-val" style="font-size: 0.8rem; color: var(--text-muted);">
            IP: {{ $enquiry->ip_address ?: 'Unknown' }} &bull; User Agent: {{ Str::limit($enquiry->user_agent ?: 'Standard Web Browser', 60) }}
          </span>
        </div>
      </div>
    </div>

    <!-- Message / Proposal Details -->
    <div class="admin-card">
      <h3 class="card-section-title">
        <span>Enquiry Message &amp; Notes</span>
      </h3>
      @if($enquiry->message)
        <div class="message-box">{{ $enquiry->message }}</div>
      @else
        <div style="color: var(--text-muted); font-size: 0.9rem; font-style: italic; padding: 1rem 0;">
          No additional message was submitted with this enquiry.
        </div>
      @endif
    </div>
  </div>

  <!-- Right Column: Status & Admin Management -->
  <div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <div class="admin-card">
      <h3 class="card-section-title">Enquiry Management</h3>

      <form action="{{ route('admin.sponsorship-enquiries.update', $enquiry) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 1.25rem;">
          <label class="form-label" style="margin-bottom: 0.5rem; display: block;">Workflow Status <span class="required">*</span></label>
          <div class="status-radio-group">
            <label class="status-radio-label">
              <input type="radio" name="status" value="new" {{ $enquiry->status === 'new' ? 'checked' : '' }} required>
              <span style="color: var(--gold);">★ New / Inbound</span>
            </label>
            <label class="status-radio-label">
              <input type="radio" name="status" value="contacted" {{ $enquiry->status === 'contacted' ? 'checked' : '' }} required>
              <span style="color: #60a5fa;">Contacted / In Discussion</span>
            </label>
            <label class="status-radio-label">
              <input type="radio" name="status" value="completed" {{ $enquiry->status === 'completed' ? 'checked' : '' }} required>
              <span style="color: #4ade80;">Completed / Confirmed Sponsor</span>
            </label>
          </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
          <label for="admin_notes" class="form-label" style="margin-bottom: 0.5rem; display: block;">Executive / Internal Notes</label>
          <textarea
            id="admin_notes"
            name="admin_notes"
            rows="5"
            class="form-control"
            placeholder="Record follow-up calls, deliverables, tier negotiation terms, or sponsorship package details..."
            style="font-size: 0.85rem; line-height: 1.5;"
          >{{ old('admin_notes', $enquiry->admin_notes) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
          <span>Update Status &amp; Notes</span>
        </button>
      </form>
    </div>

    <!-- Danger Zone Card -->
    <div class="admin-card" style="border-color: rgba(239, 68, 68, 0.3);">
      <h3 class="card-section-title" style="color: #f87171;">Danger Zone</h3>
      <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem;">
        Deleting this enquiry will permanently remove it from the IDFR database.
      </p>
      <form action="{{ route('admin.sponsorship-enquiries.destroy', $enquiry) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this sponsorship enquiry from {{ addslashes($enquiry->name) }}?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" style="width: 100%; justify-content: center;">
          <span>Delete Sponsorship Enquiry</span>
        </button>
      </form>
    </div>
  </div>
</div>
@endsection

