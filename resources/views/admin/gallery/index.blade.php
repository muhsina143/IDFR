@extends('admin.layouts.app')

@section('title', 'Gallery & Media Manager')
@section('header_title', 'Gallery & Media Manager')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Gallery &amp; <em>Media Manager</em></h2>
    <p class="module-subtitle">Manage high-resolution runway looks, backstage moments, and curated press photography across all seasons.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.gallery.create') }}" class="btn-gold">
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add Runway Look</span>
    </a>
  </div>
</div>

<!-- Quick Stats Bar -->
<div class="stats-overview-row">
  <div class="stat-pill">
    <span class="stat-pill__label">Total Media</span>
    <span class="stat-pill__val">{{ number_format($totalCount) }}</span>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Live on Website</span>
    <span class="stat-pill__val stat-pill__val--gold">{{ number_format($publishedCount) }}</span>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Runway Seasons</span>
    <span class="stat-pill__val">{{ $seasons->count() }}</span>
  </div>
</div>

<!-- Season Tab Bar -->
<div class="category-tabs-bar">
  <a href="{{ route('admin.gallery', array_merge(request()->except('season', 'page'))) }}" 
     class="category-tab {{ !request('season') || request('season') === 'all' ? 'is-active' : '' }}">
    All Seasons ({{ $totalCount }})
  </a>
  @foreach($seasons as $s)
    <a href="{{ route('admin.gallery', array_merge(request()->except('page'), ['season' => $s->title])) }}" 
       class="category-tab {{ request('season') === $s->title ? 'is-active' : '' }}">
      {{ $s->title }}
    </a>
  @endforeach
</div>

<!-- Search & Filter Bar -->
<div class="admin-card filter-card">
  <form method="GET" action="{{ route('admin.gallery') }}" class="filter-form">
    <input type="hidden" name="season" value="{{ request('season') }}">

    <div class="filter-group filter-group--search">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by caption, runway look, or alt text..." class="filter-input" />
    </div>

    <div class="filter-group">
      <select name="status" class="filter-select" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
      </select>
    </div>

    <div class="filter-group">
      <select name="sort" class="filter-select" onchange="this.form.submit()">
        <option value="order" {{ request('sort') === 'order' ? 'selected' : '' }}>Display Order</option>
        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest Uploads</option>
        <option value="featured" {{ request('sort') === 'featured' ? 'selected' : '' }}>Featured First</option>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Filter</button>
      @if(request()->hasAny(['search', 'status', 'sort', 'season']))
        <a href="{{ route('admin.gallery') }}" class="btn-reset">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Gallery Grid -->
<div class="gallery-admin-grid">
  @forelse($images as $img)
    <div class="gallery-card">
      <div class="gallery-card__preview">
        <img src="{{ $img->image_url }}" alt="{{ $img->title }}" loading="lazy" />
        <span class="gallery-season-tag">{{ $img->season_name }}</span>
        @if($img->is_featured)
          <span class="gallery-featured-tag">★ Featured</span>
        @endif
        <div class="gallery-status-overlay">
          @if($img->status === 'published')
            <span class="status-badge status-badge--active">Published</span>
          @elseif($img->status === 'draft')
            <span class="status-badge status-badge--pending">Draft</span>
          @else
            <span class="status-badge status-badge--archived">Archived</span>
          @endif
        </div>
      </div>
      
      <div class="gallery-card__body">
        <h4 class="gallery-card__title">{{ $img->title }}</h4>
        <div class="gallery-card__meta">
          <span>{{ $img->width }} × {{ $img->height }} px</span>
          <span>Order: #{{ $img->display_order }}</span>
        </div>
      </div>

      <div class="gallery-card__actions">
        <a href="{{ route('admin.gallery.edit', $img->id) }}" class="btn-action btn-action--edit" title="Edit Photo">
          <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
          <span>Edit</span>
        </a>

        <form action="{{ route('admin.gallery.destroy', $img->id) }}" method="POST" onsubmit="return confirm('Delete this gallery photo (\'{{ addslashes($img->title) }}\')? This action cannot be undone.');">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn-action btn-action--delete" title="Delete Photo">
            <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
            <span>Delete</span>
          </button>
        </form>
      </div>
    </div>
  @empty
    <div class="admin-card empty-card" style="grid-column: 1 / -1;">
      <div class="empty-state">
        <svg viewBox="0 0 24 24" class="empty-icon"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
        <h3 class="empty-title">No gallery photographs found</h3>
        <p class="empty-desc">No images match your current filter parameters. Try clearing your filters or upload a new runway photo.</p>
        <a href="{{ route('admin.gallery.create') }}" class="btn-gold" style="margin-top: 1rem;">Upload Runway Look</a>
      </div>
    </div>
  @endforelse
