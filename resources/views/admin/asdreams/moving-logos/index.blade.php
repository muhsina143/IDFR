@extends('admin.layouts.app')

@section('title', 'ASDreamStudio — Moving Client Logos')
@section('page_title', 'Moving Client Logos')

@section('styles')
<style>
  .asd-strip-preview {
    background: #000;
    border: 1px solid var(--border-subtle);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    overflow: hidden;
    position: relative;
  }
  .asd-strip-track {
    display: flex;
    align-items: center;
    gap: 3rem;
    width: max-content;
    animation: asdMarquee 30s linear infinite;
  }
  .asd-strip-track span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 140px;
    color: #fff;
    opacity: 0.65;
    transition: opacity 0.3s, color 0.3s;
    font-size: 0.85rem;
    text-align: center;
  }
  .asd-strip-track span:hover {
    opacity: 1;
    color: var(--gold);
  }
  .asd-strip-preview:hover .asd-strip-track {
    animation-play-state: paused;
  }
  @keyframes asdMarquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
  }
</style>
@endsection

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">ASDreamStudio <em>Moving Client Logos</em></h2>
    <p class="module-subtitle">Manage logos displayed in the continuously scrolling marquee strip at the bottom of the ASDreams website independently from Our Clients.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.asdreams.moving-logos.create') }}" class="btn-gold">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add New Moving Logo</span>
    </a>
  </div>
</div>

<!-- Live Strip Animation Preview -->
<div class="asd-strip-preview">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 0.5rem;">
    <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--gold); font-weight: 700;">Live Moving Strip Preview (Hover to pause)</span>
    <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $activeLogosForPreview->count() }} Active Logos in Strip</span>
  </div>

  @if($activeLogosForPreview->isNotEmpty())
    <div class="asd-strip-track">
      @foreach($activeLogosForPreview->concat($activeLogosForPreview) as $previewLogo)
        <span>
          @if($previewLogo->logo_url)
            <img src="{{ $previewLogo->logo_url }}" alt="{{ $previewLogo->name }}" style="max-height: 40px; max-width: 130px; width: auto; height: auto; object-fit: contain;" />
          @else
            <div style="border: 1px solid rgba(229, 184, 61, 0.35); border-radius: 4px; padding: 5px 10px; font-weight: 700; color: var(--gold); font-size: 0.75rem; line-height: 1.15;">
              <span>{{ $previewLogo->logo_mark ?: strtoupper(substr($previewLogo->name, 0, 1)) }}</span><br>
              <span style="font-size: 0.6rem; letter-spacing: 0.1em; color: #fff;">{{ Str::limit($previewLogo->logo_word ?: $previewLogo->name, 12) }}</span>
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
  <form method="GET" action="{{ route('admin.asdreams.moving-logos.index') }}" class="filter-form">
    <div class="filter-group filter-group--search" style="flex: 2;">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by logo name..." class="filter-input" />
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
        <option value="order" {{ request('sort', 'order') === 'order' ? 'selected' : '' }}>Display Order</option>
        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Logo Name</option>
        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Filter</button>
      @if(request()->hasAny(['search', 'status', 'sort']))
        <a href="{{ route('admin.asdreams.moving-logos.index') }}" class="btn-reset">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Logos Table -->
<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 65px;">Order</th>
          <th style="width: 150px;">Logo Preview</th>
          <th>Logo Name</th>
          <th style="width: 120px;">Status</th>
          <th style="width: 140px; text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logos as $logo)
          <tr>
            <td>
              <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--gold); font-weight: 700;">#{{ $logo->display_order }}</span>
            </td>
            <td>
              <div style="width: 130px; height: 52px; background: #000; border: 1px solid var(--border-subtle); border-radius: 4px; display: flex; align-items: center; justify-content: center; padding: 6px;">
                @if($logo->logo_url)
                  <img src="{{ $logo->logo_url }}" alt="{{ $logo->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;" />
                @else
                  <div style="font-size: 0.72rem; color: var(--gold); text-align: center; font-weight: 600; line-height: 1.15;">
                    <span>{{ $logo->logo_mark ?: strtoupper(substr($logo->name, 0, 1)) }}</span><br>
                    <span style="font-size: 0.6rem; color: var(--text-muted);">{{ Str::limit($logo->logo_word ?: $logo->name, 12) }}</span>
                  </div>
                @endif
              </div>
            </td>
            <td>
              <strong style="color: #fff; font-size: 0.98rem;">{{ $logo->name }}</strong>
            </td>
            <td>
              <form action="{{ route('admin.asdreams.moving-logos.toggle-status', $logo) }}" method="POST">
                @csrf
                <button type="submit" class="status-toggle-btn {{ $logo->is_active ? 'is-active' : 'is-inactive' }}" title="Click to toggle status">
                  <span class="status-dot"></span>
                  <span>{{ $logo->is_active ? 'Active' : 'Inactive' }}</span>
                </button>
              </form>
            </td>
            <td style="text-align: right;">
              <div style="display: inline-flex; gap: 0.5rem; justify-content: flex-end;">
                <a href="{{ route('admin.asdreams.moving-logos.edit', $logo) }}" class="btn-action btn-action--edit" title="Edit Logo">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </a>
                <button type="button" class="btn-action btn-action--delete" onclick="confirmAsdLogoDelete('{{ $logo->id }}', '{{ addslashes($logo->name) }}')" title="Delete Logo">
                  <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
                <form id="deleteAsdLogoForm-{{ $logo->id }}" action="{{ route('admin.asdreams.moving-logos.destroy', $logo) }}" method="POST" style="display: none;">
                  @csrf
                  @method('DELETE')
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" style="text-align: center; padding: 3rem 1rem;">
              <p style="color: var(--text-muted); font-size: 1rem; margin-bottom: 1rem;">No moving client logos added yet.</p>
              <a href="{{ route('admin.asdreams.moving-logos.create') }}" class="btn-gold" style="display: inline-flex;">Add First Moving Logo</a>
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
<div class="admin-modal" id="deleteAsdLogoModal" style="display: none;">
  <div class="admin-modal__backdrop" onclick="closeAsdLogoDeleteModal()"></div>
  <div class="admin-modal__panel" style="max-width: 440px;">
    <h3 style="color: #fff; font-family: var(--font-serif); font-size: 1.35rem; margin-bottom: 0.75rem;">Confirm Delete</h3>
    <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem;">
      Are you sure you want to remove <strong id="deleteAsdLogoName" style="color: var(--gold);"></strong> from Moving Client Logos? This will not affect any Our Clients cards.
    </p>
    <div style="display: flex; gap: 1rem; justify-content: flex-end;">
      <button type="button" class="btn-subtle" onclick="closeAsdLogoDeleteModal()">Cancel</button>
      <button type="button" class="btn-danger" id="confirmAsdLogoDeleteBtn">Delete Logo</button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  let targetAsdLogoId = null;

  function confirmAsdLogoDelete(id, name) {
    targetAsdLogoId = id;
    document.getElementById('deleteAsdLogoName').textContent = name;
    document.getElementById('deleteAsdLogoModal').style.display = 'flex';
  }

  function closeAsdLogoDeleteModal() {
    document.getElementById('deleteAsdLogoModal').style.display = 'none';
    targetAsdLogoId = null;
  }

  document.getElementById('confirmAsdLogoDeleteBtn').addEventListener('click', function () {
    if (targetAsdLogoId) {
      document.getElementById('deleteAsdLogoForm-' + targetAsdLogoId).submit();
    }
  });
</script>
@endsection

