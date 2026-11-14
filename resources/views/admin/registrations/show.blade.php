@extends('admin.layouts.app')

@section('title', 'Member Profile: ' . $user->name . ' | IDFR Executive Portal')
@section('page_title', 'Member Profile Review')

@section('styles')
<style>
  .profile-layout-grid {
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 1.75rem;
    align-items: start;
  }
  @media (max-width: 1024px) {
    .profile-layout-grid {
      grid-template-columns: 1fr;
    }
  }

  .card-section-label {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--gold);
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
  }

  .approval-banner-card {
    background: linear-gradient(135deg, rgba(20, 20, 20, 0.95), rgba(30, 30, 30, 0.95));
    border: 1px solid var(--border-gold);
    border-radius: 10px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
  }

  .btn-approve {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.25rem;
    border-radius: 6px;
    background: #16a34a;
    color: #fff;
    font-weight: 600;
    font-size: 0.88rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
  }
  .btn-approve:hover {
    background: #22c55e;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
  }
  .btn-approve svg {
    width: 16px;
    height: 16px;
    stroke: currentColor;
    stroke-width: 2.5;
    fill: none;
  }

  .btn-reject {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.25rem;
    border-radius: 6px;
    background: #dc2626;
    color: #fff;
    font-weight: 600;
    font-size: 0.88rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
  }
  .btn-reject:hover {
    background: #ef4444;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
  }
  .btn-reject svg {
    width: 16px;
    height: 16px;
    stroke: currentColor;
    stroke-width: 2.5;
    fill: none;
  }

  .badge-status-approved {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.35rem 0.85rem;
    border-radius: 20px;
    background: rgba(34, 197, 94, 0.15);
    border: 1px solid rgba(34, 197, 94, 0.4);
    color: #4ade80;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.04em;
  }

  .badge-status-pending {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.35rem 0.85rem;
    border-radius: 20px;
    background: rgba(229, 184, 61, 0.15);
    border: 1px solid rgba(229, 184, 61, 0.4);
    color: var(--gold);
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.04em;
  }

  .badge-status-rejected {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.35rem 0.85rem;
    border-radius: 20px;
    background: rgba(239, 68, 68, 0.15);
    border: 1px solid rgba(239, 68, 68, 0.4);
    color: #f87171;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.04em;
  }

  .user-large-avatar {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(229, 184, 61, 0.25), rgba(229, 184, 61, 0.05));
    border: 2px solid var(--border-gold);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--gold);
    font-family: var(--font-serif);
    flex-shrink: 0;
  }

  .field-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border-subtle);
    font-size: 0.88rem;
  }
  .field-row:last-child {
    border-bottom: none;
  }
  .field-label {
    color: var(--text-muted);
    font-size: 0.82rem;
  }
  .field-value {
    color: var(--text-primary);
    font-weight: 500;
    text-align: right;
  }

  .completion-checklist {
    list-style: none;
    padding: 0;
    margin: 1rem 0 0;
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
  }

  .checklist-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.85rem;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid var(--border-subtle);
  }

  .checklist-item.is-done {
    border-color: rgba(34, 197, 94, 0.25);
    background: rgba(34, 197, 94, 0.04);
  }

  .checklist-item__left {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-primary);
  }

  .checklist-item__icon {
    width: 16px;
    height: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .privacy-banner {
    padding: 1rem 1.25rem;
    background: rgba(59, 130, 246, 0.06);
    border: 1px solid rgba(59, 130, 246, 0.25);
    border-radius: 8px;
    font-size: 0.82rem;
    color: #93c5fd;
    line-height: 1.5;
    margin-bottom: 1.5rem;
    display: flex;
    gap: 0.75rem;
    align-items: flex-start;
  }

  .role-data-card {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: 10px;
    padding: 1.75rem;
    margin-bottom: 1.5rem;
  }

  .profile-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-top: 1rem;
  }
  @media (max-width: 640px) {
    .profile-grid-2 {
      grid-template-columns: 1fr;
    }
  }

  .profile-data-box {
    background: rgba(0, 0, 0, 0.25);
    border: 1px solid var(--border-subtle);
    border-radius: 6px;
    padding: 0.85rem 1rem;
  }
  .profile-data-box__label {
    font-size: 0.72rem;
    text-transform: uppercase;
    color: var(--text-muted);
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
  }
  .profile-data-box__value {
    color: var(--text-primary);
    font-size: 0.92rem;
    font-weight: 500;
    word-break: break-word;
  }

  .badge-tag-custom {
    display: inline-block;
    padding: 0.25rem 0.65rem;
    border-radius: 4px;
    background: rgba(229, 184, 61, 0.12);
    border: 1px solid rgba(229, 184, 61, 0.25);
    color: var(--gold);
    font-size: 0.75rem;
    font-weight: 600;
    margin-right: 0.35rem;
    margin-bottom: 0.35rem;
  }

  .portfolio-review-card {
    background: rgba(0, 0, 0, 0.35);
    border: 1px solid var(--border-subtle);
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.2s ease;
  }
  .portfolio-review-card:hover {
    border-color: var(--border-gold);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.5);
  }
  .portfolio-review-card img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    display: block;
    background: #0a0a0a;
  }

  .admin-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.85);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
    padding: 1.5rem;
  }
