@extends('admin.layouts.app')

@section('title', 'ASDreamStudio — Our Clients')
@section('page_title', 'Our Clients')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">ASDreamStudio <em>Our Clients</em></h2>
    <p class="module-subtitle">Manage client showcase cards, featured images, logos, categories, and client stories independently.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.asdreams.clients.create') }}" class="btn-gold">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add New Client</span>
    </a>
  </div>
</div>

<!-- Search & Filter Card -->
<div class="admin-card filter-card" style="margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('admin.asdreams.clients.index') }}" class="filter-form">
    <div class="filter-group filter-group--search" style="flex: 2;">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by client name, category, or story..." class="filter-input" />
    </div>

    <div class="filter-group">
      <select name="status" class="filter-select" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
      </select>
    </div>

    <div class="filter-group">
      <select name="featured" class="filter-select" onchange="this.form.submit()">
        <option value="">All Clients</option>
        <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>Featured Only</option>
      </select>
    </div>

    <div class="filter-group">
      <select name="sort" class="filter-select" onchange="this.form.submit()">
        <option value="order" {{ request('sort', 'order') === 'order' ? 'selected' : '' }}>Display Order</option>
        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Client Name</option>
        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Filter</button>
      @if(request()->hasAny(['search', 'status', 'featured', 'sort']))
        <a href="{{ route('admin.asdreams.clients.index') }}" class="btn-reset">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Clients Table -->
