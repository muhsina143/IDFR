@extends('admin.layouts.app')

@section('title', 'Edit About Statistic')
@section('header_title', 'Edit Statistic')

@section('styles')
<style>
  .form-layout {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 2rem;
    align-items: start;
  }

  @media (max-width: 1024px) {
    .form-layout {
      grid-template-columns: 1fr;
    }
  }

  .stat-card-preview-box {
    background: #000;
    border: 1px solid rgba(200, 169, 126, 0.35);
    border-radius: 8px;
    padding: 1.5rem;
    position: sticky;
    top: 2rem;
  }

  .preview-box-title {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: var(--gold);
    font-weight: 700;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .preview-card-replica {
    display: flex;
    flex-direction: column-reverse;
    gap: 0.35rem;
    padding: 1.5rem;
    border: 1px solid rgba(200, 169, 126, 0.4);
    background: #000;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
  }

  .preview-card-dt {
    font-size: 0.68rem;
    letter-spacing: 0.25em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
  }

  .preview-card-dd {
    font-size: clamp(2.2rem, 1.8rem + 1.2vw, 3rem);
    line-height: 1;
    color: var(--gold);
    font-family: var(--font-serif);
    font-weight: 600;
  }
</style>
@endsection

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.about-statistics.index') }}" class="back-link">
      <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
      <span>Back to About Statistics</span>
    </a>
    <h2 class="module-title">Edit Statistic: <em>{{ $statistic->title }}</em></h2>
    <p class="module-subtitle">Update numerical values, prefixes, suffixes, display order, or publication status. Changes reflect immediately on the public About page.</p>
  </div>
</div>

<form action="{{ route('admin.about-statistics.update', $statistic) }}" method="POST">
  @csrf
  @method('PUT')

  <div class="form-layout">
    <!-- Form Fields Column -->
    <div class="form-col-main">
      <div class="admin-card">
        <h3 class="card-section-title">Statistic Configuration</h3>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="statTitle">Statistic Title <span class="required">*</span></label>
            <input type="text" id="statTitle" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $statistic->title) }}" required placeholder="e.g. REGISTERED MODELS, SEASONS" oninput="updateLivePreview()" />
            <span class="form-hint">Displayed in clean, uppercase lettering below the numerical value.</span>
            @error('title') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="statValue">Numerical Value <span class="required">*</span></label>
            <input type="text" id="statValue" name="value" class="form-control @error('value') is-invalid @enderror" value="{{ old('value', $statistic->value) }}" required placeholder="e.g. 04, 1200, 350, 85" oninput="updateLivePreview()" />
            <span class="form-hint">Numbers like <code>04</code> preserve the leading zero; numbers like <code>1200</code> format as <code>1,200</code>.</span>
            @error('value') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="statPrefix">Prefix (Optional)</label>
            <input type="text" id="statPrefix" name="prefix" class="form-control @error('prefix') is-invalid @enderror" value="{{ old('prefix', $statistic->prefix) }}" placeholder="e.g. #, $, ₹" oninput="updateLivePreview()" />
            <span class="form-hint">Character displayed immediately before the number.</span>
            @error('prefix') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="statSuffix">Suffix (Optional)</label>
            <input type="text" id="statSuffix" name="suffix" class="form-control @error('suffix') is-invalid @enderror" value="{{ old('suffix', $statistic->suffix) }}" placeholder="e.g. +, %, K, M" oninput="updateLivePreview()" />
            <span class="form-hint">Character displayed immediately after the number (e.g. <code>+</code>).</span>
            @error('suffix') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="statOrder">Display Order</label>
            <input type="number" id="statOrder" name="display_order" class="form-control @error('display_order') is-invalid @enderror" value="{{ old('display_order', $statistic->display_order) }}" min="0" />
            <span class="form-hint">Determines left-to-right sorting on the About page. Lower numbers appear first.</span>
            @error('display_order') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label">Publication Status</label>
            <div style="margin-top: 0.65rem;">
              <label class="form-checkbox-label" style="display: flex; align-items: center; gap: 0.65rem; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $statistic->is_active ? '1' : '0') == '1' ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--gold);" onchange="updateLivePreview()" />
                <span style="color: #fff; font-size: 0.95rem; font-weight: 500;">Active (Published on public About page)</span>
              </label>
              <span class="form-hint">Uncheck to temporarily hide this card without deleting it from the database.</span>
            </div>
          </div>
        </div>

        <div class="form-actions" style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
          <a href="{{ route('admin.about-statistics.index') }}" class="btn-subtle">Cancel</a>
          <button type="submit" class="btn-gold">Save Changes</button>
        </div>
      </div>
    </div>

    <!-- Live Preview Sidebar Column -->
    <div class="form-col-side">
      <div class="stat-card-preview-box">
        <div class="preview-box-title">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
          <span>Live Card Preview</span>
        </div>

        <div class="preview-card-replica">
          <div class="preview-card-dt" id="previewTitle">{{ $statistic->title }}</div>
          <div class="preview-card-dd">
            <span id="previewPrefix">{{ $statistic->prefix }}</span><span id="previewValue">{{ $statistic->formatted_value }}</span><span id="previewSuffix">{{ $statistic->suffix }}</span>
          </div>
        </div>

        <div style="margin-top: 1.25rem; font-size: 0.75rem; color: var(--text-muted); line-height: 1.4;">
          This visual preview displays how this specific statistic card will be rendered within the About page grid on the public website.
        </div>
      </div>
    </div>
  </div>
</form>
@endsection

@section('scripts')
<script>
  function formatPreviewNumber(raw) {
    if (!raw) return '00';
    const trimmed = raw.trim();
    if (trimmed.length > 1 && trimmed.startsWith('0') && !isNaN(Number(trimmed))) {
      return trimmed;
    }
    const cleanNum = Number(trimmed.replace(/,/g, ''));
    if (!isNaN(cleanNum)) {
      return cleanNum.toLocaleString('en-IN');
    }
    return trimmed;
  }

  function updateLivePreview() {
    const title = document.getElementById('statTitle').value.trim() || 'STATISTIC TITLE';
    const value = document.getElementById('statValue').value.trim() || '00';
    const prefix = document.getElementById('statPrefix').value || '';
    const suffix = document.getElementById('statSuffix').value || '';

    document.getElementById('previewTitle').textContent = title;
    document.getElementById('previewPrefix').textContent = prefix;
    document.getElementById('previewValue').textContent = formatPreviewNumber(value);
    document.getElementById('previewSuffix').textContent = suffix;
  }

  document.addEventListener('DOMContentLoaded', updateLivePreview);
</script>
@endsection
