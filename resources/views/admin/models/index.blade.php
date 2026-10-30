@extends('admin.layouts.app')

@section('title', 'Model Talent Management')
@section('header_title', 'Model Talent Directory')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Fashion <em>Models</em></h2>
    <p class="module-subtitle">Manage runway talent, new faces, kid profiles, measurements, and season assignments.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.models.create') }}" class="btn-gold">
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add New Model</span>
    </a>
  </div>
</div>

<!-- Search & Filter Bar -->
<div class="admin-card filter-card">
  <form method="GET" action="{{ route('admin.models') }}" class="filter-form">
    <div class="filter-group filter-group--search">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, location, or bio..." class="filter-input" />
    </div>

    <div class="filter-group">
      <select name="gender" class="filter-select" onchange="this.form.submit()">
        <option value="">All Categories / Gender</option>
        <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
        <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
        <option value="kids" {{ request('gender') === 'kids' ? 'selected' : '' }}>Kids</option>
      </select>
    </div>

    <div class="filter-group">
      <select name="season_id" class="filter-select" onchange="this.form.submit()">
        <option value="">All Seasons</option>
        @foreach($seasons as $s)
          <option value="{{ $s->id }}" {{ request('season_id') == $s->id ? 'selected' : '' }}>{{ $s->title }} ({{ $s->year }})</option>
        @endforeach
      </select>
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
        <option value="featured" {{ request('sort') === 'featured' ? 'selected' : '' }}>Featured First</option>
        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name (A-Z)</option>
        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest Added</option>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Apply</button>
      @if(request()->anyFilled(['search', 'gender', 'season_id', 'status', 'sort']))
        <a href="{{ route('admin.models') }}" class="btn-reset">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Models Table -->
<div class="admin-card">
  @if($models->isEmpty())
    <div class="empty-state">
      <div class="empty-state__icon">
        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
      </div>
      <h3>No Models Found</h3>
      <p>No model profiles match the selected criteria. Try adjusting your search or filters.</p>
      <a href="{{ route('admin.models.create') }}" class="btn-gold">Create First Model</a>
    </div>
  @else
    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th width="70">Order</th>
            <th width="80">Photo</th>
            <th>Model Details</th>
            <th>Category & Level</th>
            <th>Location & Stats</th>
            <th>Seasons</th>
            <th>Status</th>
            <th width="120" class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($models as $model)
            <tr>
              <td class="text-muted font-mono">{{ $model->display_order }}</td>
              <td>
                <div class="model-avatar-preview">
                  <img src="{{ $model->photo_url }}" alt="{{ $model->name }}" />
                  @if($model->is_featured)
                    <span class="badge-star" title="Featured Model">★</span>
                  @endif
                </div>
              </td>
              <td>
                <div class="model-meta">
                  <strong class="model-name">{{ $model->name }}</strong>
                  <span class="model-slug">/{{ $model->slug }}</span>
                  @if(!empty($model->availability))
                    <span class="model-avail">{{ $model->availability }}</span>
                  @endif
                </div>
              </td>
              <td>
                <div class="tag-group">
                  <span class="badge-tag">{{ $model->gender }}</span>
                  <span class="badge-tag badge-tag--subtle">{{ $model->level }}</span>
                  @if($model->is_kid)
                    <span class="badge-tag badge-tag--gold">Kid</span>
                  @endif
                </div>
              </td>
              <td>
                <div class="stat-meta">
                  <span>{{ $model->location ?? '—' }}</span>
                  @if($model->height || $model->age)
                    <small class="text-muted">
                      {{ $model->height ? $model->height : '' }}
                      {{ $model->height && $model->age ? '·' : '' }}
                      {{ $model->age ? $model->age . ' yrs' : '' }}
                    </small>
                  @endif
                </div>
              </td>
              <td>
                <div class="season-badges">
                  @forelse($model->seasons as $s)
                    <span class="badge-season">{{ $s->title }}</span>
                  @empty
                    <span class="text-muted">—</span>
                  @endforelse
                </div>
              </td>
              <td>
                @if($model->status === 'published')
                  <span class="status-pill status-pill--published">Published</span>
                @elseif($model->status === 'draft')
                  <span class="status-pill status-pill--draft">Draft</span>
                @else
                  <span class="status-pill status-pill--archived">Archived</span>
                @endif
              </td>
              <td class="text-right">
                <div class="action-buttons">
                  <a href="{{ route('admin.models.edit', $model) }}" class="action-btn" title="Edit Profile">
                    <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                  </a>
                  <form action="{{ route('admin.models.destroy', $model) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete {{ $model->name }}? This action cannot be undone.');" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn action-btn--danger" title="Delete Profile">
                      <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="table-pagination">
      <div class="text-muted" style="font-size:0.85rem;">
        Showing {{ $models->firstItem() }} to {{ $models->lastItem() }} of {{ $models->total() }} models
      </div>
      <div>
        {{ $models->links() }}
      </div>
    </div>
  @endif
</div>
@endsection

