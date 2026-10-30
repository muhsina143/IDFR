@extends('admin.layouts.app')

@section('title', 'Our Partners Management')
@section('header_title', 'Our Partners Management')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Our <em>Partners</em></h2>
    <p class="module-subtitle">Manage official creative studios, ateliers, textiles, and production partners collaborating across IDFR runway shows.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.partners.create') }}" class="btn-gold">
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add New Partner</span>
    </a>
  </div>
</div>

<!-- Search & Filter Card -->
<div class="admin-card filter-card" style="margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('admin.partners.index') }}" class="filter-form">
    <div class="filter-group filter-group--search" style="flex: 2;">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by partner name, category, or description..." class="filter-input" />
    </div>

    <div class="filter-group">
      <select name="status" class="filter-select" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
      </select>
    </div>

    <div class="filter-group">
      <select name="sort" class="filter-select" onchange="this.form.submit()">
        <option value="order" {{ request('sort') === 'order' ? 'selected' : '' }}>Display Order</option>
        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Partner Name</option>
        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Filter</button>
      @if(request()->hasAny(['search', 'status', 'sort']))
        <a href="{{ route('admin.partners.index') }}" class="btn-reset">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Partners Table Card -->
<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 50px;">Order</th>
          <th style="width: 140px;">Card Image</th>
          <th style="width: 120px;">Logo</th>
          <th>Partner Details</th>
          <th>Category / Type</th>
          <th style="width: 100px;">Status</th>
          <th style="width: 150px; text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($partners as $partner)
          <tr>
            <td>
              <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--gold); font-weight: 700;">#{{ $partner->display_order }}</span>
            </td>
            <td>
              <div style="width: 120px; height: 75px; border-radius: 6px; overflow: hidden; background: #000; border: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: center;">
                <img src="{{ $partner->image_url }}" alt="{{ $partner->name }}" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
            </td>
            <td>
              <div style="width: 100px; height: 48px; background: rgba(0,0,0,0.5); border: 1px solid var(--border-subtle); border-radius: 4px; display: flex; align-items: center; justify-content: center; padding: 4px;">
                @if($partner->logo_url)
                  <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                @else
                  <div style="font-size: 0.75rem; color: var(--gold); text-align: center; font-weight: 600; line-height: 1.1;">
                    <span>{{ $partner->logo_mark ?: substr($partner->name, 0, 1) }}</span><br>
                    <span style="font-size: 0.6rem; color: var(--text-muted);">{{ Str::limit($partner->logo_word ?: $partner->name, 10) }}</span>
                  </div>
                @endif
              </div>
            </td>
            <td>
              <div style="font-weight: 600; font-size: 1rem; color: #fff;">{{ $partner->name }}</div>
              @if($partner->description)
                <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0.25rem 0 0; line-height: 1.4;">{{ Str::limit($partner->description, 90) }}</p>
              @endif
              @if($partner->website_url)
                <a href="{{ $partner->website_url }}" target="_blank" rel="noopener" style="font-size: 0.75rem; color: var(--gold); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; margin-top: 4px;">
                  <span>{{ $partner->website_url }}</span>
                  <svg style="width: 12px; height: 12px;" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                </a>
              @endif
            </td>
            <td>
              <span class="badge" style="background: rgba(200, 169, 126, 0.15); color: var(--gold); border: 1px solid rgba(200, 169, 126, 0.3);">
                {{ $partner->type }}
              </span>
            </td>
            <td>
              <form action="{{ route('admin.partners.toggle-status', $partner) }}" method="POST">
                @csrf
                <button type="submit" class="status-toggle-btn {{ $partner->is_active ? 'is-active' : 'is-inactive' }}" title="Click to toggle status">
                  <span class="status-dot"></span>
                  <span>{{ $partner->is_active ? 'Active' : 'Inactive' }}</span>
                </button>
              </form>
            </td>
            <td style="text-align: right;">
              <div style="display: inline-flex; gap: 0.5rem; justify-content: flex-end;">
                <a href="{{ route('admin.partners.edit', $partner) }}" class="btn-action btn-action--edit" title="Edit Partner">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </a>
                <button type="button" class="btn-action btn-action--delete" onclick="confirmDelete('{{ $partner->id }}', '{{ addslashes($partner->name) }}')" title="Delete Partner">
                  <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
                <form id="deleteForm-{{ $partner->id }}" action="{{ route('admin.partners.destroy', $partner) }}" method="POST" style="display: none;">
                  @csrf
                  @method('DELETE')
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align: center; padding: 3rem 1rem;">
              <p style="color: var(--text-muted); font-size: 1rem; margin-bottom: 1rem;">No partners found matching your criteria.</p>
              <a href="{{ route('admin.partners.create') }}" class="btn-gold" style="display: inline-flex;">Add First Partner</a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($partners->hasPages())
    <div style="padding: 1.5rem; border-top: 1px solid var(--border-subtle);">
      {{ $partners->links() }}
    </div>
  @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="admin-modal" id="deleteModal" style="display: none;">
  <div class="admin-modal__backdrop" onclick="closeDeleteModal()"></div>
  <div class="admin-modal__panel" style="max-width: 440px;">
    <h3 style="color: #fff; font-family: var(--font-serif); font-size: 1.35rem; margin-bottom: 0.75rem;">Confirm Delete</h3>
    <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem;">
      Are you sure you want to delete partner <strong id="deletePartnerName" style="color: var(--gold);"></strong>? This will remove the partner from the homepage immediately.
    </p>
    <div style="display: flex; gap: 1rem; justify-content: flex-end;">
      <button type="button" class="btn-subtle" onclick="closeDeleteModal()">Cancel</button>
      <button type="button" class="btn-danger" id="confirmDeleteBtn">Delete Partner</button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  let targetPartnerId = null;

  function confirmDelete(id, name) {
    targetPartnerId = id;
    document.getElementById('deletePartnerName').textContent = name;
    document.getElementById('deleteModal').style.display = 'flex';
  }

  function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    targetPartnerId = null;
  }

  document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (targetPartnerId) {
      document.getElementById('deleteForm-' + targetPartnerId).submit();
    }
  });
</script>
@endsection
