@extends('admin.layouts.app')

@section('title', 'Homepage Banners / Hero Slides')
@section('page_title', 'Hero Banner Management')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Homepage <em>Hero Banners</em></h2>
    <p class="module-subtitle">Manage high-impact hero slides, photograph backdrops, labels, italic headlines, and linked offers for ASDreamStudio.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.asdreams.banners.create') }}" class="btn-gold">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add New Banner Slide</span>
    </a>
  </div>
</div>

<!-- Filters -->
<div class="admin-card filter-card" style="margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('admin.asdreams.banners.index') }}" class="filter-form">
    <div class="filter-group filter-group--search" style="flex: 2;">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, label or highlight text..." class="filter-input" />
    </div>

    <div class="filter-group">
      <select name="status" class="filter-select" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active Only</option>
        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive Only</option>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Filter</button>
      @if(request()->hasAny(['search', 'status']))
        <a href="{{ route('admin.asdreams.banners.index') }}" class="btn-reset">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Table Card -->
<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 50px;">Order</th>
          <th style="width: 140px;">Slide Preview</th>
          <th>Category Label &amp; Heading</th>
          <th>Linked Offer</th>
          <th>Button</th>
          <th>Status</th>
          <th style="text-align: right; width: 140px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($banners as $banner)
          <tr>
            <td style="color: var(--text-muted); font-weight: 600;">#{{ $banner->order }}</td>
            <td>
              <div style="width: 120px; height: 75px; border-radius: 4px; overflow: hidden; background: #000; border: 1px solid var(--border-subtle);">
                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" style="width: 100%; height: 100%; object-fit: cover;" />
              </div>
            </td>
            <td>
              @if($banner->label)
                <span style="font-size: 0.72rem; letter-spacing: 0.1em; text-transform: uppercase; color: var(--gold); display: block; margin-bottom: 0.2rem;">{{ $banner->label }}</span>
              @endif
              <strong style="color: #fff; font-size: 1rem; font-family: var(--font-serif);">
                {{ $banner->title }}
                @if($banner->highlight_title)
                  <em style="color: var(--gold); font-weight: 400;">({{ $banner->highlight_title }})</em>
                @endif
              </strong>
              @if($banner->description)
                <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.25rem; max-width: 380px;">{{ Str::limit($banner->description, 80) }}</p>
              @endif
            </td>
            <td>
              @php
                $linkedOffer = $banner->linkedOffer ?? $banner->offer;
              @endphp
              @if($linkedOffer)
                <div>
                  <span style="color: var(--gold); font-size: 0.85rem; font-weight: 500; display: block;">
                    {{ $linkedOffer->title }}
                  </span>
                  <a href="{{ route('asd.offers.show', $linkedOffer->slug) }}" target="_blank" style="color: var(--text-muted); font-size: 0.72rem; text-decoration: underline;">
                    /asdreams/offers/{{ $linkedOffer->slug }} ↗
                  </a>
                  @if($linkedOffer->features && $linkedOffer->features->count() > 0)
                    <span style="display: block; font-size: 0.72rem; color: #a0a0b0; margin-top: 2px;">
                      ✦ {{ $linkedOffer->features->count() }} feature points
                    </span>
                  @endif
                </div>
              @elseif($banner->linked_offer_slug)
                <span style="color: var(--text-secondary); font-size: 0.85rem;">/offers/{{ $banner->linked_offer_slug }}</span>
              @else
                <span style="color: var(--text-muted); font-size: 0.82rem;">None (General)</span>
              @endif
            </td>
            <td>
              <span style="font-size: 0.8rem; background: rgba(255, 255, 255, 0.05); padding: 0.25rem 0.5rem; border-radius: 3px; color: var(--text-secondary);">
                {{ $banner->button_text ?: 'Offer Details' }}
              </span>
            </td>
            <td>
              <form action="{{ route('admin.asdreams.banners.toggle-status', $banner) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="badge badge--{{ $banner->is_active ? 'success' : 'subtle' }}" style="cursor: pointer; border: none;" title="Click to toggle">
                  {{ $banner->is_active ? 'Active' : 'Disabled' }}
                </button>
              </form>
            </td>
            <td style="text-align: right;">
              <div style="display: inline-flex; gap: 0.5rem;">
                <a href="{{ route('admin.asdreams.banners.edit', $banner) }}" class="btn-table-action" title="Edit Slide">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </a>
                <form action="{{ route('admin.asdreams.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Delete this banner slide?');" style="display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-table-action btn-table-action--delete" title="Delete Slide">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 3rem 1rem;">
              No banner slides found. Click "Add New Banner Slide" to create one.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($banners->hasPages())
    <div style="padding: 1.25rem 0 0;">
      {{ $banners->links() }}
    </div>
  @endif
</div>
@endsection

