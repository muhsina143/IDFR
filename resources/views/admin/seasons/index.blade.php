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
    <div class="admin-card season-card">
      @if($season->hero_image)
        <div class="season-card__cover" style="margin:-1.5rem -1.5rem 1rem -1.5rem; height:120px; overflow:hidden; border-top-left-radius:8px; border-top-right-radius:8px;">
          <img src="{{ $season->hero_image_url }}" alt="{{ $season->title }}" style="width:100%; height:100%; object-fit:cover;" />
        </div>
      @endif
      <div class="season-card__header">
        <div class="season-card__number">0{{ $season->number }}</div>
        <div class="season-card__status">
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
        <div style="display:flex; gap:0.5rem; align-items:center;">
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
<div id="seasonModal" class="admin-modal" style="display:none;">
  <div class="admin-modal__backdrop" onclick="closeSeasonModal()"></div>
  <div class="admin-modal__dialog">
    <div class="admin-modal__header">
      <h3 id="seasonModalTitle">Add Runway Season</h3>
      <button type="button" class="modal-close-btn" onclick="closeSeasonModal()">&times;</button>
    </div>
    <form id="seasonForm" action="{{ route('admin.seasons.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div id="seasonMethodField"></div>

      <div class="admin-modal__body">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Season Number <span class="required">*</span></label>
            <input type="number" id="seasonNumber" name="number" required min="1" class="form-control" placeholder="e.g. 5" />
          </div>
          <div class="form-group">
            <label class="form-label">Title <span class="required">*</span></label>
            <input type="text" id="seasonTitle" name="title" required class="form-control" placeholder="e.g. Season 5" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Year</label>
            <input type="text" id="seasonYear" name="year" class="form-control" placeholder="e.g. 2027" />
          </div>
          <div class="form-group">
            <label class="form-label">Host City / Venue</label>
            <input type="text" id="seasonLocation" name="location" class="form-control" placeholder="e.g. Kochi, Kerala" />
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Event Dates</label>
          <input type="text" id="seasonDates" name="dates" class="form-control" placeholder="e.g. 15 — 17 December 2027" />
        </div>

        <div class="form-group">
          <label class="form-label">Hero Cover Image (Optional)</label>
          <input type="file" id="seasonHeroImage" name="hero_image" accept="image/*" class="form-control" />
          <div id="seasonHeroPreview" style="margin-top:0.5rem; display:none;">
            <img src="" id="seasonHeroImgTag" style="height:70px; border-radius:4px; object-fit:cover; border:1px solid var(--border-subtle);" />
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Status <span class="required">*</span></label>
          <select id="seasonStatus" name="status" class="form-control">
            <option value="published">Published</option>
            <option value="draft">Draft</option>
            <option value="archived">Archived</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Overview / Theme</label>
          <textarea id="seasonDesc" name="description" rows="3" class="form-control" placeholder="Describe the season's focus, couture themes, and venue highlights..."></textarea>
        </div>
      </div>

      <div class="admin-modal__footer">
        <button type="button" class="btn-subtle" onclick="closeSeasonModal()">Cancel</button>
        <button type="submit" class="btn-gold" id="seasonSubmitBtn">Save Runway Season</button>
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

  /* Modal */
  .admin-modal {
    position: fixed;
    inset: 0;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .admin-modal__backdrop {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(4px);
  }
  .admin-modal__dialog {
    position: relative;
    background: var(--bg-card);
    border: 1px solid var(--border-gold);
    border-radius: 8px;
    width: 90%;
    max-width: 540px;
    z-index: 10;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.8);
  }
  .admin-modal__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-subtle);
  }
  .admin-modal__header h3 {
    font-family: var(--font-serif);
    color: var(--gold);
    font-size: 1.25rem;
  }
  .modal-close-btn {
    background: none;
    border: none;
    color: var(--text-muted);
    font-size: 1.5rem;
    cursor: pointer;
  }
  .admin-modal__body {
    padding: 1.5rem;
  }
  .admin-modal__footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    padding: 1.25rem 1.5rem;
    border-top: 1px solid var(--border-subtle);
    background: rgba(0, 0, 0, 0.3);
  }
  .form-group { margin-bottom: 1rem; }
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
  .form-label { display: block; font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.35rem; }
  .form-control { width: 100%; background: #080808; border: 1px solid var(--border-subtle); border-radius: 6px; padding: 0.6rem 0.85rem; color: #fff; font-size: 0.85rem; }
  .form-control:focus { outline: none; border-color: var(--gold); }
  .required { color: #ef4444; }
</style>
<script>
  function openSeasonModal() {
    document.getElementById('seasonModalTitle').textContent = 'Add Runway Season';
    document.getElementById('seasonForm').action = "{{ route('admin.seasons.store') }}";
    document.getElementById('seasonMethodField').innerHTML = '';
    document.getElementById('seasonNumber').value = '';
    document.getElementById('seasonTitle').value = '';
    document.getElementById('seasonYear').value = '';
    document.getElementById('seasonLocation').value = '';
    document.getElementById('seasonDates').value = '';
    document.getElementById('seasonStatus').value = 'published';
    document.getElementById('seasonDesc').value = '';
    document.getElementById('seasonHeroImage').value = '';
    document.getElementById('seasonHeroPreview').style.display = 'none';
    document.getElementById('seasonModal').style.display = 'flex';
  }

  function editSeason(season) {
    document.getElementById('seasonModalTitle').textContent = 'Edit ' + season.title;
    document.getElementById('seasonForm').action = "/admin/seasons/" + season.id;
    document.getElementById('seasonMethodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('seasonNumber').value = season.number;
    document.getElementById('seasonTitle').value = season.title;
    document.getElementById('seasonYear').value = season.year || '';
    document.getElementById('seasonLocation').value = season.location || '';
    document.getElementById('seasonDates').value = season.dates || '';
    document.getElementById('seasonStatus').value = season.status || 'published';
    document.getElementById('seasonDesc').value = season.description || '';
    document.getElementById('seasonHeroImage').value = '';
    if (season.hero_image) {
      const url = season.hero_image.startsWith('http') ? season.hero_image : '/' + season.hero_image.replace(/^\//, '');
      document.getElementById('seasonHeroImgTag').src = url;
      document.getElementById('seasonHeroPreview').style.display = 'block';
    } else {
      document.getElementById('seasonHeroPreview').style.display = 'none';
    }
    document.getElementById('seasonModal').style.display = 'flex';
  }

  function closeSeasonModal() {
    document.getElementById('seasonModal').style.display = 'none';
  }
</script>
@endsection
