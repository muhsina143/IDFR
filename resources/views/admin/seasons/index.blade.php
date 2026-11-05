@extends('admin.layouts.app')

@section('title', 'Runway Seasons Management')
@section('header_title', 'Runway Seasons Management')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Runway <em>Seasons</em></h2>
    <p class="module-subtitle">Configure fashion runway editions, host cities, schedule dates, and associated designer / model rosters.</p>
  </div>
  <div class="module-header__actions">
    <button type="button" class="btn-gold" onclick="openSeasonModal()">
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add New Season</span>
    </button>
  </div>
</div>

<div class="seasons-grid">
  @foreach($seasons as $season)
    <div class="admin-card season-card {{ $season->is_current ? 'season-card--active' : '' }}">
      @if($season->hero_image)
        <div class="season-card__cover" style="margin:-1.5rem -1.5rem 1rem -1.5rem; height:120px; overflow:hidden; border-top-left-radius:8px; border-top-right-radius:8px;">
          <img src="{{ $season->hero_image_url }}" alt="{{ $season->title }}" style="width:100%; height:100%; object-fit:cover;" />
        </div>
      @endif
      <div class="season-card__header">
        <div class="season-card__number">0{{ $season->number }}</div>
        <div class="season-card__status" style="display:flex; gap:0.4rem; align-items:center; flex-wrap:wrap;">
          @if($season->is_current)
            <span class="status-pill status-pill--active" style="background: rgba(229,184,61,0.18); color: var(--gold); border: 1px solid var(--gold); font-weight:700;">
              ★ Active Season
            </span>
          @endif
          @if($season->status === 'published')
            <span class="status-pill status-pill--published">Published</span>
          @elseif($season->status === 'draft')
            <span class="status-pill status-pill--draft">Draft</span>
          @else
            <span class="status-pill status-pill--archived">Archived</span>
          @endif
        </div>
      </div>

      <h3 class="season-card__title">{{ $season->title }}</h3>
      <p class="season-card__meta">
        <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
        <span>{{ $season->location ?? 'TBA' }} · {{ $season->year }}</span>
      </p>

      @if($season->dates)
        <p class="season-card__dates">
          <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
          <span>{{ $season->dates }}</span>
        </p>
      @endif

      <p style="font-size:0.75rem; color:var(--gold); margin-bottom:0.25rem;">
        <span style="color:var(--text-muted);">Hero Title:</span> {{ $season->hero_heading_display }} <em>{{ $season->hero_highlighted_display }}</em>
      </p>

      @if($season->description)
        <p class="season-card__desc">{{ Str::limit($season->description, 140) }}</p>
      @endif

      <div class="season-card__stats">
        <div class="season-stat">
          <span class="season-stat__num">{{ $season->images_count }}</span>
          <span class="season-stat__label">Hero Looks</span>
        </div>
        <div class="season-stat">
          <span class="season-stat__num">{{ $season->models_count }}</span>
          <span class="season-stat__label">Models</span>
        </div>
        <div class="season-stat">
          <span class="season-stat__num">{{ $season->designers_count }}</span>
          <span class="season-stat__label">Designers</span>
        </div>
      </div>

      <div class="season-card__actions">
        <div style="display:flex; gap:0.45rem; align-items:center; flex-wrap:wrap;">
          @if(! $season->is_current)
            <form action="{{ route('admin.seasons.activate', $season) }}" method="POST" style="display:inline;">
              @csrf
              <button type="submit" class="btn-activate btn-sm" title="Activate this season across the entire website">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                <span>Set as Active</span>
              </button>
            </form>
          @else
            <span class="badge-active-live">
              <span class="live-dot"></span> Live Active
            </span>
          @endif
          <a href="{{ route('admin.seasons.images', $season) }}" class="btn-gold btn-sm" title="Manage multiple hero slider images for this season">
            <svg viewBox="0 0 24 24" style="width:14px;height:14px;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
            <span>Images ({{ $season->images_count }})</span>
          </a>
          <button type="button" class="btn-subtle btn-sm" onclick='editSeason(@json($season))'>Edit</button>
        </div>
        <form action="{{ route('admin.seasons.destroy', $season) }}" method="POST" onsubmit="return confirm('Are you sure? Profile records associated with this season will NOT be deleted, but the season edition will be removed.');" style="display:inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="action-btn action-btn--danger" title="Delete Season">
            <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
          </button>
        </form>
      </div>
    </div>
  @endforeach
