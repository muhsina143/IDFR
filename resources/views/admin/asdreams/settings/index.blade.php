@extends('admin.layouts.app')

@section('title', 'Website Settings — ASDreamStudio')
@section('page_title', 'Studio Global Settings')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Studio <em>Global Settings</em></h2>
    <p class="module-subtitle">Configure hero watermark calligraphy, studio rental rates, and social media channels across the ASDreamStudio website.</p>
  </div>
</div>

<form action="{{ route('admin.asdreams.settings.update') }}" method="POST">
  @csrf
  @method('PUT')

  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
      <!-- Hero Watermark Typography -->
      <div class="admin-card">
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Hero Calligraphy Watermark</h3>
        <p class="form-hint" style="margin-bottom: 1rem;">Large stylized typography floating in the background of the hero banner slider.</p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
          <div class="form-group">
            <label class="form-label" for="hero_overlay_top_word">Top Calligraphy Word</label>
            <input type="text" id="hero_overlay_top_word" name="hero_overlay_top_word" value="{{ old('hero_overlay_top_word', $settings['hero_overlay_top_word'] ?? 'AS') }}" class="form-control" placeholder="AS" />
            @error('hero_overlay_top_word') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="hero_overlay_bottom_word">Bottom Calligraphy Word</label>
            <input type="text" id="hero_overlay_bottom_word" name="hero_overlay_bottom_word" value="{{ old('hero_overlay_bottom_word', $settings['hero_overlay_bottom_word'] ?? 'Dream') }}" class="form-control" placeholder="Dream" />
            @error('hero_overlay_bottom_word') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group" style="grid-column: span 2;">
            <label class="form-label" for="hero_overlay_bottom_highlight">Bottom Script Highlight</label>
            <input type="text" id="hero_overlay_bottom_highlight" name="hero_overlay_bottom_highlight" value="{{ old('hero_overlay_bottom_highlight', $settings['hero_overlay_bottom_highlight'] ?? 'Studio') }}" class="form-control" placeholder="Studio" />
            @error('hero_overlay_bottom_highlight') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>
      </div>

      <!-- Studio Rental Pricing & CTA -->
      <div class="admin-card">
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Studio Rental Space &amp; Pricing</h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
          <div class="form-group" style="grid-column: span 2;">
            <label class="form-label" for="studio_rental_title">Rental Banner Heading</label>
            <input type="text" id="studio_rental_title" name="studio_rental_title" value="{{ old('studio_rental_title', $settings['studio_rental_title'] ?? 'Full Day Studio Hire') }}" class="form-control" />
            @error('studio_rental_title') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group" style="grid-column: span 2;">
            <label class="form-label" for="studio_rental_subtitle">Rental Details &amp; Perks</label>
            <textarea id="studio_rental_subtitle" name="studio_rental_subtitle" rows="3" class="form-control">{{ old('studio_rental_subtitle', $settings['studio_rental_subtitle'] ?? 'Includes Cyclorama Infinity Wall, Profoto lighting pack, high-speed WiFi, private makeup suite and client lounge.') }}</textarea>
            @error('studio_rental_subtitle') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="studio_rental_price">Display Price</label>
            <input type="text" id="studio_rental_price" name="studio_rental_price" value="{{ old('studio_rental_price', $settings['studio_rental_price'] ?? '₹18,000') }}" class="form-control" placeholder="₹18,000" />
            @error('studio_rental_price') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="studio_rental_per">Rate Unit</label>
            <input type="text" id="studio_rental_per" name="studio_rental_per" value="{{ old('studio_rental_per', $settings['studio_rental_per'] ?? '/ 8-hour shift') }}" class="form-control" placeholder="/ 8-hour shift" />
            @error('studio_rental_per') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>
      </div>
    </div>

    <!-- Social Links Sidebar -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
      <div class="admin-card">
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Official Social Links</h3>

        <div class="form-group">
          <label class="form-label" for="instagram_url">Instagram Profile</label>
          <input type="text" id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $settings['instagram_url'] ?? 'https://instagram.com/asdreamstudio') }}" class="form-control" />
          @error('instagram_url') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label" for="facebook_url">Facebook Page</label>
          <input type="text" id="facebook_url" name="facebook_url" value="{{ old('facebook_url', $settings['facebook_url'] ?? 'https://facebook.com/asdreamstudio') }}" class="form-control" />
          @error('facebook_url') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label" for="youtube_url">YouTube Channel</label>
          <input type="text" id="youtube_url" name="youtube_url" value="{{ old('youtube_url', $settings['youtube_url'] ?? 'https://youtube.com/@asdreamstudio') }}" class="form-control" />
          @error('youtube_url') <span class="form-error">{{ $message }}</span> @enderror
        </div>
      </div>

      <div class="admin-card" style="background: rgba(245, 200, 66, 0.04); border-color: var(--gold-a35);">
        <button type="submit" class="btn-gold" style="width: 100%; justify-content: center; padding: 0.85rem;">
          Save Studio Settings
        </button>
      </div>
    </div>
  </div>
</form>
@endsection