<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 65px;">Order</th>
          <th style="width: 95px;">Featured Img</th>
          <th style="width: 115px;">Client Logo</th>
          <th>Client Name &amp; Category</th>
          <th>Client Description / Story</th>
          <th style="width: 120px;">Default</th>
          <th style="width: 110px;">Status</th>
          <th style="width: 140px; text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($clients as $client)
          <tr style="{{ $client->is_featured ? 'background: rgba(229, 184, 61, 0.06);' : '' }}">
            <td>
              <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--gold); font-weight: 700;">#{{ $client->display_order }}</span>
            </td>
            <td>
              <div style="width: 76px; height: 95px; border-radius: 4px; overflow: hidden; background: #000; border: 1px solid var(--border-subtle);">
                <img src="{{ $client->featured_image_url }}" alt="{{ $client->name }}" style="width: 100%; height: 100%; object-fit: cover;" />
              </div>
            </td>
            <td>
              <div style="width: 100px; height: 48px; background: rgba(0,0,0,0.5); border: 1px solid var(--border-subtle); border-radius: 4px; display: flex; align-items: center; justify-content: center; padding: 4px;">
                @if($client->logo_url)
                  <img src="{{ $client->logo_url }}" alt="{{ $client->name }} logo" style="max-width: 100%; max-height: 100%; object-fit: contain;" />
                @else
                  <div style="font-size: 0.72rem; color: var(--gold); text-align: center; font-weight: 600; line-height: 1.15;">
                    <span>{{ $client->logo_mark ?: strtoupper(substr($client->name, 0, 1)) }}</span><br>
                    <span style="font-size: 0.6rem; color: var(--text-muted);">{{ Str::limit($client->logo_word ?: $client->name, 10) }}</span>
                  </div>
                @endif
              </div>
            </td>
            <td>
              <strong style="color: #fff; font-size: 0.98rem; display: block;">{{ $client->name }}</strong>
              @if($client->category)
                <span class="badge" style="background: rgba(255,255,255,0.06); color: var(--gold); font-size: 0.7rem; margin-top: 0.25rem; display: inline-block;">{{ $client->category }}</span>
              @endif
              @if($client->author_name)
                <span style="display: block; font-size: 0.78rem; color: var(--text-secondary); margin-top: 0.25rem;">
                  {{ $client->author_name }}@if($client->author_role) &mdash; <em>{{ $client->author_role }}</em>@endif
                </span>
              @endif
            </td>
            <td style="max-width: 280px;">
              @if($client->description)
                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.4; margin: 0; font-style: italic; overflow-wrap: anywhere;">&ldquo;{{ Str::limit($client->description, 110) }}&rdquo;</p>
              @else
                <span style="color: var(--text-muted); font-size: 0.8rem;">No description</span>
              @endif
            </td>
            <td>
              @if($client->is_featured)
                <span class="badge" style="background: var(--gold); color: #000; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                  Featured
                </span>
              @else
                <form action="{{ route('admin.asdreams.clients.set-featured', $client) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn-subtle" style="font-size: 0.72rem; padding: 0.3rem 0.6rem;">Set Featured</button>
                </form>
              @endif
            </td>
            <td>
              <form action="{{ route('admin.asdreams.clients.toggle-status', $client) }}" method="POST">
                @csrf
                <button type="submit" class="status-toggle-btn {{ $client->is_active ? 'is-active' : 'is-inactive' }}" title="Click to toggle status">
                  <span class="status-dot"></span>
                  <span>{{ $client->is_active ? 'Active' : 'Inactive' }}</span>
                </button>
              </form>
            </td>
            <td style="text-align: right;">
              <div style="display: inline-flex; gap: 0.5rem; justify-content: flex-end;">
                <a href="{{ route('admin.asdreams.clients.edit', $client) }}" class="btn-action btn-action--edit" title="Edit Client">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </a>
                <button type="button" class="btn-action btn-action--delete" onclick="confirmAsdClientDelete('{{ $client->id }}', '{{ addslashes($client->name) }}')" title="Delete Client">
                  <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
                <form id="deleteAsdClientForm-{{ $client->id }}" action="{{ route('admin.asdreams.clients.destroy', $client) }}" method="POST" style="display: none;">
                  @csrf
                  @method('DELETE')
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" style="text-align: center; padding: 3rem 1rem;">
              <p style="color: var(--text-muted); font-size: 1rem; margin-bottom: 1rem;">No clients added yet for ASDreams.</p>
              <a href="{{ route('admin.asdreams.clients.create') }}" class="btn-gold" style="display: inline-flex;">Add First Client</a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($clients->hasPages())
    <div style="padding: 1.5rem; border-top: 1px solid var(--border-subtle);">
      {{ $clients->links() }}
    </div>
  @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="admin-modal" id="deleteAsdClientModal" style="display: none;">
  <div class="admin-modal__backdrop" onclick="closeAsdClientDeleteModal()"></div>
  <div class="admin-modal__panel" style="max-width: 440px;">
    <h3 style="color: #fff; font-family: var(--font-serif); font-size: 1.35rem; margin-bottom: 0.75rem;">Confirm Delete</h3>
    <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem;">
      Are you sure you want to delete <strong id="deleteAsdClientName" style="color: var(--gold);"></strong> from ASDreams Our Clients? This will remove the client card and featured story immediately without affecting Moving Client Logos.
    </p>
    <div style="display: flex; gap: 1rem; justify-content: flex-end;">
      <button type="button" class="btn-subtle" onclick="closeAsdClientDeleteModal()">Cancel</button>
      <button type="button" class="btn-danger" id="confirmAsdClientDeleteBtn">Delete Client</button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  let targetAsdClientId = null;

  function confirmAsdClientDelete(id, name) {
    targetAsdClientId = id;
    document.getElementById('deleteAsdClientName').textContent = name;
    document.getElementById('deleteAsdClientModal').style.display = 'flex';
  }

  function closeAsdClientDeleteModal() {
    document.getElementById('deleteAsdClientModal').style.display = 'none';
    targetAsdClientId = null;
  }

  document.getElementById('confirmAsdClientDeleteBtn').addEventListener('click', function () {
    if (targetAsdClientId) {
      document.getElementById('deleteAsdClientForm-' + targetAsdClientId).submit();
    }
  });
</script>
@endsection

