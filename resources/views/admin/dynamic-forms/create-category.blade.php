@extends('admin.layouts.app')

@section('title', 'Add New Category | Dynamic Form Builder')
@section('header_title', 'Create Professional Category')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <div style="margin-bottom: 0.5rem;">
      <a href="{{ route('admin.dynamic-forms.index') }}" style="color: var(--gold); text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.35rem;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        <span>Back to Form Categories</span>
      </a>
    </div>
    <h2 class="module-title">Add New <em>Category</em></h2>
    <p class="module-subtitle">Create a new creative professional category. It will automatically appear on the website and inherit standard profile fields.</p>
  </div>
</div>

<div class="admin-card" style="max-width: 900px; padding: 2rem;">
  <form method="POST" action="{{ route('admin.dynamic-forms.categories.store') }}" enctype="multipart/form-data">
    @csrf

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
      <div class="form-group">
        <label for="name" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">
          Category Name <span style="color: #f87171;">*</span>
        </label>
        <input type="text" id="name" name="name" class="filter-input" style="width: 100%; padding: 0.75rem 1rem;" placeholder="e.g. Fashion Stylist" required value="{{ old('name') }}" />
      </div>

      <div class="form-group">
        <label for="slug" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">
          URL Slug (optional)
        </label>
        <input type="text" id="slug" name="slug" class="filter-input" style="width: 100%; padding: 0.75rem 1rem;" placeholder="auto-generated from name if left blank" value="{{ old('slug') }}" />
        <small style="color: var(--text-muted); font-size: 0.75rem;">e.g. <code>fashion-stylist</code>. Will be used for <code>/professionals/fashion-stylist</code> and <code>/profiles/fashion-stylist</code>.</small>
      </div>
    </div>

    <div class="form-group" style="margin-bottom: 1.5rem;">
      <label for="hero_title" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">
        Hero Section Title (supports HTML formatting)
      </label>
      <input type="text" id="hero_title" name="hero_title" class="filter-input" style="width: 100%; padding: 0.75rem 1rem;" placeholder="e.g. Fashion &lt;em&gt;Stylist&lt;/em&gt;" value="{{ old('hero_title') }}" />
      <small style="color: var(--text-muted); font-size: 0.75rem;">Defaults to Category Name if blank.</small>
    </div>

    <div class="form-group" style="margin-bottom: 1.5rem;">
      <label for="description" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">
        Public Page Intro / Description
      </label>
      <textarea id="description" name="description" class="filter-input" style="width: 100%; padding: 0.75rem 1rem; min-height: 80px;" placeholder="Brief description of this creative profession for the category listing page...">{{ old('description') }}</textarea>
    </div>

    <div style="border-top: 1px solid var(--border-subtle); padding-top: 1.5rem; margin-top: 1.5rem; margin-bottom: 1.5rem;">
      <h4 style="color: var(--gold); font-size: 1rem; margin-top: 0; margin-bottom: 1.25rem;">Registration Form Section Headings</h4>

      <div class="form-group" style="margin-bottom: 1.5rem;">
        <label for="form_title" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">
          Form Heading Title
        </label>
        <input type="text" id="form_title" name="form_title" class="filter-input" style="width: 100%; padding: 0.75rem 1rem;" placeholder="e.g. Fashion Stylist Details" value="{{ old('form_title') }}" />
      </div>

      <div class="form-group" style="margin-bottom: 1.5rem;">
        <label for="form_lead" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">
          Form Lead / Instruction Subtitle
        </label>
        <textarea id="form_lead" name="form_lead" class="filter-input" style="width: 100%; padding: 0.75rem 1rem; min-height: 80px;" placeholder="Fill in your experience, specialisations and contact details to publish your creative profile.">{{ old('form_lead', 'Fill in your experience, specialisations and contact details to publish your creative profile.') }}</textarea>
      </div>
    </div>

    <div style="border-top: 1px solid var(--border-subtle); padding-top: 1.5rem; margin-top: 1.5rem; margin-bottom: 1.5rem;">
      <h4 style="color: var(--gold); font-size: 1rem; margin-top: 0; margin-bottom: 1.25rem;">Images &amp; Display Order</h4>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        <div class="form-group">
          <label for="default_photo" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">
            Category Thumbnail / Showcase Photo
          </label>
          <input type="file" id="default_photo" name="default_photo" accept="image/*" class="filter-input" style="width: 100%; padding: 0.5rem;" />
          <small style="color: var(--text-muted); font-size: 0.75rem;">Appears in the creative professionals tab grid and card preview.</small>
        </div>

        <div class="form-group">
          <label for="banner_image" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">
            Category Banner Image (Hero)
          </label>
          <input type="file" id="banner_image" name="banner_image" accept="image/*" class="filter-input" style="width: 100%; padding: 0.5rem;" />
          <small style="color: var(--text-muted); font-size: 0.75rem;">Displayed in the dedicated category listing page hero banner.</small>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <div class="form-group">
          <label for="display_order" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">
            Display Order #
          </label>
          <input type="number" id="display_order" name="display_order" class="filter-input" style="width: 100%; padding: 0.75rem 1rem;" value="{{ old('display_order', $nextOrder) }}" />
        </div>

        <div class="form-group" style="display: flex; align-items: flex-end; padding-bottom: 0.75rem;">
          <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; color: #cbd5e1; font-size: 0.9rem;">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--gold);" />
            <span>Category &amp; Form Active Immediately</span>
          </label>
        </div>
      </div>
    </div>

    <div style="background: rgba(229,184,61,0.08); border: 1px solid rgba(229,184,61,0.25); border-radius: 8px; padding: 1rem 1.25rem; margin-top: 1.5rem; margin-bottom: 2rem;">
      <div style="display: flex; align-items: center; gap: 0.75rem; color: var(--gold); font-weight: 600; font-size: 0.9rem; margin-bottom: 0.25rem;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
        <span>Automatic Form Field Attachment</span>
      </div>
      <p style="margin: 0; color: var(--text-secondary); font-size: 0.85rem; line-height: 1.5;">
        Standard fields (Name, Profession, Location, Experience, Phone, Email, About, Outstation Availability) will be attached automatically. You can add category-specific custom fields, reorder, or toggle optionality right after creation.
      </p>
    </div>

    <div style="display: flex; gap: 1rem;">
      <button type="submit" class="btn-gold" style="padding: 0.75rem 2rem;">Create Category</button>
      <a href="{{ route('admin.dynamic-forms.index') }}" class="btn-subtle" style="padding: 0.75rem 1.5rem; text-decoration: none;">Cancel</a>
    </div>
  </form>
</div>
@endsection

