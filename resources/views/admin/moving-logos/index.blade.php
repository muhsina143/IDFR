@extends('admin.layouts.app')

@section('title', 'Moving Client Logos Management')
@section('header_title', 'Moving Client Logos')

@section('styles')
<style>
  .admin-strip-preview {
    background: #000;
    border: 1px solid var(--border-subtle);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    overflow: hidden;
    position: relative;
  }
  .admin-strip-track {
    display: flex;
    align-items: center;
    gap: 3rem;
    width: max-content;
    animation: marquee 30s linear infinite;
  }
  .admin-strip-track span {
    display: block;
    width: 140px;
    color: #fff;
    opacity: 0.65;
    transition: opacity 0.3s, color 0.3s;
    font-size: 0.85rem;
    text-align: center;
  }
  .admin-strip-track span:hover {
    opacity: 1;
    color: var(--gold);
  }
  .admin-strip-preview:hover .admin-strip-track {
    animation-play-state: paused;
  }
  @keyframes marquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
  }
</style>
@endsection

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Moving Client &amp; <em>Sponsor Logos</em></h2>
    <p class="module-subtitle">Manage the dynamic, continuously moving horizontal logo marquee strip displayed above the homepage footer.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.moving-logos.create') }}" class="btn-gold">
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add Logo to Strip</span>
    </a>
  </div>
</div>

<!-- Live Strip Animation Preview -->
<div class="admin-strip-preview">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 0.5rem;">
    <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--gold); font-weight: 700;">Live Strip Animation Preview (Hover to pause)</span>
    <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $activeLogosForPreview->count() }} Active Logos in Loop</span>
  </div>

  @if($activeLogosForPreview->isNotEmpty())
    <div class="admin-strip-track">
      @foreach($activeLogosForPreview->concat($activeLogosForPreview) as $logo)
        <span>
          @if($logo->logo_url)
            <img src="{{ $logo->logo_url }}" alt="{{ $logo->company_name }}" style="max-height: 38px; max-width: 130px; object-fit: contain;">
          @else
            <div style="border: 1px solid rgba(200, 169, 126, 0.4); border-radius: 4px; padding: 4px 8px; font-weight: 700; color: var(--gold); font-size: 0.75rem; line-height: 1.1;">
              <span>{{ $logo->logo_mark ?: substr($logo->company_name, 0, 1) }}</span><br>
              <span style="font-size: 0.6rem; letter-spacing: 0.1em; color: #fff;">{{ Str::limit($logo->logo_word ?: $logo->company_name, 10) }}</span>
            </div>
          @endif
        </span>
      @endforeach
    </div>
  @else
    <p style="color: var(--text-muted); font-size: 0.85rem; text-align: center; margin: 1rem 0;">No active logos in the moving strip.</p>
  @endif
</div>

<!-- Search & Filter Card -->
<div class="admin-card filter-card" style="margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('admin.moving-logos.index') }}" class="filter-form">
    <div class="filter-group filter-group--search" style="flex: 2;">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by company name..." class="filter-input" />
    </div>

    <div class="filter-group">
      <select name="status" class="filter-select" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active in Strip</option>
        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
      </select>
    </div>

    <div class="filter-group">
      <select name="sort" class="filter-select" onchange="this.form.submit()">
        <option value="order" {{ request('sort') === 'order' ? 'selected' : '' }}>Display Order</option>
        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Company Name</option>
        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Filter</button>
      @if(request()->hasAny(['search', 'status', 'sort']))
        <a href="{{ route('admin.moving-logos.index') }}" class="btn-reset">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Logos Table Card -->
