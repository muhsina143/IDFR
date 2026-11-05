@extends('admin.layouts.app')

@section('title', 'Model Registrations | IDFR Admin')
@section('header_title', 'Season Model Registrations')

@section('styles')
<style>
  .reg-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.65rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .reg-badge--paid { background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); }
  .reg-badge--unpaid { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
  .reg-badge--pending { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
  .reg-badge--approved { background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); }
  .reg-badge--rejected { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
  .reg-badge--cancelled { background: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.3); }
  .stat-grid-models {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }
  .stat-card-mini {
    background: var(--bg-card, #141416);
    border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
    border-radius: 8px;
    padding: 1.15rem;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    transition: transform 0.2s ease, border-color 0.2s ease;
  }
  .stat-card-mini:hover {
    border-color: var(--border-gold, rgba(212, 175, 55, 0.4));
    transform: translateY(-2px);
  }
  .stat-card-mini__label {
    font-size: 0.75rem;
    color: var(--text-muted, #94a3b8);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    font-weight: 600;
  }
  .stat-card-mini__value {
    font-size: 1.6rem;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.1;
  }
  .stat-card-mini__note {
    font-size: 0.72rem;
    color: var(--text-muted, #64748b);
  }
</style>
@endsection

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Model <em>Registrations</em></h2>
    <p class="module-subtitle">Manage participant applications, review age &amp; guardian profiles, track fee collection, and record manual payments across runway seasons.</p>
  </div>
  <div class="module-header__actions" style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
    <a href="{{ route('admin.season-registrations.export', request()->query()) }}" class="btn-subtle" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; border-radius: 6px; border: 1px solid var(--border-subtle); color: var(--text-primary); text-decoration: none;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
      <span>Export to CSV</span>
    </a>
    <a href="{{ route('admin.season-prices.index') }}" class="btn-subtle" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; border-radius: 6px; border: 1px solid var(--border-subtle); color: var(--text-primary); text-decoration: none;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v12M17 10H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
      <span>Season Fees</span>
    </a>
    <a href="{{ route('admin.season-payments.index') }}" class="btn-gold" style="display: inline-flex; align-items: center; gap: 0.5rem;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
      <span>All Payments</span>
    </a>
  </div>
</div>

@if(session('success'))
  <div class="alert alert--success" style="padding: 1rem 1.25rem; background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; border-radius: 6px; color: #4ade80; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
    <span>{{ session('success') }}</span>
  </div>
@endif

<!-- Metric Statistics Cards -->
<div class="stat-grid-models">
  <div class="stat-card-mini">
    <span class="stat-card-mini__label">Total Registrations</span>
    <span class="stat-card-mini__value">{{ number_format($stats['total']) }}</span>
    <span class="stat-card-mini__note">{{ request('season_id') ? 'Filtered Season' : 'Across All Seasons' }}</span>
  </div>
  <div class="stat-card-mini">
    <span class="stat-card-mini__label">Approved Models</span>
    <span class="stat-card-mini__value" style="color: #4ade80;">{{ number_format($stats['approved']) }}</span>
    <span class="stat-card-mini__note">Casting Confirmed</span>
  </div>
  <div class="stat-card-mini">
    <span class="stat-card-mini__label">Pending Review</span>
    <span class="stat-card-mini__value" style="color: #fbbf24;">{{ number_format($stats['pending']) }}</span>
    <span class="stat-card-mini__note">Awaiting Casting Decision</span>
  </div>
  <div class="stat-card-mini">
    <span class="stat-card-mini__label">Fully Paid</span>
    <span class="stat-card-mini__value" style="color: #4ade80;">{{ number_format($stats['paid']) }}</span>
    <span class="stat-card-mini__note">Complete Settlements</span>
  </div>
  <div class="stat-card-mini">
    <span class="stat-card-mini__label">Unpaid / Balance</span>
    <span class="stat-card-mini__value" style="color: #f87171;">{{ number_format($stats['unpaid']) }}</span>
    <span class="stat-card-mini__note">Awaiting Payment</span>
  </div>
  <div class="stat-card-mini">
    <span class="stat-card-mini__label">Total Collected</span>
    <span class="stat-card-mini__value" style="color: var(--gold, #d4af37);">₹{{ number_format($stats['collected'], 0) }}</span>
    <span class="stat-card-mini__note">Recorded Revenue</span>
  </div>
  <div class="stat-card-mini">
    <span class="stat-card-mini__label">Outstanding Balance</span>
    <span class="stat-card-mini__value" style="color: #cbd5e1;">₹{{ number_format($stats['outstanding'], 0) }}</span>
    <span class="stat-card-mini__note">Pending Collection</span>
  </div>
</div>

<!-- Search & Multi-Criteria Filter Bar -->
<div class="admin-card filter-card" style="margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('admin.season-registrations.index') }}" class="filter-form" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
    
    <div class="filter-group filter-group--search" style="flex: 1; min-width: 240px;">
      <svg class="search-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, WhatsApp, email, Reg ID..." class="filter-input" />
    </div>

    <div class="filter-group">
      <select name="season_id" class="filter-select" onchange="this.form.submit()">
        <option value="">All Seasons</option>
        @foreach($seasons as $s)
          <option value="{{ $s->id }}" {{ request('season_id') == $s->id ? 'selected' : '' }}>{{ $s->title }}</option>
        @endforeach
      </select>
    </div>

    <div class="filter-group">
      <select name="category" class="filter-select" onchange="this.form.submit()">
        <option value="">All Categories</option>
        <option value="adult" {{ request('category') === 'adult' ? 'selected' : '' }}>Adult</option>
        <option value="kids" {{ request('category') === 'kids' ? 'selected' : '' }}>Kids / Minor</option>
      </select>
    </div>

    <div class="filter-group">
      <select name="gender" class="filter-select" onchange="this.form.submit()">
        <option value="">All Genders</option>
        <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
        <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
        <option value="other" {{ request('gender') === 'other' ? 'selected' : '' }}>Other</option>
      </select>
    </div>

    <div class="filter-group">
      <select name="payment_status" class="filter-select" onchange="this.form.submit()">
        <option value="">All Payments</option>
        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
        <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
        <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
      </select>
    </div>

    <div class="filter-group">
      <select name="registration_status" class="filter-select" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="pending" {{ request('registration_status') === 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="approved" {{ request('registration_status') === 'approved' ? 'selected' : '' }}>Approved</option>
        <option value="rejected" {{ request('registration_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        <option value="cancelled" {{ request('registration_status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
      </select>
    </div>

    <div class="filter-actions" style="display: flex; gap: 0.5rem;">
      <button type="submit" class="btn-subtle" style="padding: 0.5rem 1rem;">Filter</button>
      @if(request()->anyFilled(['search', 'season_id', 'category', 'gender', 'payment_status', 'registration_status']))
        <a href="{{ route('admin.season-registrations.index') }}" class="btn-reset" style="padding: 0.5rem 0.85rem;">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Registrations Table -->
<div class="admin-card">
  @if($registrations->isEmpty())
    <div class="empty-state" style="padding: 3.5rem 1.5rem; text-align: center;">
      <div class="empty-state__icon" style="width: 56px; height: 56px; margin: 0 auto 1.25rem; display: flex; align-items: center; justify-content: center; background: rgba(229,184,61,0.1); border-radius: 50%; color: var(--gold);">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
      </div>
      <h3 style="color: #fff; font-family: var(--font-serif); margin-bottom: 0.5rem;">No Registrations Found</h3>
      <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.25rem;">No model registration records matched the active filters.</p>
      @if(request()->anyFilled(['search', 'season_id', 'category', 'gender', 'payment_status', 'registration_status']))
        <a href="{{ route('admin.season-registrations.index') }}" class="btn-subtle" style="display: inline-block;">Clear All Filters</a>
      @endif
    </div>
  @else
    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Reg ID</th>
            <th>Applicant Name</th>
            <th>WhatsApp</th>
            <th>Age / Gender</th>
            <th>Category</th>
            <th>Season</th>
            <th>Fee (INR)</th>
            <th>Paid (INR)</th>
            <th>Payment</th>
            <th>Reg Status</th>
            <th>Date</th>
            <th style="text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($registrations as $r)
            <tr>
              <td>
                <a href="{{ route('admin.season-registrations.show', $r) }}" style="font-family: monospace; font-weight: 700; color: var(--gold); text-decoration: none;">
                  {{ $r->registration_number }}
                </a>
              </td>
              <td>
                <div style="font-weight: 600; color: #fff;">{{ $r->full_name }}</div>
                @if($r->email)
                  <div style="font-size: 0.78rem; color: var(--text-muted);">{{ $r->email }}</div>
                @endif
                @if($r->isKid() && $r->parent_name)
                  <div style="font-size: 0.75rem; color: #fbbf24;">Guardian: {{ $r->parent_name }}</div>
                @endif
              </td>
              <td>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $r->whatsapp_number) }}" target="_blank" rel="noopener" style="color: #4ade80; text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                  <span>{{ $r->whatsapp_number }}</span>
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                </a>
              </td>
              <td>
                <span style="color: #fff; font-weight: 500;">{{ $r->age }} yrs</span>
                <span style="color: var(--text-muted); font-size: 0.8rem;">· {{ ucfirst($r->gender) }}</span>
              </td>
              <td>
                @if($r->category === 'kids')
                  <span class="reg-badge" style="background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3);">Kids / Minor</span>
                @else
                  <span class="reg-badge" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3);">Adult</span>
                @endif
              </td>
              <td>
                <span style="color: #e2e8f0; font-weight: 500;">{{ $r->season ? $r->season->title : 'Season '.$r->season_id }}</span>
              </td>
              <td style="font-weight: 600; color: #fff;">
                ₹{{ number_format($r->registration_fee, 0) }}
              </td>
              <td style="font-weight: 600; color: {{ $r->amount_paid > 0 ? '#4ade80' : 'var(--text-muted)' }};">
                ₹{{ number_format($r->amount_paid, 0) }}
              </td>
              <td>
                @if($r->payment_status === 'paid')
                  <span class="reg-badge reg-badge--paid">Paid</span>
                @elseif($r->payment_status === 'unpaid')
                  <span class="reg-badge reg-badge--unpaid">Unpaid</span>
                @else
                  <span class="reg-badge reg-badge--pending">{{ ucfirst($r->payment_status) }}</span>
                @endif
              </td>
              <td>
                @if($r->registration_status === 'approved')
                  <span class="reg-badge reg-badge--approved">Approved</span>
                @elseif($r->registration_status === 'pending')
                  <span class="reg-badge reg-badge--pending">Pending</span>
                @elseif($r->registration_status === 'rejected')
                  <span class="reg-badge reg-badge--rejected">Rejected</span>
                @else
                  <span class="reg-badge reg-badge--cancelled">{{ ucfirst($r->registration_status) }}</span>
                @endif
              </td>
              <td style="font-size: 0.8rem; color: var(--text-muted); white-space: nowrap;">
                {{ $r->created_at->format('d M Y') }}
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <div style="display: inline-flex; gap: 0.35rem;">
                  <a href="{{ route('admin.season-registrations.show', $r) }}" class="btn-subtle" title="View details & Record Payment" style="padding: 0.35rem 0.65rem; font-size: 0.78rem;">
                    View
                  </a>
                  <a href="{{ route('admin.season-registrations.edit', $r) }}" class="btn-subtle" title="Edit Registration" style="padding: 0.35rem 0.65rem; font-size: 0.78rem;">
                    Edit
                  </a>
                  <form method="POST" action="{{ route('admin.season-registrations.destroy', $r) }}" onsubmit="return confirm('Permanently delete registration for {{ addslashes($r->full_name) }} ({{ $r->registration_number }})?');" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-subtle" style="padding: 0.35rem 0.65rem; font-size: 0.78rem; color: #f87171;" title="Delete">
                      Del
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if($registrations->hasPages())
      <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--border-subtle);">
        {{ $registrations->links() }}
      </div>
    @endif
  @endif
</div>
@endsection

