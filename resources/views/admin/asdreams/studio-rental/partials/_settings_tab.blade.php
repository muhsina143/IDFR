<form method="POST" action="{{ route('admin.asdreams.studio-rental.pricing.update') }}" data-hub-form="settings">
  @csrf
  <input type="hidden" name="from_hub" value="1" />

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.5rem; align-items: start;">
    <!-- A. Rental Pricing -->
    <div class="admin-card">
      <div style="border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.75rem; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
        <div>
          <h3 style="font-family: var(--font-serif); font-size: 1.15rem; color: #fff; margin: 0;">A. Rental Pricing</h3>
          <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem;">Controls price amount, currency symbol, billing unit, and public visibility.</p>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label class="form-label" for="rentalPriceAmount">Rental Price <span style="color: var(--gold);">*</span></label>
          <input type="number" id="rentalPriceAmount" name="price_amount" value="{{ $rentalConfig['price_amount'] }}" step="any" min="0" max="9999999" required class="form-control" placeholder="1000" data-preview-input />
          @error('price_amount') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="rentalCurrency">Currency <span style="color: var(--gold);">*</span></label>
          <select id="rentalCurrency" name="currency" class="form-control" data-preview-input>
            @foreach($currencies as $code => $meta)
              <option value="{{ $code }}" {{ $rentalConfig['currency'] === $code ? 'selected' : '' }}>{{ $meta['label'] }}</option>
            @endforeach
          </select>
          @error('currency') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="rentalUnit">Rental Unit <span style="color: var(--gold);">*</span></label>
          <select id="rentalUnit" name="rental_unit" class="form-control" data-preview-input>
            @foreach($units as $unitKey => $unitMeta)
              <option value="{{ $unitKey }}" {{ $rentalConfig['rental_unit'] === $unitKey ? 'selected' : '' }}>{{ $unitMeta['label'] }}</option>
            @endforeach
          </select>
          @error('rental_unit') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="rentalPricingLabel">Pricing Display Label</label>
          <input type="text" id="rentalPricingLabel" name="pricing_label" value="{{ $rentalConfig['pricing_label'] }}" maxlength="100" class="form-control" placeholder="Starting From" data-preview-input />
          @error('pricing_label') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="grid-column: span 2;">
          <label class="form-label" for="rentalPriceDescription">Price Description / Note</label>
          <input type="text" id="rentalPriceDescription" name="price_description" value="{{ $rentalConfig['price_description'] }}" maxlength="500" class="form-control" placeholder="Final pricing may vary depending on rental duration, studio requirements and additional services." data-preview-input />
          @error('price_description') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="grid-column: span 2; margin-bottom: 0; padding: 0.85rem 1rem; border-radius: 6px; background: rgba(229, 184, 61, 0.06); border: 1px solid var(--border-gold);">
          <input type="hidden" name="price_enabled" value="0" />
          <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; gap: 1rem;">
            <span>
              <strong style="color: #fff; font-size: 0.9rem; display: block;">Enable Price Display on Website</strong>
              <span style="font-size: 0.76rem; color: var(--text-secondary);">Turn off to hide pricing figures while keeping Studio Information &amp; the Book Studio button active.</span>
            </span>
            <input type="checkbox" id="rentalPriceEnabled" name="price_enabled" value="1" {{ $rentalConfig['price_enabled'] ? 'checked' : '' }} data-preview-input style="width: 18px; height: 18px; accent-color: var(--gold);" />
          </label>
        </div>
      </div>
    </div>

    <!-- B. Studio Information & Live Frontend Preview -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
      <div class="admin-card">
        <div style="border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.75rem; margin-bottom: 1.25rem;">
          <h3 style="font-family: var(--font-serif); font-size: 1.15rem; color: #fff; margin: 0;">B. Studio Information</h3>
          <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem;">Customize the title, overview description, and optional booking notes shown alongside pricing.</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div class="form-group">
            <label class="form-label" for="rentalTitle">Studio Rental Title <span style="color: var(--gold);">*</span></label>
            <input type="text" id="rentalTitle" name="title" value="{{ $rentalConfig['title'] }}" required maxlength="150" class="form-control" placeholder="Studio Rental" data-preview-input />
            @error('title') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="rentalStudioArea">Studio Area</label>
            <input type="text" id="rentalStudioArea" name="studio_area" value="{{ $rentalConfig['studio_area'] }}" maxlength="100" class="form-control" placeholder="4,000 sq. ft." />
            @error('studio_area') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="rentalShortDescription">Short Description</label>
          <textarea id="rentalShortDescription" name="short_description" rows="3" maxlength="500" class="form-control" placeholder="Professional photography and creative studio space available for your projects." data-preview-input>{{ $rentalConfig['short_description'] }}</textarea>
          @error('short_description') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="rentalBookingInfo">Booking Information <span style="color: var(--text-muted); font-weight: 400;">(Optional)</span></label>
            <input type="text" id="rentalBookingInfo" name="booking_info" value="{{ $rentalConfig['booking_info'] }}" maxlength="500" class="form-control" placeholder="e.g. Advance booking required" data-preview-input />
            @error('booking_info') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="rentalAdditionalNote">Additional Pricing Note <span style="color: var(--text-muted); font-weight: 400;">(Optional)</span></label>
            <input type="text" id="rentalAdditionalNote" name="additional_note" value="{{ $rentalConfig['additional_note'] }}" maxlength="500" class="form-control" placeholder="e.g. Overtime charges apply" data-preview-input />
            @error('additional_note') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>
      </div>

      <!-- Live Preview + Save Action -->
      <div class="admin-card" style="background: linear-gradient(135deg, rgba(245, 200, 66, 0.08) 0%, rgba(17, 17, 17, 0.98) 100%); border-color: var(--border-gold);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
          <span style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--gold); font-weight: 700;">Live Frontend Card Preview</span>
          <span id="previewStatusBadge" class="badge" style="font-size: 0.7rem; background: rgba(34, 197, 94, 0.12); color: #4ade80;">
            {{ $rentalConfig['price_enabled'] ? 'Price Visible' : 'Price Hidden' }}
          </span>
        </div>

        <p id="previewTitle" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--gold); margin: 0 0 0.25rem;">{{ $rentalConfig['title'] }}</p>

        <div id="previewPriceBlock" style="{{ $rentalConfig['price_enabled'] ? '' : 'display: none;' }}">
          <p id="previewPricingLabel" style="font-size: 0.78rem; color: var(--text-secondary); margin: 0.15rem 0 0.2rem;">{{ $rentalConfig['pricing_label'] }}</p>
          <p style="margin: 0 0 0.45rem; display: flex; align-items: baseline; gap: 0.45rem;">
            <span id="previewFormattedPrice" style="font-family: var(--font-serif); font-size: 2rem; font-weight: 700; color: var(--gold-light); line-height: 1.05;">{{ $rentalConfig['formatted_price'] }}</span>
            <span id="previewUnit" style="font-size: 0.92rem; color: var(--text-secondary);">{{ $rentalConfig['unit_display'] }}</span>
          </p>
        </div>

        <p id="previewShortDesc" style="font-size: 0.86rem; color: #fff; line-height: 1.5; margin: 0 0 0.35rem;">{{ $rentalConfig['short_description'] }}</p>
        <p id="previewPriceDesc" style="font-size: 0.76rem; color: var(--text-secondary); line-height: 1.45; margin: 0; {{ $rentalConfig['price_enabled'] ? '' : 'display: none;' }}">{{ $rentalConfig['price_description'] }}</p>
        <p id="previewBookingInfo" style="font-size: 0.76rem; color: var(--gold); margin: 0.3rem 0 0; {{ filled($rentalConfig['booking_info']) ? '' : 'display: none;' }}">{{ $rentalConfig['booking_info'] }}</p>
        <p id="previewAdditionalNote" style="font-size: 0.74rem; color: var(--text-muted); margin: 0.25rem 0 0; {{ ($rentalConfig['price_enabled'] && filled($rentalConfig['additional_note'])) ? '' : 'display: none;' }}">{{ $rentalConfig['additional_note'] }}</p>

        <div style="margin-top: 1.25rem; display: flex; justify-content: flex-end;">
          <button type="submit" class="btn-gold" style="padding: 0.75rem 1.75rem;">
            Save Pricing &amp; Studio Settings
          </button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- Collapsible Brand Hero & Social Settings -->
