@extends('admin.layouts.app')

@section('title', 'ASDreamStudio Offers Management')
@section('page_title', 'Studio Offers & Packages')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Studio <em>Offers &amp; Packages</em></h2>
    <p class="module-subtitle">Manage all 7 photography packages, inclusions, features, hero links, and client enquiry forms.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.asdreams.offers.create') }}" class="btn-gold">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add New Offer</span>
    </a>
  </div>
</div>

<!-- Filters -->
<div class="admin-card filter-card" style="margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('admin.asdreams.offers.index') }}" class="filter-form">
    <div class="filter-group filter-group--search" style="flex: 2;">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search offer title, slug or category..." class="filter-input" />
    </div>

    <div class="filter-group">
      <select name="status" class="filter-select" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published Only</option>
        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft Only</option>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Filter</button>
      @if(request()->hasAny(['search', 'status']))
        <a href="{{ route('admin.asdreams.offers.index') }}" class="btn-reset">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Offers Table -->
<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 50px;">Order</th>
          <th style="width: 80px;">Cover</th>
          <th>Offer Title &amp; Category</th>
          <th>Slug</th>
          <th>Features</th>
          <th>Enquiries</th>
          <th>Status</th>
          <th style="text-align: right; width: 140px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($offers as $offer)
          <tr>
            <td style="color: var(--text-muted); font-weight: 600;">#{{ $offer->order }}</td>
            <td>
              <div style="width: 60px; height: 45px; border-radius: 4px; overflow: hidden; background: #111;">
                <img src="{{ $offer->image_url }}" alt="{{ $offer->title }}" style="width: 100%; height: 100%; object-fit: cover;" />
              </div>
            </td>
            <td>
              <strong style="color: #fff; font-size: 0.95rem; display: block;">{{ $offer->title }}</strong>
              <span style="color: var(--gold); font-size: 0.78rem; letter-spacing: 0.05em; text-transform: uppercase;">{{ $offer->category_label }}</span>
            </td>
            <td>
              <code style="background: rgba(255,255,255,0.06); padding: 2px 6px; border-radius: 3px; font-size: 0.8rem; color: #ccc;">{{ $offer->slug }}</code>
            </td>
            <td>
              <span class="badge" style="background: rgba(245, 200, 66, 0.12); color: var(--gold); border: 1px solid var(--gold-a35);">
                {{ $offer->features_count }} inclusions
              </span>
            </td>
            <td>
              <a href="{{ route('admin.asdreams.enquiries.index', ['type' => 'offers', 'offer_id' => $offer->id]) }}" style="color: var(--text-light); text-decoration: underline;">
                {{ $offer->enquiries_count }} enquiries
              </a>
            </td>
            <td>
              @if($offer->status === 'published')
                <span class="status-badge status-badge--active">Published</span>
              @else
                <span class="status-badge status-badge--inactive">Draft</span>
              @endif
            </td>
            <td style="text-align: right;">
              <div class="table-actions" style="justify-content: flex-end;">
                <a href="{{ route('asd.offers.show', $offer->slug) }}" target="_blank" class="btn-action" title="View Live Page">
                  <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </a>
                <a href="{{ route('admin.asdreams.offers.edit', $offer) }}" class="btn-action" title="Edit Offer">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </a>
                <form action="{{ route('admin.asdreams.offers.destroy', $offer) }}" method="POST" onsubmit="return confirm('Delete offer {{ $offer->title }}? This cannot be undone.');" style="display: inline-block;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-action btn-action--delete" title="Delete Offer">
                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
              No offers found matching your criteria.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($offers->hasPages())
    <div style="margin-top: 1.5rem;">
      {{ $offers->links() }}
    </div>
  @endif
</div>
@endsection

