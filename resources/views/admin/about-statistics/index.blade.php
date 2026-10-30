@extends('admin.layouts.app')

@section('title', 'About Statistics Management')
@section('header_title', 'About Page Statistics')

@section('styles')
<style>
  .stats-summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
  }

  .stat-summary-card {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: 8px;
    padding: 1.25rem 1.5rem;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s ease, border-color 0.2s ease;
  }

  .stat-summary-card:hover {
    border-color: var(--border-gold);
    transform: translateY(-2px);
  }

  .stat-summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 3px;
    height: 100%;
    background: var(--gold);
    opacity: 0.8;
  }

  .stat-summary-card__title {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: var(--text-muted);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .stat-summary-card__val {
    font-size: 2rem;
    font-weight: 700;
    color: #fff;
    line-height: 1.1;
    font-family: var(--font-serif);
  }

  .stat-summary-card__hint {
    font-size: 0.75rem;
    color: var(--gold);
    margin-top: 0.35rem;
  }

  /* Live About Stats Preview */
  .about-stats-live-preview {
    background: radial-gradient(circle at 50% 0%, rgba(229, 184, 61, 0.08) 0%, transparent 70%), #000;
    border: 1px solid var(--border-subtle);
    border-radius: 8px;
    padding: 1.75rem 2rem;
    margin-bottom: 2rem;
  }

  .preview-header-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.25rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    padding-bottom: 0.75rem;
  }

  .preview-badge {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    color: var(--gold);
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .preview-badge::before {
    content: '';
    display: inline-block;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #4ade80;
    box-shadow: 0 0 8px #4ade80;
  }

  .about-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    border-top: 1px solid rgba(200, 169, 126, 0.35);
    border-left: 1px solid rgba(200, 169, 126, 0.35);
    background: #000;
  }

  .about-preview-cell {
    display: flex;
    flex-direction: column-reverse;
    gap: 0.35rem;
    padding: 1.25rem 1.4rem;
    border-right: 1px solid rgba(200, 169, 126, 0.35);
    border-bottom: 1px solid rgba(200, 169, 126, 0.35);
    position: relative;
    transition: background 0.2s ease;
  }

  .about-preview-cell:hover {
    background: rgba(229, 184, 61, 0.04);
  }

  .about-preview-title {
    font-size: 0.68rem;
    letter-spacing: 0.25em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
  }

  .about-preview-value {
    font-size: clamp(1.8rem, 1.4rem + 1.2vw, 2.75rem);
    line-height: 1;
    color: var(--gold);
    font-family: var(--font-serif);
    font-weight: 600;
  }

  .about-preview-badge-order {
    position: absolute;
    top: 6px;
    right: 8px;
    font-size: 0.6rem;
    color: var(--text-muted);
    letter-spacing: 0.1em;
  }

  .reorder-input {
    width: 58px;
    padding: 0.3rem 0.4rem;
    background: rgba(0, 0, 0, 0.6);
    border: 1px solid var(--border-subtle);
    border-radius: 4px;
    color: var(--gold);
    font-weight: 700;
    text-align: center;
    font-size: 0.85rem;
  }

  .reorder-input:focus {
    border-color: var(--gold);
    outline: none;
  }
</style>
@endsection

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">About Page <em>Statistics</em></h2>
    <p class="module-subtitle">Manage the dynamic numerical highlights displayed on the public About page. Control exact values, leading zeros, prefixes, suffixes, display sequence, and publication status.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('idfr.about') }}#about" target="_blank" class="btn-subtle" style="display: inline-flex; align-items: center; gap: 0.5rem;" title="View Live Public About Page">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
      <span>Live About Page</span>
    </a>
    <a href="{{ route('admin.about-statistics.create') }}" class="btn-gold">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add New Statistic</span>
    </a>
  </div>
</div>

<!-- Stats Summary Cards -->
<div class="stats-summary-grid">
  <div class="stat-summary-card">
    <div class="stat-summary-card__title">
      <span>Total Statistics</span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
    </div>
    <div class="stat-summary-card__val">{{ $totalCount }}</div>
    <div class="stat-summary-card__hint">Total records in database</div>
  </div>

  <div class="stat-summary-card">
    <div class="stat-summary-card__title">
      <span>Active On Website</span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
    </div>
    <div class="stat-summary-card__val" style="color: #4ade80;">{{ $activeCount }}</div>
    <div class="stat-summary-card__hint">Visible on public About page</div>
  </div>

  <div class="stat-summary-card">
    <div class="stat-summary-card__title">
      <span>Inactive (Hidden)</span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
    </div>
    <div class="stat-summary-card__val" style="color: #f87171;">{{ $inactiveCount }}</div>
    <div class="stat-summary-card__hint">Draft or archived cards</div>
  </div>
