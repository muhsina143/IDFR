@extends('admin.layouts.app')

@section('title', 'Form Fields Library | IDFR Admin')
@section('header_title', 'Form Fields Library')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <div style="margin-bottom: 0.5rem;">
      <a href="{{ route('admin.dynamic-forms.index') }}" style="color: var(--gold); text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.35rem;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        <span>Back to Form Categories</span>
      </a>
    </div>
    <h2 class="module-title">Form Fields <em>Library</em></h2>
    <p class="module-subtitle">Manage shared common fields and custom dynamic inputs across all creative professional categories.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.dynamic-forms.fields.create') }}" class="btn-gold" style="display: inline-flex; align-items: center; gap: 0.5rem;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Create New Field</span>
    </a>
  </div>
</div>

<!-- Search & Filter Card -->
<div class="admin-card filter-card" style="margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('admin.dynamic-forms.fields.index') }}" class="filter-form">
    <div class="filter-group filter-group--search">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by label, key, or placeholder..." class="filter-input" />
    </div>

    <div class="filter-group">
      <select name="type" class="filter-select" onchange="this.form.submit()">
        <option value="">All Field Types</option>
        <option value="text" {{ request('type') === 'text' ? 'selected' : '' }}>Text Input</option>
        <option value="number" {{ request('type') === 'number' ? 'selected' : '' }}>Number</option>
        <option value="tel" {{ request('type') === 'tel' ? 'selected' : '' }}>Telephone</option>
        <option value="email" {{ request('type') === 'email' ? 'selected' : '' }}>Email</option>
        <option value="combobox" {{ request('type') === 'combobox' ? 'selected' : '' }}>Combobox</option>
        <option value="textarea" {{ request('type') === 'textarea' ? 'selected' : '' }}>Textarea</option>
        <option value="checkbox_group" {{ request('type') === 'checkbox_group' ? 'selected' : '' }}>Checkbox Group</option>
        <option value="radio_group" {{ request('type') === 'radio_group' ? 'selected' : '' }}>Radio Group</option>
        <option value="select" {{ request('type') === 'select' ? 'selected' : '' }}>Select Dropdown</option>
      </select>
    </div>

    @if(isset($allCategories) && $allCategories->isNotEmpty())
      <div class="filter-group">
        <select name="category" class="filter-select" onchange="this.form.submit()">
          <option value="">All Categories</option>
          @foreach($allCategories as $c)
            <option value="{{ $c->slug }}" {{ request('category') === $c->slug ? 'selected' : '' }}>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
    @endif

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Filter</button>
      @if(request()->anyFilled(['search', 'type', 'category']))
        <a href="{{ route('admin.dynamic-forms.fields.index') }}" class="btn-reset">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Fields Table -->
