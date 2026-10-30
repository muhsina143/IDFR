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

  .user-header-card {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.5rem;
    background: rgba(229, 184, 61, 0.04);
    border: 1px solid var(--border-gold);
    border-radius: 10px;
    margin-bottom: 1.5rem;
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
    padding: 1.5rem;
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

  .inquiry-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
  }
  .inquiry-table th {
    text-align: left;
    padding: 0.65rem 0.85rem;
    color: var(--gold);
    font-size: 0.72rem;
    text-transform: uppercase;
    border-bottom: 1px solid var(--border-subtle);
  }
  .inquiry-table td {
    padding: 0.75rem 0.85rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    color: var(--text-secondary);
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
          Active
        </span>
      @else
        <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.55rem; border-radius: 4px; background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); font-size: 0.72rem; font-weight: 600;">
          <span style="width: 6px; height: 6px; border-radius: 50%; background: #f87171; display: inline-block;"></span>
          Deactivated
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
        <div class="field-row">
          <span class="field-label">Deactivation Reason</span>
          <span class="field-value" style="color: #f87171; max-width: 200px; text-align: right;">{{ $user->deactivation_reason ?: 'None specified' }}</span>
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
        Sensitive authentication credentials (passwords, OTP hashes, private session tokens, and SMTP secrets) are strictly isolated and never rendered in administrative views.
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
        @php
          $steps = $completion['steps'] ?? [];
        @endphp
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
            <span>Contact Information / Phone (20%)</span>
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
            <span>Extended Profile / Portfolio (15%)</span>
          </div>
          <span style="font-size: 0.75rem; color: {{ !empty($steps['extended_profile']) ? '#4ade80' : '#888' }}; font-weight: 600;">
            {{ !empty($steps['extended_profile']) ? 'Submitted' : 'Pending' }}
          </span>
        </li>
      </ul>
    </div>

    <!-- Administrative Audit Activities -->
    <div class="admin-card">
      <span class="card-section-label">Audit Log &amp; Administrative Telemetry</span>
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
              @if($activity->ip_address)
                <div style="color: var(--text-muted); font-size: 0.7rem; margin-top: 2px;">
                  IP: {{ $activity->ip_address }}
                </div>
              @endif
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

  <!-- RIGHT COLUMN: Role-Specific Profile Details -->
  <div>
    <!-- Role-Specific Profile Details Card -->
    <div class="role-data-card">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.75rem;">
        <div>
          <span class="card-section-label" style="margin-bottom: 0.2rem;">Role Profile Data</span>
          <h3 style="margin: 0; font-size: 1.15rem; color: var(--text-primary); font-family: var(--font-serif);">
            @if($user->role === 'model')
              Model Portfolio &amp; Statistics
            @elseif($user->role === 'fashion-designer')
              Fashion Designer Atelier Profile
            @else
              Creative Professional Profile ({{ $user->roleEnum() ? $user->roleEnum()->label() : $user->role }})
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

      <!-- Model Role Details -->
      @if($user->role === 'model')
        @if($roleProfile instanceof \App\Models\ModelProfile)
          <div style="display: flex; gap: 1.25rem; align-items: center; margin-bottom: 1.25rem;">
            @if($roleProfile->profile_photo)
              <img src="{{ $roleProfile->photo_url }}" alt="{{ $roleProfile->name }}" style="width: 80px; height: 95px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-gold);" />
            @else
              <div style="width: 80px; height: 95px; border-radius: 8px; background: rgba(229,184,61,0.1); border: 1px solid var(--border-gold); display: flex; align-items: center; justify-content: center; color: var(--gold); font-size: 1.5rem;">
                {{ substr($roleProfile->name, 0, 1) }}
              </div>
            @endif
            <div>
              <h4 style="margin: 0; color: #fff; font-size: 1.15rem; font-family: var(--font-serif);">{{ $roleProfile->name }}</h4>
              <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 0.85rem;">
                {{ $roleProfile->gender }} &middot; Level: <strong style="color: var(--gold);">{{ $roleProfile->level }}</strong>
              </p>
              <div style="margin-top: 6px;">
                <span style="font-size: 0.72rem; padding: 2px 8px; border-radius: 4px; background: rgba(34, 197, 94, 0.1); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3);">
                  Model ID #{{ $roleProfile->id }} &middot; {{ ucfirst($roleProfile->status ?? 'active') }}
                </span>
              </div>
            </div>
          </div>

          <div class="profile-grid-2">
            <div class="profile-data-box">
              <div class="profile-data-box__label">Height</div>
              <div class="profile-data-box__value">{{ $roleProfile->height ? $roleProfile->height . ' cm' : 'N/A' }}</div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Location</div>
              <div class="profile-data-box__value">{{ $roleProfile->location ?: 'Kerala' }}</div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Measurements (B-W-H)</div>
              <div class="profile-data-box__value">
                @if(is_array($roleProfile->measurements))
                  {{ $roleProfile->measurements['bust'] ?? '—' }} &middot; {{ $roleProfile->measurements['waist'] ?? '—' }} &middot; {{ $roleProfile->measurements['hips'] ?? '—' }}
                @else
                  Not specified
                @endif
              </div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Experience</div>
              <div class="profile-data-box__value">{{ $roleProfile->experience ?: 'Not specified' }}</div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Model Categories</div>
              <div class="profile-data-box__value">
                @if(is_array($roleProfile->types) && count($roleProfile->types) > 0)
                  {{ implode(', ', $roleProfile->types) }}
                @else
                  Runway, High Fashion
                @endif
              </div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Availability</div>
              <div class="profile-data-box__value">
                {{ $roleProfile->availability ?: 'Available for bookings' }}
                @if($roleProfile->outstation) &middot; Outstation @endif
                @if($roleProfile->international) &middot; International @endif
              </div>
            </div>
          </div>

          @if($roleProfile->description)
            <div style="margin-top: 1rem; padding: 1rem; background: rgba(0,0,0,0.25); border-radius: 6px; border: 1px solid var(--border-subtle);">
              <div class="profile-data-box__label">Model Biography / Description</div>
              <div style="color: var(--text-secondary); font-size: 0.88rem; line-height: 1.6; margin-top: 0.35rem;">
                {{ $roleProfile->description }}
              </div>
            </div>
          @endif
        @else
          <div style="padding: 2rem; text-align: center; color: var(--text-muted); background: rgba(0,0,0,0.15); border-radius: 8px; border: 1px dashed var(--border-subtle);">
            <p style="margin: 0 0 0.5rem; font-size: 0.95rem; color: var(--text-primary);">Extended Model Profile Pending Submission</p>
            <p style="margin: 0; font-size: 0.82rem;">This member is registered under the Model role, but has not yet submitted an extended portfolio form.</p>
          </div>
        @endif

      <!-- C) Fashion Designer Role Details -->
      @elseif($user->role === 'fashion-designer')
        @if($roleProfile instanceof \App\Models\Designer)
          <div style="display: flex; gap: 1.25rem; align-items: center; margin-bottom: 1.25rem;">
            @if($roleProfile->profile_photo)
              <img src="{{ $roleProfile->photo_url }}" alt="{{ $roleProfile->name }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-gold);" onerror="this.src='/images/idfr-logo-gold.svg'" />
            @else
              <div style="width: 80px; height: 80px; border-radius: 8px; background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.3); display: flex; align-items: center; justify-content: center; color: #60a5fa; font-size: 1.5rem;">
                {{ substr($roleProfile->name, 0, 1) }}
              </div>
            @endif
            <div>
              <h4 style="margin: 0; color: #fff; font-size: 1.15rem; font-family: var(--font-serif);">{{ $roleProfile->name }}</h4>
              <p style="margin: 4px 0 0; color: var(--gold); font-size: 0.85rem; font-weight: 500;">
                Brand: {{ $roleProfile->brand_name ?: 'Independent Atelier' }}
              </p>
              <div style="margin-top: 6px;">
                <span style="font-size: 0.72rem; padding: 2px 8px; border-radius: 4px; background: rgba(59, 130, 246, 0.1); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3);">
                  Designer ID #{{ $roleProfile->id }} &middot; {{ ucfirst($roleProfile->status ?? 'active') }}
                </span>
              </div>
            </div>
          </div>

          <div class="profile-grid-2">
            <div class="profile-data-box">
              <div class="profile-data-box__label">Atelier Location</div>
              <div class="profile-data-box__value">{{ $roleProfile->location ?: 'Kerala, India' }}</div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Experience</div>
              <div class="profile-data-box__value">{{ $roleProfile->experience ?: 'Not specified' }}</div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Collection Title</div>
              <div class="profile-data-box__value">{{ $roleProfile->collection_title ?: 'Signature Runway Collection' }}</div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Website / Portfolio</div>
              <div class="profile-data-box__value">
                @if($roleProfile->website_url)
                  <a href="{{ $roleProfile->website_url }}" target="_blank" rel="noopener" style="color: var(--gold);">{{ $roleProfile->website_url }}</a>
                @else
                  Not provided
                @endif
              </div>
            </div>
          </div>

          @if($roleProfile->biography)
            <div style="margin-top: 1rem; padding: 1rem; background: rgba(0,0,0,0.25); border-radius: 6px; border: 1px solid var(--border-subtle);">
              <div class="profile-data-box__label">Designer Biography &amp; Vision</div>
              <div style="color: var(--text-secondary); font-size: 0.88rem; line-height: 1.6; margin-top: 0.35rem;">
                {{ $roleProfile->biography }}
              </div>
            </div>
          @endif
        @else
          <div style="padding: 2rem; text-align: center; color: var(--text-muted); background: rgba(0,0,0,0.15); border-radius: 8px; border: 1px dashed var(--border-subtle);">
            <p style="margin: 0 0 0.5rem; font-size: 0.95rem; color: var(--text-primary);">Extended Designer Profile Pending Submission</p>
            <p style="margin: 0; font-size: 0.82rem;">This member is registered under the Fashion Designer role, but has not yet submitted an extended atelier form.</p>
          </div>
        @endif

      <!-- D) Creative Professional Details (MUA, Hairstylist, Photographer, Videographer, etc.) -->
      @else
        @if($roleProfile instanceof \App\Models\CreativeProfessional)
          <div style="display: flex; gap: 1.25rem; align-items: center; margin-bottom: 1.25rem;">
            @if($roleProfile->profile_photo)
              <img src="{{ $roleProfile->photo_url }}" alt="{{ $roleProfile->name }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-gold);" onerror="this.src='/images/idfr-logo-gold.svg'" />
            @else
              <div style="width: 80px; height: 80px; border-radius: 8px; background: rgba(20, 184, 166, 0.1); border: 1px solid rgba(20, 184, 166, 0.3); display: flex; align-items: center; justify-content: center; color: #2dd4bf; font-size: 1.5rem;">
                {{ substr($roleProfile->name, 0, 1) }}
              </div>
            @endif
            <div>
              <h4 style="margin: 0; color: #fff; font-size: 1.15rem; font-family: var(--font-serif);">{{ $roleProfile->name }}</h4>
              <p style="margin: 4px 0 0; color: #2dd4bf; font-size: 0.85rem; font-weight: 500;">
                {{ $roleProfile->professional_title ?: ($roleProfile->category ?: ucfirst($user->role)) }}
                @if($roleProfile->brand) &middot; {{ $roleProfile->brand }} @endif
              </p>
              <div style="margin-top: 6px;">
                <span style="font-size: 0.72rem; padding: 2px 8px; border-radius: 4px; background: rgba(20, 184, 166, 0.1); color: #2dd4bf; border: 1px solid rgba(20, 184, 166, 0.3);">
                  Creative ID #{{ $roleProfile->id }} &middot; {{ ucfirst($roleProfile->status ?? 'active') }}
                </span>
              </div>
            </div>
          </div>

          <div class="profile-grid-2">
            <div class="profile-data-box">
              <div class="profile-data-box__label">Operating Location</div>
              <div class="profile-data-box__value">{{ $roleProfile->location ?: 'Kerala, India' }}</div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Industry Experience</div>
              <div class="profile-data-box__value">{{ $roleProfile->experience ?: 'Not specified' }}</div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Equipment &amp; Kit</div>
              <div class="profile-data-box__value">{{ $roleProfile->equipment ?: 'Professional Grade Studio Gear' }}</div>
            </div>
            <div class="profile-data-box">
              <div class="profile-data-box__label">Website / Portfolio</div>
              <div class="profile-data-box__value">
                @if($roleProfile->website_url)
                  <a href="{{ $roleProfile->website_url }}" target="_blank" rel="noopener" style="color: var(--gold);">{{ $roleProfile->website_url }}</a>
                @elseif($roleProfile->instagram_url)
                  <a href="{{ $roleProfile->instagram_url }}" target="_blank" rel="noopener" style="color: var(--gold);">Instagram Profile</a>
                @else
                  Not provided
                @endif
              </div>
            </div>
          </div>

          @if($roleProfile->biography)
            <div style="margin-top: 1rem; padding: 1rem; background: rgba(0,0,0,0.25); border-radius: 6px; border: 1px solid var(--border-subtle);">
              <div class="profile-data-box__label">Creative Bio &amp; Specialization</div>
              <div style="color: var(--text-secondary); font-size: 0.88rem; line-height: 1.6; margin-top: 0.35rem;">
                {{ $roleProfile->biography }}
              </div>
            </div>
          @endif
        @else
          <div style="padding: 2rem; text-align: center; color: var(--text-muted); background: rgba(0,0,0,0.15); border-radius: 8px; border: 1px dashed var(--border-subtle);">
            <p style="margin: 0 0 0.5rem; font-size: 0.95rem; color: var(--text-primary);">Extended Creative Profile Pending Submission</p>
            <p style="margin: 0; font-size: 0.82rem;">This member is registered under the {{ $user->roleEnum() ? $user->roleEnum()->label() : $user->role }} role, but has not yet submitted an extended portfolio form.</p>
          </div>
        @endif
      @endif
    </div>
  </div>
</div>
@endsection