</div>

<!-- Pagination -->
<div class="pagination-wrapper">
  {{ $images->links('pagination::simple-bootstrap-5') }}
</div>

<style>
  .module-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
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
    padding: 1.25rem;
    margin-bottom: 1.5rem;
  }
  .filter-form {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    align-items: center;
  }
  .filter-group--search {
    flex: 1;
    min-width: 250px;
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
  .filter-select {
    padding: 0.55rem 1.85rem 0.55rem 0.85rem;
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
    display: flex;
    align-items: center;
  }
  .gallery-admin-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }
  .gallery-card {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform 0.2s, border-color 0.2s;
  }
  .gallery-card:hover {
    transform: translateY(-3px);
    border-color: rgba(229, 184, 61, 0.4);
  }
  .gallery-card__preview {
    position: relative;
    width: 100%;
    height: 240px;
    background: #000;
    overflow: hidden;
  }
  .gallery-card__preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
  }
  .gallery-card:hover .gallery-card__preview img {
    transform: scale(1.04);
  }
  .gallery-season-tag {
    position: absolute;
    top: 0.75rem;
    left: 0.75rem;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(4px);
    padding: 0.25rem 0.6rem;
    border-radius: 4px;
    font-size: 0.75rem;
    color: var(--gold);
    border: 1px solid rgba(229, 184, 61, 0.3);
    font-weight: 500;
  }
  .gallery-featured-tag {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    background: rgba(229, 184, 61, 0.9);
    color: #000;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 700;
  }
  .gallery-status-overlay {
    position: absolute;
    bottom: 0.75rem;
    left: 0.75rem;
  }
  .gallery-card__body {
    padding: 1rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .gallery-card__title {
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
  }
  .gallery-card__meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    color: var(--text-muted);
  }
  .gallery-card__actions {
    display: flex;
    border-top: 1px solid var(--border-subtle);
    background: rgba(255, 255, 255, 0.02);
  }
  .gallery-card__actions .btn-action {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    padding: 0.65rem;
    font-size: 0.8rem;
    text-decoration: none;
    border: none;
    background: transparent;
    cursor: pointer;
    transition: background 0.2s;
  }
  .gallery-card__actions form {
    flex: 1;
    display: flex;
  }
  .gallery-card__actions .btn-action--edit {
    color: var(--gold);
    border-right: 1px solid var(--border-subtle);
  }
  .gallery-card__actions .btn-action--edit:hover {
    background: rgba(229, 184, 61, 0.1);
  }
  .gallery-card__actions .btn-action--delete {
    color: #f87171;
    width: 100%;
  }
  .gallery-card__actions .btn-action--delete:hover {
    background: rgba(239, 68, 68, 0.1);
  }
  .gallery-card__actions svg {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    stroke-width: 2;
    fill: none;
  }
  .empty-card {
    padding: 3rem;
    text-align: center;
  }
  .empty-icon {
    width: 48px;
    height: 48px;
    stroke: var(--text-muted);
    stroke-width: 1.5;
    fill: none;
    margin-bottom: 1rem;
  }
  .empty-title {
    font-size: 1.15rem;
    color: var(--text-primary);
    margin-bottom: 0.4rem;
  }
  .empty-desc {
    font-size: 0.85rem;
    color: var(--text-muted);
    max-width: 450px;
    margin: 0 auto;
  }
  .pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 1rem;
  }
</style>
@endsection
