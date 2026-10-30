@extends('admin.layouts.app')

@section('title', 'Contact & Sponsorship Enquiries')
@section('header_title', 'Inbound Enquiries')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Contact &amp; <em>Sponsorship Enquiries</em></h2>
    <p class="module-subtitle">Manage public inbound inquiries, model auditions, designer showcase pitches, and sponsor communications.</p>
  </div>
</div>

<!-- Stats Bar -->
<div class="stats-overview-row">
  <div class="stat-pill">
    <span class="stat-pill__label">Total Enquiries</span>
    <span class="stat-pill__val">{{ number_format($totalCount) }}</span>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Unread Inquiries</span>
    <span class="stat-pill__val stat-pill__val--gold">{{ number_format($unreadCount) }}</span>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Responded</span>
    <span class="stat-pill__val stat-pill__val--green">{{ number_format($respondedCount) }}</span>
  </div>
</div>

<!-- Status Filter Tabs -->
<div class="category-tabs-bar">
  <a href="{{ route('admin.enquiries', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}" 
     class="category-tab {{ !request('status') || request('status') === 'all' ? 'is-active' : '' }}">
    All Enquiries ({{ $totalCount }})
  </a>
  <a href="{{ route('admin.enquiries', array_merge(request()->except('page'), ['status' => 'unread'])) }}" 
     class="category-tab {{ request('status') === 'unread' ? 'is-active' : '' }}">
    Unread ({{ $unreadCount }})
  </a>
  <a href="{{ route('admin.enquiries', array_merge(request()->except('page'), ['status' => 'read'])) }}" 
     class="category-tab {{ request('status') === 'read' ? 'is-active' : '' }}">
    Read
  </a>
  <a href="{{ route('admin.enquiries', array_merge(request()->except('page'), ['status' => 'responded'])) }}" 
     class="category-tab {{ request('status') === 'responded' ? 'is-active' : '' }}">
    Responded ({{ $respondedCount }})
  </a>
  <a href="{{ route('admin.enquiries', array_merge(request()->except('page'), ['status' => 'archived'])) }}" 
     class="category-tab {{ request('status') === 'archived' ? 'is-active' : '' }}">
    Archived
  </a>
</div>

<!-- Search Bar -->
<div class="admin-card filter-card">
  <form method="GET" action="{{ route('admin.enquiries') }}" class="filter-form">
    <input type="hidden" name="status" value="{{ request('status') }}">

    <div class="filter-group filter-group--search">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, phone, email, subject, or message keyword..." class="filter-input" />
    </div>

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Search</button>
      @if(request()->hasAny(['search', 'status']))
        <a href="{{ route('admin.enquiries') }}" class="btn-reset">Reset</a>
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
          <th style="width: 50px;">Status</th>
          <th>Sender Name &amp; Contact</th>
          <th>Subject / Enquiry Topic</th>
          <th>Message Preview</th>
          <th>Received At</th>
          <th style="text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($enquiries as $item)
          <tr class="{{ $item->status === 'unread' ? 'row--unread' : '' }}">
            <td>
              @if($item->status === 'unread')
                <span class="badge-dot badge-dot--unread" title="Unread Message"></span>
              @elseif($item->status === 'read')
                <span class="badge-dot badge-dot--read" title="Read"></span>
              @elseif($item->status === 'responded')
                <span class="badge-dot badge-dot--responded" title="Responded"></span>
              @else
                <span class="badge-dot badge-dot--archived" title="Archived"></span>
              @endif
            </td>
            <td>
              <div class="sender-info">
                <span class="sender-name">{{ $item->name }}</span>
                <span class="sender-phone"><a href="tel:{{ $item->phone }}">{{ $item->phone }}</a></span>
                @if($item->email)
                  <span class="sender-email"><a href="mailto:{{ $item->email }}">{{ $item->email }}</a></span>
                @endif
              </div>
            </td>
            <td>
              <span class="enquiry-subject">{{ $item->subject ?: 'General Enquiry' }}</span>
            </td>
            <td>
              <p class="enquiry-preview">{{ \Illuminate\Support\Str::limit($item->message ?: 'No additional message text provided.', 80) }}</p>
            </td>
            <td>
              <div class="enquiry-date">
                <span>{{ $item->created_at->format('M j, Y') }}</span>
                <small>{{ $item->created_at->format('g:i A') }}</small>
              </div>
            </td>
            <td style="text-align: right;">
              <div class="table-actions">
                <a href="{{ route('admin.enquiries.show', $item->id) }}" class="btn-action btn-action--view">
                  <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                  <span>View</span>
                </a>

                <form action="{{ route('admin.enquiries.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete enquiry from \'{{ addslashes($item->name) }}\'?');" style="display:inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-action btn-action--delete" title="Delete">
                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="table-empty">
              <svg viewBox="0 0 24 24" class="empty-icon"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
              <p>No inbound enquiries found matching your criteria.</p>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Pagination -->
<div class="pagination-wrapper">
  {{ $enquiries->links('pagination::simple-bootstrap-5') }}
</div>