@section('styles')
<style>
  .module-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 1.75rem;
    gap: 1rem;
    flex-wrap: wrap;
  }
  .module-title {
    font-family: var(--font-serif);
    font-size: 1.85rem;
    color: var(--text-primary);
    font-weight: 700;
  }
  .module-title em {
    font-style: italic;
    color: var(--gold);
    font-weight: 300;
  }
  .module-subtitle {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-top: 0.25rem;
  }
  .btn-gold {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--gold);
    color: #000;
    padding: 0.65rem 1.25rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.85rem;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .btn-gold:hover {
    background: #f0c54e;
    transform: translateY(-1px);
  }
  .btn-gold svg {
    width: 16px;
    height: 16px;
    stroke: currentColor;
    stroke-width: 2.2;
    fill: none;
  }
  .btn-subtle {
    background: #222;
    color: #fff;
    border: 1px solid var(--border-subtle);
    padding: 0.55rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.85rem;
  }
  .btn-reset {
    color: var(--text-muted);
    font-size: 0.8rem;
    text-decoration: underline;
    align-self: center;
    margin-left: 0.5rem;
  }
  .btn-reset:hover {
    color: var(--gold);
  }

  /* Filter card */
  .filter-card {
    margin-bottom: 1.5rem;
    padding: 1.25rem;
  }
  .filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: center;
  }
  .filter-group {
    flex: 1;
    min-width: 140px;
  }
  .filter-group--search {
    flex: 2;
    min-width: 220px;
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
    background: #000;
    border: 1px solid var(--border-subtle);
    border-radius: 6px;
    padding: 0.6rem 0.85rem 0.6rem 2.4rem;
    color: #fff;
    font-size: 0.85rem;
  }
  .filter-input:focus {
    outline: none;
    border-color: var(--gold);
  }
  .filter-select {
    width: 100%;
    background: #000;
    border: 1px solid var(--border-subtle);
    border-radius: 6px;
    padding: 0.6rem 0.85rem;
    color: #fff;
    font-size: 0.85rem;
    cursor: pointer;
  }
  .filter-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  /* Table styling */
  .admin-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
  }
  .admin-table th {
    text-align: left;
    padding: 1rem 0.85rem;
    border-bottom: 1px solid var(--border-subtle);
    color: var(--text-muted);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
  }
  .admin-table td {
    padding: 1rem 0.85rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    vertical-align: middle;
  }
  .admin-table tr:hover td {
    background: rgba(229, 184, 61, 0.02);
  }

  .model-avatar-preview {
    position: relative;
    width: 50px;
    height: 65px;
    border-radius: 4px;
    overflow: hidden;
    background: #000;
    border: 1px solid var(--border-subtle);
  }
  .model-avatar-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .badge-star {
    position: absolute;
    top: 2px;
    right: 2px;
    background: var(--gold);
    color: #000;
    font-size: 0.65rem;
    width: 14px;
    height: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-weight: bold;
  }
  .model-meta {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
  }
  .model-name {
    color: var(--text-primary);
    font-size: 0.95rem;
  }
  .model-slug {
    font-size: 0.75rem;
    color: var(--text-muted);
    font-family: monospace;
  }
  .model-avail {
    font-size: 0.75rem;
    color: #4ade80;
  }

  .tag-group {
    display: flex;
    gap: 0.35rem;
    flex-wrap: wrap;
  }
  .badge-tag {
    background: #1c1c1c;
    border: 1px solid var(--border-subtle);
    border-radius: 4px;
    padding: 0.2rem 0.5rem;
    font-size: 0.75rem;
    color: var(--text-primary);
  }
  .badge-tag--gold {
    background: rgba(229, 184, 61, 0.15);
    border-color: rgba(229, 184, 61, 0.4);
    color: var(--gold);
  }

  .stat-meta {
    display: flex;
    flex-direction: column;
    font-size: 0.85rem;
  }

  .season-badges {
    display: flex;
    gap: 0.35rem;
    flex-wrap: wrap;
  }
  .badge-season {
    background: rgba(229, 184, 61, 0.08);
    border: 1px solid rgba(229, 184, 61, 0.25);
    color: var(--gold);
    border-radius: 3px;
    padding: 0.15rem 0.45rem;
    font-size: 0.75rem;
  }

  .status-pill {
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: capitalize;
    display: inline-block;
  }
  .status-pill--published {
    background: rgba(34, 197, 94, 0.12);
    color: #4ade80;
    border: 1px solid rgba(34, 197, 94, 0.3);
  }
  .status-pill--draft {
    background: rgba(234, 179, 8, 0.12);
    color: #facc15;
    border: 1px solid rgba(234, 179, 8, 0.3);
  }
  .status-pill--archived {
    background: rgba(163, 163, 163, 0.12);
    color: #a3a3a3;
    border: 1px solid rgba(163, 163, 163, 0.3);
  }

  .action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
  }
  .action-btn {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    background: #1a1a1a;
    border: 1px solid var(--border-subtle);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
  }
  .action-btn:hover {
    background: #252525;
    color: var(--gold);
    border-color: var(--gold);
  }
  .action-btn svg {
    width: 15px;
    height: 15px;
    stroke: currentColor;
    stroke-width: 2;
    fill: none;
  }
  .action-btn--danger:hover {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    border-color: #ef4444;
  }

  .empty-state {
    text-align: center;
    padding: 3rem 1rem;
  }
  .empty-state__icon svg {
    width: 48px;
    height: 48px;
    stroke: var(--text-muted);
    stroke-width: 1.5;
    fill: none;
    margin-bottom: 1rem;
  }
  .empty-state h3 {
    font-size: 1.25rem;
    color: #fff;
    margin-bottom: 0.5rem;
  }
  .empty-state p {
    color: var(--text-muted);
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
  }

  .table-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 1.25rem;
    border-top: 1px solid var(--border-subtle);
    margin-top: 1rem;
    flex-wrap: wrap;
    gap: 1rem;
  }
</style>
@endsection
