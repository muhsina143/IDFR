@extends('admin.layouts.app')

@section('title', 'Our Clients & Testimonials Management')
@section('header_title', 'Our Clients & Testimonials')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Our Clients &amp; <em>Testimonials</em></h2>
    <p class="module-subtitle">Manage client fashion houses, publications, and agencies collaborating with IDFR talent. Select which client quote is featured on the homepage.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.clients.create') }}" class="btn-gold">
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add New Client Testimonial</span>
    </a>
  </div>
</div>

<!-- Search & Filter Card -->
<div class="admin-card filter-card" style="margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('admin.clients.index') }}" class="filter-form">
    <div class="filter-group filter-group--search" style="flex: 2;">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by client, industry, author name, or testimonial..." class="filter-input" />
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
        <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>Featured Testimonial Only</option>
      </select>
    </div>

    <div class="filter-group">
      <select name="sort" class="filter-select" onchange="this.form.submit()">
        <option value="order" {{ request('sort') === 'order' ? 'selected' : '' }}>Display Order</option>
        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Client Name</option>
        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Filter</button>
      @if(request()->hasAny(['search', 'status', 'featured', 'sort']))
        <a href="{{ route('admin.clients.index') }}" class="btn-reset">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Clients Table Card -->
<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 50px;">Order</th>
          <th style="width: 100px;">Photo</th>
          <th style="width: 120px;">Logo</th>
          <th>Client &amp; Author</th>
          <th>Testimonial Quote</th>
          <th style="width: 130px;">Featured</th>
          <th style="width: 100px;">Status</th>
          <th style="width: 150px; text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($clients as $client)
          <tr style="{{ $client->is_featured ? 'background: rgba(200, 169, 126, 0.06);' : '' }}">
            <td>
              <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--gold); font-weight: 700;">#{{ $client->display_order }}</span>
            </td>
            <td>
              <div style="width: 80px; height: 100px; border-radius: 4px; overflow: hidden; background: #000; border: 1px solid var(--border-subtle);">
                <img src="{{ $client->image_url }}" alt="{{ $client->name }}" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
            </td>
            <td>
              <div style="width: 100px; height: 48px; background: rgba(0,0,0,0.5); border: 1px solid var(--border-subtle); border-radius: 4px; display: flex; align-items: center; justify-content: center; padding: 4px;">
                @if($client->logo_url)
                  <img src="{{ $client->logo_url }}" alt="{{ $client->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                @else
                  <div style="font-size: 0.75rem; color: var(--gold); text-align: center; font-weight: 600; line-height: 1.1;">
                    <span>{{ $client->logo_mark ?: substr($client->name, 0, 1) }}</span><br>
                    <span style="font-size: 0.6rem; color: var(--text-muted);">{{ Str::limit($client->logo_word ?: $client->name, 10) }}</span>
                  </div>
                @endif
              </div>
            </td>
            <td>
              <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-weight: 600; font-size: 1rem; color: #fff;">{{ $client->name }}</span>
                @if($client->industry)
                  <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--gold); font-size: 0.7rem;">{{ $client->industry }}</span>
                @endif
              </div>
              @if($client->author_name || $client->author_role)
                <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0.25rem 0 0;">
                  <strong>{{ $client->author_name }}</strong>
                  @if($client->author_role) — <em>{{ $client->author_role }}</em> @endif
                </p>
              @endif
              @if($client->website_url)
                <a href="{{ $client->website_url }}" target="_blank" rel="noopener" style="font-size: 0.75rem; color: var(--gold); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; margin-top: 4px;">
                  <span>{{ $client->website_url }}</span>
                  <svg style="width: 12px; height: 12px;" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                </a>
              @endif
            </td>
            <td>
              @if($client->testimonial_quote)
                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.4; margin: 0; font-style: italic;">“{{ Str::limit($client->testimonial_quote, 110) }}”</p>
              @else
                <span style="color: var(--text-muted); font-size: 0.8rem;">No quote provided</span>
              @endif
            </td>
            <td>
              @if($client->is_featured)
                <span class="badge" style="background: var(--gold); color: #000; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                  <svg style="width: 12px; height: 12px;" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                  Featured
                </span>
              @else
                <form action="{{ route('admin.clients.set-featured', $client) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn-subtle" style="font-size: 0.75rem; padding: 0.35rem 0.65rem;" title="Set as featured testimonial">Set Featured</button>
                </form>
              @endif
            </td>
            <td>
              <form action="{{ route('admin.clients.toggle-status', $client) }}" method="POST">
                @csrf
                <button type="submit" class="status-toggle-btn {{ $client->is_active ? 'is-active' : 'is-inactive' }}" title="Click to toggle status">
                  <span class="status-dot"></span>
                  <span>{{ $client->is_active ? 'Active' : 'Inactive' }}</span>
                </button>
              </form>
            </td>
            <td style="text-align: right;">
              <div style="display: inline-flex; gap: 0.5rem; justify-content: flex-end;">
                <a href="{{ route('admin.clients.edit', $client) }}" class="btn-action btn-action--edit" title="Edit Client">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </a>
                <button type="button" class="btn-action btn-action--delete" onclick="confirmDelete('{{ $client->id }}', '{{ addslashes($client->name) }}')" title="Delete Client">
                  <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
                <form id="deleteForm-{{ $client->id }}" action="{{ route('admin.clients.destroy', $client) }}" method="POST" style="display: none;">
                  @csrf
                  @method('DELETE')
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" style="text-align: center; padding: 3rem 1rem;">
              <p style="color: var(--text-muted); font-size: 1rem; margin-bottom: 1rem;">No clients or testimonials found.</p>
              <a href="{{ route('admin.clients.create') }}" class="btn-gold" style="display: inline-flex;">Add First Client</a>
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
<div class="admin-modal" id="deleteModal" style="display: none;">
  <div class="admin-modal__backdrop" onclick="closeDeleteModal()"></div>
  <div class="admin-modal__panel" style="max-width: 440px;">
    <h3 style="color: #fff; font-family: var(--font-serif); font-size: 1.35rem; margin-bottom: 0.75rem;">Confirm Delete</h3>
    <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem;">
      Are you sure you want to delete client testimonial <strong id="deleteClientName" style="color: var(--gold);"></strong>? This will remove the client quote and logo immediately.
    </p>
    <div style="display: flex; gap: 1rem; justify-content: flex-end;">
      <button type="button" class="btn-subtle" onclick="closeDeleteModal()">Cancel</button>
      <button type="button" class="btn-danger" id="confirmDeleteBtn">Delete Client</button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  let targetClientId = null;

  function confirmDelete(id, name) {
    targetClientId = id;
    document.getElementById('deleteClientName').textContent = name;
    document.getElementById('deleteModal').style.display = 'flex';
  }

  function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    targetClientId = null;
  }

  document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (targetClientId) {
      document.getElementById('deleteForm-' + targetClientId).submit();
    }
  });
</script>
@endsection