<style>
  .module-header {
    margin-bottom: 1.5rem;
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
    margin-top: 0.35rem;
  }
  .stats-overview-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
  }
  .stat-pill {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    padding: 0.6rem 1.25rem;
    border-radius: 8px;
    font-size: 0.85rem;
  }
  .stat-pill__label {
    color: var(--text-muted);
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
  }
  .stat-pill__val {
    font-size: 1.15rem;
    font-weight: 600;
    color: var(--text-primary);
  }
  .stat-pill__val--gold {
    color: var(--gold);
  }
  .stat-pill__val--green {
    color: #4ade80;
  }
  .category-tabs-bar {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    overflow-x: auto;
    padding-bottom: 0.5rem;
  }
  .category-tab {
    display: inline-block;
    padding: 0.5rem 1rem;
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: 6px;
    color: var(--text-secondary);
    font-size: 0.85rem;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.2s ease;
  }
  .category-tab:hover {
    border-color: rgba(229, 184, 61, 0.4);
    color: var(--gold);
  }
  .category-tab.is-active {
    background: rgba(229, 184, 61, 0.15);
    border-color: var(--gold);
    color: var(--gold);
    font-weight: 600;
  }
  .filter-card {
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
  }
  .filter-form {
    display: flex;
    gap: 1rem;
    align-items: center;
  }
  .filter-group--search {
    flex: 1;
    position: relative;
  }
  .search-icon {
    position: absolute;
    left: 0.85rem;
    top: 50%;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    stroke: var(--text-muted);
    stroke-width: 2;
    fill: none;
  }
  .filter-input {
    width: 100%;
    padding: 0.55rem 0.85rem 0.55rem 2.4rem;
    background: #000;
    border: 1px solid var(--border-subtle);
    border-radius: 6px;
    color: var(--text-primary);
    font-size: 0.85rem;
  }
  .filter-actions {
    display: flex;
    gap: 0.5rem;
  }
  .btn-subtle {
    padding: 0.55rem 1rem;
    background: #222;
    border: 1px solid var(--border-subtle);
    border-radius: 6px;
    color: var(--text-primary);
    font-size: 0.85rem;
    cursor: pointer;
  }
  .btn-reset {
    padding: 0.55rem 1rem;
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    color: var(--text-muted);
    font-size: 0.85rem;
    text-decoration: none;
  }
  .table-card {
    padding: 0;
    overflow: hidden;
  }
  .admin-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
  }
  .admin-table th {
    padding: 0.85rem 1rem;
    text-align: left;
    color: var(--text-muted);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    background: rgba(255, 255, 255, 0.02);
    border-bottom: 1px solid var(--border-subtle);
  }
  .admin-table td {
    padding: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    vertical-align: middle;
  }
  .admin-table tr:hover td {
    background: rgba(255, 255, 255, 0.02);
  }
  .row--unread td {
    background: rgba(229, 184, 61, 0.04);
  }
  .badge-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
  }
  .badge-dot--unread {
    background: var(--gold);
    box-shadow: 0 0 8px var(--gold);
    animation: pulse 1.8s infinite;
  }
  .badge-dot--read {
    background: #60a5fa;
  }
  .badge-dot--responded {
    background: #4ade80;
  }
  .badge-dot--archived {
    background: #6b7280;
  }
  .sender-info {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
  }
  .sender-name {
    font-weight: 600;
    color: var(--text-primary);
  }
  .sender-phone a, .sender-email a {
    color: var(--text-muted);
    font-size: 0.8rem;
    text-decoration: none;
  }
  .sender-phone a:hover, .sender-email a:hover {
    color: var(--gold);
  }
  .enquiry-subject {
    color: var(--text-primary);
    font-weight: 500;
  }
  .enquiry-preview {
    color: var(--text-muted);
    font-size: 0.8rem;
    margin: 0;
    max-width: 320px;
  }
  .enquiry-date {
    display: flex;
    flex-direction: column;
    color: var(--text-secondary);
    font-size: 0.8rem;
  }
  .enquiry-date small {
    color: var(--text-muted);
    font-size: 0.72rem;
  }
  .table-actions {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  .btn-action {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.65rem;
    border-radius: 4px;
    font-size: 0.75rem;
    text-decoration: none;
    border: 1px solid var(--border-subtle);
    background: #1a1a1a;
    cursor: pointer;
    transition: all 0.2s;
  }
  .btn-action--view {
    color: var(--gold);
    border-color: rgba(229, 184, 61, 0.3);
  }
  .btn-action--view:hover {
    background: rgba(229, 184, 61, 0.15);
  }
  .btn-action--delete {
    color: #f87171;
    border-color: rgba(239, 68, 68, 0.3);
  }
  .btn-action--delete:hover {
    background: rgba(239, 68, 68, 0.15);
  }
  .btn-action svg {
    width: 13px;
    height: 13px;
    stroke: currentColor;
    stroke-width: 2;
    fill: none;
  }
  .table-empty {
    text-align: center;
    padding: 3rem !important;
    color: var(--text-muted);
  }
  .empty-icon {
    width: 40px;
    height: 40px;
    stroke: var(--text-muted);
    stroke-width: 1.5;
    fill: none;
    margin-bottom: 0.75rem;
  }
  .pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 1.5rem;
  }
</style>
@endsection