</div>

<!-- Modal Dialog for Creating/Editing Season -->
<div id="seasonModal" class="admin-modal" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="seasonModalTitle">
  <div class="admin-modal__backdrop" onclick="closeSeasonModal()"></div>
  <div class="admin-modal__dialog">
    <form id="seasonForm" action="{{ route('admin.seasons.store') }}" method="POST" enctype="multipart/form-data" class="admin-modal__form">
      @csrf
      <div id="seasonMethodField"></div>

      <div class="admin-modal__header">
        <div class="admin-modal__header-title">
          <h3 id="seasonModalTitle">Add Runway Season</h3>
          <p class="admin-modal__header-subtitle">Configure edition number, schedule dates, location, status and runway hero banner content.</p>
        </div>
        <button type="button" class="modal-close-btn" onclick="closeSeasonModal()" aria-label="Close modal">&times;</button>
      </div>

      <div class="admin-modal__body">
        @if ($errors->any())
          <div class="modal-alert modal-alert--danger">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <div>
              <strong>Please correct the following errors:</strong>
              <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        @endif

        <!-- 1. Season Number & Season Name (two columns) -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="seasonNumber">Season Number <span class="required">*</span></label>
            <input type="number" id="seasonNumber" name="number" required min="1" class="form-control" placeholder="e.g. 8" value="{{ old('number') }}" />
            <small class="form-hint">Unique numerical identifier for the runway edition.</small>
          </div>
          <div class="form-group">
            <label class="form-label" for="seasonTitle">Season Name <span class="required">*</span></label>
            <input type="text" id="seasonTitle" name="title" required class="form-control" placeholder="e.g. Season 8" value="{{ old('title') }}" />
            <small class="form-hint">Public season title (e.g. Season 8, Summer Couture).</small>
          </div>
        </div>

        <!-- 2. Year & Host City / Venue (two columns) -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="seasonYear">Year</label>
            <input type="text" id="seasonYear" name="year" class="form-control" placeholder="e.g. 2028" value="{{ old('year') }}" />
            <small class="form-hint">Calendar year of the runway edition.</small>
          </div>
          <div class="form-group">
            <label class="form-label" for="seasonLocation">Host City / Venue</label>
            <input type="text" id="seasonLocation" name="location" class="form-control" placeholder="e.g. Kannur, Kerala" value="{{ old('location') }}" />
            <small class="form-hint">Host city or event venue.</small>
          </div>
        </div>

        <!-- 3. Event Dates (full width) -->
        <div class="form-group">
          <label class="form-label" for="seasonDates">Event Dates</label>
          <input type="text" id="seasonDates" name="dates" class="form-control" placeholder="e.g. 15 — 17 December 2028" value="{{ old('dates') }}" />
          <small class="form-hint">Display schedule range formatted for banners & tickets (e.g. 15 – 17 Dec 2028).</small>
        </div>

        <!-- 4. Hero Cover Image (full width) -->
        <div class="form-group">
          <label class="form-label" for="seasonHeroImage">Hero Cover Image (Optional)</label>
          <input type="file" id="seasonHeroImage" name="hero_image" accept="image/jpeg,image/png,image/jpg,image/webp" class="form-control" onchange="previewSeasonHero(this)" />
          <small class="form-hint">High-resolution banner cover image (JPEG, PNG, WebP up to 5MB).</small>
          <div id="seasonHeroPreview" class="season-hero-preview" style="display:none;">
            <img src="" id="seasonHeroImgTag" alt="Season hero preview" />
            <div class="season-hero-preview__meta">
              <span id="seasonHeroPreviewName" class="season-hero-preview__name"></span>
              <button type="button" class="btn-clear-preview" onclick="clearHeroImagePreview()">Remove</button>
            </div>
          </div>
        </div>

        <!-- 5. Status (full width) -->
        <div class="form-group">
          <label class="form-label" for="seasonStatus">Status <span class="required">*</span></label>
          <select id="seasonStatus" name="status" class="form-control">
            <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published (Visible on Website)</option>
            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Internal Preview Only)</option>
            <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived (Past Runway Archive)</option>
          </select>
          <small class="form-hint">Controls public display on homepage, tickets, and rosters.</small>
        </div>

        <!-- 5b. Set as Active Season toggle -->
        <div class="form-group" style="background:#080808; border:1px solid rgba(229,184,61,0.3); border-radius:6px; padding:0.85rem 1rem;">
          <label style="display:flex; align-items:center; gap:0.65rem; cursor:pointer; margin-bottom:0;">
            <input type="checkbox" id="seasonIsCurrent" name="is_current" value="1" style="width:18px; height:18px; accent-color:var(--gold); cursor:pointer;" />
            <span style="font-size:0.88rem; color:#ffffff; font-weight:600;">Set as currently active runway season across entire website</span>
          </label>
          <small class="form-hint" style="margin-left:1.8rem; margin-top:0.25rem;">Only one season can be active at a time. Activating this season will automatically deactivate any previously active season.</small>
        </div>

        <!-- 6. Main Heading & Highlighted Heading (two columns) -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="seasonHeroHeading">Main Heading</label>
            <input type="text" id="seasonHeroHeading" name="hero_heading" class="form-control" placeholder="e.g. The Runway" value="{{ old('hero_heading') }}" />
            <small class="form-hint">Homepage hero main heading (Default: The Runway).</small>
          </div>
          <div class="form-group">
            <label class="form-label" for="seasonHeroHighlighted">Highlighted Heading</label>
            <input type="text" id="seasonHeroHighlighted" name="hero_highlighted" class="form-control" placeholder="e.g. Returns" value="{{ old('hero_highlighted') }}" />
            <small class="form-hint">Gold italic subheading word/phrase (Default: Returns).</small>
          </div>
        </div>

        <!-- 7. Hero Description (full width) -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" for="seasonDesc">Hero Description</label>
          <textarea id="seasonDesc" name="description" rows="3" class="form-control" placeholder="Couture, heritage and new faces under the lights — three nights of glamour in Kozhikode. Claim your place on the front row.">{{ old('description') }}</textarea>
          <small class="form-hint">Hero tagline displayed under the title in the homepage runway hero section.</small>
        </div>

        <!-- 8. Runway Nights / Event Schedule Cards (Details Modal) -->
        <div class="form-group" style="margin-top: 1.25rem; margin-bottom: 0;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
            <div>
              <label class="form-label" style="margin-bottom:0.15rem; font-size:0.95rem; font-weight:600; color:var(--gold,#e5b83d);">Runway Night Cards (Details Modal)</label>
              <p class="form-hint" style="margin-top:0;">Configure the 3 night event cards displayed in the Season Details modal (Reference Design).</p>
            </div>
          </div>

          <div style="display:flex; flex-direction:column; gap:0.85rem;">
            @for ($i = 0; $i < 3; $i++)
              @php
                $nightOrd = match($i) { 0 => 'Night 1', 1 => 'Night 2', 2 => 'Night 3' };
                $defaultNum = str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT);
                $defaultTitle = match($i) { 0 => 'Night One — New Faces', 1 => 'Night Two — Heritage & Handloom', 2 => 'Night Three — The Grand Finale' };
                $defaultMeta = match($i) { 0 => '18 December · 7:00 PM', 1 => '19 December · 7:00 PM', 2 => '20 December · 7:30 PM' };
              @endphp
              <div style="background:#0a0a0a; border:1px solid rgba(229,184,61,0.2); border-radius:6px; padding:0.85rem 1rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.6rem; border-bottom:1px solid rgba(255,255,255,0.06); padding-bottom:0.35rem;">
                  <span style="font-size:0.8rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:var(--gold,#e5b83d);">{{ $nightOrd }} Card</span>
                  <span style="font-size:0.75rem; color:#888;">Card #{{ $defaultNum }}</span>
                </div>
                <div class="form-row" style="margin-bottom:0.6rem;">
                  <div class="form-group" style="flex:0 0 90px; min-width:80px; margin-bottom:0;">
                    <label class="form-label" style="font-size:0.75rem;" for="eventNum{{ $i }}">Number</label>
                    <input type="text" id="eventNum{{ $i }}" name="schedule[{{ $i }}][number]" class="form-control" style="padding:0.45rem 0.65rem; font-size:0.85rem;" placeholder="{{ $defaultNum }}" />
                  </div>
                  <div class="form-group" style="flex:1; margin-bottom:0;">
                    <label class="form-label" style="font-size:0.75rem;" for="eventMeta{{ $i }}">Date & Time Meta</label>
                    <input type="text" id="eventMeta{{ $i }}" name="schedule[{{ $i }}][meta]" class="form-control" style="padding:0.45rem 0.65rem; font-size:0.85rem;" placeholder="e.g. {{ $defaultMeta }}" />
                  </div>
                  <div class="form-group" style="flex:1.5; margin-bottom:0;">
                    <label class="form-label" style="font-size:0.75rem;" for="eventTitle{{ $i }}">Title</label>
                    <input type="text" id="eventTitle{{ $i }}" name="schedule[{{ $i }}][title]" class="form-control" style="padding:0.45rem 0.65rem; font-size:0.85rem;" placeholder="e.g. {{ $defaultTitle }}" />
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                  <label class="form-label" style="font-size:0.75rem;" for="eventDesc{{ $i }}">Description</label>
                  <textarea id="eventDesc{{ $i }}" name="schedule[{{ $i }}][description]" rows="2" class="form-control" style="padding:0.45rem 0.65rem; font-size:0.85rem;" placeholder="Brief description of the collections, designers and showcase for this night..."></textarea>
                </div>
              </div>
            @endfor
          </div>
        </div>
      </div>

      <div class="admin-modal__footer">
        <button type="button" class="btn-subtle" onclick="closeSeasonModal()">Cancel</button>
        <button type="submit" class="btn-gold" id="seasonSubmitBtn">
          <span>Save Runway Season</span>
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('styles')
<style>
  .module-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.75rem; gap: 1rem; flex-wrap: wrap; }
  .module-title { font-family: var(--font-serif); font-size: 1.85rem; color: var(--text-primary); font-weight: 700; }
  .module-title em { font-style: italic; color: var(--gold); font-weight: 300; }
  .module-subtitle { font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem; }
  .btn-gold { display: inline-flex; align-items: center; gap: 0.5rem; background: var(--gold); color: #000; padding: 0.65rem 1.25rem; border-radius: 6px; font-weight: 600; font-size: 0.85rem; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s ease; }
  .btn-gold:hover { background: #f0c54e; }
  .btn-gold svg { width: 16px; height: 16px; stroke: currentColor; stroke-width: 2.2; fill: none; }
  .btn-subtle { background: #222; color: #fff; border: 1px solid var(--border-subtle); padding: 0.55rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.85rem; text-decoration: none; }
  .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.8rem; }

  .seasons-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
  }
  .season-card {
    display: flex;
    flex-direction: column;
    position: relative;
    border: 1px solid var(--border-subtle);
    transition: transform 0.2s ease, border-color 0.2s ease;
  }
  .season-card:hover {
    border-color: rgba(229, 184, 61, 0.4);
    transform: translateY(-2px);
  }
  .season-card__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
  }
  .season-card__number {
    font-family: var(--font-serif);
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--gold);
    line-height: 1;
  }
  .season-card__title {
    font-size: 1.25rem;
    color: #fff;
    font-weight: 600;
    margin-bottom: 0.35rem;
  }
  .season-card__meta, .season-card__dates {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-bottom: 0.35rem;
  }
  .season-card__meta svg, .season-card__dates svg {
    width: 14px;
    height: 14px;
    stroke: var(--gold);
    stroke-width: 2;
    fill: none;
  }
  .season-card__desc {
    font-size: 0.85rem;
    color: var(--text-secondary);
    line-height: 1.5;
    margin: 0.75rem 0 1.25rem;
    flex: 1;
  }
  .season-card__stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    background: #080808;
    border: 1px solid var(--border-subtle);
    border-radius: 6px;
    padding: 0.75rem;
    text-align: center;
    margin-bottom: 1.25rem;
  }
  .season-stat {
    display: flex;
    flex-direction: column;
  }
  .season-stat__num {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--gold);
  }
  .season-stat__label {
    font-size: 0.7rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .season-card__actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 0.75rem;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
  }

  .status-pill { padding: 0.2rem 0.55rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; text-transform: capitalize; }
  .status-pill--published { background: rgba(34, 197, 94, 0.12); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); }
  .status-pill--draft { background: rgba(234, 179, 8, 0.12); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.3); }
  .status-pill--archived { background: rgba(163, 163, 163, 0.12); color: #a3a3a3; border: 1px solid rgba(163, 163, 163, 0.3); }

  .action-btn { width: 32px; height: 32px; border-radius: 6px; background: #1a1a1a; border: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: center; color: var(--text-secondary); cursor: pointer; }
  .action-btn svg { width: 14px; height: 14px; stroke: currentColor; stroke-width: 2; fill: none; }
  .action-btn--danger:hover { background: rgba(239, 68, 68, 0.2); color: #ef4444; border-color: #ef4444; }

  /* Active Season Indicator & Action */
  .season-card--active {
    border-color: var(--gold) !important;
    box-shadow: 0 0 25px rgba(229, 184, 61, 0.15);
  }
  .btn-activate {
    background: transparent;
    border: 1px solid rgba(229, 184, 61, 0.5);
    color: var(--gold);
    padding: 0.35rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    transition: all 0.2s ease;
  }
  .btn-activate:hover {
    background: var(--gold);
    color: #000;
  }
  .badge-active-live {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--gold);
    background: rgba(229, 184, 61, 0.12);
    border: 1px solid rgba(229, 184, 61, 0.35);
    padding: 0.35rem 0.65rem;
    border-radius: 6px;
  }
  .live-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--gold);
    box-shadow: 0 0 8px var(--gold);
    display: inline-block;
  }

  /* Modal Layout & Responsiveness */
  .admin-modal {
    position: fixed;
    inset: 0;
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    overflow-x: hidden;
    overflow-y: auto;
  }
  .admin-modal__backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    z-index: 1;
  }
  .admin-modal__dialog {
    position: relative;
    background: var(--bg-card, #111111);
    border: 1px solid var(--border-gold, rgba(229, 184, 61, 0.3));
    border-radius: 10px;
    width: 92%;
    max-width: 780px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    z-index: 2;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.9), 0 0 25px rgba(229, 184, 61, 0.08);
    overflow: hidden;
    margin: auto;
  }
  .admin-modal__form {
    display: flex;
    flex-direction: column;
    height: 100%;
    max-height: 90vh;
    min-height: 0;
    overflow: hidden;
  }
  .admin-modal__header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 1.35rem 1.75rem;
    border-bottom: 1px solid var(--border-subtle, #222222);
    background: #0d0d0d;
    flex-shrink: 0;
    gap: 1rem;
  }
  .admin-modal__header-title h3 {
    font-family: var(--font-serif, 'Playfair Display', serif);
    color: var(--gold, #E5B83D);
    font-size: 1.35rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    margin-bottom: 0.25rem;
  }
  .admin-modal__header-subtitle {
    font-size: 0.8rem;
    color: var(--text-muted, #777777);
    line-height: 1.4;
  }
  .modal-close-btn {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--border-subtle, #222222);
    color: var(--text-muted, #888888);
    width: 34px;
    height: 34px;
    border-radius: 6px;
    font-size: 1.35rem;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
  }
  .modal-close-btn:hover {
    color: #ffffff;
    background: rgba(239, 68, 68, 0.15);
    border-color: rgba(239, 68, 68, 0.4);
  }
  .admin-modal__body {
    padding: 1.75rem;
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
    overflow-x: hidden;
  }
  /* Custom scrollbar for modal body */
  .admin-modal__body::-webkit-scrollbar {
    width: 6px;
  }
  .admin-modal__body::-webkit-scrollbar-track {
    background: #080808;
  }
  .admin-modal__body::-webkit-scrollbar-thumb {
    background: #2a2a2a;
    border-radius: 3px;
  }
  .admin-modal__body::-webkit-scrollbar-thumb:hover {
    background: var(--gold, #E5B83D);
  }
  .admin-modal__footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 0.85rem;
    padding: 1.1rem 1.75rem;
    border-top: 1px solid var(--border-subtle, #222222);
    background: #0d0d0d;
    flex-shrink: 0;
  }
  .form-group {
    margin-bottom: 1.25rem;
  }
  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
    margin-bottom: 1.25rem;
  }
  .form-label {
    display: block;
    font-size: 0.82rem;
    font-weight: 500;
    color: #d1d5db;
    margin-bottom: 0.45rem;
    letter-spacing: 0.02em;
  }
  .form-control {
    width: 100%;
    background: #080808;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 6px;
    padding: 0.65rem 0.9rem;
    color: #ffffff;
    font-size: 0.88rem;
    font-family: var(--font-sans);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }
  .form-control:focus {
    outline: none;
    border-color: var(--gold, #E5B83D);
    box-shadow: 0 0 0 3px rgba(229, 184, 61, 0.15);
  }
  .form-control::placeholder {
    color: #555555;
  }
  .form-hint {
    display: block;
    font-size: 0.74rem;
    color: var(--text-muted, #777777);
    margin-top: 0.35rem;
    line-height: 1.4;
  }
  .required {
    color: #ef4444;
    font-weight: bold;
  }
  .season-hero-preview {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 0.75rem;
    padding: 0.65rem 0.85rem;
    background: #080808;
    border: 1px solid var(--border-subtle, #222222);
    border-radius: 6px;
  }
  .season-hero-preview img {
    height: 60px;
    width: 90px;
    border-radius: 4px;
    object-fit: cover;
    border: 1px solid var(--border-gold, rgba(229, 184, 61, 0.3));
    background: #000;
  }
  .season-hero-preview__meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex: 1;
    min-width: 0;
    gap: 0.75rem;
  }
  .season-hero-preview__name {
    font-size: 0.78rem;
    color: var(--text-secondary, #A3A3A3);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .btn-clear-preview {
    background: none;
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #ef4444;
    padding: 0.3rem 0.65rem;
    border-radius: 4px;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
  }
  .btn-clear-preview:hover {
    background: rgba(239, 68, 68, 0.15);
  }
  .modal-alert {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.85rem 1rem;
    border-radius: 6px;
    margin-bottom: 1.25rem;
    font-size: 0.82rem;
    line-height: 1.4;
  }
  .modal-alert--danger {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.35);
    color: #fca5a5;
  }
  .modal-alert--danger ul {
    margin: 0.35rem 0 0 1.2rem;
    padding: 0;
  }
  .btn-gold:disabled, .btn-subtle:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  /* Responsive Breakpoints */
  @media (max-width: 768px) {
    .admin-modal {
      padding: 1rem;
    }
    .admin-modal__dialog {
      width: 95%;
      max-height: 92vh;
    }
    .admin-modal__form {
      max-height: 92vh;
    }
    .admin-modal__header,
    .admin-modal__body,
    .admin-modal__footer {
      padding: 1.25rem;
    }
    .form-row {
      gap: 1rem;
    }
  }

  @media (max-width: 640px) {
    .admin-modal {
      padding: 0.5rem;
      align-items: flex-end;
    }
    .admin-modal__dialog {
      width: 100%;
      max-height: 95vh;
      border-radius: 10px 10px 0 0;
    }
    .admin-modal__form {
      max-height: 95vh;
    }
    .form-row {
      grid-template-columns: 1fr;
      gap: 1.15rem;
      margin-bottom: 1.15rem;
    }
    .form-group {
      margin-bottom: 1.15rem;
    }
    .admin-modal__footer {
      flex-direction: column-reverse;
      gap: 0.5rem;
    }
    .admin-modal__footer button {
      width: 100%;
      justify-content: center;
    }
  }
</style>
<script>
  let isSeasonSubmitting = false;

  function openSeasonModal() {
    isSeasonSubmitting = false;
    document.getElementById('seasonModalTitle').textContent = 'Add Runway Season';
    const submitBtn = document.getElementById('seasonSubmitBtn');
    submitBtn.innerHTML = '<span>Save Runway Season</span>';
    submitBtn.disabled = false;
    document.getElementById('seasonForm').action = "{{ route('admin.seasons.store') }}";
    document.getElementById('seasonMethodField').innerHTML = '';
    document.getElementById('seasonNumber').value = '';
    document.getElementById('seasonTitle').value = '';
    document.getElementById('seasonHeroHeading').value = 'The Runway';
    document.getElementById('seasonHeroHighlighted').value = 'Returns';
    document.getElementById('seasonYear').value = '';
    document.getElementById('seasonLocation').value = '';
    document.getElementById('seasonDates').value = '';
    document.getElementById('seasonStatus').value = 'published';
    document.getElementById('seasonIsCurrent').checked = false;
    document.getElementById('seasonDesc').value = '';
    document.getElementById('seasonHeroImage').value = '';
    document.getElementById('seasonHeroPreview').style.display = 'none';
    document.getElementById('seasonHeroImgTag').src = '';
    document.getElementById('seasonHeroPreviewName').textContent = '';
    for (let i = 0; i < 3; i++) {
      const numInput = document.getElementById('eventNum' + i);
      const metaInput = document.getElementById('eventMeta' + i);
      const titleInput = document.getElementById('eventTitle' + i);
      const descInput = document.getElementById('eventDesc' + i);
      if (numInput) numInput.value = '0' + (i + 1);
      if (metaInput) metaInput.value = '';
      if (titleInput) titleInput.value = '';
      if (descInput) descInput.value = '';
    }
    document.getElementById('seasonModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }

  function editSeason(season) {
    isSeasonSubmitting = false;
    document.getElementById('seasonModalTitle').textContent = 'Edit ' + season.title;
    const submitBtn = document.getElementById('seasonSubmitBtn');
    submitBtn.innerHTML = '<span>Update Runway Season</span>';
    submitBtn.disabled = false;
    document.getElementById('seasonForm').action = "/admin/seasons/" + season.id;
    document.getElementById('seasonMethodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('seasonNumber').value = season.number;
    document.getElementById('seasonTitle').value = season.title;
    document.getElementById('seasonHeroHeading').value = season.hero_heading || 'The Runway';
    document.getElementById('seasonHeroHighlighted').value = season.hero_highlighted || 'Returns';
    document.getElementById('seasonYear').value = season.year || '';
    document.getElementById('seasonLocation').value = season.location || '';
    document.getElementById('seasonDates').value = season.dates || '';
    document.getElementById('seasonStatus').value = season.status || 'published';
    document.getElementById('seasonIsCurrent').checked = Boolean(season.is_current);
    document.getElementById('seasonDesc').value = season.description || '';
    document.getElementById('seasonHeroImage').value = '';
    if (season.hero_image) {
      const url = season.hero_image.startsWith('http') ? season.hero_image : '/' + season.hero_image.replace(/^\//, '');
      document.getElementById('seasonHeroImgTag').src = url;
      document.getElementById('seasonHeroPreviewName').textContent = 'Current Cover Image';
      document.getElementById('seasonHeroPreview').style.display = 'flex';
    } else {
      document.getElementById('seasonHeroPreview').style.display = 'none';
      document.getElementById('seasonHeroImgTag').src = '';
      document.getElementById('seasonHeroPreviewName').textContent = '';
    }
    const events = (season.schedule && Array.isArray(season.schedule)) ? season.schedule : [];
    for (let i = 0; i < 3; i++) {
      const ev = events[i] || {};
      const numInput = document.getElementById('eventNum' + i);
      const metaInput = document.getElementById('eventMeta' + i);
      const titleInput = document.getElementById('eventTitle' + i);
      const descInput = document.getElementById('eventDesc' + i);
      if (numInput) numInput.value = ev.number || ('0' + (i + 1));
      if (metaInput) metaInput.value = ev.meta || '';
      if (titleInput) titleInput.value = ev.title || '';
      if (descInput) descInput.value = ev.description || '';
    }
    document.getElementById('seasonModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }

  function closeSeasonModal() {
    if (isSeasonSubmitting) return;
    document.getElementById('seasonModal').style.display = 'none';
    document.body.style.overflow = '';
  }

  function previewSeasonHero(input) {
    const preview = document.getElementById('seasonHeroPreview');
    const img = document.getElementById('seasonHeroImgTag');
    const nameTag = document.getElementById('seasonHeroPreviewName');
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        img.src = e.target.result;
        if (nameTag) nameTag.textContent = input.files[0].name;
        preview.style.display = 'flex';
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  function clearHeroImagePreview() {
    document.getElementById('seasonHeroImage').value = '';
    document.getElementById('seasonHeroPreview').style.display = 'none';
    document.getElementById('seasonHeroImgTag').src = '';
    document.getElementById('seasonHeroPreviewName').textContent = '';
  }

  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('seasonForm');
    if (form) {
      form.addEventListener('submit', function() {
        isSeasonSubmitting = true;
        const btn = document.getElementById('seasonSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span>Saving...</span>';
      });
    }

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        const modal = document.getElementById('seasonModal');
        if (modal && modal.style.display !== 'none') {
          closeSeasonModal();
        }
      }
    });

    @if ($errors->any())
      document.getElementById('seasonModal').style.display = 'flex';
      document.body.style.overflow = 'hidden';
    @endif
  });
</script>
@endsection