</div>

<!-- Live About Page Visual Preview -->
<div class="about-stats-live-preview">
  <div class="preview-header-bar">
    <span class="preview-badge">Live Public About Page Preview</span>
    <span style="font-size: 0.75rem; color: var(--text-muted);">
      {{ $statistics->where('is_active', true)->count() }} active card{{ $statistics->where('is_active', true)->count() === 1 ? '' : 's' }} in sequence
    </span>
  </div>

  @php
    $previewStats = $statistics->where('is_active', true)->sortBy('display_order');
  @endphp

  @if($previewStats->isNotEmpty())
    <div class="about-preview-grid">
      @foreach($previewStats as $stat)
        <div class="about-preview-cell" title="Order #{{ $stat->display_order }}">
          <span class="about-preview-badge-order">#{{ $stat->display_order }}</span>
          <span class="about-preview-title">{{ $stat->title }}</span>
          <span class="about-preview-value">
            {{ $stat->prefix }}<span>{{ $stat->formatted_value }}</span>{{ $stat->suffix }}
          </span>
        </div>
      @endforeach
    </div>
  @else
    <div style="text-align: center; padding: 2rem 1rem; color: var(--text-muted); font-size: 0.9rem;">
      <p>No active statistics currently enabled. Enable or add cards below to display them on the About page.</p>
    </div>
  @endif
</div>

<!-- Search & Filter Card -->
<div class="admin-card filter-card" style="margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('admin.about-statistics.index') }}" class="filter-form">
    <div class="filter-group filter-group--search" style="flex: 2;">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, number value, prefix, or suffix..." class="filter-input" />
    </div>

    <div class="filter-group">
      <select name="status" class="filter-select" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active Only</option>
        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive Only</option>
      </select>
    </div>

    <div class="filter-group">
      <select name="sort" class="filter-select" onchange="this.form.submit()">
        <option value="order" {{ request('sort') === 'order' ? 'selected' : '' }}>Display Order</option>
        <option value="title" {{ request('sort') === 'title' ? 'selected' : '' }}>Statistic Title</option>
        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest Created</option>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Filter</button>
      @if(request()->hasAny(['search', 'status', 'sort']))
        <a href="{{ route('admin.about-statistics.index') }}" class="btn-reset">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Statistics Table Card -->
