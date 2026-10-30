@extends('admin.layouts.app')

@section('title', 'Registered Members & Talent Profiles | IDFR Executive Portal')
@section('page_title', 'Registrations & Profiles')

@section('styles')
<style>
  .user-avatar-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(229, 184, 61, 0.15);
    border: 1px solid var(--border-gold);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: var(--gold);
    font-size: 0.9rem;
    flex-shrink: 0;
  }

  .role-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: capitalize;
    letter-spacing: 0.04em;
    white-space: nowrap;
  }

  .role-pill--model {
    background: rgba(229, 184, 61, 0.15);
    border: 1px solid rgba(229, 184, 61, 0.35);
    color: var(--gold);
  }

  .role-pill--designer {
    background: rgba(59, 130, 246, 0.15);
    border: 1px solid rgba(59, 130, 246, 0.35);
    color: #60a5fa;
  }

  .role-pill--creative {
    background: rgba(20, 184, 166, 0.15);
    border: 1px solid rgba(20, 184, 166, 0.35);
    color: #2dd4bf;
  }

  .completion-progress-wrap {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 120px;
  }

  .completion-bar-mini {
    flex: 1;
    height: 6px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
    overflow: hidden;
  }

  .completion-fill-mini {
    height: 100%;
    border-radius: 3px;
    transition: width 0.3s ease;
  }

  .completion-fill-mini.complete {
    background: #4ade80;
  }

  .completion-fill-mini.incomplete {
    background: var(--gold);
  }

  .status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
  }

  .status-dot--active {
    background: #4ade80;
    box-shadow: 0 0 6px #4ade80;
  }

  .status-dot--deactivated {
    background: #f87171;
    box-shadow: 0 0 6px #f87171;
  }
</style>
@endsection

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Registered Members &amp; <em>Profiles</em></h2>
    <p class="module-subtitle">Audit authenticated member accounts, role specializations, email verification status, and profile completion across IDFR.</p>
  </div>
</div>

<!-- Stats Bar -->
<div class="stats-overview-row">
  <div class="stat-pill">
    <span class="stat-pill__label">Total Members</span>
    <span class="stat-pill__val">{{ number_format($stats['total_users']) }}</span>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Verified Email</span>
    <span class="stat-pill__val stat-pill__val--green">{{ number_format($stats['verified_users']) }}</span>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Runway Models</span>
    <span class="stat-pill__val stat-pill__val--gold">{{ number_format($stats['models']) }}</span>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Fashion Designers</span>
    <span class="stat-pill__val" style="color: #60a5fa;">{{ number_format($stats['designers']) }}</span>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Creative Specialists</span>
    <span class="stat-pill__val" style="color: #c084fc;">{{ number_format($stats['creative_pros']) }}</span>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Deactivated</span>
    <span class="stat-pill__val" style="color: #f87171;">{{ number_format($stats['deactivated']) }}</span>
  </div>
</div>

<!-- Category Tabs Bar -->
<div class="category-tabs-bar">
  <a href="{{ route('admin.registrations.index', array_merge(request()->except('role', 'status', 'verified', 'page'), ['role' => 'all'])) }}"
     class="category-tab {{ !request('role') || request('role') === 'all' && !request('status') && !request('verified') ? 'is-active' : '' }}">
    All Members ({{ $stats['total_users'] }})
  </a>
  <a href="{{ route('admin.registrations.index', array_merge(request()->except('page'), ['role' => 'model'])) }}"
     class="category-tab {{ request('role') === 'model' ? 'is-active' : '' }}">
    Models ({{ $stats['models'] }})
  </a>
  <a href="{{ route('admin.registrations.index', array_merge(request()->except('page'), ['role' => 'fashion-designer'])) }}"
     class="category-tab {{ request('role') === 'fashion-designer' ? 'is-active' : '' }}">
    Designers ({{ $stats['designers'] }})
  </a>
  <a href="{{ route('admin.registrations.index', array_merge(request()->except('page'), ['verified' => '0'])) }}"
     class="category-tab {{ request('verified') === '0' ? 'is-active' : '' }}">
    Pending Verification
  </a>
  <a href="{{ route('admin.registrations.index', array_merge(request()->except('page'), ['status' => 'deactivated'])) }}"
     class="category-tab {{ request('status') === 'deactivated' ? 'is-active' : '' }}">
    Deactivated ({{ $stats['deactivated'] }})
  </a>
</div>

<!-- Search & Filter Card -->
<div class="admin-card filter-card">
  <form method="GET" action="{{ route('admin.registrations.index') }}" class="filter-form">
    <div class="filter-group filter-group--search" style="flex: 1; min-width: 240px;">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, phone..." class="filter-input" />
    </div>

    <div class="filter-group" style="min-width: 170px;">
      <select name="role" class="filter-input" onchange="this.form.submit()">
        <option value="">All Roles</option>
        @foreach($roles as $r)
          <option value="{{ $r->value }}" {{ request('role') === $r->value ? 'selected' : '' }}>{{ $r->label() }}</option>
        @endforeach
      </select>
    </div>

    <div class="filter-group" style="min-width: 160px;">
      <select name="verified" class="filter-input" onchange="this.form.submit()">
        <option value="">Email Verification</option>
        <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Verified Only</option>
        <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>Unverified Only</option>
      </select>
    </div>

    <div class="filter-group" style="min-width: 150px;">
      <select name="status" class="filter-input" onchange="this.form.submit()">
        <option value="">Account Status</option>
        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
        <option value="deactivated" {{ request('status') === 'deactivated' ? 'selected' : '' }}>Deactivated Only</option>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Search</button>
      @if(request()->hasAny(['search', 'role', 'verified', 'status']))
        <a href="{{ route('admin.registrations.index') }}" class="btn-reset">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Table Card -->
