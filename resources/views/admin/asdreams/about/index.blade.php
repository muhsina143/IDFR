@extends('admin.layouts.app')

@section('title', 'About Page Content Management')
@section('page_title', 'About Page Content')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">About Page <em>Content &amp; Story</em></h2>
    <p class="module-subtitle">Manage hero banner, video showreel cover, experience stats, studio story, bullet highlights, and studio interior media.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('asd.about') }}" target="_blank" class="btn-subtle">
      <svg viewBox="0 0 24 24" width="14" height="14"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
      <span>View Live About Page</span>
    </a>
  </div>
</div>

<form action="{{ route('admin.asdreams.about.update') }}" method="POST" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Main Content Sections -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
      <!-- Hero Header & Stats -->
      <div class="admin-card">
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Hero Header &amp; Experience</h3>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.25rem;">
          <div class="form-group">
            <label class="form-label" for="title">Page Title <span style="color: var(--gold);">*</span></label>
            <input type="text" id="title" name="title" value="{{ old('title', $about->title) }}" required class="form-control" />
            @error('title') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="years_of_experience">Years of Experience <span style="color: var(--gold);">*</span></label>
            <input type="number" id="years_of_experience" name="years_of_experience" value="{{ old('years_of_experience', $about->years_of_experience) }}" min="0" max="100" class="form-control" />
            @error('years_of_experience') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>
      </div>

      <!-- How We Work Section -->
      <div class="admin-card">
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">"How We Work" / Approach</h3>

        <div class="form-group">
          <label class="form-label" for="how_we_work_title">Section Title <span style="color: var(--gold);">*</span></label>
          <input type="text" id="how_we_work_title" name="how_we_work_title" value="{{ old('how_we_work_title', $about->how_we_work_title) }}" required class="form-control" />
          @error('how_we_work_title') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label" for="how_we_work_text">Philosophy &amp; Approach Paragraph</label>
          <textarea id="how_we_work_text" name="how_we_work_text" rows="3" class="form-control">{{ old('how_we_work_text', $about->how_we_work_text) }}</textarea>
          @error('how_we_work_text') <span class="form-error">{{ $message }}</span> @enderror
        </div>
      </div>

      <!-- The Story Section -->
      <div class="admin-card">
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Our Studio Story</h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
          <div class="form-group">
            <label class="form-label" for="story_heading_main">Story Heading <span style="color: var(--gold);">*</span></label>
            <input type="text" id="story_heading_main" name="story_heading_main" value="{{ old('story_heading_main', $about->story_heading_main) }}" required class="form-control" placeholder="e.g. A space built for" />
            @error('story_heading_main') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="story_heading_highlight">Highlight Italic Word</label>
            <input type="text" id="story_heading_highlight" name="story_heading_highlight" value="{{ old('story_heading_highlight', $about->story_heading_highlight) }}" class="form-control" placeholder="e.g. visionaries" />
            @error('story_heading_highlight') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group" style="grid-column: span 2;">
            <label class="form-label" for="story_paragraph_1">Primary Story Paragraph</label>
            <textarea id="story_paragraph_1" name="story_paragraph_1" rows="4" class="form-control">{{ old('story_paragraph_1', $about->story_paragraph_1) }}</textarea>
            @error('story_paragraph_1') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group" style="grid-column: span 2;">
            <label class="form-label" for="story_bullets_raw">Key Facility Bullets (One per line)</label>
            <textarea id="story_bullets_raw" name="story_bullets_raw" rows="4" class="form-control">{{ old('story_bullets_raw', is_array($about->story_bullets) ? implode("\n", $about->story_bullets) : '') }}</textarea>
            <span class="form-hint">Each line will become a golden bullet with an icon.</span>
            @error('story_bullets_raw') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group" style="grid-column: span 2;">
            <label class="form-label" for="story_extended_text">Secondary Story Paragraph</label>
            <textarea id="story_extended_text" name="story_extended_text" rows="4" class="form-control">{{ old('story_extended_text', $about->story_extended_text) }}</textarea>
            @error('story_extended_text') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>
      </div>
    </div>

    <!-- Media Controls Sidebar -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
      <!-- Hero Banner Image -->
      <div class="admin-card">
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Hero Banner Backdrop</h3>

        @if($about->banner_image_url)
          <div style="margin-bottom: 0.75rem; border-radius: 4px; overflow: hidden; background: #000;">
            <img src="{{ $about->banner_image_url }}" alt="Banner" style="max-height: 100px; width: 100%; object-fit: cover; display: block;" />
          </div>
        @endif
        <div class="form-group">
          <input type="file" id="banner_image" name="banner_image" accept="image/*" class="form-control" />
          <span class="form-hint">Wide panorama photo for the page top header.</span>
          @error('banner_image') <span class="form-error">{{ $message }}</span> @enderror
        </div>
      </div>

      <!-- Video Showreel -->
      <div class="admin-card">
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Video Showreel</h3>

        @if($about->video_cover_image_url)
          <div style="margin-bottom: 0.75rem; border-radius: 4px; overflow: hidden; background: #000;">
            <img src="{{ $about->video_cover_image_url }}" alt="Video cover" style="max-height: 100px; width: 100%; object-fit: cover; display: block;" />
          </div>
        @endif
        <div class="form-group">
          <label class="form-label" for="video_cover_image">Cover Poster Image</label>
          <input type="file" id="video_cover_image" name="video_cover_image" accept="image/*" class="form-control" />
          @error('video_cover_image') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label" for="video_url">Video Stream / MP4 URL</label>
          <input type="text" id="video_url" name="video_url" value="{{ old('video_url', $about->video_url) }}" class="form-control" placeholder="videos/showreel.mp4" />
          @error('video_url') <span class="form-error">{{ $message }}</span> @enderror
        </div>
      </div>

      <!-- Studio Interior Photo -->
      <div class="admin-card">
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Studio Space Image</h3>

        @if($about->studio_image_url)
          <div style="margin-bottom: 0.75rem; border-radius: 4px; overflow: hidden; background: #000;">
            <img src="{{ $about->studio_image_url }}" alt="Studio" style="max-height: 100px; width: 100%; object-fit: cover; display: block;" />
          </div>
        @endif
        <div class="form-group">
          <input type="file" id="studio_image" name="studio_image" accept="image/*" class="form-control" />
          @error('studio_image') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label" for="studio_caption_title">Badge Heading</label>
          <input type="text" id="studio_caption_title" name="studio_caption_title" value="{{ old('studio_caption_title', $about->studio_caption_title) }}" class="form-control" placeholder="STUDIO A · CYCLORAMA" />
          @error('studio_caption_title') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="margin-top: 0.75rem;">
          <label class="form-label" for="studio_caption_subtitle">Badge Subtitle</label>
          <input type="text" id="studio_caption_subtitle" name="studio_caption_subtitle" value="{{ old('studio_caption_subtitle', $about->studio_caption_subtitle) }}" class="form-control" placeholder="1800 sq.ft Infinity Wall" />
          @error('studio_caption_subtitle') <span class="form-error">{{ $message }}</span> @enderror
        </div>
      </div>

      <!-- Save Button -->
      <div class="admin-card" style="background: rgba(245, 200, 66, 0.04); border-color: var(--gold-a35);">
        <button type="submit" class="btn-gold" style="width: 100%; justify-content: center; padding: 0.85rem;">
          Save About Changes
        </button>
      </div>
    </div>
  </div>
</form>
@endsection

