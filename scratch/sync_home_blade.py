import re

with open('resources/views/idfr/home.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# Normalize line endings
text = text.replace('\r\n', '\n')

# 1. Replace Hero text
old_hero_info = '''          <p class="hero__info-label">IDFR Fashion Show · Season 4</p>
          <p class="hero__info-date">
            <svg class="icon" aria-hidden="true">
              <use href="#i-calendar" /></svg
            >18 — 20 December 2026 · Kozhikode
          </p>
          <p class="hero__info-text">
            Season 4 brings together emerging talent, established models,
            designers and fashion enthusiasts on one remarkable stage. Discover
            new faces, experience contemporary Indian fashion and witness the
            next generation of modelling talent take the runway.
          </p>
          <a class="hero__explore" href="#season-4" data-program="season-4">
            <span>Explore Season 4</span
            ><svg class="icon" aria-hidden="true">
              <use href="#i-arrow-right" />
            </svg>
          </a>'''

new_hero_info = '''          <p class="hero__info-label">IDFR Fashion Show · {{ $currentSeason ? $currentSeason->title : 'Season 4' }}</p>
          <p class="hero__info-date">
            <svg class="icon" aria-hidden="true">
              <use href="#i-calendar" /></svg
            >{{ $currentSeason ? $currentSeason->dates . ' · ' . $currentSeason->location : '18 — 20 December 2026 · Kozhikode' }}
          </p>
          <p class="hero__info-text">
            {{ $currentSeason && $currentSeason->description ? $currentSeason->description : 'Season 4 brings together emerging talent, established models, designers and fashion enthusiasts on one remarkable stage. Discover new faces, experience contemporary Indian fashion and witness the next generation of modelling talent take the runway.' }}
          </p>
          <a class="hero__explore" href="#season-4-banner" data-program="season-4">
            <span>Explore {{ $currentSeason ? $currentSeason->title : 'Season 4' }}</span
            ><svg class="icon" aria-hidden="true">
              <use href="#i-arrow-right" />
            </svg>
          </a>'''

if old_hero_info in text:
    text = text.replace(old_hero_info, new_hero_info)
    print("1. Updated Hero Section with dynamic season")
else:
    # Try fuzzy regex for hero info
    hero_pattern = r'<p class="hero__info-label">IDFR Fashion Show[\s\S]*?<span>Explore Season 4</span>[\s\S]*?</a>'
    if re.search(hero_pattern, text):
        text = re.sub(hero_pattern, new_hero_info, text)
        print("1. Updated Hero Section with regex")
    else:
        print("1. Hero section not matched")

# 2. Replace Season 4 Banner
old_banner = '''          <p class="season-banner__label" data-reveal="up">
            IDFR Fashion Show · Season 4
          </p>
          <h2
            class="season-banner__title"
            id="seasonBannerTitle"
            data-reveal="up"
            data-delay="80"
          >
            The Runway
            <em>Returns</em>
          </h2>
          <p class="season-banner__tagline" data-reveal="up" data-delay="140">
            Couture, heritage and new faces under the lights — three nights of
            glamour in Kozhikode. Claim your place on the front row.
          </p>
          <p class="season-banner__meta" data-reveal="up" data-delay="180">
            18 – 20 Dec 2026 · IDFR Fashion House
          </p>'''

new_banner = '''          <p class="season-banner__label" data-reveal="up">
            IDFR Fashion Show · {{ $currentSeason ? $currentSeason->title : 'Season 4' }}
          </p>
          <h2
            class="season-banner__title"
            id="seasonBannerTitle"
            data-reveal="up"
            data-delay="80"
          >
            The Runway
            <em>Returns</em>
          </h2>
          <p class="season-banner__tagline" data-reveal="up" data-delay="140">
            {{ $currentSeason && $currentSeason->description ? $currentSeason->description : 'Couture, heritage and new faces under the lights — three nights of glamour in Kozhikode. Claim your place on the front row.' }}
          </p>
          <p class="season-banner__meta" data-reveal="up" data-delay="180">
            {{ $currentSeason ? $currentSeason->dates . ' · ' . $currentSeason->location : '18 – 20 Dec 2026 · IDFR Fashion House' }}
          </p>'''

if old_banner in text:
    text = text.replace(old_banner, new_banner)
    print("2. Updated Season 4 Banner with dynamic season")
else:
    print("2. Season banner exact match failed, trying regex...")
    banner_pattern = r'<p class="season-banner__label"[\s\S]*?18 – 20 Dec 2026[\s\S]*?</p>'
    if re.search(banner_pattern, text):
        text = re.sub(banner_pattern, new_banner, text)
        print("2. Updated Season Banner with regex")

# 3. Replace <template id="modelsData"> in home.blade.php
dynamic_models_template = '''<template id="modelsData">
      @if(isset($models) && $models->isNotEmpty())
        @foreach($models as $m)
        <article
          data-id="{{ $m->id }}"
          data-gender="{{ $m->gender }}"
          data-level="{{ $m->level }}"
          data-premium="{{ $m->is_featured ? 'true' : 'false' }}"
          @if($m->is_kid) data-kid="true" @endif
        >
          <h3>{{ $m->name }}</h3>
          <img src="{{ $m->photo_url }}" alt="{{ $m->name }}" />
          <dl data-stats>
            @if($m->age)<dt>Age</dt><dd data-age>{{ $m->age }}</dd>@endif
            @if($m->height)<dt>Height</dt><dd data-height>{{ $m->height }}</dd>@endif
            @if($m->location)<dt>Location</dt><dd data-location>{{ $m->location }}</dd>@endif
            @if($m->experience)<dt>Experience</dt><dd data-experience>{{ $m->experience }}</dd>@endif
          </dl>
          @if(!empty($m->types) && is_array($m->types))
          <ul data-types>
            @foreach($m->types as $t)
              <li>{{ $t }}</li>
            @endforeach
          </ul>
          @endif
          @if(!empty($m->portfolio_images) && is_array($m->portfolio_images))
          <ul data-portfolio>
            @foreach($m->portfolio_images as $pImg)
              @php $pUrl = str_starts_with($pImg, 'http') ? $pImg : asset(ltrim($pImg, '/')); @endphp
              <li><img src="{{ $pUrl }}" alt="" /></li>
            @endforeach
          </ul>
          @endif
          @if(!empty($m->measurements) && is_array($m->measurements))
          <dl data-measurements>
            @foreach($m->measurements as $mLabel => $mVal)
              <dt>{{ $mLabel }}</dt>
              <dd>{{ $mVal }}</dd>
            @endforeach
          </dl>
          @endif
          @if(!empty($m->skills) && is_array($m->skills))
          <ul data-skills>
            @foreach($m->skills as $skill)
              <li>{{ $skill }}</li>
            @endforeach
          </ul>
          @endif
          @if(!empty($m->specialities) && is_array($m->specialities))
          <ul data-specialities>
            @foreach($m->specialities as $spec)
              <li>{{ $spec }}</li>
            @endforeach
          </ul>
          @endif
          @if($m->availability)<p data-availability>{{ $m->availability }}</p>@endif
          @if($m->description)<p data-description>{{ $m->description }}</p>@endif
        </article>
        @endforeach
      @endif
    </template>'''

models_template_pattern = r'<template id="modelsData">[\s\S]*?</template>'
if re.search(models_template_pattern, text):
    text = re.sub(models_template_pattern, dynamic_models_template, text)
    print("3. Replaced modelsData with dynamic database loop in home.blade.php")
else:
    print("3. modelsData pattern not found in home.blade.php")

# 4. Replace <template id="creativeProsData"> in home.blade.php
dynamic_pros_template = '''<template id="creativeProsData">
      @if(isset($professionals) && $professionals->isNotEmpty())
        @foreach($professionals as $pro)
        <article data-id="{{ $pro->slug }}" data-category="{{ $pro->category }}" data-brand="IDFR">
          <h3>{{ $pro->name }}</h3>
          <img src="{{ $pro->photo_url }}" alt="{{ $pro->name }}" width="700" height="875" />
          <dl>
            <dt>Location</dt>
            <dd data-location>{{ $pro->location ?: 'Kerala, India' }}</dd>
            <dt>Experience</dt>
            <dd data-experience>{{ $pro->experience ?: 'Professional' }}</dd>
          </dl>
          <p data-bio>{{ $pro->biography ?: $pro->short_intro }}</p>
          @if(!empty($pro->services) && is_array($pro->services))
          <ul data-services>
            @foreach($pro->services as $srv)
              <li>{{ $srv }}</li>
            @endforeach
          </ul>
          @endif
          @if(!empty($pro->portfolio_images) && is_array($pro->portfolio_images))
          <ul data-portfolio>
            @foreach($pro->portfolio_images as $pImg)
              @php 
                $pSrc = is_array($pImg) ? ($pImg['image'] ?? '') : $pImg;
                $pCap = is_array($pImg) ? ($pImg['caption'] ?? 'Runway Portfolio') : 'Runway Portfolio';
                $pUrl = str_starts_with($pSrc, 'http') ? $pSrc : asset(ltrim($pSrc, '/'));
              @endphp
              <li><figure><img src="{{ $pUrl }}" alt="{{ $pCap }}" width="1200" height="900" /><figcaption>{{ $pCap }}</figcaption></figure></li>
            @endforeach
          </ul>
          @endif
        </article>
        @endforeach
      @endif
    </template>'''

pros_template_pattern = r'<template id="creativeProsData">[\s\S]*?</template>'
if re.search(pros_template_pattern, text):
    text = re.sub(pros_template_pattern, dynamic_pros_template, text)
    print("4. Replaced creativeProsData with dynamic database loop in home.blade.php")
else:
    print("4. creativeProsData pattern not found in home.blade.php")

# 5. Replace <template id="program-season-4"> in home.blade.php
dynamic_program_s4 = '''<template id="program-season-4">
      <p class="info__label">IDFR Fashion Show</p>
      <h2 class="info__title" id="infoModalTitle">{{ $currentSeason ? $currentSeason->title : 'Season 4' }}</h2>
      <p class="info__intro">
        {{ $currentSeason && $currentSeason->description ? $currentSeason->description : 'Three runway nights celebrating emerging designers, established names and the next generation of Indian modelling talent. Season 4 brings couture, resort, menswear and handloom collections to one stage in Kozhikode.' }}
      </p>
      <dl class="info__facts">
        <div>
          <dt>Dates</dt>
          <dd>{{ $currentSeason ? $currentSeason->dates : '18 – 20 Dec 2026' }}</dd>
        </div>
        <div>
          <dt>Venue</dt>
          <dd>{{ $currentSeason ? $currentSeason->location : 'IDFR Fashion House' }}</dd>
        </div>
        <div>
          <dt>Designers</dt>
          <dd>{{ isset($designers) && $designers->count() > 0 ? $designers->count() . ' Labels' : '24 Labels' }}</dd>
        </div>
        <div>
          <dt>Models</dt>
          <dd>{{ isset($models) && $models->count() > 0 ? $models->count() . ' Talent' : '60+ Talent' }}</dd>
        </div>
      </dl>
      <div class="info__schedule">
        <article>
          <h3>Day 1 · Opening Night</h3>
          <p>Debut collections walked by models from the Season 4 open auditions.</p>
        </article>
        <article>
          <h3>Day 2 · Designer Showcase</h3>
          <p>Evening gowns, bridal couture and prêt lines from across South India.</p>
        </article>
        <article>
          <h3>Day 3 · Grand Finale</h3>
          <p>Couture showcases, awards and the closing walk of {{ $currentSeason ? $currentSeason->title : 'Season 4' }}.</p>
        </article>
      </div>
    </template>'''

program_pattern = r'<template id="program-season-4">[\s\S]*?</template>'
if re.search(program_pattern, text):
    text = re.sub(program_pattern, dynamic_program_s4, text)
    print("5. Replaced program-season-4 with dynamic database modal in home.blade.php")
else:
    print("5. program-season-4 pattern not found in home.blade.php")

with open('resources/views/idfr/home.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)
print("Saved home.blade.php successfully")
