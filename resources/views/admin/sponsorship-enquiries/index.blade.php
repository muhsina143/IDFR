@extends('admin.layouts.app')

@section('title', 'Sponsorship Enquiries')
@section('header_title', 'Sponsorship Enquiries')

@section('styles')
<style>
  .badge-status {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .badge-status--new {
    background: rgba(229, 184, 61, 0.15);
    border: 1px solid rgba(229, 184, 61, 0.5);
    color: var(--gold);
    box-shadow: 0 0 10px rgba(229, 184, 61, 0.15);
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
  .company-pill {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
  }
  .company-pill__name {
    font-weight: 600;
    color: var(--text-primary);
  }
  .company-pill__contact {
    font-size: 0.75rem;
    color: var(--text-muted);
  }
  .company-pill__contact a {
    color: var(--text-muted);
    text-decoration: none;
    transition: color 0.2s ease;
  }
  .company-pill__contact a:hover {
    color: var(--gold);
  }
  .filter-select {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid var(--border-subtle);
    color: var(--text-primary);
    padding: 0.5rem 0.85rem;
    border-radius: 6px;
    font-size: 0.85rem;
  }
  .filter-select:focus {
    outline: none;
    border-color: var(--gold);
  }
  .filter-select option {
    background: #141419;
    color: #fff;
  }
  .tier-tag {
    display: inline-block;
    padding: 0.2rem 0.55rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gold);
    background: rgba(229, 184, 61, 0.08);
    border: 1px solid rgba(229, 184, 61, 0.25);
  }
  .tier-tag--title {
    background: rgba(229, 184, 61, 0.2);
    border-color: var(--gold);
    color: #fff;
  }
</style>
@endsection

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Brand &amp; <em>Sponsorship Enquiries</em></h2>
    <p class="module-subtitle">Manage incoming corporate sponsorship requests, tier negotiations, and official IDFR runway partnerships.</p>
  </div>
</div>

<!-- Stats Overview Bar -->
<div class="stats-overview-row">
  <div class="stat-pill">
    <span class="stat-pill__label">Total Enquiries</span>
    <span class="stat-pill__val">{{ number_format($totalCount) }}</span>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">New / Pending</span>
    <span class="stat-pill__val stat-pill__val--gold">{{ number_format($newCount) }}</span>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Contacted</span>
    <span class="stat-pill__val" style="color: #60a5fa;">{{ number_format($contactedCount) }}</span>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Completed</span>
    <span class="stat-pill__val stat-pill__val--green">{{ number_format($completedCount) }}</span>
  </div>
</div>

<!-- Status Filter Tabs -->
<div class="category-tabs-bar">
  <a href="{{ route('admin.sponsorship-enquiries.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}" 
     class="category-tab {{ !request('status') || request('status') === 'all' ? 'is-active' : '' }}">
    All ({{ $totalCount }})
  </a>
  <a href="{{ route('admin.sponsorship-enquiries.index', array_merge(request()->except('page'), ['status' => 'new'])) }}" 
     class="category-tab {{ request('status') === 'new' ? 'is-active' : '' }}">
    New ({{ $newCount }})
  </a>
  <a href="{{ route('admin.sponsorship-enquiries.index', array_merge(request()->except('page'), ['status' => 'contacted'])) }}" 
     class="category-tab {{ request('status') === 'contacted' ? 'is-active' : '' }}">
    Contacted ({{ $contactedCount }})
  </a>
  <a href="{{ route('admin.sponsorship-enquiries.index', array_merge(request()->except('page'), ['status' => 'completed'])) }}" 
     class="category-tab {{ request('status') === 'completed' ? 'is-active' : '' }}">
    Completed ({{ $completedCount }})
  </a>
</div>

<!-- Search and Advanced Filters -->
<div class="admin-card filter-card">
  <form method="GET" action="{{ route('admin.sponsorship-enquiries.index') }}" class="filter-form" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
    <input type="hidden" name="status" value="{{ request('status') }}">

    <div class="filter-group filter-group--search" style="flex: 1; min-width: 240px;">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search company, contact, email, phone, message or tier..." class="filter-input" />
    </div>

    <div>
      <select name="type" class="filter-select" onchange="this.form.submit()">
        <option value="all">All Sponsorship Types</option>
        @foreach($availableTypes as $val => $lbl)
          <option value="{{ $val }}" {{ request('type') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
        @endforeach
      </select>
    </div>

    @if($seasons->isNotEmpty())
      <div>
        <select name="season_id" class="filter-select" onchange="this.form.submit()">
          <option value="all">All Runway Seasons</option>
          @foreach($seasons as $s)
            <option value="{{ $s->id }}" {{ (string) request('season_id') === (string) $s->id ? 'selected' : '' }}>
              {{ $s->title }}{{ $s->is_current ? ' (Active)' : '' }}
            </option>
          @endforeach
        </select>
      </div>
    @endif

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Filter</button>
      @if(request()->hasAny(['search', 'status', 'type', 'season_id']))
        <a href="{{ route('admin.sponsorship-enquiries.index') }}" class="btn-reset">Reset</a>
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
          <th style="width: 110px;">Status</th>
          <th>Company / Sponsor Name</th>
          <th>Contact Information</th>
          <th>Sponsorship Tier</th>
          <th>Season</th>
          <th>Message Preview</th>
          <th>Submitted</th>
          <th style="text-align: right; width: 140px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($enquiries as $item)
          <tr>
            <td>
              <span class="badge-status {{ $item->statusBadgeClass() }}">
                {{ $item->statusLabel() }}
              </span>
            </td>
            <td>
              <div class="company-pill">
                <span class="company-pill__name">{{ $item->name }}</span>
                @if($item->created_at && $item->created_at->isToday())
                  <span style="font-size: 0.65rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">★ Today</span>
                @endif
              </div>
            </td>
            <td>
              <div class="company-pill">
                <span class="company-pill__contact">
                  <a href="mailto:{{ $item->email }}">{{ $item->email }}</a>
                </span>
                <span class="company-pill__contact">
                  <a href="tel:{{ $item->phone }}">{{ $item->phone }}</a>
                </span>
              </div>
            </td>
            <td>
              <span class="tier-tag {{ str_contains(strtolower($item->sponsorship_type), 'title') ? 'tier-tag--title' : '' }}">
                {{ $item->sponsorship_type }}
              </span>
            </td>
            <td>
              <span style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 500;">
                {{ $item->season_title ?: ($item->season?->title ?? 'Season 8') }}
              </span>
            </td>
            <td>
              <div style="font-size: 0.8rem; color: var(--text-secondary); max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $item->message }}">
                {{ $item->message ?: '— No message provided —' }}
              </div>
            </td>
            <td>
              <span style="font-size: 0.78rem; color: var(--text-muted);" title="{{ $item->created_at ? $item->created_at->format('M d, Y h:i A') : 'N/A' }}">
                {{ $item->created_at ? $item->created_at->diffForHumans() : '—' }}
              </span>
            </td>
            <td style="text-align: right;">
              <div class="actions-cluster" style="justify-content: flex-end;">
                <a href="{{ route('admin.sponsorship-enquiries.show', $item) }}" class="btn-action" title="View &amp; Manage Enquiry">
                  <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                  <span>View</span>
                </a>
                <form action="{{ route('admin.sponsorship-enquiries.destroy', $item) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this sponsorship enquiry from {{ addslashes($item->name) }}? This action cannot be undone.');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-action btn-action--danger" title="Delete Enquiry">
                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" style="text-align: center; padding: 3rem 1rem;">
              <div style="color: var(--text-muted); font-size: 0.95rem;">
                No sponsorship enquiries found matching the selected filters.
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($enquiries->hasPages())
    <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--border-subtle);">
      {{ $enquiries->links() }}
    </div>
  @endif
</div>
@endsection