<details class="admin-card" style="margin-top: 1.5rem;">
  <summary style="cursor: pointer; font-family: var(--font-serif); font-size: 1.05rem; color: #fff; list-style: none; display: flex; justify-content: space-between; align-items: center;">
    <span>Hero Watermark &amp; Social Media Links <span style="font-size: 0.78rem; color: var(--text-muted); font-family: var(--font-sans); font-weight: 400;">(Optional global ASDreams branding settings)</span></span>
    <span style="color: var(--gold); font-size: 0.8rem;">Toggle</span>
  </summary>

  <form action="{{ route('admin.asdreams.settings.update') }}" method="POST" data-hub-form="settings" style="margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px solid var(--border-subtle);">
    @csrf
    <input type="hidden" name="from_hub" value="1" />

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="hub_hero_top">Hero Top Word</label>
        <input type="text" id="hub_hero_top" name="hero_overlay_top_word" value="{{ $settings['hero_overlay_top_word'] ?? 'Photography' }}" class="form-control" />
      </div>
      <div class="form-group">
        <label class="form-label" for="hub_hero_bottom">Hero Bottom Word</label>
        <input type="text" id="hub_hero_bottom" name="hero_overlay_bottom_word" value="{{ $settings['hero_overlay_bottom_word'] ?? 'Capturing' }}" class="form-control" />
      </div>
      <div class="form-group">
        <label class="form-label" for="hub_hero_highlight">Hero Highlight Script</label>
        <input type="text" id="hub_hero_highlight" name="hero_overlay_bottom_highlight" value="{{ $settings['hero_overlay_bottom_highlight'] ?? 'Moments' }}" class="form-control" />
      </div>
      <div class="form-group">
        <label class="form-label" for="hub_instagram">Instagram URL</label>
        <input type="text" id="hub_instagram" name="instagram_url" value="{{ $settings['instagram_url'] ?? 'https://instagram.com/asdreamstudio' }}" class="form-control" />
      </div>
      <div class="form-group">
        <label class="form-label" for="hub_facebook">Facebook URL</label>
        <input type="text" id="hub_facebook" name="facebook_url" value="{{ $settings['facebook_url'] ?? 'https://facebook.com/asdreamstudio' }}" class="form-control" />
      </div>
      <div class="form-group">
        <label class="form-label" for="hub_youtube">YouTube URL</label>
        <input type="text" id="hub_youtube" name="youtube_url" value="{{ $settings['youtube_url'] ?? 'https://youtube.com/@asdreamstudio' }}" class="form-control" />
      </div>
    </div>

    <div style="display: flex; justify-content: flex-end; margin-top: 0.75rem;">
      <button type="submit" class="btn-subtle">Save Branding &amp; Social Links</button>
    </div>
  </form>
</details>

