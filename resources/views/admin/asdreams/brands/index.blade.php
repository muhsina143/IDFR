@extends('admin.layouts.app')

@section('title', 'Homepage Brands Management')
@section('page_title', 'Homepage Brands')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Homepage <em>Brands</em></h2>
    <p class="module-subtitle">Manage creative houses shown in the "Our Brands" modal and homepage showcase: logos, taglines, cards, and links.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.asdreams.brands.create') }}" class="btn-gold">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add New Brand</span>
    </a>
  </div>
</div>

<!-- Filters -->
<div class="admin-card filter-card" style="margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('admin.asdreams.brands.index') }}" class="filter-form">
    <div class="filter-group filter-group--search" style="flex: 2;">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search brand name or tagline..." class="filter-input" />
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
        <a href="{{ route('admin.asdreams.brands.index') }}" class="btn-reset">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Brands Table -->
<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 50px;">Order</th>
          <th style="width: 90px;">Logo</th>
          <th>Brand Name &amp; Tagline</th>
          <th>Card Variant</th>
          <th>Target Link</th>
          <th>Status</th>
          <th style="text-align: right; width: 140px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($brands as $brand)
          <tr>
            <td style="color: var(--text-muted); font-weight: 600;">#{{ $brand->order }}</td>
            <td>
              <div style="width: 70px; height: 50px; border-radius: 4px; background: #000; border: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: center; padding: 4px;">
                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;" />
              </div>
            </td>
            <td>
              <strong style="color: #fff; font-size: 0.95rem; display: block;">{{ $brand->name }}</strong>
              @if($brand->tagline)
                <span style="font-size: 0.78rem; color: var(--gold);">{{ $brand->tagline }}</span>
              @endif
            </td>
            <td>
              <span style="font-size: 0.75rem; text-transform: uppercase; background: rgba(255, 255, 255, 0.05); padding: 0.2rem 0.5rem; border-radius: 3px; color: var(--text-secondary);">
                {{ $brand->css_variant ?: 'standard' }}
              </span>
            </td>
            <td>
              <span style="font-size: 0.82rem; color: var(--text-secondary); word-break: break-all;">
                {{ $brand->link_url ?: 'Default (#)' }}
              </span>
            </td>
            <td>
              <form action="{{ route('admin.asdreams.brands.toggle-status', $brand) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="badge badge--{{ $brand->is_active ? 'success' : 'subtle' }}" style="cursor: pointer; border: none;">
                  {{ $brand->is_active ? 'Active' : 'Disabled' }}
                </button>
              </form>
            </td>
            <td style="text-align: right;">
              <div style="display: inline-flex; gap: 0.5rem;">
                <a href="{{ route('admin.asdreams.brands.edit', $brand) }}" class="btn-table-action" title="Edit Brand">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </a>
                <form action="{{ route('admin.asdreams.brands.destroy', $brand) }}" method="POST" onsubmit="return confirm('Delete this brand card?');" style="display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-table-action btn-table-action--delete" title="Delete Brand">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 3rem 1rem;">
              No brands found. Click "Add New Brand" to create one.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

