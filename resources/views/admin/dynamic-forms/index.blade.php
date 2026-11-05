@extends('admin.layouts.app')

@section('title', 'Dynamic Form Builder | IDFR Admin')
@section('header_title', 'Dynamic Profile Forms')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Dynamic Form <em>Builder</em></h2>
    <p class="module-subtitle">Manage creative professional categories, customise dynamic form fields, set validation rules, and preview profile registration forms.</p>
  </div>
  <div class="module-header__actions" style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
    <a href="{{ route('admin.dynamic-forms.categories.create') }}" class="btn-gold" style="display: inline-flex; align-items: center; gap: 0.5rem;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
      <span>Add New Category</span>
    </a>
    <a href="{{ route('admin.dynamic-forms.fields.index') }}" class="btn-subtle" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; border-radius: 6px; border: 1px solid var(--border-subtle); color: var(--text-primary); text-decoration: none;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
      <span>Fields Library ({{ $totalFieldsCount }})</span>
    </a>
    <a href="{{ route('admin.dynamic-forms.fields.create') }}" class="btn-subtle" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; border-radius: 6px; border: 1px solid var(--border-subtle); color: var(--text-primary); text-decoration: none;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Create Form Field</span>
    </a>
  </div>
</div>

<!-- Stats Row -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
  <div class="admin-card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem;">
    <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(229,184,61,0.12); display: flex; align-items: center; justify-content: center; color: var(--gold);">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
    </div>
    <div>
      <div style="font-size: 1.75rem; font-weight: 700; color: #fff; line-height: 1.2;">{{ $categories->count() }}</div>
      <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Professional Categories</div>
    </div>
  </div>

  <div class="admin-card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem;">
    <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(34,197,94,0.12); display: flex; align-items: center; justify-content: center; color: #4ade80;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
    </div>
    <div>
      <div style="font-size: 1.75rem; font-weight: 700; color: #fff; line-height: 1.2;">{{ $categories->where('is_active', true)->count() }}</div>
      <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Active Form Categories</div>
    </div>
  </div>

  <div class="admin-card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem;">
    <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(99,102,241,0.12); display: flex; align-items: center; justify-content: center; color: #818cf8;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
    </div>
    <div>
      <div style="font-size: 1.75rem; font-weight: 700; color: #fff; line-height: 1.2;">{{ $totalFieldsCount }}</div>
      <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Total Defined Fields</div>
    </div>
  </div>

  <div class="admin-card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem;">
    <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(245,158,11,0.12); display: flex; align-items: center; justify-content: center; color: #fbbf24;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
    </div>
    <div>
      <div style="font-size: 1.75rem; font-weight: 700; color: #fff; line-height: 1.2;">100%</div>
      <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Dynamic Migration</div>
    </div>
  </div>
</div>

<!-- Categories Table -->
<div class="admin-card">
  <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: space-between;">
    <div>
      <h3 style="margin: 0; font-size: 1.15rem; color: #fff; font-family: var(--font-serif);">Creative Professional Categories</h3>
      <p style="margin: 0.25rem 0 0; font-size: 0.85rem; color: var(--text-muted);">Each category dynamically renders its assigned fields, specialisations, and custom inputs.</p>
    </div>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th width="70">Order</th>
          <th>Category &amp; Route Slug</th>
          <th>Form Header &amp; Lead</th>
          <th width="120" style="text-align: center;">Assigned Fields</th>
          <th width="110" style="text-align: center;">Status</th>
          <th width="260" style="text-align: right;">Form Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($categories as $cat)
          <tr>
            <td>
              <span style="font-weight: 600; color: var(--gold); font-family: monospace;">#{{ $cat->display_order }}</span>
            </td>
            <td>
              <div style="font-weight: 600; color: #fff; font-size: 1rem;">{{ $cat->name }}</div>
              <div style="font-size: 0.8rem; color: var(--text-muted); font-family: monospace; margin-top: 2px;">
                <code>/profiles/{{ $cat->slug }}</code>
              </div>
            </td>
            <td>
              <div style="color: #e2e8f0; font-size: 0.9rem; font-weight: 500;">{{ $cat->form_title }}</div>
              <div style="color: var(--text-muted); font-size: 0.8rem; line-height: 1.4; max-width: 460px; margin-top: 2px;">
                {{ Str::limit($cat->form_lead, 110) }}
              </div>
            </td>
            <td style="text-align: center;">
              <span class="badge" style="background: rgba(229,184,61,0.15); color: var(--gold); border: 1px solid rgba(229,184,61,0.3); font-weight: 600; padding: 4px 10px; border-radius: 20px;">
                {{ $cat->fields_count }} fields
              </span>
            </td>
            <td style="text-align: center;">
              @if($cat->is_active)
                <span class="badge" style="background: rgba(34,197,94,0.15); color: #4ade80; border: 1px solid rgba(34,197,94,0.3); padding: 4px 10px; border-radius: 20px; font-size: 0.75rem;">
                  ● Active
                </span>
              @else
                <span class="badge" style="background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3); padding: 4px 10px; border-radius: 20px; font-size: 0.75rem;">
                  ○ Disabled
                </span>
              @endif
            </td>
            <td style="text-align: right;">
              <div style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <a href="{{ route('admin.dynamic-forms.preview', $cat->slug) }}" target="_blank" title="Preview live form" class="btn-subtle" style="padding: 6px 10px; border-radius: 5px; font-size: 0.8rem; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(255,255,255,0.15);">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                  <span>Preview</span>
                </a>

                <a href="{{ route('admin.dynamic-forms.categories.fields', $cat) }}" title="Configure fields for this category" class="btn-subtle" style="padding: 6px 12px; border-radius: 5px; font-size: 0.8rem; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; background: rgba(229,184,61,0.1); border: 1px solid rgba(229,184,61,0.3); color: var(--gold);">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
                  <span>Fields ({{ $cat->fields_count }})</span>
                </a>

                <a href="{{ route('admin.dynamic-forms.categories.edit', $cat) }}" title="Edit category metadata" class="btn-subtle" style="padding: 6px 10px; border-radius: 5px; font-size: 0.8rem; text-decoration: none; border: 1px solid rgba(255,255,255,0.15);">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </a>

                <form action="{{ route('admin.dynamic-forms.categories.toggle-status', $cat) }}" method="POST" style="display:inline;">
                  @csrf
                  <button type="submit" class="btn-subtle" style="padding: 6px 10px; border-radius: 5px; font-size: 0.8rem; border: 1px solid rgba(255,255,255,0.15); color: {{ $cat->is_active ? '#f87171' : '#4ade80' }};" title="{{ $cat->is_active ? 'Disable Form' : 'Activate Form' }}">
                    @if($cat->is_active)
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                    @else
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    @endif
                  </button>
                </form>

                @php
                  $isCoreCategory = in_array($cat->slug, [
                    'makeup-artist',
                    'hairstylist',
                    'photographer',
                    'videographer',
                    'fashion-designer',
                    'drone-pilot',
                    'photo-video-editor',
                    'jewellery-designer',
                  ], true);
                @endphp
                @if(! $isCoreCategory)
                  <form action="{{ route('admin.dynamic-forms.categories.destroy', $cat) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete category \'{{ $cat->name }}\'? This will remove the category and its form configurations.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-subtle" style="padding: 6px 10px; border-radius: 5px; font-size: 0.8rem; border: 1px solid rgba(239,68,68,0.3); color: #f87171;" title="Delete Category">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection

