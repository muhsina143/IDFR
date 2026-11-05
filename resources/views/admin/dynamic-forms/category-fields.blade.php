@extends('admin.layouts.app')

@section('title', 'Manage Form Fields | ' . $category->name)
@section('header_title', 'Category Form Fields')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <div style="margin-bottom: 0.5rem;">
      <a href="{{ route('admin.dynamic-forms.index') }}" style="color: var(--gold); text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.35rem;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        <span>Back to Form Categories</span>
      </a>
    </div>
    <h2 class="module-title">Form Fields for <em>{{ $category->name }}</em></h2>
    <p class="module-subtitle">Reorder fields, configure custom labels or placeholders specifically for this profession, and toggle active status.</p>
  </div>
  <div class="module-header__actions" style="display: flex; gap: 0.75rem;">
    <a href="{{ route('admin.dynamic-forms.preview', $category->slug) }}" target="_blank" class="btn-subtle" style="display: inline-flex; align-items: center; gap: 0.5rem;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
      <span>Live Preview</span>
    </a>
  </div>
</div>

<!-- Add Existing Field to this Category -->
@if($availableFields->isNotEmpty())
  <div class="admin-card" style="padding: 1.5rem; margin-bottom: 2rem; background: rgba(229,184,61,0.03); border: 1px dashed rgba(229,184,61,0.3);">
    <form method="POST" action="{{ route('admin.dynamic-forms.categories.fields.attach', $category) }}" style="display: flex; flex-wrap: wrap; align-items: flex-end; gap: 1rem;">
      @csrf
      <div style="flex: 1; min-width: 250px;">
        <label for="field_id" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">
          Attach Field from Library
        </label>
        <select id="field_id" name="field_id" class="filter-select" style="width: 100%;" required>
          <option value="">-- Choose an available field --</option>
          @foreach($availableFields as $af)
            <option value="{{ $af->id }}">{{ $af->label }} ({{ $af->field_key }} &middot; {{ $af->field_type }})</option>
          @endforeach
        </select>
      </div>

      <div style="width: 120px;">
        <label for="attach_order" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">
          Order #
        </label>
        <input type="number" id="attach_order" name="display_order" class="filter-input" style="width: 100%;" placeholder="e.g. 75" />
      </div>

      <div style="display: flex; align-items: center; gap: 0.5rem; padding-bottom: 0.6rem;">
        <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; color: #cbd5e1; font-size: 0.85rem;">
          <input type="checkbox" name="is_required" value="1" style="accent-color: var(--gold);" />
          <span>Required</span>
        </label>
      </div>

      <div>
        <button type="submit" class="btn-gold" style="padding: 0.65rem 1.25rem;">
          Add to {{ $category->name }} Form
        </button>
      </div>
    </form>
  </div>
@endif