</style>
@endsection

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
      <h2 class="module-title" style="margin: 0;">{{ $user->name }}</h2>
      @if($user->isActive())
        <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.55rem; border-radius: 4px; background: rgba(34, 197, 94, 0.12); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); font-size: 0.72rem; font-weight: 600;">
          <span style="width: 6px; height: 6px; border-radius: 50%; background: #4ade80; display: inline-block;"></span>
          Active Account
        </span>
      @else
        <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.55rem; border-radius: 4px; background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); font-size: 0.72rem; font-weight: 600;">
          <span style="width: 6px; height: 6px; border-radius: 50%; background: #f87171; display: inline-block;"></span>
          Deactivated Account
        </span>
      @endif
    </div>
    <p class="module-subtitle">
      Registered member record #{{ $user->id }} &middot; Role: <strong style="color: var(--gold); text-transform: capitalize;">{{ $user->roleEnum() ? $user->roleEnum()->label() : $user->role }}</strong>
    </p>
  </div>
  <div class="module-header__actions" style="display: flex; gap: 0.65rem; align-items: center;">
    <a href="{{ route('admin.registrations.index') }}" class="btn-subtle" style="text-decoration: none;">
      &larr; Back to Members
    </a>
    <a href="{{ route('admin.registrations.edit', $user) }}" class="btn-subtle" style="text-decoration: none; background: rgba(229, 184, 61, 0.15); border-color: var(--border-gold); color: var(--gold);">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
      Edit Account
    </a>
    <form action="{{ route('admin.registrations.toggle-status', $user) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to {{ $user->isActive() ? 'deactivate' : 'activate' }} this user account?');">
      @csrf
      <button type="submit" class="btn-subtle" style="cursor: pointer; color: {{ $user->isActive() ? '#f87171' : '#4ade80' }}; border-color: {{ $user->isActive() ? 'rgba(239,68,68,0.3)' : 'rgba(34,197,94,0.3)' }};">
        {{ $user->isActive() ? 'Deactivate Account' : 'Activate Account' }}
      </button>
    </form>
  </div>
</div>

@php
  $currentStatus = $user->profile_status;
@endphp

<!-- Prominent Admin Review & Approval Banner -->
<div class="approval-banner-card">
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.25rem;">
    <div>
      <span class="card-section-label" style="margin-bottom: 0.35rem;">Admin Profile Approval Workflow</span>
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        @if($currentStatus === 'published' || $currentStatus === 'approved')
          <span class="badge-status-approved">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            ACCEPTED &amp; APPROVED — Live on Public Website
          </span>
        @elseif($currentStatus === 'rejected')
          <span class="badge-status-rejected">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            REJECTED — Hidden from Public Website
          </span>
        @else
          <span class="badge-status-pending">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            PENDING APPROVAL — Hidden Until Accepted by Admin
          </span>
        @endif
      </div>
    </div>

    <!-- Approval Action Controls -->
    <div style="display: flex; gap: 0.65rem; align-items: center; flex-wrap: wrap;">
      @if($currentStatus !== 'published' && $currentStatus !== 'approved')
        <form action="{{ route('admin.registrations.approval-status', $user) }}" method="POST" style="margin: 0;">
          @csrf
          <input type="hidden" name="status" value="published" />
          <button type="submit" class="btn-approve" onclick="return confirm('Accept and publish this profile? It will immediately appear on the IDFR public website.');">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span>Accept Profile</span>
          </button>
        </form>
      @endif

      @if($currentStatus !== 'rejected')
        <button type="button" class="btn-reject" onclick="document.getElementById('rejectModal').style.display='flex'">
          <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          <span>Reject Profile</span>
        </button>
      @endif

      @if($currentStatus !== 'pending')
        <form action="{{ route('admin.registrations.approval-status', $user) }}" method="POST" style="margin: 0;">
          @csrf
          <input type="hidden" name="status" value="pending" />
          <button type="submit" class="btn-subtle" style="font-size: 0.82rem; padding: 0.6rem 0.95rem; cursor: pointer;" onclick="return confirm('Reset this profile back to Pending Approval status?');">
            <span>Reset to Pending</span>
          </button>
        </form>
      @endif
    </div>
  </div>
</div>

