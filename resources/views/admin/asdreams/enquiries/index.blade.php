@extends('admin.layouts.app')

@section('title', 'Client Enquiries — ASDreamStudio')
@section('page_title', 'Studio Enquiries')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Studio <em>Client Enquiries</em></h2>
    <p class="module-subtitle">Track incoming leads and bookings from Offer Details package pages and the Contact page form.</p>
  </div>
</div>

<!-- Tabs for Offer Enquiries vs General Contact Enquiries -->
<div style="display: flex; gap: 0.75rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.75rem;">
  <a href="{{ route('admin.asdreams.enquiries.index', ['type' => 'offers']) }}"
     class="{{ $type === 'offers' ? 'btn-gold' : 'btn-subtle' }}"
     style="font-size: 0.88rem; padding: 0.5rem 1.1rem; display: inline-flex; align-items: center; gap: 0.5rem;">
    <span>Package Offer Enquiries</span>
    @if($newOffersCount > 0)
      <span style="background: #e53e3e; color: #fff; font-size: 0.7rem; font-weight: 700; padding: 2px 7px; border-radius: 10px;">
        {{ $newOffersCount }} new
      </span>
    @endif
  </a>

  <a href="{{ route('admin.asdreams.enquiries.index', ['type' => 'contact']) }}"
     class="{{ $type === 'contact' ? 'btn-gold' : 'btn-subtle' }}"
     style="font-size: 0.88rem; padding: 0.5rem 1.1rem; display: inline-flex; align-items: center; gap: 0.5rem;">
    <span>General Contact Messages</span>
    @if($newContactCount > 0)
      <span style="background: #e53e3e; color: #fff; font-size: 0.7rem; font-weight: 700; padding: 2px 7px; border-radius: 10px;">
        {{ $newContactCount }} new
      </span>
    @endif
  </a>
</div>

<!-- Filters -->
<div class="admin-card filter-card" style="margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('admin.asdreams.enquiries.index') }}" class="filter-form">
    <input type="hidden" name="type" value="{{ $type }}" />

    <div class="filter-group filter-group--search" style="flex: 2;">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search client name, phone, email, message..." class="filter-input" />
    </div>

    @if($type === 'offers')
      <div class="filter-group">
        <select name="offer_id" class="filter-select" onchange="this.form.submit()">
          <option value="">All Offers</option>
          @foreach($offers as $off)
            <option value="{{ $off->id }}" {{ request('offer_id') == $off->id ? 'selected' : '' }}>{{ $off->title }}</option>
          @endforeach
        </select>
      </div>
    @else
      <div class="filter-group">
        <select name="brand" class="filter-select" onchange="this.form.submit()">
          <option value="">All Brands</option>
          <option value="asdreams" {{ request('brand') === 'asdreams' ? 'selected' : '' }}>ASDreamStudio</option>
          <option value="athuls" {{ request('brand') === 'athuls' ? 'selected' : '' }}>Athul's Academy</option>
          <option value="lakme" {{ request('brand') === 'lakme' ? 'selected' : '' }}>Lakmé Academy</option>
        </select>
      </div>
    @endif

    <div class="filter-group">
      <select name="status" class="filter-select" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New (Unread)</option>
        <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Filter</button>
      @if(request()->hasAny(['search', 'status', 'offer_id', 'brand']))
        <a href="{{ route('admin.asdreams.enquiries.index', ['type' => $type]) }}" class="btn-reset">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Enquiries Table -->