<div class="admin-card table-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>User</th>
          <th>Registered Role</th>
          <th>Email Verification</th>
          <th>Phone</th>
          <th>Profile Completion</th>
          <th>Status</th>
          <th>Registered</th>
          <th style="text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
          @php
            $completion = $user->profileCompletion();
            $roleEnum = $user->roleEnum();
            $initial = strtoupper(substr($user->name ?: 'U', 0, 1));
          @endphp
          <tr>
            <td>
              <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div class="user-avatar-circle">
                  {{ $initial }}
                </div>
                <div>
                  <a href="{{ route('admin.registrations.show', $user) }}" style="font-weight: 600; color: var(--text-primary); text-decoration: none;">
                    {{ $user->name }}
                  </a>
                  <div style="font-size: 0.75rem; color: var(--text-muted);">
                    {{ $user->email }}
                  </div>
                </div>
              </div>
            </td>
            <td>
              @if($user->role === 'model')
                <span class="role-pill role-pill--model">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                  Model
                </span>
              @elseif($user->role === 'fashion-designer')
                <span class="role-pill role-pill--designer">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                  Fashion Designer
                </span>
              @else
                <span class="role-pill role-pill--creative">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                  {{ $roleEnum ? $roleEnum->label() : ucfirst($user->role) }}
                </span>
              @endif
            </td>
            <td>
              @if($user->isEmailVerified())
                <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.55rem; border-radius: 4px; background: rgba(34, 197, 94, 0.12); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); font-size: 0.72rem; font-weight: 500;">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                  Verified
                </span>
              @else
                <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.55rem; border-radius: 4px; background: rgba(229, 184, 61, 0.12); color: var(--gold); border: 1px solid rgba(229, 184, 61, 0.3); font-size: 0.72rem; font-weight: 500;">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                  Pending OTP
                </span>
              @endif
            </td>
            <td style="font-size: 0.85rem; color: var(--text-secondary);">
              {{ $user->phone ?: '—' }}
            </td>
            <td>
              <div class="completion-progress-wrap">
                <div class="completion-bar-mini">
                  <div class="completion-fill-mini {{ $completion['is_complete'] ? 'complete' : 'incomplete' }}" style="width: {{ $completion['percentage'] }}%;"></div>
                </div>
                <span style="font-size: 0.72rem; font-weight: 600; color: {{ $completion['is_complete'] ? '#4ade80' : 'var(--gold)' }};">
                  {{ $completion['percentage'] }}%
                </span>
              </div>
            </td>
            <td>
              @if($user->isActive())
                <span style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; color: #4ade80; font-weight: 500;">
                  <span class="status-dot status-dot--active"></span>
                  Active
                </span>
              @else
                <span style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; color: #f87171; font-weight: 500;" title="{{ $user->deactivation_reason }}">
                  <span class="status-dot status-dot--deactivated"></span>
                  Deactivated
                </span>
              @endif
            </td>
            <td style="font-size: 0.75rem; color: var(--text-muted); white-space: nowrap;">
              {{ $user->created_at->format('M d, Y') }}
            </td>
            <td style="text-align: right;">
              <div style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <a href="{{ route('admin.registrations.show', $user) }}" class="btn-quick-action" style="padding: 0.3rem 0.65rem; font-size: 0.75rem;" title="Review details & profile data">
                  <span>Review</span>
                </a>
                <a href="{{ route('admin.registrations.edit', $user) }}" class="btn-quick-action btn-quick-action--subtle" style="padding: 0.3rem 0.55rem; font-size: 0.75rem;" title="Edit Account">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
                </a>
                <form action="{{ route('admin.registrations.toggle-status', $user) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to {{ $user->isActive() ? 'deactivate' : 'activate' }} this user account?')">
                  @csrf
                  <button type="submit" class="btn-quick-action" style="padding: 0.3rem 0.55rem; font-size: 0.75rem; color: {{ $user->isActive() ? '#f87171' : '#4ade80' }}; border-color: {{ $user->isActive() ? 'rgba(239,68,68,0.3)' : 'rgba(34,197,94,0.3)' }};" title="{{ $user->isActive() ? 'Deactivate Account' : 'Activate Account' }}">
                    @if($user->isActive())
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                    @else
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    @endif
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" style="text-align: center; padding: 3rem 1.5rem; color: var(--text-muted);">
              <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 0.75rem; opacity: 0.5;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
              <div style="font-size: 1rem; color: var(--text-primary); margin-bottom: 0.25rem;">No registered members found</div>
              <div style="font-size: 0.85rem;">Try adjusting your search criteria, role filters, or verification filters.</div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($users->hasPages())
    <div style="padding: 1.25rem; border-top: 1px solid var(--border-subtle);">
      {{ $users->links() }}
    </div>
  @endif
</div>
@endsection