<!-- Rejection Modal -->
<div id="rejectModal" class="admin-modal-overlay">
  <div class="admin-card" style="width: 100%; max-width: 520px; padding: 2rem; border: 1px solid rgba(239, 68, 68, 0.4); box-shadow: 0 20px 40px rgba(0,0,0,0.8);">
    <h3 style="margin-top: 0; color: #f87171; font-family: var(--font-serif); font-size: 1.3rem;">Reject Member Profile</h3>
    <p style="color: var(--text-secondary); font-size: 0.88rem; line-height: 1.5; margin-bottom: 1.25rem;">
      Rejecting this profile keeps it strictly hidden from the public website. You may specify an optional reason for your records.
    </p>
    <form action="{{ route('admin.registrations.approval-status', $user) }}" method="POST">
      @csrf
      <input type="hidden" name="status" value="rejected" />
      <div style="margin-bottom: 1.5rem;">
        <label for="modal_rejection_reason" class="card-section-label">Rejection Reason / Internal Notes (Optional):</label>
        <textarea name="rejection_reason" id="modal_rejection_reason" rows="3" style="width: 100%; background: #0a0a0a; border: 1px solid var(--border-subtle); border-radius: 6px; padding: 0.75rem; color: #fff; font-size: 0.88rem;" placeholder="e.g. Incomplete measurements or low-resolution portfolio images..."></textarea>
      </div>
      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" class="btn-subtle" onclick="document.getElementById('rejectModal').style.display='none'">Cancel</button>
        <button type="submit" class="btn-reject" style="padding: 0.65rem 1.25rem;">Confirm Rejection</button>
      </div>
    </form>
  </div>
</div>