<!-- Fields Reorder and Overrides Table -->
<div class="admin-card">
  <form method="POST" action="{{ route('admin.dynamic-forms.categories.fields.update', $category) }}">
    @csrf
    @method('PUT')

    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: space-between;">
      <div>
        <h3 style="margin: 0; font-size: 1.15rem; color: #fff; font-family: var(--font-serif);">Active Fields Configuration</h3>
        <p style="margin: 0.25rem 0 0; font-size: 0.85rem; color: var(--text-muted);">Adjust display order, edit label overrides or custom placeholders, and manage field requirements.</p>
      </div>
      <div>
        <button type="submit" class="btn-gold" style="padding: 0.6rem 1.5rem;">
          Save All Changes
        </button>
      </div>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th width="75">Order</th>
            <th width="140">Field Key &amp; Type</th>
            <th>Label (Override)</th>
            <th>Placeholder (Override)</th>
            <th width="90" style="text-align: center;">Required</th>
            <th width="90" style="text-align: center;">Active</th>
            <th width="80" style="text-align: center;">Remove</th>
          </tr>
        </thead>
        <tbody>
          @foreach($assignedPivots as $pivot)
            @php $field = $pivot->field; @endphp
            <tr>
              <td>
                <input
                  type="number"
                  name="pivots[{{ $pivot->id }}][display_order]"
                  value="{{ $pivot->display_order }}"
                  class="filter-input"
                  style="width: 60px; text-align: center; font-weight: 600; color: var(--gold); padding: 4px;"
                />
              </td>
              <td>
                <div style="font-weight: 600; color: #fff; font-size: 0.9rem;">{{ $field->field_key }}</div>
                <div style="margin-top: 3px;">
                  <span class="badge" style="font-size: 0.7rem; background: rgba(255,255,255,0.08); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.15);">
                    {{ $field->field_type }}
                  </span>
                  @if($field->is_common)
                    <span class="badge" style="font-size: 0.65rem; background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3);">
                      common
                    </span>
                  @endif
                </div>
              </td>
              <td>
                <input
                  type="text"
                  name="pivots[{{ $pivot->id }}][label_override]"
                  value="{{ $pivot->label_override ?: $field->label }}"
                  class="filter-input"
                  style="width: 100%; padding: 6px 10px; font-size: 0.85rem;"
                  placeholder="{{ $field->label }}"
                />
                @if(in_array($field->field_type, ['checkbox_group', 'radio_group', 'select'], true))
                  @php
                    $pOpts = $pivot->options_override;
                    if (is_string($pOpts)) {
                      $pOpts = json_decode($pOpts, true) ?: [];
                    }
                    if (empty($pOpts) && !empty($field->options)) {
                      $pOpts = is_string($field->options) ? (json_decode($field->options, true) ?: []) : $field->options;
                    }
                    $lines = [];
                    if (is_array($pOpts)) {
                      foreach ($pOpts as $k => $v) {
                        $lines[] = "{$k}: {$v}";
                      }
                    }
                    $optsText = implode("\n", $lines);
                  @endphp
                  <details style="margin-top: 6px;">
                    <summary style="font-size: 0.75rem; color: var(--gold); cursor: pointer; user-select: none;">
                      &#9881; Configure Options ({{ is_array($pOpts) ? count($pOpts) : 0 }})
                    </summary>
                    <div style="margin-top: 6px;">
                      <textarea
                        name="pivots[{{ $pivot->id }}][options_override]"
                        rows="4"
                        class="filter-input"
                        style="width: 100%; font-size: 0.75rem; font-family: monospace; padding: 6px; line-height: 1.4;"
                        placeholder="slug: Label (one per line)&#10;e.g.&#10;fashion-show: Fashion Show Coverage&#10;luxury-wedding: Luxury Wedding Planning"
                      >{{ $optsText }}</textarea>
                      <small style="color: var(--text-muted); font-size: 0.7rem; display: block; margin-top: 2px;">
                        One option per line in <code>key: Label</code> format.
                      </small>
                    </div>
                  </details>
                @endif
              </td>
              <td>
                <input
                  type="text"
                  name="pivots[{{ $pivot->id }}][placeholder_override]"
                  value="{{ $pivot->placeholder_override !== null ? $pivot->placeholder_override : $field->placeholder }}"
                  class="filter-input"
                  style="width: 100%; padding: 6px 10px; font-size: 0.85rem;"
                  placeholder="{{ $field->placeholder }}"
                />
              </td>
              <td style="text-align: center;">
                <input
                  type="checkbox"
                  name="pivots[{{ $pivot->id }}][is_required]"
                  value="1"
                  {{ ($pivot->is_required !== null ? $pivot->is_required : $field->is_required) ? 'checked' : '' }}
                  style="width: 18px; height: 18px; accent-color: var(--gold);"
                />
              </td>
              <td style="text-align: center;">
                <input
                  type="checkbox"
                  name="pivots[{{ $pivot->id }}][is_active]"
                  value="1"
                  {{ $pivot->is_active ? 'checked' : '' }}
                  style="width: 18px; height: 18px; accent-color: #4ade80;"
                />
              </td>
              <td style="text-align: center;">
                @if(!$field->is_common)
                  <button
                    type="button"
                    onclick="if(confirm('Remove this field from {{ $category->name }} form?')) document.getElementById('detach-form-{{ $pivot->id }}').submit();"
                    class="btn-subtle"
                    style="padding: 4px 8px; color: #f87171; border: 1px solid rgba(239,68,68,0.3); border-radius: 4px;"
                    title="Remove field from category"
                  >
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                  </button>
                @else
                  <span style="color: var(--text-muted); font-size: 0.75rem;" title="Core common field cannot be detached">—</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--border-subtle); display: flex; justify-content: flex-end; gap: 1rem;">
      <a href="{{ route('admin.dynamic-forms.index') }}" class="btn-subtle" style="padding: 0.65rem 1.5rem; text-decoration: none;">Back</a>
      <button type="submit" class="btn-gold" style="padding: 0.65rem 2rem;">
        Save All Changes
      </button>
    </div>
  </form>
</div>

@foreach($assignedPivots as $pivot)
  <form id="detach-form-{{ $pivot->id }}" action="{{ route('admin.dynamic-forms.categories.fields.detach', ['category' => $category, 'field' => $pivot->field_id]) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
  </form>
@endforeach
@endsection