<div class="admin-card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
    <h3 style="color: #fff; font-size: 1.05rem; font-family: var(--font-serif); font-weight: 600;">Configured Statistics</h3>
    <button type="button" class="btn-subtle" id="saveOrderBtn" style="font-size: 0.8rem; display: none;" onclick="saveReorder()">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
      <span>Save Display Order</span>
    </button>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 80px;">Order</th>
          <th style="width: 140px;">Value</th>
          <th>Statistic Title</th>
          <th style="width: 120px;">Affixes</th>
          <th style="width: 160px;">Formatted Display</th>
          <th style="width: 120px;">Status</th>
          <th style="width: 140px; text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody id="statsTableBody">
        @forelse($statistics as $statistic)
          <tr data-id="{{ $statistic->id }}">
            <td>
              <input type="number" class="reorder-input" data-stat-id="{{ $statistic->id }}" value="{{ $statistic->display_order }}" min="0" onchange="showSaveOrderBtn()" />
            </td>
            <td>
              <div style="font-size: 1.25rem; font-family: var(--font-serif); font-weight: 700; color: var(--gold);">
                {{ $statistic->value }}
              </div>
              @if($statistic->pad_length > 0)
                <span style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                  Leading Zero (Pad {{ $statistic->pad_length }})
                </span>
              @endif
            </td>
            <td>
              <div style="font-weight: 600; font-size: 0.95rem; color: #fff; letter-spacing: 0.05em;">
                {{ $statistic->title }}
              </div>
              <span style="font-size: 0.7rem; color: var(--text-muted);">
                Database ID: #{{ $statistic->id }}
              </span>
            </td>
            <td>
              <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                @if($statistic->prefix)
                  <span class="badge" style="background: rgba(229, 184, 61, 0.12); color: var(--gold); border: 1px solid rgba(229, 184, 61, 0.3);">
                    Prefix: "{{ $statistic->prefix }}"
                  </span>
                @endif
                @if($statistic->suffix)
                  <span class="badge" style="background: rgba(229, 184, 61, 0.12); color: var(--gold); border: 1px solid rgba(229, 184, 61, 0.3);">
                    Suffix: "{{ $statistic->suffix }}"
                  </span>
                @endif
                @if(!$statistic->prefix && !$statistic->suffix)
                  <span style="color: var(--text-muted); font-size: 0.75rem;">None</span>
                @endif
              </div>
            </td>
            <td>
              <div style="font-size: 1.15rem; font-weight: 600; color: #fff; font-family: var(--font-serif);">
                {{ $statistic->full_display }}
              </div>
            </td>
            <td>
              <form action="{{ route('admin.about-statistics.toggle-status', $statistic) }}" method="POST">
                @csrf
                <button type="submit" class="status-toggle-btn {{ $statistic->is_active ? 'is-active' : 'is-inactive' }}" title="Click to toggle Active/Inactive">
                  <span class="status-dot"></span>
                  <span>{{ $statistic->is_active ? 'Active' : 'Inactive' }}</span>
                </button>
              </form>
            </td>
            <td style="text-align: right;">
              <div style="display: inline-flex; gap: 0.5rem; justify-content: flex-end;">
                <a href="{{ route('admin.about-statistics.edit', $statistic) }}" class="btn-action btn-action--edit" title="Edit Statistic">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </a>
                <button type="button" class="btn-action btn-action--delete" onclick="confirmDelete('{{ $statistic->id }}', '{{ addslashes($statistic->title) }}', '{{ addslashes($statistic->full_display) }}')" title="Delete Statistic">
                  <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
                <form id="deleteForm-{{ $statistic->id }}" action="{{ route('admin.about-statistics.destroy', $statistic) }}" method="POST" style="display: none;">
                  @csrf
                  @method('DELETE')
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align: center; padding: 3rem 1rem;">
              <p style="color: var(--text-muted); font-size: 1rem; margin-bottom: 1rem;">No statistics found matching your criteria.</p>
              <a href="{{ route('admin.about-statistics.create') }}" class="btn-gold" style="display: inline-flex;">Create First Statistic</a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($statistics->hasPages())
    <div style="padding: 1.5rem; border-top: 1px solid var(--border-subtle);">
      {{ $statistics->links() }}
    </div>
  @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="admin-modal" id="deleteModal" style="display: none;">
  <div class="admin-modal__backdrop" onclick="closeDeleteModal()"></div>
  <div class="admin-modal__panel" style="max-width: 440px;">
    <h3 style="color: #fff; font-family: var(--font-serif); font-size: 1.35rem; margin-bottom: 0.75rem;">Confirm Delete</h3>
    <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem;">
      Are you sure you want to delete statistic <strong id="deleteStatTitle" style="color: var(--gold);"></strong> (<span id="deleteStatDisplay" style="color: #fff;"></span>)?
      <br><span style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem; display: block;">This card will immediately be removed from the About page.</span>
    </p>
    <div style="display: flex; gap: 1rem; justify-content: flex-end;">
      <button type="button" class="btn-subtle" onclick="closeDeleteModal()">Cancel</button>
      <button type="button" class="btn-danger" id="confirmDeleteBtn">Delete Statistic</button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  let targetStatId = null;

  function confirmDelete(id, title, display) {
    targetStatId = id;
    document.getElementById('deleteStatTitle').textContent = title;
    document.getElementById('deleteStatDisplay').textContent = display;
    document.getElementById('deleteModal').style.display = 'flex';
  }

  function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    targetStatId = null;
  }

  document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (targetStatId) {
      document.getElementById('deleteForm-' + targetStatId).submit();
    }
  });

  function showSaveOrderBtn() {
    document.getElementById('saveOrderBtn').style.display = 'inline-flex';
  }

  function saveReorder() {
    const inputs = document.querySelectorAll('.reorder-input');
    const orders = [];
    inputs.forEach(input => {
      orders.push({
        id: input.dataset.statId,
        display_order: input.value
      });
    });

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('admin.about-statistics.reorder') }}";

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = "{{ csrf_token() }}";
    form.appendChild(csrf);

    orders.forEach((item, index) => {
      const idInput = document.createElement('input');
      idInput.type = 'hidden';
      idInput.name = `orders[${index}][id]`;
      idInput.value = item.id;
      form.appendChild(idInput);

      const orderInput = document.createElement('input');
      orderInput.type = 'hidden';
      orderInput.name = `orders[${index}][display_order]`;
      orderInput.value = item.display_order;
      form.appendChild(orderInput);
    });

    document.body.appendChild(form);
    form.submit();
  }
</script>
@endsection