<div class="profile-layout-grid">
  <!-- LEFT COLUMN: Account Details, Completion Audit & Security -->
  <div>
    <!-- User Overview Card -->
    <div class="admin-card" style="margin-bottom: 1.5rem;">
      <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem;">
        <div class="user-large-avatar">
          {{ strtoupper(substr($user->name ?: 'U', 0, 1)) }}
        </div>
        <div>
          <h3 style="margin: 0; font-size: 1.2rem; color: var(--text-primary); font-family: var(--font-serif);">
            {{ $user->name }}
          </h3>
          <div style="color: var(--text-muted); font-size: 0.85rem; margin-top: 2px;">
            {{ $user->email }}
          </div>
          <div style="margin-top: 6px;">
            <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--gold); font-weight: 600;">
              {{ $user->roleEnum() ? $user->roleEnum()->label() : ucfirst($user->role) }}
            </span>
          </div>
        </div>
      </div>

      <div class="field-row">
        <span class="field-label">User ID</span>
        <span class="field-value">#{{ $user->id }}</span>
      </div>
      <div class="field-row">
        <span class="field-label">Phone</span>
        <span class="field-value">
          @if($user->phone)
            <a href="tel:{{ $user->phone }}" style="color: var(--text-primary); text-decoration: none;">{{ $user->phone }}</a>
          @else
            <span style="color: var(--text-muted);">Not provided</span>
          @endif
        </span>
      </div>
      <div class="field-row">
        <span class="field-label">Email Status</span>
        <span class="field-value">
          @if($user->isEmailVerified())
            <span style="color: #4ade80; font-weight: 600; font-size: 0.8rem;">
              Verified ({{ $user->email_verified_at->format('M d, Y') }})
            </span>
          @else
            <span style="color: var(--gold); font-weight: 600; font-size: 0.8rem;">
              Unverified (Pending OTP)
            </span>
          @endif
        </span>
      </div>
      <div class="field-row">
        <span class="field-label">Approval Status</span>
        <span class="field-value">
          @if($currentStatus === 'published' || $currentStatus === 'approved')
            <span style="color: #4ade80; font-weight: 600;">Approved</span>
          @elseif($currentStatus === 'rejected')
            <span style="color: #f87171; font-weight: 600;">Rejected</span>
          @else
            <span style="color: var(--gold); font-weight: 600;">Pending Review</span>
          @endif
        </span>
      </div>
      <div class="field-row">
        <span class="field-label">Account Status</span>
        <span class="field-value">
          @if($user->isActive())
            <span style="color: #4ade80;">Active</span>
          @else
            <span style="color: #f87171;">Deactivated</span>
          @endif
        </span>
      </div>
      @if(!$user->isActive() && $user->deactivated_at)
        <div class="field-row">
          <span class="field-label">Deactivated On</span>
          <span class="field-value" style="color: #f87171;">{{ $user->deactivated_at->format('M d, Y H:i') }}</span>
        </div>
      @endif
      @if($user->deactivation_reason)
        <div class="field-row">
          <span class="field-label">Status Notes</span>
          <span class="field-value" style="color: #f87171; max-width: 200px; text-align: right;">{{ $user->deactivation_reason }}</span>
        </div>
      @endif
      <div class="field-row">
        <span class="field-label">Payment Status</span>
        <span class="field-value" style="text-transform: uppercase; font-size: 0.78rem; font-weight: 600;">
          {{ $user->payment_status ?: 'exempt' }}
        </span>
      </div>
      <div class="field-row">
        <span class="field-label">Joined IDFR</span>
        <span class="field-value">{{ $user->created_at->format('M d, Y g:i A') }}</span>
      </div>
    </div>

    <!-- Privacy & Security Notice -->
    <div class="privacy-banner">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
      <div>
        <strong style="display: block; color: #fff; margin-bottom: 2px;">Privacy &amp; Security Protected</strong>
        Sensitive credentials (passwords, OTP hashes, private tokens) are strictly isolated and never rendered in review interfaces.
      </div>
    </div>

    <!-- Profile Completion Card -->
    <div class="admin-card" style="margin-bottom: 1.5rem;">
      <span class="card-section-label">Profile Completion Audit</span>

      <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.5rem;">
        <span style="font-size: 1.15rem; font-weight: 700; color: {{ $completion['is_complete'] ? '#4ade80' : 'var(--gold)' }};">
          {{ $completion['percentage'] }}%
        </span>
        <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: {{ $completion['is_complete'] ? '#4ade80' : 'var(--gold)' }}; font-weight: 600;">
          {{ $completion['label'] }}
        </span>
      </div>

      <div style="height: 8px; background: rgba(255, 255, 255, 0.08); border-radius: 4px; overflow: hidden; margin-bottom: 1rem;">
        <div style="height: 100%; width: {{ $completion['percentage'] }}%; background: {{ $completion['is_complete'] ? '#4ade80' : 'var(--gold)' }}; border-radius: 4px; transition: width 0.3s ease;"></div>
      </div>

      <ul class="completion-checklist">
        @php $steps = $completion['steps'] ?? []; @endphp
        <li class="checklist-item {{ !empty($steps['name']) ? 'is-done' : '' }}">
          <div class="checklist-item__left">
            <span class="checklist-item__icon" style="color: {{ !empty($steps['name']) ? '#4ade80' : '#888' }};">
              @if(!empty($steps['name']))
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              @else
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg>
              @endif
            </span>
            <span>Identity &amp; Legal Name (20%)</span>
          </div>
          <span style="font-size: 0.75rem; color: {{ !empty($steps['name']) ? '#4ade80' : '#888' }}; font-weight: 600;">
            {{ !empty($steps['name']) ? 'Verified' : 'Missing' }}
          </span>
        </li>

        <li class="checklist-item {{ !empty($steps['email_verified']) ? 'is-done' : '' }}">
          <div class="checklist-item__left">
            <span class="checklist-item__icon" style="color: {{ !empty($steps['email_verified']) ? '#4ade80' : '#888' }};">
              @if(!empty($steps['email_verified']))
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              @else
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg>
              @endif
            </span>
            <span>Email Verification OTP (25%)</span>
          </div>
          <span style="font-size: 0.75rem; color: {{ !empty($steps['email_verified']) ? '#4ade80' : '#888' }}; font-weight: 600;">
            {{ !empty($steps['email_verified']) ? 'Verified' : 'Pending' }}
          </span>
        </li>

        <li class="checklist-item {{ !empty($steps['phone']) ? 'is-done' : '' }}">
          <div class="checklist-item__left">
            <span class="checklist-item__icon" style="color: {{ !empty($steps['phone']) ? '#4ade80' : '#888' }};">
              @if(!empty($steps['phone']))
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              @else
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg>
              @endif
            </span>
            <span>Contact Phone (20%)</span>
          </div>
          <span style="font-size: 0.75rem; color: {{ !empty($steps['phone']) ? '#4ade80' : '#888' }}; font-weight: 600;">
            {{ !empty($steps['phone']) ? 'Complete' : 'Pending' }}
          </span>
        </li>

        <li class="checklist-item {{ !empty($steps['role']) ? 'is-done' : '' }}">
          <div class="checklist-item__left">
            <span class="checklist-item__icon" style="color: {{ !empty($steps['role']) ? '#4ade80' : '#888' }};">
              @if(!empty($steps['role']))
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              @else
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg>
              @endif
            </span>
            <span>Role Specialization (20%)</span>
          </div>
          <span style="font-size: 0.75rem; color: {{ !empty($steps['role']) ? '#4ade80' : '#888' }}; font-weight: 600;">
            {{ !empty($steps['role']) ? 'Assigned' : 'Unassigned' }}
          </span>
        </li>

        <li class="checklist-item {{ !empty($steps['extended_profile']) ? 'is-done' : '' }}">
          <div class="checklist-item__left">
            <span class="checklist-item__icon" style="color: {{ !empty($steps['extended_profile']) ? '#4ade80' : '#888' }};">
              @if(!empty($steps['extended_profile']))
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              @else
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg>
              @endif
            </span>
            <span>Extended Profile / Media (15%)</span>
          </div>
          <span style="font-size: 0.75rem; color: {{ !empty($steps['extended_profile']) ? '#4ade80' : '#888' }}; font-weight: 600;">
            {{ !empty($steps['extended_profile']) ? 'Submitted' : 'Pending' }}
          </span>
        </li>
      </ul>
    </div>

    <!-- Administrative Audit Activities -->
    <div class="admin-card">
      <span class="card-section-label">Audit Log &amp; Telemetry</span>
      @if($auditActivities->isNotEmpty())
        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 0.75rem;">
          @foreach($auditActivities as $activity)
            <div style="font-size: 0.8rem; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
              <div style="display: flex; justify-content: space-between; color: var(--gold); font-weight: 600; margin-bottom: 2px;">
                <span>{{ ucfirst(str_replace('_', ' ', $activity->activity)) }}</span>
                <span style="color: var(--text-muted); font-size: 0.72rem;">{{ $activity->created_at->format('M d, g:i A') }}</span>
              </div>
              <div style="color: var(--text-secondary);">
                {{ $activity->description }}
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div style="color: var(--text-muted); font-size: 0.82rem; padding: 0.5rem 0;">
          No administrative activity logged for this member yet.
        </div>
      @endif
    </div>
  </div>

  <!-- RIGHT COLUMN: Complete Profile Review & All Images Showcase -->
  <div>
    <div class="role-data-card">
      <!-- Section Header -->
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 1rem;">
        <div>
          <span class="card-section-label" style="margin-bottom: 0.2rem;">Complete Profile Review</span>
          <h3 style="margin: 0; font-size: 1.25rem; color: var(--text-primary); font-family: var(--font-serif);">
            @if($user->role === 'model')
              Fashion Model Profile &amp; Measurements
            @elseif($user->role === 'fashion-designer')
              Fashion Designer Atelier Profile
            @else
              {{ $user->roleEnum() ? $user->roleEnum()->label() : ucwords(str_replace('-', ' ', $user->role)) }} Portfolio
            @endif
          </h3>
        </div>

        @if($roleProfile instanceof \App\Models\ModelProfile)
          <a href="{{ route('admin.models.edit', $roleProfile->id) }}" class="btn-subtle" style="font-size: 0.78rem; padding: 0.4rem 0.8rem; text-decoration: none;">
            Edit in Model Manager &rarr;
          </a>
        @elseif($roleProfile instanceof \App\Models\Designer)
          <a href="{{ route('admin.designers.edit', $roleProfile->id) }}" class="btn-subtle" style="font-size: 0.78rem; padding: 0.4rem 0.8rem; text-decoration: none;">
            Edit in Designer Manager &rarr;
          </a>
        @elseif($roleProfile instanceof \App\Models\CreativeProfessional)
          <a href="{{ route('admin.professionals.edit', $roleProfile->id) }}" class="btn-subtle" style="font-size: 0.78rem; padding: 0.4rem 0.8rem; text-decoration: none;">
            Edit in Professional Manager &rarr;
          </a>
        @endif
      </div>

      @if($roleProfile)
        <!-- Profile Identity & Large Photo Card -->
        <div style="display: flex; gap: 1.5rem; align-items: flex-start; margin-bottom: 1.5rem; background: rgba(0,0,0,0.25); border: 1px solid var(--border-subtle); border-radius: 8px; padding: 1.25rem;">
          <div style="flex-shrink: 0; text-align: center;">
            <a href="{{ $roleProfile->photo_url }}" target="_blank" rel="noopener" title="Click to view full image">
              <img
                src="{{ $roleProfile->photo_url }}"
                alt="{{ $roleProfile->name }}"
                style="width: 130px; height: 165px; object-fit: cover; border-radius: 8px; border: 2px solid var(--border-gold); display: block;"
                onerror="this.onerror=null; this.src='/images/models/model-01.jpg';"
              />
            </a>
            <a href="{{ $roleProfile->photo_url }}" target="_blank" rel="noopener" style="font-size: 0.72rem; color: var(--gold); display: inline-block; margin-top: 6px; text-decoration: none;">
              View Full Photo ↗
            </a>
          </div>

          <div style="flex: 1;">
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
              <h4 style="margin: 0; color: #fff; font-size: 1.35rem; font-family: var(--font-serif);">
                {{ $roleProfile->name }}
              </h4>
              @if($roleProfile->status === 'published' || $roleProfile->status === 'approved')
                <span class="badge-status-approved" style="font-size: 0.72rem; padding: 0.15rem 0.55rem;">Live</span>
              @elseif($roleProfile->status === 'rejected')
                <span class="badge-status-rejected" style="font-size: 0.72rem; padding: 0.15rem 0.55rem;">Rejected</span>
              @else
                <span class="badge-status-pending" style="font-size: 0.72rem; padding: 0.15rem 0.55rem;">Pending Approval</span>
              @endif
            </div>

            <p style="margin: 6px 0 0; color: var(--gold); font-size: 0.92rem; font-weight: 500;">
              @if($roleProfile instanceof \App\Models\ModelProfile)
                {{ $roleProfile->gender }} Model &middot; Level: <strong>{{ $roleProfile->level ?: 'New Face' }}</strong>
              @elseif($roleProfile instanceof \App\Models\Designer)
                Brand: <strong>{{ $roleProfile->brand_name ?: 'Atelier' }}</strong>
              @else
                {{ $roleProfile->professional_title ?: $roleProfile->category_label }}
                @if($roleProfile->brand) &middot; {{ $roleProfile->brand }} @endif
              @endif
            </p>

            <div style="margin-top: 0.75rem; display: flex; flex-wrap: wrap; gap: 0.5rem; font-size: 0.82rem; color: var(--text-secondary);">
              <div>📍 {{ $roleProfile->location ?: ($user->location ?? 'Kerala, India') }}</div>
              <div>&middot;</div>
              <div>📞 {{ $roleProfile->phone ?: ($user->phone ?: 'Not provided') }}</div>
              <div>&middot;</div>
              <div>✉️ {{ $roleProfile->email ?: $user->email }}</div>
            </div>

            @if($roleProfile->outstation || $roleProfile->international)
              <div style="margin-top: 0.65rem; font-size: 0.78rem; color: var(--text-muted);">
                Availability:
                @if($roleProfile->outstation === 'yes' || $roleProfile->outstation === 'Yes')
                  <span style="color: #4ade80;">✓ Outstation</span>
                @endif
                @if($roleProfile->international === 'yes' || $roleProfile->international === 'Yes')
                  <span style="color: #4ade80; margin-left: 8px;">✓ International</span>
                @endif
              </div>
            @endif
          </div>
        </div>

        <!-- 1. MODEL PROFILE DETAILS -->
        @if($roleProfile instanceof \App\Models\ModelProfile)
          <span class="card-section-label">Measurements &amp; Physical Attributes</span>
          @php
            $meas = is_array($roleProfile->measurements) ? $roleProfile->measurements : [];
            $bust = $meas['Bust'] ?? ($meas['Chest'] ?? ($meas['bust'] ?? null));
            $waist = $meas['Waist'] ?? ($meas['waist'] ?? null);
            $hips = $meas['Hips'] ?? ($meas['hips'] ?? null);
            $shoe = $meas['Shoe'] ?? ($meas['shoe'] ?? null);
          @endphp
          <div class="profile-grid-2">
            <div class="profile-data-box">
              <div class="profile-data-box__label">Height &amp; Age</div>
              <div class="profile-data-box__value">
                {{ $roleProfile->height ? $roleProfile->height . ' cm' : '—' }}
                @if($roleProfile->age) &middot; {{ $roleProfile->age }} yrs @endif
              </div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Bust / Chest</div>
              <div class="profile-data-box__value">{{ $bust ?: 'Not specified' }}</div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Waist</div>
              <div class="profile-data-box__value">{{ $waist ?: 'Not specified' }}</div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Hips</div>
              <div class="profile-data-box__value">{{ $hips ?: 'Not specified' }}</div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Shoe Size</div>
              <div class="profile-data-box__value">{{ $shoe ?: 'Not specified' }}</div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Industry Experience</div>
              <div class="profile-data-box__value">{{ $roleProfile->experience ? $roleProfile->experience . ' yrs' : 'New Face' }}</div>
            </div>
          </div>

          <div style="margin-top: 1rem;">
            <div class="profile-data-box__label">Model Categories / Types</div>
            <div style="margin-top: 4px;">
              @if(is_array($roleProfile->types) && count($roleProfile->types) > 0)
                @foreach($roleProfile->types as $t)
                  <span class="badge-tag-custom">{{ ucfirst($t) }}</span>
                @endforeach
              @else
                <span style="color: var(--text-muted); font-size: 0.85rem;">Runway, Editorial</span>
              @endif
            </div>
          </div>

          @if(!empty($roleProfile->skills))
            <div style="margin-top: 1rem;">
              <div class="profile-data-box__label">Skills &amp; Talents</div>
              <div style="margin-top: 4px;">
                @if(is_array($roleProfile->skills))
                  @foreach($roleProfile->skills as $sk)
                    <span class="badge-tag-custom" style="background: rgba(59, 130, 246, 0.12); border-color: rgba(59, 130, 246, 0.3); color: #93c5fd;">{{ $sk }}</span>
                  @endforeach
                @else
                  <span style="color: var(--text-secondary); font-size: 0.88rem;">{{ $roleProfile->skills }}</span>
                @endif
              </div>
            </div>
          @endif

          @if($roleProfile->description)
            <div style="margin-top: 1.25rem; padding: 1rem; background: rgba(0,0,0,0.25); border-radius: 6px; border: 1px solid var(--border-subtle);">
              <div class="profile-data-box__label">Model Biography / Self Introduction</div>
              <div style="color: var(--text-secondary); font-size: 0.88rem; line-height: 1.6; margin-top: 0.35rem;">
                {{ $roleProfile->description }}
              </div>
            </div>
          @endif

        <!-- 2. FASHION DESIGNER PROFILE DETAILS -->
        @elseif($roleProfile instanceof \App\Models\Designer)
          <span class="card-section-label">Atelier &amp; Collection Details</span>
          <div class="profile-grid-2">
            <div class="profile-data-box">
              <div class="profile-data-box__label">Brand / Label</div>
              <div class="profile-data-box__value">{{ $roleProfile->brand_name ?: 'Independent Atelier' }}</div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Experience</div>
              <div class="profile-data-box__value">{{ $roleProfile->experience ?: 'Not specified' }}</div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Collection Title</div>
              <div class="profile-data-box__value">{{ $roleProfile->collection_title ?: 'Runway Collection' }}</div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Website / Online Store</div>
              <div class="profile-data-box__value">
                @if($roleProfile->website_url)
                  <a href="{{ $roleProfile->website_url }}" target="_blank" rel="noopener" style="color: var(--gold);">{{ $roleProfile->website_url }} ↗</a>
                @else
                  Not provided
                @endif
              </div>
            </div>
          </div>

          @if(!empty($roleProfile->services) && is_array($roleProfile->services))
            <div style="margin-top: 1rem;">
              <div class="profile-data-box__label">Couture Services &amp; Specialties</div>
              <div style="margin-top: 4px;">
                @foreach($roleProfile->services as $srv)
                  <span class="badge-tag-custom">{{ $srv }}</span>
                @endforeach
              </div>
            </div>
          @endif

          @if($roleProfile->biography)
            <div style="margin-top: 1.25rem; padding: 1rem; background: rgba(0,0,0,0.25); border-radius: 6px; border: 1px solid var(--border-subtle);">
              <div class="profile-data-box__label">Designer Biography &amp; Vision</div>
              <div style="color: var(--text-secondary); font-size: 0.88rem; line-height: 1.6; margin-top: 0.35rem;">
                {{ $roleProfile->biography }}
              </div>
            </div>
          @endif

        <!-- 3. CREATIVE PROFESSIONAL PROFILE DETAILS -->
        @elseif($roleProfile instanceof \App\Models\CreativeProfessional)
          <span class="card-section-label">Professional Specialisations &amp; Equipment</span>
          <div class="profile-grid-2">
            <div class="profile-data-box">
              <div class="profile-data-box__label">Category</div>
              <div class="profile-data-box__value">{{ $roleProfile->category_label }}</div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Industry Experience</div>
              <div class="profile-data-box__value">{{ $roleProfile->experience ? $roleProfile->experience . ' yrs' : 'Not specified' }}</div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Equipment, Kit &amp; Gear</div>
              <div class="profile-data-box__value">{{ $roleProfile->equipment ?: 'Professional Grade Studio Gear' }}</div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Website / Portfolio</div>
              <div class="profile-data-box__value">
                @if($roleProfile->website_url)
                  <a href="{{ $roleProfile->website_url }}" target="_blank" rel="noopener" style="color: var(--gold);">{{ $roleProfile->website_url }} ↗</a>
                @elseif($roleProfile->instagram_url)
                  <a href="{{ $roleProfile->instagram_url }}" target="_blank" rel="noopener" style="color: var(--gold);">Instagram Link ↗</a>
                @else
                  Not provided
                @endif
              </div>
            </div>
          </div>

          @if(!empty($roleProfile->services) && is_array($roleProfile->services) && count($roleProfile->services) > 0)
            <div style="margin-top: 1rem;">
              <div class="profile-data-box__label">Services &amp; Specialisations</div>
              <div style="margin-top: 4px;">
                @foreach($roleProfile->services as $srv)
                  <span class="badge-tag-custom" style="background: rgba(20, 184, 166, 0.12); border-color: rgba(20, 184, 166, 0.3); color: #2dd4bf;">{{ $srv }}</span>
                @endforeach
              </div>
            </div>
          @endif

          <!-- Custom Dynamic Form Fields (if present) -->
          @if(!empty($roleProfile->custom_fields) && is_array($roleProfile->custom_fields))
            <div style="margin-top: 1rem;">
              <div class="profile-data-box__label">Additional Submitted Information</div>
              <div class="profile-grid-2" style="margin-top: 6px;">
                @foreach($roleProfile->custom_fields as $fKey => $fVal)
                  @php
                    $fieldObj = isset($formFields) ? $formFields->firstWhere('field_key', $fKey) : null;
                    $fLabel = $fieldObj ? ($fieldObj->label ?? ucwords(str_replace('_', ' ', $fKey))) : ucwords(str_replace('_', ' ', $fKey));
                  @endphp
                  <div class="profile-data-box">
                    <div class="profile-data-box__label">{{ $fLabel }}</div>
                    <div class="profile-data-box__value">
                      @if(is_array($fVal))
                        {{ implode(', ', $fVal) }}
                      @else
                        {{ (string) $fVal }}
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          @endif

          @if($roleProfile->biography)
            <div style="margin-top: 1.25rem; padding: 1rem; background: rgba(0,0,0,0.25); border-radius: 6px; border: 1px solid var(--border-subtle);">
              <div class="profile-data-box__label">Biography &amp; Professional Statement</div>
              <div style="color: var(--text-secondary); font-size: 0.88rem; line-height: 1.6; margin-top: 0.35rem;">
                {{ $roleProfile->biography }}
              </div>
            </div>
          @endif
        @endif

        <!-- ==============================================
             PORTFOLIO & GALLERY WORK SHOWCASE
             CRITICAL: Shows EVERY uploaded look/image directly in dashboard
             ============================================== -->
        @php
          $portfolioItems = method_exists($roleProfile, 'getPortfolioItems') ? $roleProfile->getPortfolioItems() : [];
        @endphp

        <div style="margin-top: 2rem; border-top: 1px solid var(--border-subtle); padding-top: 1.5rem;">
          <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 1.25rem;">
            <div>
              <span class="card-section-label" style="margin-bottom: 0.2rem;">Submitted Media &amp; Lookbook</span>
              <h4 style="margin: 0; color: #fff; font-size: 1.15rem; font-family: var(--font-serif);">
                Portfolio Gallery ({{ count($portfolioItems) }} {{ count($portfolioItems) === 1 ? 'image' : 'images' }})
              </h4>
            </div>
            <span style="font-size: 0.75rem; color: var(--gold);">Click any image to view full resolution</span>
          </div>

          @if(count($portfolioItems) > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
              @foreach($portfolioItems as $pItem)
                <div class="portfolio-review-card">
                  <a href="{{ $pItem['url'] }}" target="_blank" rel="noopener" style="display: block; position: relative;">
                    <img
                      src="{{ $pItem['url'] }}"
                      alt="{{ $pItem['caption'] }}"
                      onerror="this.onerror=null; this.src='/images/models/model-01.jpg';"
                    />
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); padding: 0.5rem 0.65rem; color: #fff; font-size: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
                      <span style="font-weight: 600; color: var(--gold);">Look #{{ $pItem['index'] + 1 }}</span>
                      <span style="color: #fff;">↗</span>
                    </div>
                  </a>
                  @if(!empty($pItem['caption']))
                    <div style="padding: 0.5rem 0.65rem; font-size: 0.75rem; color: var(--text-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $pItem['caption'] }}">
                      {{ $pItem['caption'] }}
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          @else
            <div style="padding: 2.5rem; text-align: center; color: var(--text-muted); background: rgba(0,0,0,0.2); border-radius: 8px; border: 1px dashed var(--border-subtle);">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 0.5rem; opacity: 0.5;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
              <p style="margin: 0 0 0.25rem; font-size: 0.95rem; color: var(--text-primary); font-weight: 500;">No Portfolio Work Uploaded Yet</p>
              <p style="margin: 0; font-size: 0.82rem;">This candidate has not uploaded lookbook or portfolio photos yet.</p>
            </div>
          @endif
        </div>

      @else
        <!-- No Profile Record Found -->
        <div style="padding: 3rem; text-align: center; color: var(--text-muted); background: rgba(0,0,0,0.15); border-radius: 8px; border: 1px dashed var(--border-subtle);">
          <p style="margin: 0 0 0.5rem; font-size: 1.05rem; color: var(--text-primary); font-weight: 600;">Extended Profile Submission Pending</p>
          <p style="margin: 0; font-size: 0.85rem;">This member is registered under the {{ $user->roleEnum() ? $user->roleEnum()->label() : $user->role }} role, but has not yet submitted an extended portfolio form.</p>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
