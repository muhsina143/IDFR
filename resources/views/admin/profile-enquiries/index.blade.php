@extends('admin.layouts.app')

@section('title', 'Profile Booking Enquiries')
@section('header_title', 'Profile Enquiries')

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
    border: 1px solid rgba(229, 184, 61, 0.4);
    color: var(--gold);
  }
  .badge-status--contacted {
    background: rgba(59, 130, 246, 0.15);
    border: 1px solid rgba(59, 130, 246, 0.4);
    color: #60a5fa;
  }
  .badge-status--in_progress {
    background: rgba(234, 179, 8, 0.15);
    border: 1px solid rgba(234, 179, 8, 0.4);
    color: #facc15;
  }
  .badge-status--completed {
    background: rgba(34, 197, 94, 0.15);
    border: 1px solid rgba(34, 197, 94, 0.4);
    color: #4ade80;
  }
  .badge-status--cancelled {
    background: rgba(239, 68, 68, 0.15);
    border: 1px solid rgba(239, 68, 68, 0.4);
    color: #f87171;
  }
  .pro-pill {
    display: inline-flex;
    flex-direction: column;
  }
  .pro-pill__name {
    font-weight: 600;
    color: var(--text-primary);
  }
  .pro-pill__cat {
    font-size: 0.72rem;
    color: var(--gold);
    text-transform: capitalize;
  }
  .client-pill {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
  }
  .client-pill__name {
    font-weight: 600;
    color: var(--text-primary);
  }
  .client-pill__contact {
    font-size: 0.75rem;
    color: var(--text-muted);
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
</style>
@endsection

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Model &amp; Creative <em>Booking Enquiries</em></h2>
    <p class="module-subtitle">Manage client booking requests, assignment schedules, client communications, and talent appointments.</p>
  </div>
</div>

<!-- Stats Bar -->
<div class="stats-overview-row">
  <div class="stat-pill">
    <span class="stat-pill__label">Total Enquiries</span>
    <span class="stat-pill__val">{{ number_format($totalCount) }}</span>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">New Requests</span>
    <span class="stat-pill__val stat-pill__val--gold">{{ number_format($newCount) }}</span>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">In Progress</span>
    <span class="stat-pill__val" style="color: #facc15;">{{ number_format($inProgressCount) }}</span>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Completed</span>
    <span class="stat-pill__val stat-pill__val--green">{{ number_format($completedCount) }}</span>
  </div>
</div>

<!-- Status Filter Tabs -->
<div class="category-tabs-bar">
  <a href="{{ route('admin.profile-enquiries.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}" 
     class="category-tab {{ !request('status') || request('status') === 'all' ? 'is-active' : '' }}">
    All ({{ $totalCount }})
  </a>
  <a href="{{ route('admin.profile-enquiries.index', array_merge(request()->except('page'), ['status' => 'new'])) }}" 
     class="category-tab {{ request('status') === 'new' ? 'is-active' : '' }}">
    New ({{ $newCount }})
  </a>
  <a href="{{ route('admin.profile-enquiries.index', array_merge(request()->except('page'), ['status' => 'contacted'])) }}" 
     class="category-tab {{ request('status') === 'contacted' ? 'is-active' : '' }}">
    Contacted
  </a>
  <a href="{{ route('admin.profile-enquiries.index', array_merge(request()->except('page'), ['status' => 'in_progress'])) }}" 
     class="category-tab {{ request('status') === 'in_progress' ? 'is-active' : '' }}">
    In Progress ({{ $inProgressCount }})
  </a>
  <a href="{{ route('admin.profile-enquiries.index', array_merge(request()->except('page'), ['status' => 'completed'])) }}" 
     class="category-tab {{ request('status') === 'completed' ? 'is-active' : '' }}">
    Completed ({{ $completedCount }})
  </a>
  <a href="{{ route('admin.profile-enquiries.index', array_merge(request()->except('page'), ['status' => 'cancelled'])) }}" 
     class="category-tab {{ request('status') === 'cancelled' ? 'is-active' : '' }}">
    Cancelled
  </a>
</div>

<!-- Search and Category Filter -->
<div class="admin-card filter-card">
  <form method="GET" action="{{ route('admin.profile-enquiries.index') }}" class="filter-form" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
    <input type="hidden" name="status" value="{{ request('status') }}">

    <div class="filter-group filter-group--search" style="flex: 1; min-width: 240px;">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by client, talent, phone, location or brief..." class="filter-input" />
    </div>

    <div>
      <select name="category" class="filter-select" onchange="this.form.submit()">
        <option value="all">All Categories</option>
        @foreach($categories as $cat)
          <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
        @endforeach
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Filter</button>
      @if(request()->hasAny(['search', 'status', 'category']))
        <a href="{{ route('admin.profile-enquiries.index') }}" class="btn-reset">Reset</a>
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
          <th style="width: 100px;">Status</th>
          <th>Target Talent / Pro</th>
          <th>Client Details</th>
          <th>Assignment</th>
          <th>Preferred Date</th>
          <th>Location</th>
          <th>Received At</th>
          <th style="text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($enquiries as $item)
          <tr>
            <td>
              <span class="badge-status badge-status--{{ $item->status }}">
                {{ $item->statusLabel() }}
              </span>
            </td>
            <td>
              <div class="pro-pill">
                <span class="pro-pill__name">{{ $item->professional_name }}</span>
                <span class="pro-pill__cat">{{ $item->professional_category }}</span>
              </div>
            </td>
            <td>
              <div class="client-pill">
                <span class="client-pill__name">{{ $item->name }}</span>
                <span class="client-pill__contact">{{ $item->email }} &bull; {{ $item->phone }}</span>
              </div>
            </td>
            <td>
              <div style="font-weight: 500; color: var(--text-primary);">{{ $item->assignment_type }}</div>
            </td>
            <td>
              <span style="font-size: 0.8rem; color: var(--text-secondary);">
                {{ $item->preferred_date ? $item->preferred_date->format('M d, Y') : 'Flexible' }}
              </span>
            </td>
            <td>
              <span style="font-size: 0.8rem; color: var(--text-secondary);">{{ $item->location }}</span>
            </td>
            <td>
              <span style="font-size: 0.78rem; color: var(--text-muted);" title="{{ $item->created_at->format('M d, Y h:i A') }}">
                {{ $item->created_at->diffForHumans() }}
              </span>
            </td>
            <td style="text-align: right;">
              <div class="actions-cluster" style="justify-content: flex-end;">
                <a href="{{ route('admin.profile-enquiries.show', $item) }}" class="btn-action" title="View &amp; Manage">
                  <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                  <span>View</span>
                </a>
                <form action="{{ route('admin.profile-enquiries.destroy', $item) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this enquiry from {{ addslashes($item->name) }}? This cannot be undone.');">
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
                No booking enquiries found matching the selected filters.
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