<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th width="70">Order</th>
          <th>Field Label &amp; Database Key</th>
          <th width="140">Type &amp; Width</th>
          <th width="140">Scope</th>
          <th width="120" style="text-align: center;">Categories</th>
          <th width="90" style="text-align: center;">Required</th>
          <th width="90" style="text-align: center;">Status</th>
          <th width="140" style="text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($fields as $field)
          <tr>
            <td>
              <span style="font-weight: 600; color: var(--gold); font-family: monospace;">#{{ $field->display_order }}</span>
            </td>
            <td>
              <div style="font-weight: 600; color: #fff; font-size: 0.95rem;">{{ $field->label }}</div>
              <div style="font-size: 0.8rem; color: var(--text-muted); font-family: monospace; margin-top: 2px;">
                <code>{{ $field->field_key }}</code>
              </div>
            </td>
            <td>
              <span class="badge" style="background: rgba(229,184,61,0.12); color: var(--gold); border: 1px solid rgba(229,184,61,0.3); font-size: 0.75rem; text-transform: uppercase;">
                {{ str_replace('_', ' ', $field->field_type) }}
              </span>
              @php
                $optsCount = 0;
                if (!empty($field->options)) {
                    $cOpts = is_string($field->options) ? json_decode($field->options, true) : $field->options;
                    $optsCount = is_array($cOpts) ? count($cOpts) : 0;
                }
              @endphp
              @if($optsCount > 0)
                <div style="font-size: 0.72rem; color: var(--gold); margin-top: 3px; font-weight: 500;">
                  &#9679; {{ $optsCount }} choices
                </div>
              @endif
              <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">
                Width: {{ $field->width === 'full' ? 'Full (100%)' : 'Half (50%)' }}
              </div>
            </td>
            <td>
              @if($field->is_common)
                <span class="badge" style="background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); font-size: 0.75rem;">
                  Shared Common
                </span>
              @else
                <span class="badge" style="background: rgba(168,85,247,0.15); color: #c084fc; border: 1px solid rgba(168,85,247,0.3); font-size: 0.75rem;">
                  Category-Specific
                </span>
              @endif
            </td>
            <td style="text-align: center;">
              @if($field->is_common)
                <span class="badge" style="background: rgba(59,130,246,0.12); color: #93c5fd; border: 1px solid rgba(59,130,246,0.3); font-size: 0.72rem;">
                  All Professions
                </span>
              @elseif($field->categories->isNotEmpty())
                @if($field->categories->count() <= 2)
                  @foreach($field->categories as $c)
                    <span class="badge" style="background: rgba(229,184,61,0.15); color: #fde047; border: 1px solid rgba(229,184,61,0.3); font-size: 0.72rem; display: inline-block; margin: 2px 0;">
                      {{ $c->name }}
                    </span>
                  @endforeach
                @else
                  <span class="badge" style="background: rgba(255,255,255,0.08); color: #e2e8f0; border: 1px solid rgba(255,255,255,0.15); font-size: 0.75rem;" title="{{ $field->categories->pluck('name')->join(', ') }}">
                    {{ $field->categories->count() }} categories
                  </span>
                @endif
              @else
                <span style="color: var(--text-muted); font-size: 0.75rem;">None</span>
              @endif
            </td>
            <td style="text-align: center;">
              @if($field->is_required)
                <span style="color: var(--gold); font-weight: 600; font-size: 0.8rem;">Yes</span>
              @else
                <span style="color: var(--text-muted); font-size: 0.8rem;">Optional</span>
              @endif
            </td>
            <td style="text-align: center;">
              @if($field->is_active)
                <span class="badge" style="background: rgba(34,197,94,0.15); color: #4ade80; border: 1px solid rgba(34,197,94,0.3); padding: 3px 8px; border-radius: 20px; font-size: 0.75rem;">
                  Active
                </span>
              @else
                <span class="badge" style="background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3); padding: 3px 8px; border-radius: 20px; font-size: 0.75rem;">
                  Inactive
                </span>
              @endif
            </td>
            <td style="text-align: right;">
              <div style="display: inline-flex; align-items: center; gap: 0.35rem;">
                <a href="{{ route('admin.dynamic-forms.fields.edit', $field) }}" class="btn-subtle" style="padding: 5px 9px; border-radius: 5px; border: 1px solid rgba(255,255,255,0.15);" title="Edit Field">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </a>

                <form action="{{ route('admin.dynamic-forms.fields.toggle-status', $field) }}" method="POST" style="display:inline;">
                  @csrf
                  <button type="submit" class="btn-subtle" style="padding: 5px 9px; border-radius: 5px; border: 1px solid rgba(255,255,255,0.15); color: {{ $field->is_active ? '#f87171' : '#4ade80' }};" title="{{ $field->is_active ? 'Deactivate' : 'Activate' }}">
                    @if($field->is_active)
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                    @else
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    @endif
                  </button>
                </form>

                @if(!$field->is_common)
                  <form action="{{ route('admin.dynamic-forms.fields.destroy', $field) }}" method="POST" style="display:inline;" onsubmit="return confirm('Permanently delete custom field \'{{ $field->label }}\'?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-subtle" style="padding: 5px 9px; border-radius: 5px; border: 1px solid rgba(239,68,68,0.3); color: #f87171;" title="Delete Field">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-muted);">
              No fields found matching your criteria.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($fields->hasPages())
    <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--border-subtle);">
      {{ $fields->links() }}
    </div>
  @endif
</div>
@endsection