<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 50px;">Order</th>
          <th style="width: 140px;">Logo Badge</th>
          <th>Company Name</th>
          <th>Linked Entity</th>
          <th style="width: 100px;">Strip Status</th>
          <th style="width: 150px; text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logos as $logo)
          <tr>
            <td>
              <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--gold); font-weight: 700;">#{{ $logo->display_order }}</span>
            </td>
            <td>
              <div style="width: 120px; height: 50px; background: #000; border: 1px solid var(--border-subtle); border-radius: 4px; display: flex; align-items: center; justify-content: center; padding: 4px;">
                @if($logo->logo_url)
                  <img src="{{ $logo->logo_url }}" alt="{{ $logo->company_name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                @else
                  <div style="font-size: 0.75rem; color: var(--gold); text-align: center; font-weight: 600; line-height: 1.1;">
                    <span>{{ $logo->logo_mark ?: substr($logo->company_name, 0, 1) }}</span><br>
                    <span style="font-size: 0.6rem; color: var(--text-muted);">{{ Str::limit($logo->logo_word ?: $logo->company_name, 12) }}</span>
                  </div>
                @endif
              </div>
            </td>
            <td>
              <div style="font-weight: 600; font-size: 1rem; color: #fff;">{{ $logo->company_name }}</div>
              @if($logo->website_url)
                <a href="{{ $logo->website_url }}" target="_blank" rel="noopener" style="font-size: 0.75rem; color: var(--gold); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; margin-top: 4px;">
                  <span>{{ $logo->website_url }}</span>
                  <svg style="width: 12px; height: 12px;" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                </a>
              @endif
            </td>
            <td>
              @if($logo->client)
                <span class="badge" style="background: rgba(200, 169, 126, 0.15); color: var(--gold);">Client: {{ $logo->client->name }}</span>
              @elseif($logo->sponsor)
                <span class="badge" style="background: rgba(255,255,255,0.08); color: var(--text-secondary);">Sponsor: {{ $logo->sponsor->name }}</span>
              @else
                <span style="color: var(--text-muted); font-size: 0.8rem;">Standalone Logo</span>
              @endif
            </td>
            <td>
              <form action="{{ route('admin.moving-logos.toggle-status', $logo) }}" method="POST">
                @csrf
                <button type="submit" class="status-toggle-btn {{ $logo->is_active ? 'is-active' : 'is-inactive' }}" title="Click to toggle status">
                  <span class="status-dot"></span>
                  <span>{{ $logo->is_active ? 'Active' : 'Inactive' }}</span>
                </button>
              </form>
            </td>
            <td style="text-align: right;">
              <div style="display: inline-flex; gap: 0.5rem; justify-content: flex-end;">
                <a href="{{ route('admin.moving-logos.edit', $logo) }}" class="btn-action btn-action--edit" title="Edit Logo">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </a>
                <button type="button" class="btn-action btn-action--delete" onclick="confirmDelete('{{ $logo->id }}', '{{ addslashes($logo->company_name) }}')" title="Delete Logo">
                  <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
                <form id="deleteForm-{{ $logo->id }}" action="{{ route('admin.moving-logos.destroy', $logo) }}" method="POST" style="display: none;">
                  @csrf
                  @method('DELETE')
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="text-align: center; padding: 3rem 1rem;">
              <p style="color: var(--text-muted); font-size: 1rem; margin-bottom: 1rem;">No logos in the moving strip.</p>
              <a href="{{ route('admin.moving-logos.create') }}" class="btn-gold" style="display: inline-flex;">Add First Logo</a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($logos->hasPages())
    <div style="padding: 1.5rem; border-top: 1px solid var(--border-subtle);">
      {{ $logos->links() }}
    </div>
  @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="admin-modal" id="deleteModal" style="display: none;">
  <div class="admin-modal__backdrop" onclick="closeDeleteModal()"></div>
  <div class="admin-modal__panel" style="max-width: 440px;">
    <h3 style="color: #fff; font-family: var(--font-serif); font-size: 1.35rem; margin-bottom: 0.75rem;">Confirm Delete</h3>
    <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem;">
      Are you sure you want to remove logo <strong id="deleteLogoName" style="color: var(--gold);"></strong> from the moving strip?
    </p>
    <div style="display: flex; gap: 1rem; justify-content: flex-end;">
      <button type="button" class="btn-subtle" onclick="closeDeleteModal()">Cancel</button>
      <button type="button" class="btn-danger" id="confirmDeleteBtn">Delete Logo</button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  let targetLogoId = null;

  function confirmDelete(id, name) {
    targetLogoId = id;
    document.getElementById('deleteLogoName').textContent = name;
    document.getElementById('deleteModal').style.display = 'flex';
  }

  function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    targetLogoId = null;
  }

  document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (targetLogoId) {
      document.getElementById('deleteForm-' + targetLogoId).submit();
    }
  });
</script>
@endsection
