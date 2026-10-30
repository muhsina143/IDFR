import re

with open('resources/views/idfr/home.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# 1. Update Preload link
preload_pattern = r'@section\(\'preload\'\)[\s\S]*?@endsection'
new_preload = '''@section('preload')
<link rel="preload" as="image" href="{{ $currentSeason && $currentSeason->hero_image ? $currentSeason->hero_image_url : asset('images/hero/hero-01.jpg') }}" />
@endsection'''
text = re.sub(preload_pattern, new_preload, text)

# 2. Update Hero vertical year
text = re.sub(
    r'<p class="hero__vertical"[^>]*>[\s\S]*?Runway\s+2026[\s\S]*?</p>',
    '''<p class="hero__vertical" aria-hidden="true">
            IDFR Presents <span></span> Runway {{ $currentSeason && $currentSeason->year ? $currentSeason->year : '2026' }}
          </p>''',
    text
)

# 3. Update Hero Big Title (solid)
solid_title_pattern = r'<div class="hero__title-layer hero__title-layer--solid">[\s\S]*?</div>\s*</div>'
new_solid_title = '''<div class="hero__title-layer hero__title-layer--solid">
            <div class="hero__title-box">
              <p class="hero__tagline">Where talent meets the runway</p>
              <h1 class="hero__title">
                <span class="visually-hidden">IDFR Fashion Show </span>
                @if($currentSeason && str_contains($currentSeason->title, ' '))
                  @php $parts = explode(' ', $currentSeason->title, 2); @endphp
                  <span class="hero__title-a">{{ $parts[0] }}</span><span class="hero__title-b">-{{ $parts[1] }}</span>
                @elseif($currentSeason)
                  <span class="hero__title-a">{{ $currentSeason->title }}</span>
                @else
                  <span class="hero__title-a">Season</span><span class="hero__title-b">-4</span>
                @endif
              </h1>
            </div>
          </div>'''
text = re.sub(solid_title_pattern, new_solid_title, text)

# 4. Update Hero Big Title (outline)
outline_title_pattern = r'<div\s+class="hero__title-layer hero__title-layer--outline"[\s\S]*?</div>\s*</div>'
new_outline_title = '''<div
            class="hero__title-layer hero__title-layer--outline"
            aria-hidden="true"
          >
            <div class="hero__title-box">
              <p class="hero__tagline">Where talent meets the runway</p>
              <p class="hero__title">
                @if($currentSeason && str_contains($currentSeason->title, ' '))
                  @php $parts = explode(' ', $currentSeason->title, 2); @endphp
                  <span class="hero__title-a">{{ $parts[0] }}</span><span class="hero__title-b">-{{ $parts[1] }}</span>
                @elseif($currentSeason)
                  <span class="hero__title-a">{{ $currentSeason->title }}</span>
                @else
                  <span class="hero__title-a">Season</span><span class="hero__title-b">-4</span>
                @endif
              </p>
            </div>
          </div>'''
text = re.sub(outline_title_pattern, new_outline_title, text)

# 5. Update Hero Info Box
hero_info_pattern = r'<div class="hero__info">[\s\S]*?</div>\s*</section>'
new_hero_info = '''<div class="hero__info">
          <p class="hero__info-label">IDFR Fashion Show — {{ $currentSeason ? $currentSeason->title : 'Season 4' }}</p>
          <p class="hero__info-date">
            <svg class="icon" aria-hidden="true">
              <use href="#i-calendar" /></svg
            >{{ $currentSeason ? ($currentSeason->dates ? $currentSeason->dates . ' · ' . ($currentSeason->location ?? '') : $currentSeason->location) : '18 — 20 December 2026 · Kozhikode' }}
          </p>
          <p class="hero__info-text">
            {{ $currentSeason && $currentSeason->description ? $currentSeason->description : 'Season 4 brings together emerging talent, established models, designers and fashion enthusiasts on one remarkable stage. Discover new faces, experience contemporary Indian fashion and witness the next generation of modelling talent take the runway.' }}
          </p>
          <a class="hero__explore" href="#season-4-banner" data-program="season-4">
            <span>Explore {{ $currentSeason ? $currentSeason->title : 'Season 4' }}</span><svg class="icon" aria-hidden="true">
              <use href="#i-arrow-right" />
            </svg>
          </a>
        </div>
      </section>'''
text = re.sub(hero_info_pattern, new_hero_info, text)

# 6. Update Gallery Season Filter Buttons in home.blade.php
gallery_filters_pattern = r'<div class="gallery__filters"[\s\S]*?</div>\s*</div>\s*<div class="gallery__grid"'
new_gallery_filters = '''<div
              class="gallery__filters"
              role="group"
              aria-label="Filter gallery by season"
              data-reveal="up"
              data-delay="200"
            >
              <button
                class="filter-btn is-active"
                type="button"
                data-season="all"
                aria-pressed="true"
              >
                All
              </button>
              @if(isset($seasons) && $seasons->count() > 0)
                @foreach($seasons as $s)
                  <button
                    class="filter-btn"
                    type="button"
                    data-season="{{ $s->title }}"
                    aria-pressed="false"
                  >
                    {{ $s->title }}
                  </button>
                @endforeach
              @else
                <button class="filter-btn" type="button" data-season="Season 1" aria-pressed="false">Season 1</button>
                <button class="filter-btn" type="button" data-season="Season 2" aria-pressed="false">Season 2</button>
                <button class="filter-btn" type="button" data-season="Season 3" aria-pressed="false">Season 3</button>
              @endif
            </div>
          </div>

          <div class="gallery__grid"'''
text = re.sub(gallery_filters_pattern, new_gallery_filters, text)

# 7. Update heroSlidesData template
hero_slides_pattern = r'<template id="heroSlidesData">[\s\S]*?</template>'
new_hero_slides = '''<template id="heroSlidesData">
      @if($currentSeason && $currentSeason->hero_image)
      <figure data-designer="IDFR Runway">
        <img
          src="{{ $currentSeason->hero_image_url }}"
          alt="{{ $currentSeason->title }}"
        />
        <figcaption>{{ $currentSeason->title }}</figcaption>
      </figure>
      @endif
      <figure data-designer="Aarav Khanna">
        <img
          src="{{ asset('images/hero/hero-01.jpg') }}"
          alt="Model in a dramatic wide-leg ensemble under stage lights"
        />
        <figcaption>Neon Noir</figcaption>
      </figure>
      <figure data-designer="Meher Sinha">
        <img
          src="{{ asset('images/hero/hero-02.jpg') }}"
          alt="Model in a tailored trouser suit and sunglasses"
        />
        <figcaption>Golden Hour Tailoring</figcaption>
      </figure>
      <figure data-designer="Nikhil Joseph">
        <img
          src="{{ asset('images/hero/hero-03.jpg') }}"
          alt="Model wearing a long overcoat in front of historic architecture"
        />
        <figcaption>Heritage Overcoat</figcaption>
      </figure>
      <figure data-designer="Ritu Anand">
        <img
          src="{{ asset('images/hero/hero-04.jpg') }}"
          alt="Model twirling in a flowing gown"
        />
        <figcaption>The Twirl Gown</figcaption>
      </figure>
      <figure data-designer="Kunal Sethi">
        <img
          src="{{ asset('images/hero/hero-05.jpg') }}"
          alt="Male model in a sharp double-breasted suit"
        />
        <figcaption>Modern Gentleman</figcaption>
      </figure>
      <figure data-designer="Leena D\'Souza">
        <img
          src="{{ asset('images/hero/hero-06.jpg') }}"
          alt="Model in an off-shoulder velvet evening gown"
        />
        <figcaption>Velvet Evening</figcaption>
      </figure>
      <figure data-designer="Anjali Menon">
        <img
          src="{{ asset('images/hero/hero-07.jpg') }}"
          alt="Model walking the ramp with confident poise"
        />
        <figcaption>Emerald Silk</figcaption>
      </figure>
    </template>'''
text = re.sub(hero_slides_pattern, new_hero_slides, text)

with open('resources/views/idfr/home.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print('home.blade.php successfully updated with dynamic hero, filters, and slides!')
