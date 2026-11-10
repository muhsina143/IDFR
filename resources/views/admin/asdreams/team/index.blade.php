@extends('admin.layouts.app')

@section('title', 'Team & Photographers Management')
@section('page_title', 'Studio Team')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Studio <em>Team &amp; Photographers</em></h2>
    <p class="module-subtitle">Manage creative directors, lead fashion photographers, cinematographers, and colorists shown on the Team page.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.asdreams.team.create') }}" class="btn-gold">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add Team Member</span>
    </a>
  </div>
</div>

<!-- Filters -->
<div class="admin-card filter-card" style="margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('admin.asdreams.team.index') }}" class="filter-form">
    <div class="filter-group filter-group--search" style="flex: 2;">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or designation..." class="filter-input" />
    </div>

    <div class="filter-group">
      <select name="status" class="filter-select" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Published Only</option>
        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Hidden Only</option>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Filter</button>
      @if(request()->hasAny(['search', 'status']))
        <a href="{{ route('admin.asdreams.team.index') }}" class="btn-reset">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Team Table -->
<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 50px;">Order</th>
          <th style="width: 80px;">Portrait</th>
          <th>Full Name &amp; Role</th>
          <th>Designation</th>
          <th>Socials</th>
          <th>Status</th>
          <th style="text-align: right; width: 140px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($team as $member)
          <tr>
            <td style="color: var(--text-muted); font-weight: 600;">#{{ $member->order }}</td>
            <td>
              <div style="width: 55px; height: 70px; border-radius: 4px; overflow: hidden; background: #000; border: 1px solid var(--border-subtle);">
                <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" style="width: 100%; height: 100%; object-fit: cover;" />
              </div>
            </td>
            <td>
              <strong style="color: #fff; font-size: 0.95rem; display: block;">{{ $member->name }}</strong>
              @if($member->bio)
                <span style="color: var(--text-muted); font-size: 0.78rem; display: block; max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                  {{ $member->bio }}
                </span>
              @endif
            </td>
            <td>
              <span class="badge" style="background: rgba(245, 200, 66, 0.12); color: var(--gold); border: 1px solid var(--gold-a35);">
                {{ $member->designation }}
              </span>
            </td>
            <td>
              <div style="display: flex; gap: 0.5rem; font-size: 0.78rem; color: var(--text-muted);">
                @if($member->instagram)
                  <a href="{{ $member->instagram }}" target="_blank" style="color: var(--gold);" title="Instagram">IG</a>
                @endif
                @if($member->linkedin)
                  <a href="{{ $member->linkedin }}" target="_blank" style="color: #8bb4e7;" title="LinkedIn">IN</a>
                @endif
              </div>
            </td>
            <td>
              <form action="{{ route('admin.asdreams.team.toggle-status', $member) }}" method="POST" style="margin: 0;">
                @csrf
                @method('PATCH')
                <button type="submit" style="background: none; border: none; cursor: pointer; padding: 0;">
                  @if($member->is_published)
                    <span class="status-badge status-badge--active">Published</span>
                  @else
                    <span class="status-badge status-badge--inactive">Hidden</span>
                  @endif
                </button>
              </form>
            </td>
            <td style="text-align: right;">
              <div class="table-actions" style="justify-content: flex-end;">
                <a href="{{ route('admin.asdreams.team.edit', $member) }}" class="btn-action" title="Edit Member">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </a>
                <form action="{{ route('admin.asdreams.team.destroy', $member) }}" method="POST" onsubmit="return confirm('Remove team member {{ $member->name }}?');" style="display: inline-block;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-action btn-action--delete" title="Delete Member">
                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
              No team members found.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($team->hasPages())
    <div style="margin-top: 1.5rem;">
      {{ $team->links() }}
    </div>
  @endif
</div>
@endsection
