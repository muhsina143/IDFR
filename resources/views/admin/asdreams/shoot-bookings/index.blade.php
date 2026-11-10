@extends('admin.layouts.app')

@section('title', 'Shoot Bookings & Studio Reservations')
@section('page_title', 'Shoot Bookings')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Studio <em>Shoot Bookings</em></h2>
    <p class="module-subtitle">Manage client shoot requests and studio rental reservations submitted through the "Book a Shoot" popup modal.</p>
  </div>
</div>

<!-- Filters -->
<div class="admin-card filter-card" style="margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('admin.asdreams.shoot-bookings.index') }}" class="filter-form" style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;">
    <div class="filter-group filter-group--search" style="flex: 2; min-width: 220px;">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search client name, phone, shoot type..." class="filter-input" />
    </div>

    <div class="filter-group" style="min-width: 160px;">
      <select name="status" class="filter-select" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New (Uncontacted)</option>
        <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
      </select>
    </div>

    <div class="filter-group" style="display: flex; align-items: center; gap: 0.4rem;">
      <span style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">From:</span>
      <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-input" style="padding: 0.4rem 0.6rem; font-size: 0.85rem;" onchange="this.form.submit()" />
    </div>

    <div class="filter-group" style="display: flex; align-items: center; gap: 0.4rem;">
      <span style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">To:</span>
      <input type="date" name="date_to" value="{{ request('date_to') }}" class="filter-input" style="padding: 0.4rem 0.6rem; font-size: 0.85rem;" onchange="this.form.submit()" />
    </div>

    <div class="filter-actions" style="margin-left: auto;">
      <button type="submit" class="btn-subtle">Filter</button>
      @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
        <a href="{{ route('admin.asdreams.shoot-bookings.index') }}" class="btn-reset">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Bookings Table -->
<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 100px;">Submission</th>
          <th>Client Name</th>
          <th>Contact Info</th>
          <th>Shoot Date</th>
          <th>Shoot Type</th>
          <th>Requirements Snippet</th>
          <th>Status</th>
          <th style="text-align: right; width: 110px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($bookings as $booking)
          <tr>
            <td style="color: var(--text-muted); font-size: 0.8rem; white-space: nowrap;">
              {{ $booking->created_at->format('d M Y') }}<br>
              <span style="font-size: 0.72rem; color: #777;">{{ $booking->created_at->format('h:i A') }}</span>
            </td>
            <td>
              <strong style="color: #fff; font-size: 0.95rem; display: block;">{{ $booking->name }}</strong>
            </td>
            <td>
              <div style="font-size: 0.82rem; line-height: 1.4;">
                <a href="tel:{{ $booking->phone }}" style="color: var(--gold); display: block;">{{ $booking->phone }}</a>
                @if($booking->email)
                  <a href="mailto:{{ $booking->email }}" style="color: var(--text-muted);">{{ $booking->email }}</a>
                @endif
              </div>
            </td>
            <td style="white-space: nowrap;">
              @if($booking->shoot_date)
                <strong style="color: var(--gold); font-size: 0.88rem;">{{ $booking->shoot_date->format('d M Y') }}</strong>
                <span style="display: block; font-size: 0.72rem; color: var(--text-muted);">{{ $booking->shoot_date->format('l') }}</span>
              @else
                <span style="color: var(--text-muted); font-size: 0.82rem;">Flexible</span>
              @endif
            </td>
            <td>
              <span class="badge" style="background: rgba(245, 200, 66, 0.12); color: var(--gold); border: 1px solid var(--gold-a35);">
                {{ $booking->subject ?: 'Studio Shoot' }}
              </span>
            </td>
            <td>
              <span style="color: var(--text-muted); font-size: 0.8rem; display: block; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                {{ $booking->message }}
              </span>
            </td>
            <td>
              @php
                $statusColors = [
                  'new' => ['bg' => 'rgba(234, 67, 53, 0.15)', 'color' => '#ff8080', 'border' => 'rgba(234, 67, 53, 0.4)'],
                  'contacted' => ['bg' => 'rgba(245, 200, 66, 0.15)', 'color' => 'var(--gold)', 'border' => 'var(--gold-a35)'],
                  'confirmed' => ['bg' => 'rgba(52, 168, 83, 0.15)', 'color' => '#70e090', 'border' => 'rgba(52, 168, 83, 0.4)'],
                  'completed' => ['bg' => 'rgba(66, 153, 225, 0.15)', 'color' => '#90cdf4', 'border' => 'rgba(66, 153, 225, 0.4)'],
                  'cancelled' => ['bg' => 'rgba(255, 255, 255, 0.05)', 'color' => '#888', 'border' => 'rgba(255, 255, 255, 0.1)'],
                ];
                $conf = $statusColors[$booking->status] ?? $statusColors['new'];
              @endphp
              <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; background: {{ $conf['bg'] }}; color: {{ $conf['color'] }}; border: 1px solid {{ $conf['border'] }};">
                {{ $booking->status }}
              </span>
            </td>
            <td style="text-align: right;">
              <div class="table-actions" style="justify-content: flex-end;">
                <a href="{{ route('admin.asdreams.shoot-bookings.show', $booking) }}" class="btn-action" title="View Booking Details">
                  <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </a>
                <form action="{{ route('admin.asdreams.shoot-bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('Delete this shoot booking?');" style="display: inline-block;">
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
            <td colspan="8" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
              No shoot bookings recorded yet.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($bookings->hasPages())
    <div style="margin-top: 1.5rem;">
      {{ $bookings->links() }}
    </div>
  @endif
</div>
@endsection

