@extends('admin.layouts.app')

@section('title', 'Edit Category | ' . $category->name)
@section('header_title', 'Edit Form Category')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <div style="margin-bottom: 0.5rem;">
      <a href="{{ route('admin.dynamic-forms.index') }}" style="color: var(--gold); text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.35rem;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        <span>Back to Form Categories</span>
      </a>
    </div>
    <h2 class="module-title">Edit Category: <em>{{ $category->name }}</em></h2>
    <p class="module-subtitle">Configure hero titles, form intro texts, and public display parameters for {{ $category->name }}.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.dynamic-forms.preview', $category->slug) }}" target="_blank" class="btn-subtle" style="display: inline-flex; align-items: center; gap: 0.5rem;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
      <span>Live Form Preview</span>
    </a>
  </div>
</div>

<div class="admin-card" style="max-width: 900px; padding: 2rem;">
  <form method="POST" action="{{ route('admin.dynamic-forms.categories.update', $category) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
      <div class="form-group">
        <label for="name" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">Category Name <span style="color: #f87171;">*</span></label>
        <input type="text" id="name" name="name" class="filter-input" style="width: 100%; padding: 0.75rem 1rem;" required value="{{ old('name', $category->name) }}" />
      </div>

      <div class="form-group">
        <label for="slug" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">URL Slug (Read-only)</label>
        <input type="text" id="slug" class="filter-input" style="width: 100%; padding: 0.75rem 1rem; opacity: 0.6; cursor: not-allowed;" readonly value="{{ $category->slug }}" />
      </div>
    </div>

    <div class="form-group" style="margin-bottom: 1.5rem;">
      <label for="hero_title" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">Hero Section Title (supports HTML formatting) <span style="color: #f87171;">*</span></label>
      <input type="text" id="hero_title" name="hero_title" class="filter-input" style="width: 100%; padding: 0.75rem 1rem;" required value="{{ old('hero_title', $category->hero_title) }}" />
      <small style="color: var(--text-muted); font-size: 0.75rem;">Example: <code>Makeup &lt;em&gt;Artist&lt;/em&gt;</code></small>
    </div>

    <div class="form-group" style="margin-bottom: 1.5rem;">
      <label for="description" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">Public Page Intro / Description</label>
      <textarea id="description" name="description" class="filter-input" style="width: 100%; padding: 0.75rem 1rem; min-height: 80px;">{{ old('description', $category->description) }}</textarea>
    </div>

    <div style="border-top: 1px solid var(--border-subtle); padding-top: 1.5rem; margin-top: 1.5rem; margin-bottom: 1.5rem;">
      <h4 style="color: var(--gold); font-size: 1rem; margin-top: 0; margin-bottom: 1.25rem;">Form Section Headings</h4>

      <div class="form-group" style="margin-bottom: 1.5rem;">
        <label for="form_title" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">Form Heading Title <span style="color: #f87171;">*</span></label>
        <input type="text" id="form_title" name="form_title" class="filter-input" style="width: 100%; padding: 0.75rem 1rem;" required value="{{ old('form_title', $category->form_title) }}" />
      </div>

      <div class="form-group" style="margin-bottom: 1.5rem;">
        <label for="form_lead" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">Form Lead / Instruction Subtitle</label>
        <textarea id="form_lead" name="form_lead" class="filter-input" style="width: 100%; padding: 0.75rem 1rem; min-height: 80px;">{{ old('form_lead', $category->form_lead) }}</textarea>
      </div>
    </div>

    <div style="border-top: 1px solid var(--border-subtle); padding-top: 1.5rem; margin-top: 1.5rem; margin-bottom: 1.5rem;">
      <h4 style="color: var(--gold); font-size: 1rem; margin-top: 0; margin-bottom: 1.25rem;">Showcase Thumbnail &amp; Banner Images</h4>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        <div class="form-group">
          <label for="default_photo" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">Category Thumbnail / Showcase Photo</label>
          @if($category->default_photo)
            <div style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 1rem;">
              <img src="{{ $category->photo_url }}" alt="{{ $category->name }}" style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px; border: 1px solid var(--gold);" />
              <div style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace; word-break: break-all;">
                Current: {{ $category->default_photo }}
              </div>
            </div>
          @endif
          <input type="file" id="default_photo" name="default_photo" accept="image/*" class="filter-input" style="width: 100%; padding: 0.5rem;" />
          <small style="color: var(--text-muted); font-size: 0.75rem;">Upload new image or leave empty to keep current.</small>
        </div>

        <div class="form-group">
          <label for="banner_image" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">Category Banner Image (Hero)</label>
          @if($category->banner_image)
            <div style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 1rem;">
              <img src="{{ $category->banner_url }}" alt="{{ $category->name }} banner" style="width: 120px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid var(--gold);" />
              <div style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace; word-break: break-all;">
                Current: {{ $category->banner_image }}
              </div>
            </div>
          @endif
          <input type="file" id="banner_image" name="banner_image" accept="image/*" class="filter-input" style="width: 100%; padding: 0.5rem;" />
          <small style="color: var(--text-muted); font-size: 0.75rem;">Displayed in dedicated category listing hero section.</small>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <div class="form-group">
          <label for="display_order" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">Display Order #</label>
          <input type="number" id="display_order" name="display_order" class="filter-input" style="width: 100%; padding: 0.75rem 1rem;" value="{{ old('display_order', $category->display_order) }}" />
        </div>

        <div class="form-group" style="display: flex; align-items: flex-end; padding-bottom: 0.75rem;">
          <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; color: #cbd5e1; font-size: 0.9rem;">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--gold);" />
            <span>Category &amp; Form Active for Public and Registered Users</span>
          </label>
        </div>
      </div>
    </div>

    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
      <button type="submit" class="btn-gold" style="padding: 0.75rem 2rem;">Save Changes</button>
      <a href="{{ route('admin.dynamic-forms.index') }}" class="btn-subtle" style="padding: 0.75rem 1.5rem; text-decoration: none;">Cancel</a>
    </div>
  </form>
</div>
@endsection

