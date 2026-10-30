@extends('admin.layouts.app')

@section('title', 'Executive Dashboard')
@section('page_title', 'Executive Overview')

@section('styles')
<style>
  .dashboard-welcome {
    background: linear-gradient(135deg, rgba(229, 184, 61, 0.1) 0%, rgba(17, 17, 17, 0.6) 100%);
    border: 1px solid var(--border-gold);
    border-radius: 12px;
    padding: 2rem 2.25rem;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    overflow: hidden;
  }

  .dashboard-welcome::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(229, 184, 61, 0.15) 0%, transparent 70%);
    pointer-events: none;
  }

  .welcome-copy h2 {
    font-family: var(--font-serif);
    font-size: 1.85rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.35rem;
  }

  .welcome-copy h2 em {
    font-style: italic;
    color: var(--gold);
  }

  .welcome-copy p {
    color: var(--text-secondary);
    font-size: 0.95rem;
  }

  .welcome-meta {
    text-align: right;
  }

  .live-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.35rem 0.85rem;
    background: rgba(34, 197, 94, 0.12);
    border: 1px solid rgba(34, 197, 94, 0.3);
    border-radius: 20px;
    color: #4ade80;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
  }

  .live-dot {
    width: 6px;
    height: 6px;
    background: #22c55e;
    border-radius: 50%;
    box-shadow: 0 0 8px #22c55e;
    animation: pulse 2s infinite;
  }

  @keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(0.85); }
  }

  .welcome-date {
    margin-top: 0.5rem;
    font-size: 0.85rem;
    color: var(--text-muted);
  }

  /* Quick Actions Bar */
  .quick-actions-bar {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-bottom: 2rem;
    padding: 0.85rem 1.25rem;
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: 8px;
  }
  .quick-actions-label {
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
    font-weight: 600;
    margin-right: 0.25rem;
  }
  .btn-quick-action {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 0.85rem;
    background: #1a1a1a;
    border: 1px solid var(--border-subtle);
    border-radius: 6px;
    color: var(--text-primary);
    font-size: 0.82rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
  }
  .btn-quick-action:hover {
    background: rgba(229, 184, 61, 0.15);
    border-color: var(--gold);
    color: var(--gold);
    transform: translateY(-1px);
  }
  .btn-quick-action svg {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    stroke-width: 2.2;
    fill: none;
  }
  .btn-quick-action--subtle {
    margin-left: auto;
    color: var(--gold);
    border-color: rgba(229, 184, 61, 0.3);
  }

  /* Grid Cards */
  .metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
  }

  .metric-card {
    background-color: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: 10px;
    padding: 1.5rem;
    transition: all 0.25s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
  }

  .metric-card:hover {
    border-color: rgba(229, 184, 61, 0.4);
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
  }

  .metric-card__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
  }

  .metric-card__title {
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--text-secondary);
  }

  .metric-card__icon-box {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: rgba(229, 184, 61, 0.08);
    border: 1px solid rgba(229, 184, 61, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gold);
  }

  .metric-card__icon-box svg {
    width: 18px;
    height: 18px;
    stroke: currentColor;
    stroke-width: 2;
    fill: none;
  }

  .metric-card__value {
    font-family: var(--font-serif);
    font-size: 2.25rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.1;
    margin-bottom: 0.5rem;
  }

  .metric-card__foot {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.75rem;
    color: var(--text-muted);
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    padding-top: 0.75rem;
    margin-top: 0.5rem;
  }

  .status-badge {
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
  }

  .status-badge--active {
    background: rgba(34, 197, 94, 0.1);
    color: #4ade80;
    border: 1px solid rgba(34, 197, 94, 0.2);
  }

  .status-badge--pending {
    background: rgba(229, 184, 61, 0.1);
    color: var(--gold);
    border: 1px solid rgba(229, 184, 61, 0.2);
  }

  /* Two Column Section */
  .dashboard-split {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
  }

  .panel-card {
    background-color: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: 10px;
    padding: 1.75rem;
  }

  .panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
    padding-bottom: 0.85rem;
    border-bottom: 1px solid var(--border-subtle);
  }

  .panel-title {
    font-family: var(--font-serif);
    font-size: 1.15rem;
    color: var(--text-primary);
    font-weight: 600;
    letter-spacing: 0.03em;
  }

  .panel-subtitle {
    font-size: 0.75rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.1em;
  }

  /* Activity Table */
  .activity-table-wrapper {
    overflow-x: auto;
  }

  .activity-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
  }

  .activity-table th {
    text-align: left;
    padding: 0.65rem 0.75rem;
    color: var(--text-muted);
    font-weight: 600;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    border-bottom: 1px solid var(--border-subtle);
  }

  .activity-table td {
    padding: 0.75rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    color: var(--text-secondary);
  }

  .activity-table tr:hover td {
    background-color: rgba(255, 255, 255, 0.02);
  }

  .tag-event {
    display: inline-block;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
  }

  .tag-event--login_success {
    background: rgba(34, 197, 94, 0.15);
    color: #4ade80;
  }

  .tag-event--login_failed {
    background: rgba(239, 68, 68, 0.15);
    color: #f87171;
  }

  .tag-event--logout {
    background: rgba(163, 163, 163, 0.15);
    color: #d4d4d4;
  }

  .tag-event--admin_created {
    background: rgba(229, 184, 61, 0.15);
    color: var(--gold);
  }

  /* Telemetry Widget */
  .telemetry-list {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
  }

  .telemetry-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.65rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    font-size: 0.85rem;
  }

  .telemetry-label {
    color: var(--text-muted);
  }

  .telemetry-val {
    color: var(--text-primary);
    font-weight: 500;
  }

  @media (max-width: 1200px) {
    .dashboard-split {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 768px) {
    .dashboard-welcome {
      flex-direction: column;
      align-items: flex-start;
      gap: 1rem;
      padding: 1.5rem;
    }

    .welcome-meta {
      text-align: left;
    }
  }
</style>
@endsection

@section('content')
  <!-- Welcome Banner -->
  <div class="dashboard-welcome">
    <div class="welcome-copy">
      <h2>Welcome back, <em>{{ (Auth::guard('admin')->user() ?? Auth::user())?->name ?? 'Admin' }}</em></h2>
      <p>IDFR Indian Designer Fashion Runway Executive Portal — Active System</p>
    </div>
    <div class="welcome-meta">
      <div class="live-pill">
        <span class="live-dot"></span>
        <span>Runway Engine Active</span>
      </div>
      <div class="welcome-date">
        {{ now()->format('l, F j, Y') }}
      </div>
    </div>
  </div>

  <!-- Quick Actions Bar -->
  <div class="quick-actions-bar">
    <span class="quick-actions-label">Quick Actions:</span>
    <a href="{{ route('admin.models.create') }}" class="btn-quick-action">
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add Model</span>
    </a>
    <a href="{{ route('admin.designers.create') }}" class="btn-quick-action">
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add Designer</span>
    </a>
    <a href="{{ route('admin.professionals.create') }}" class="btn-quick-action">
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add Creative Pro</span>
    </a>
    <a href="{{ route('admin.gallery.create') }}" class="btn-quick-action">
      <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
      <span>Add Runway Look</span>
    <a href="{{ route('admin.registrations.index') }}" class="btn-quick-action">
      <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
      <span>Registrations &amp; Profiles</span>
    </a>
    <a href="{{ route('admin.enquiries') }}" class="btn-quick-action">
      <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
      <span>Inbound Enquiries</span>
    </a>
    <a href="{{ route('admin.seasons') }}" class="btn-quick-action">
      <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
      <span>Manage Seasons</span>
    </a>
    <a href="{{ route('admin.models') }}" class="btn-quick-action btn-quick-action--subtle">
      <span>Manage Profiles ↗</span>
    </a>
  </div>

  <!-- Metric Summary Cards -->
  <div class="metrics-grid">
    @foreach($metrics as $key => $item)
      <div class="metric-card">
        <div>
          <div class="metric-card__head">
            <span class="metric-card__title">{{ $item['label'] }}</span>
            <div class="metric-card__icon-box">
              @if($item['icon'] === 'calendar')
                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              @elseif($item['icon'] === 'globe')
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
              @elseif($item['icon'] === 'image')
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
              @elseif($item['icon'] === 'users')
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
              @elseif($item['icon'] === 'star')
                <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
              @elseif($item['icon'] === 'camera')
                <svg viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
              @elseif($item['icon'] === 'mail')
                <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
              @elseif($item['icon'] === 'user-check')
                <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
              @else
                <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
              @endif
            </div>
          </div>
          <div class="metric-card__value">
            {{ is_null($item['count']) ? '0' : number_format($item['count']) }}
          </div>
        </div>
        <div class="metric-card__foot">
          <span>{{ $item['description'] }}</span>
          @if($item['exists'])
            <span class="status-badge status-badge--active">Database Synced</span>
          @else
            <span class="status-badge status-badge--pending">Phase 2 Ready</span>
          @endif
        </div>
      </div>
    @endforeach
  </div>

  <!-- Recent Member Registrations & Profiles -->
  @if(isset($recentRegistrations) && $recentRegistrations->isNotEmpty())
    <div class="panel-card" style="margin-bottom: 2rem;">
      <div class="panel-header">
        <div>
          <h3 class="panel-title">Recent Member Registrations &amp; Profiles</h3>
          <span class="panel-subtitle">Newly registered talent and creative professionals</span>
        </div>
        <a href="{{ route('admin.registrations.index') }}" class="btn-quick-action" style="padding: 0.4rem 0.85rem; font-size: 0.75rem;">
          <span>View All Members ({{ $metrics['total_registrations']['count'] ?? 0 }}) ↗</span>
        </a>
      </div>

      <div class="activity-table-wrapper">
        <table class="activity-table">
          <thead>
            <tr>
              <th>Member</th>
              <th>Registered Role</th>
              <th>Email Verification</th>
              <th>Profile Completion</th>
              <th>Status</th>
              <th>Registered</th>
              <th style="text-align: right;">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recentRegistrations as $regUser)
              @php
                $regComp = $regUser->profileCompletion();
                $regInitial = strtoupper(substr($regUser->name ?: 'U', 0, 1));
              @endphp
              <tr>
                <td>
                  <div style="display: flex; align-items: center; gap: 0.65rem;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(229,184,61,0.15); border: 1px solid var(--border-gold); display: flex; align-items: center; justify-content: center; color: var(--gold); font-weight: 600; font-size: 0.8rem;">
                      {{ $regInitial }}
                    </div>
                    <div>
                      <div style="font-weight: 500; color: var(--text-primary);">{{ $regUser->name }}</div>
                      <div style="font-size: 0.72rem; color: var(--text-muted);">{{ $regUser->email }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  <span style="font-size: 0.75rem; text-transform: capitalize; color: var(--gold); font-weight: 600;">
                    {{ $regUser->roleEnum() ? $regUser->roleEnum()->label() : $regUser->role }}
                  </span>
                </td>
                <td>
                  @if($regUser->isEmailVerified())
                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.72rem; color: #4ade80;">
                      <span style="width: 5px; height: 5px; border-radius: 50%; background: #4ade80;"></span>
                      Verified
                    </span>
                  @else
                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.72rem; color: var(--gold);">
                      <span style="width: 5px; height: 5px; border-radius: 50%; background: var(--gold);"></span>
                      Pending OTP
                    </span>
                  @endif
                </td>
                <td>
                  <div style="display: flex; align-items: center; gap: 0.5rem; min-width: 100px;">
                    <div style="flex: 1; height: 5px; background: rgba(255,255,255,0.08); border-radius: 3px; overflow: hidden;">
                      <div style="height: 100%; width: {{ $regComp['percentage'] }}%; background: {{ $regComp['is_complete'] ? '#4ade80' : 'var(--gold)' }};"></div>
                    </div>
                    <span style="font-size: 0.72rem; font-weight: 600; color: {{ $regComp['is_complete'] ? '#4ade80' : 'var(--gold)' }};">
                      {{ $regComp['percentage'] }}%
                    </span>
                  </div>
                </td>
                <td>
                  @if($regUser->isActive())
                    <span style="font-size: 0.72rem; color: #4ade80;">Active</span>
                  @else
                    <span style="font-size: 0.72rem; color: #f87171;">Deactivated</span>
                  @endif
                </td>
                <td style="font-size: 0.75rem; color: var(--text-muted); white-space: nowrap;">
                  {{ $regUser->created_at->diffForHumans() }}
                </td>
                <td style="text-align: right;">
                  <a href="{{ route('admin.registrations.show', $regUser) }}" class="btn-quick-action" style="padding: 0.3rem 0.65rem; font-size: 0.75rem; display: inline-flex;">
                    Review
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif

  <!-- Split Activity & Diagnostics -->
  <div class="dashboard-split">
    <!-- Activity Log -->
    <div class="panel-card">
      <div class="panel-header">
        <div>
          <h3 class="panel-title">Audit & Security Log</h3>
          <span class="panel-subtitle">Real-time administrator session telemetry</span>
        </div>
      </div>

      <div class="activity-table-wrapper">
        <table class="activity-table">
          <thead>
            <tr>
              <th>Event</th>
              <th>Administrator</th>
              <th>IP Address</th>
              <th>Details</th>
              <th>Time</th>
            </tr>
          </thead>
          <tbody>
            @forelse($activities as $act)
              <tr>
                <td>
                  <span class="tag-event tag-event--{{ $act->activity }}">
                    {{ str_replace('_', ' ', strtoupper($act->activity)) }}
                  </span>
                </td>
                <td>
                  <span style="color: var(--text-primary); font-weight: 500;">
                    {{ $act->user ? $act->user->name : ($act->admin_email ?: 'System') }}
                  </span>
                </td>
                <td style="font-family: monospace; font-size: 0.75rem;">
                  {{ $act->ip_address ?: '127.0.0.1' }}
                </td>
                <td style="font-size: 0.8rem;">
                  {{ $act->description ?: 'Security log entry' }}
                </td>
                <td style="font-size: 0.75rem; color: var(--text-muted); white-space: nowrap;">
                  {{ $act->created_at->diffForHumans() }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                  No security activities recorded yet.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- System Telemetry -->
    <div class="panel-card">
      <div class="panel-header">
        <div>
          <h3 class="panel-title">System Architecture</h3>
          <span class="panel-subtitle">Core Infrastructure Diagnostics</span>
        </div>
      </div>

      <div class="telemetry-list">
        <div class="telemetry-item">
          <span class="telemetry-label">Framework Engine</span>
          <span class="telemetry-val" style="color: var(--gold);">Laravel {{ $systemInfo['laravel_version'] }}</span>
        </div>
        <div class="telemetry-item">
          <span class="telemetry-label">PHP Runtime</span>
          <span class="telemetry-val">{{ $systemInfo['php_version'] }}</span>
        </div>
        <div class="telemetry-item">
          <span class="telemetry-label">Database Driver</span>
          <span class="telemetry-val" style="text-transform: uppercase;">{{ $systemInfo['database_driver'] }}</span>
        </div>
        <div class="telemetry-item">
          <span class="telemetry-label">Database Schema</span>
          <span class="telemetry-val">{{ $systemInfo['database_name'] }}</span>
        </div>
        <div class="telemetry-item">
          <span class="telemetry-label">Environment</span>
          <span class="telemetry-val" style="text-transform: capitalize;">{{ $systemInfo['server_environment'] }}</span>
        </div>
        <div class="telemetry-item">
          <span class="telemetry-label">Rate Limiter</span>
          <span class="telemetry-val" style="color: #4ade80;">Active (5 req/min)</span>
        </div>
        <div class="telemetry-item">
          <span class="telemetry-label">CSRF Protection</span>
          <span class="telemetry-val" style="color: #4ade80;">Enforced</span>
        </div>
        <div class="telemetry-item">
          <span class="telemetry-label">Password Hashing</span>
          <span class="telemetry-val" style="color: #4ade80;">Bcrypt (Rounds 12)</span>
        </div>
      </div>
    </div>
  </div>
@endsection