<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 100px;">Date</th>
          <th>Client Name</th>
          <th>Contact Info</th>
          @if($type === 'offers')
            <th>Requested Offer</th>
            <th>Preferred Date</th>
          @else
            <th>Brand</th>
            <th>Subject</th>
          @endif
          <th>Message Preview</th>
          <th>Status</th>
          <th style="text-align: right; width: 110px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($enquiries as $item)
          <tr>
            <td style="color: var(--text-muted); font-size: 0.8rem; white-space: nowrap;">
              {{ $item->created_at->format('d M Y') }}<br>
              <span style="font-size: 0.72rem; color: #777;">{{ $item->created_at->format('h:i A') }}</span>
            </td>
            <td>
              <strong style="color: #fff; font-size: 0.95rem; display: block;">{{ $item->name }}</strong>
            </td>
            <td>
              <div style="font-size: 0.82rem; line-height: 1.4;">
                <a href="tel:{{ $item->phone }}" style="color: var(--gold); display: block;">{{ $item->phone }}</a>
                @if($item->email)
                  <a href="mailto:{{ $item->email }}" style="color: var(--text-muted);">{{ $item->email }}</a>
                @endif
              </div>
            </td>
            @if($type === 'offers')
              <td>
                <span class="badge" style="background: rgba(245, 200, 66, 0.12); color: var(--gold); border: 1px solid var(--gold-a35);">
                  {{ $item->offer->title ?? $item->offer_title ?? 'Offer' }}
                </span>
                @if($item->banner)
                  <span style="display: block; font-size: 0.72rem; color: #a0a0b0; margin-top: 3px;">
                    Banner: {{ Str::limit($item->banner->title, 22) }}
                  </span>
                @endif
              </td>
              <td style="font-size: 0.82rem; color: var(--text-light); white-space: nowrap;">
                {{ $item->preferred_date ? $item->preferred_date->format('d M Y') : 'Flexible / TBD' }}
              </td>
            @else
              <td>
                @php
                  $brandBadge = match($item->brand) {
                    'athuls', 'athuls-academy' => ['label' => "Athul's Academy", 'color' => '#f5c842', 'bg' => 'rgba(245, 200, 66, 0.12)'],
                    'lakme', 'lakme-academy' => ['label' => 'Lakmé Academy', 'color' => '#d4af37', 'bg' => 'rgba(212, 175, 55, 0.15)'],
                    default => ['label' => 'ASDreamStudio', 'color' => '#64b5f6', 'bg' => 'rgba(100, 181, 246, 0.12)'],
                  };
                @endphp
                <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; background: {{ $brandBadge['bg'] }}; color: {{ $brandBadge['color'] }}; white-space: nowrap;">
                  {{ $brandBadge['label'] }}
                </span>
              </td>
              <td>
                <span style="color: #fff; font-size: 0.85rem; font-weight: 500;">
                  {{ $item->subject ?: 'General Enquiry' }}
                </span>
              </td>
            @endif
            <td>
              <span style="color: var(--text-muted); font-size: 0.8rem; display: block; max-width: 280px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                {{ $item->message }}
              </span>
            </td>
            <td>
              @php
                $statusColors = [
                  'new' => ['bg' => 'rgba(234, 67, 53, 0.15)', 'color' => '#ff8080', 'border' => 'rgba(234, 67, 53, 0.4)'],
                  'contacted' => ['bg' => 'rgba(245, 200, 66, 0.15)', 'color' => 'var(--gold)', 'border' => 'var(--gold-a35)'],
                  'completed' => ['bg' => 'rgba(52, 168, 83, 0.15)', 'color' => '#70e090', 'border' => 'rgba(52, 168, 83, 0.4)'],
                  'archived' => ['bg' => 'rgba(255, 255, 255, 0.05)', 'color' => '#888', 'border' => 'rgba(255, 255, 255, 0.1)'],
                ];
                $conf = $statusColors[$item->status] ?? $statusColors['new'];
              @endphp
              <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; background: {{ $conf['bg'] }}; color: {{ $conf['color'] }}; border: 1px solid {{ $conf['border'] }};">
                {{ $item->status }}
              </span>
            </td>
            <td style="text-align: right;">
              <div class="table-actions" style="justify-content: flex-end;">
                @if($type === 'offers')
                  <a href="{{ route('admin.asdreams.enquiries.show-offer', $item) }}" class="btn-action" title="View Enquiry Details">
                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                  </a>
                  <form action="{{ route('admin.asdreams.enquiries.destroy-offer', $item) }}" method="POST" onsubmit="return confirm('Delete this enquiry?');" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-action--delete" title="Delete">
                      <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                  </form>
                @else
                  <a href="{{ route('admin.asdreams.enquiries.show-contact', $item) }}" class="btn-action" title="View Message Details">
                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                  </a>
                  <form action="{{ route('admin.asdreams.enquiries.destroy-contact', $item) }}" method="POST" onsubmit="return confirm('Delete this contact message?');" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-action--delete" title="Delete">
                      <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
              No enquiries found in this category.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($enquiries->hasPages())
    <div style="margin-top: 1.5rem;">
      {{ $enquiries->links() }}
    </div>
  @endif
</div>
@endsection
