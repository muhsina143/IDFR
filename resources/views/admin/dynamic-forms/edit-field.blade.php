@extends('admin.layouts.app')

@section('title', 'Edit Field | ' . $field->label)
@section('header_title', 'Edit Form Field')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <div style="margin-bottom: 0.5rem;">
      <a href="{{ route('admin.dynamic-forms.fields.index') }}" style="color: var(--gold); text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.35rem;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        <span>Back to Fields Library</span>
      </a>
    </div>
    <h2 class="module-title">Edit Field: <em>{{ $field->label }}</em></h2>
    <p class="module-subtitle">Update field parameters, validation rules, choices and category assignments.</p>
  </div>
</div>

<div class="admin-card" style="max-width: 900px; padding: 2rem;">
  <form method="POST" action="{{ route('admin.dynamic-forms.fields.update', $field) }}">
    @csrf
    @method('PUT')

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
      <div class="form-group">
        <label for="label" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">
          Field Label <span style="color: #f87171;">*</span>
        </label>
        <input type="text" id="label" name="label" class="filter-input" style="width: 100%; padding: 0.75rem 1rem;" required value="{{ old('label', $field->label) }}" />
      </div>

      <div class="form-group">
        <label for="field_key" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">
          Database Key (Read-only)
        </label>
        <input type="text" id="field_key" class="filter-input" style="width: 100%; padding: 0.75rem 1rem; opacity: 0.6; cursor: not-allowed;" readonly value="{{ $field->field_key }}" />
      </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
      <div class="form-group">
        <label for="field_type" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">
          Field Input Type <span style="color: #f87171;">*</span>
        </label>
        <select id="field_type" name="field_type" class="filter-select" style="width: 100%; padding: 0.75rem 1rem;" required>
          <option value="text" {{ old('field_type', $field->field_type) === 'text' ? 'selected' : '' }}>Text Input</option>
          <option value="number" {{ old('field_type', $field->field_type) === 'number' ? 'selected' : '' }}>Number</option>
          <option value="tel" {{ old('field_type', $field->field_type) === 'tel' ? 'selected' : '' }}>Telephone</option>
          <option value="email" {{ old('field_type', $field->field_type) === 'email' ? 'selected' : '' }}>Email</option>
          <option value="url" {{ old('field_type', $field->field_type) === 'url' ? 'selected' : '' }}>Website / Portfolio URL</option>
          <option value="combobox" {{ old('field_type', $field->field_type) === 'combobox' ? 'selected' : '' }}>Combobox (Searchable City)</option>
          <option value="textarea" {{ old('field_type', $field->field_type) === 'textarea' ? 'selected' : '' }}>Textarea (Multi-line)</option>
          <option value="checkbox_group" {{ old('field_type', $field->field_type) === 'checkbox_group' ? 'selected' : '' }}>Checkbox Group (Multiple)</option>
          <option value="radio_group" {{ old('field_type', $field->field_type) === 'radio_group' ? 'selected' : '' }}>Radio Group (Single choice)</option>
          <option value="select" {{ old('field_type', $field->field_type) === 'select' ? 'selected' : '' }}>Select Dropdown</option>
          <option value="image" {{ old('field_type', $field->field_type) === 'image' ? 'selected' : '' }}>Single Image Upload</option>
          <option value="file" {{ old('field_type', $field->field_type) === 'file' ? 'selected' : '' }}>Document / File Upload</option>
          <option value="multiple_images" {{ old('field_type', $field->field_type) === 'multiple_images' ? 'selected' : '' }}>Multiple Images Upload</option>
        </select>
      </div>

      <div class="form-group">
        <label for="width" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">
          Form Grid Width <span style="color: #f87171;">*</span>
        </label>
        <select id="width" name="width" class="filter-select" style="width: 100%; padding: 0.75rem 1rem;" required>
          <option value="half" {{ old('width', $field->width) === 'half' ? 'selected' : '' }}>Half Width (50%)</option>
          <option value="full" {{ old('width', $field->width) === 'full' ? 'selected' : '' }}>Full Width (100%)</option>
        </select>
      </div>

      <div class="form-group">
        <label for="display_order" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">
          Display Order #
        </label>
        <input type="number" id="display_order" name="display_order" class="filter-input" style="width: 100%; padding: 0.75rem 1rem;" value="{{ old('display_order', $field->display_order) }}" />
      </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
      <div class="form-group">
        <label for="placeholder" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">
          Placeholder Text
        </label>
        <input type="text" id="placeholder" name="placeholder" class="filter-input" style="width: 100%; padding: 0.75rem 1rem;" value="{{ old('placeholder', $field->placeholder) }}" />
      </div>

      <div class="form-group">
        <label for="help_text" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">
          Help / Subtitle Note
        </label>
        <input type="text" id="help_text" name="help_text" class="filter-input" style="width: 100%; padding: 0.75rem 1rem;" value="{{ old('help_text', $field->help_text) }}" />
      </div>
    </div>

    @php
      $rawOptions = '';
      if (!empty($field->options) && is_array($field->options)) {
          $lines = [];
          foreach ($field->options as $k => $v) {
              $lines[] = "{$k}: {$v}";
          }
          $rawOptions = implode("\n", $lines);
      }
    @endphp

    <div class="form-group" style="margin-bottom: 1.5rem;">
      <label for="options" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">
        Options / Choices (For Checkbox Groups, Radio Groups, and Selects)
      </label>
      <textarea id="options" name="options" class="filter-input" style="width: 100%; padding: 0.75rem 1rem; min-height: 90px;">{{ old('options', $rawOptions) }}</textarea>
      <small style="color: var(--text-muted); font-size: 0.75rem;">One option per line or key:value format.</small>
    </div>

    <div class="form-group" style="margin-bottom: 1.5rem;">
      <label for="validation_rules" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">
        Laravel Validation Rules
      </label>
      <input type="text" id="validation_rules" name="validation_rules" class="filter-input" style="width: 100%; padding: 0.75rem 1rem;" value="{{ old('validation_rules', $field->validation_rules) }}" />
    </div>

    <!-- Category Assignments -->
    <div style="border-top: 1px solid var(--border-subtle); padding-top: 1.5rem; margin-top: 1.5rem; margin-bottom: 1.5rem;">
      <h4 style="color: var(--gold); font-size: 1rem; margin-top: 0; margin-bottom: 0.5rem;">Category Assignments</h4>
      <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1rem;">Select categories that feature this field:</p>

      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.75rem;">
        @foreach($categories as $cat)
          <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: #e2e8f0; font-size: 0.85rem; background: rgba(255,255,255,0.03); padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border-subtle);">
            <input type="checkbox" name="categories[]" value="{{ $cat->id }}" {{ in_array($cat->id, (array) old('categories', $attachedCategoryIds)) ? 'checked' : '' }} style="accent-color: var(--gold);" />
            <span>{{ $cat->name }}</span>
          </label>
        @endforeach
      </div>
    </div>

    <!-- Toggles -->
    <div style="border-top: 1px solid var(--border-subtle); padding-top: 1.5rem; margin-top: 1.5rem; margin-bottom: 1.5rem; display: flex; flex-wrap: wrap; gap: 2rem;">
      <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; color: #cbd5e1; font-size: 0.9rem;">
        <input type="checkbox" name="is_required" value="1" {{ old('is_required', $field->is_required) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--gold);" />
        <span>Required Field</span>
      </label>

      <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; color: #cbd5e1; font-size: 0.9rem;">
        <input type="checkbox" name="is_common" value="1" {{ old('is_common', $field->is_common) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #60a5fa;" />
        <span>Common Field</span>
      </label>

      <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; color: #cbd5e1; font-size: 0.9rem;">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $field->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #4ade80;" />
        <span>Active &amp; Visible</span>
      </label>
    </div>

    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
      <button type="submit" class="btn-gold" style="padding: 0.75rem 2rem;">Update Field</button>
      <a href="{{ route('admin.dynamic-forms.fields.index') }}" class="btn-subtle" style="padding: 0.75rem 1.5rem; text-decoration: none;">Cancel</a>
    </div>
  </form>
</div>
@endsection

