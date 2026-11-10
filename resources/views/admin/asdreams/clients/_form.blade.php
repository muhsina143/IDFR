@php
  $isEdit = isset($client);
@endphp

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
  <div class="form-group">
    <label class="form-label" for="clientName">Client Name <span style="color: var(--gold);">*</span></label>
    <input type="text" id="clientName" name="name" value="{{ old('name', $client->name ?? '') }}" required class="form-control" placeholder="e.g. Maison Kerala" />
    @error('name') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="clientCategory">Client Category <span style="color: var(--gold);">*</span></label>
    <input type="text" id="clientCategory" name="category" value="{{ old('category', $client->category ?? '') }}" required class="form-control" placeholder="e.g. Couture House, Bridal Wear, Advertising Agency" />
    @error('category') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group" style="grid-column: span 2;">
    <label class="form-label" for="clientDescription">Client Description / Story <span style="color: var(--gold);">*</span></label>
    <textarea id="clientDescription" name="description" rows="4" required class="form-control" placeholder="Client story or testimonial shown inside the featured panel on the left...">{{ old('description', $client->description ?? '') }}</textarea>
    @error('description') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="authorName">Author / Contact Name (Optional)</label>
    <input type="text" id="authorName" name="author_name" value="{{ old('author_name', $client->author_name ?? '') }}" class="form-control" placeholder="e.g. Neha Varghese" />
    @error('author_name') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="authorRole">Author Designation / Role (Optional)</label>
    <input type="text" id="author_role" name="author_role" value="{{ old('author_role', $client->author_role ?? '') }}" class="form-control" placeholder="e.g. Creative Director" />
    @error('author_role') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="clientLogo">Client Logo @unless($isEdit)<span style="color: var(--gold);">*</span>@endunless</label>
    @if($isEdit)
      <div style="margin-bottom: 0.75rem; width: 140px; height: 56px; border-radius: 4px; background: #000; border: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: center; padding: 6px;">
        @if($client->logo_url)
          <img src="{{ $client->logo_url }}" alt="{{ $client->name }} logo" style="max-width: 100%; max-height: 100%; object-fit: contain;" />
        @else
          <span style="font-size: 0.75rem; color: var(--gold); text-align: center; font-weight: 600;">{{ $client->logo_word ?: $client->name }}</span>
        @endif
      </div>
    @endif
    <input type="file" id="clientLogo" name="logo" {{ $isEdit ? '' : 'required' }} accept="image/*,.svg" class="form-control" />
    <span class="form-hint">{{ $isEdit ? 'Upload a new file only to replace the current logo. ' : '' }}PNG, SVG, JPG, or WebP (max 5MB).</span>
    @error('logo') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="clientFeaturedImage">Featured Client Image @unless($isEdit)<span style="color: var(--gold);">*</span>@endunless</label>
    @if($isEdit && $client->featured_image)
      <div style="margin-bottom: 0.75rem; width: 100px; height: 120px; border-radius: 4px; overflow: hidden; background: #000; border: 1px solid var(--border-subtle);">
        <img src="{{ $client->featured_image_url }}" alt="{{ $client->name }}" style="width: 100%; height: 100%; object-fit: cover;" />
      </div>
    @endif
    <input type="file" id="clientFeaturedImage" name="featured_image" {{ $isEdit ? '' : 'required' }} accept="image/*" class="form-control" />
    <span class="form-hint">{{ $isEdit ? 'Upload a new photo only to replace the current image. ' : '' }}High-resolution portrait photo displayed on the left when selected.</span>
    @error('featured_image') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="displayOrder">Display Order</label>
    <input type="number" id="displayOrder" name="display_order" value="{{ old('display_order', $client->display_order ?? $nextOrder ?? 1) }}" min="0" class="form-control" />
    @error('display_order') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group" style="display: flex; flex-direction: column; justify-content: center; gap: 0.75rem;">
    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
      <input type="checkbox" name="is_active" value="1" {{ old('is_active', $isEdit ? ($client->is_active ? '1' : '0') : '1') == '1' ? 'checked' : '' }} />
      <span style="font-weight: 500; color: #fff;">Active &amp; Visible on ASDreams Website</span>
    </label>

    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
      <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $isEdit ? ($client->is_featured ? '1' : '0') : '0') == '1' ? 'checked' : '' }} />
      <span style="font-weight: 500; color: var(--gold);">Selected by Default on Load (Featured Client)</span>
    </label>
  </div>
</div>

