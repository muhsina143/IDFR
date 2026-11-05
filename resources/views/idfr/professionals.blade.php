@extends('layouts.idfr')

@section('title', 'Creative Professionals | IDFR — Indian Designer Fashion Runway')
@section('description', 'Browse IDFR creative professionals — makeup artists, hairstylists, photographers, videographers, fashion and jewellery designers, drone pilots and editors.')
@section('canonical', route('idfr.professionals'))

@section('content')
<main id="main">
      <!-- ============ CREATIVE PROFESSIONALS ============ -->
      <!-- Each panel id doubles as its category link: professionals.html#creative-makeup-artist, etc. -->
      <section
        class="section section--dark creative-pros creative-pros--page"
        id="creative-professionals"
        data-spy="creative"
        aria-labelledby="creativeTitle"
      >
        <div class="container">
          <div class="section-head">
            <div>
              <p class="section-label" data-reveal="up">
                IDFR × ASDreams Studio
              </p>
              <h1
                class="section-title"
                id="creativeTitle"
                data-reveal="up"
                data-delay="100"
              >
                Creative
                <em>Professionals</em>
              </h1>
            </div>
            <p class="section-intro" data-reveal="up" data-delay="200">
              Discover talented creative professionals who bring fashion,
              beauty, photography and visual storytelling to life. Explore
              their profiles, experience, services and portfolios.
            </p>
          </div>

          <div class="creative-pros__tabs-wrap" data-reveal="up">
            <div
              class="filters filters--dark creative-pros__tabs"
              role="tablist"
              aria-label="Creative professional categories"
              id="creativeTabs"
            >
              @foreach($categories as $cat)
                @php
                  $panelId = ($cat->slug === 'fashion-designer') ? 'creative-fashion-designers' : 'creative-' . $cat->slug;
                @endphp
                <button
                  class="filter-btn {{ $loop->first ? 'is-active' : '' }}"
                  type="button"
                  role="tab"
                  id="creativeTab-{{ $cat->slug }}"
                  aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                  aria-controls="{{ $panelId }}"
                  data-category="{{ $cat->slug }}"
                  data-profession="{{ $cat->name }}"
                  {{ !$loop->first ? 'tabindex="-1"' : '' }}
                >
                  {{ $cat->name }}
                </button>
              @endforeach
            </div>
          </div>

          <!-- Cards are built dynamically for every category from the creativeProsData template -->
          @foreach($categories as $cat)
            @php
              $panelId = ($cat->slug === 'fashion-designer') ? 'creative-fashion-designers' : 'creative-' . $cat->slug;
            @endphp
            <div
              class="creative-pros__panel"
              role="tabpanel"
              id="{{ $panelId }}"
              aria-labelledby="creativeTab-{{ $cat->slug }}"
              tabindex="0"
              {{ !$loop->first ? 'hidden' : '' }}
            >
              <div class="creative-pros__grid" data-category="{{ $cat->slug }}"></div>
            </div>
          @endforeach
        </div>
      </section>

    </main>
@endsection
@section('page_content_templates')
<!-- ============ PAGE CONTENT ============
         Content for the sections built by js/script.js. <template> content is not displayed;
         the script reads it and renders each section. Edit text, images and options here. -->

    <!-- Creative Professionals (#creative-professionals + profile modal): one article per professional.
         data-id is the profile's permanent ID (share links use #pro-ID); data-category must match a tab's
         data-category. data-portfolio (three figures) is shown for photographers, videographers,
         fashion designers, drone pilots, photo &amp; video editors and jewellery designers.
         Names, photos and details are sample content for demonstration only. -->
    <template id="creativeProsData">
      @if(isset($professionals) && $professionals->isNotEmpty())
        @foreach($professionals as $pro)
        <article data-id="{{ $pro->slug }}" data-db-id="{{ $pro->id }}" data-pro-type="{{ $pro->category === 'fashion-designer' ? 'designer' : 'creative_professional' }}" data-category="{{ $pro->category }}" data-brand="IDFR">
          <h3>{{ $pro->name }}</h3>
          <img src="{{ $pro->photo_url }}" alt="{{ $pro->name }}" width="700" height="875" />
          <dl>
            @if($pro->professional_title)<dt>Title</dt><dd data-profession>{{ $pro->professional_title }}</dd>@endif
            @if($pro->location)<dt>Location</dt><dd data-location>{{ $pro->location }}</dd>@endif
            @if($pro->experience)<dt>Experience</dt><dd data-experience>{{ $pro->experience }}</dd>@endif
          </dl>
          @if($pro->biography)<p data-bio>{{ $pro->biography }}</p>@endif
          @if(!empty($pro->services) && is_array($pro->services))
          <ul data-services>
            @foreach($pro->services as $srv)
              <li>{{ $srv }}</li>
            @endforeach
          </ul>
          @endif
          @php
            $portfolioItems = method_exists($pro, 'getPortfolioItems') ? $pro->getPortfolioItems() : [];
          @endphp
          @if(!empty($portfolioItems))
          <ul data-portfolio>
            @foreach($portfolioItems as $pItem)
              <li><figure><img src="{{ $pItem['url'] }}" alt="{{ $pro->name }} - {{ $pItem['caption'] }}" width="1200" height="900" /><figcaption>{{ $pItem['caption'] }}</figcaption></figure></li>
            @endforeach
          </ul>
          @endif
        </article>
        @endforeach
      @endif
    </template>

    <!-- Model profile modal: booking enquiry form ("Contact / Enquire"). The heading becomes "Book <first name>". -->
    

    <!-- Info modal content: one template per program, opened by links with a matching data-program. -->
    @include('partials.season-info-template', ['season' => $currentSeason ?? null, 'templateId' => 'program-season-4'])
    @if(isset($currentSeason) && $currentSeason && $currentSeason->number != 4)
      @include('partials.season-info-template', ['season' => $currentSeason, 'templateId' => 'program-season-' . $currentSeason->number])
    @endif
    <template id="program-athuls-academy">
      <p class="info__label">Modelling & Grooming</p>
      <h2 class="info__title" id="infoModalTitle">Athul’s <em>Academy</em></h2>
      <p class="info__intro">
        A finishing school for models — from first walk to first casting. Small
        batches, working industry mentors and a direct pathway to IDFR runways
        and ASDreamStudio portfolio shoots.
      </p>
      <dl class="info__facts">
        <div>
          <dt>Batches</dt>
          <dd>Weekend & Evening</dd>
        </div>
        <div>
          <dt>Mentors</dt>
          <dd>Runway & Casting</dd>
        </div>
        <div>
          <dt>Next Intake</dt>
          <dd>19 Jan 2027</dd>
        </div>
      </dl>
      <div class="info__list">
        <div class="info__item">
          <span class="info__item-num">01</span>
          <p class="info__item-meta">10 Weeks · Beginners</p>
          <h3 class="info__item-name">Model Training</h3>
          <p class="info__item-desc">
            Runway walk, posture, turns, posing and camera confidence.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">02</span>
          <p class="info__item-meta">6 Weeks · All levels</p>
          <h3 class="info__item-name">Grooming &amp; Etiquette</h3>
          <p class="info__item-desc">
            Skincare, fitness, wardrobe, communication and interview skills.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">03</span>
          <p class="info__item-meta">1 Day · With ASDreamStudio</p>
          <h3 class="info__item-name">Portfolio Shoot</h3>
          <p class="info__item-desc">
            Studio and outdoor looks, retouched and ready for agencies.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">04</span>
          <p class="info__item-meta">Ongoing</p>
          <h3 class="info__item-name">Casting &amp; Industry Opportunities</h3>
          <p class="info__item-desc">
            Audition preparation, casting calls and placement with IDFR
            partners.
          </p>
        </div>
      </div>
      <div class="info__actions">
        <button
          class="btn btn--gold"
          type="button"
          data-auth="register"
          data-role="fresher"
        >
          <span>Apply Now</span
          ><svg class="icon" aria-hidden="true">
            <use href="#i-arrow-right" />
          </svg>
        </button>
      </div>
    </template>
    <template id="program-lakme-academy">
      <p class="info__label">Beauty & Styling</p>
      <h2 class="info__title" id="infoModalTitle">Lakme <em>Academy</em></h2>
      <p class="info__intro">
        Industry-led beauty and styling courses for aspiring makeup artists,
        hair stylists and fashion stylists — with live backstage experience at
        IDFR fashion shows.
      </p>
      <dl class="info__facts">
        <div>
          <dt>Format</dt>
          <dd>Classroom + Backstage</dd>
        </div>
        <div>
          <dt>Certification</dt>
          <dd>Professional</dd>
        </div>
        <div>
          <dt>Next Intake</dt>
          <dd>12 Jan 2027</dd>
        </div>
      </dl>
      <div class="info__list">
        <div class="info__item">
          <span class="info__item-num">01</span>
          <p class="info__item-meta">16 Weeks</p>
          <h3 class="info__item-name">Professional Makeup Artistry</h3>
          <p class="info__item-desc">
            Bridal, editorial, runway and camera makeup techniques.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">02</span>
          <p class="info__item-meta">12 Weeks</p>
          <h3 class="info__item-name">Hair Styling &amp; Design</h3>
          <p class="info__item-desc">
            Cutting, colour theory, runway hair and bridal styling.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">03</span>
          <p class="info__item-meta">8 Weeks</p>
          <h3 class="info__item-name">Fashion Styling</h3>
          <p class="info__item-desc">
            Wardrobe styling for shoots, campaigns and runway shows.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">04</span>
          <p class="info__item-meta">6 Weeks</p>
          <h3 class="info__item-name">Skin &amp; Beauty Essentials</h3>
          <p class="info__item-desc">
            Skincare science, consultation and beauty treatments.
          </p>
        </div>
      </div>
      <div class="info__actions">
        <button
          class="btn btn--gold"
          type="button"
          data-auth="register"
          data-role="fresher"
        >
          <span>Enquire Now</span
          ><svg class="icon" aria-hidden="true">
            <use href="#i-arrow-right" />
          </svg>
        </button>
      </div>
    </template>
    <template id="program-privacy">
      <p class="info__label">Legal</p>
      <h2 class="info__title" id="infoModalTitle">Privacy <em>Policy</em></h2>
      <div class="info__text">
        <h3>Information we collect</h3>
        <p>
          When you create an IDFR profile we collect the details you provide —
          such as your name, contact information, measurements and portfolio
          images — to present your profile to verified industry partners and clients.
        </p>
        <h3>How we use it</h3>
        <p>
          Your information is used to match you with relevant assignments,
          communicate about fashion shows and improve our platform. We never
          sell personal data to third parties.
        </p>
        <h3>Your choices</h3>
        <p>
          You can edit, hide or delete your profile at any time from your
          account settings, or by writing to info@idfr.example.
        </p>
      </div>
      <div class="info__actions">
        <button class="btn btn--gold" type="button" data-close>
          <span>Close</span>
        </button>
      </div>
    </template>
    <template id="program-terms">
      <p class="info__label">Legal</p>
      <h2 class="info__title" id="infoModalTitle">
        Terms &amp; <em>Conditions</em>
      </h2>
      <div class="info__text">
        <h3>Using IDFR</h3>
        <p>
          IDFR is a marketplace that connects models with
          brands and fashion shows. Members agree to provide accurate
          information and to conduct themselves professionally.
        </p>
        <h3>Profiles & content</h3>
        <p>
          Models retain ownership of their portfolio images and grant IDFR
          permission to display them on the platform for the purpose of
          discovery and casting.
        </p>
        <h3>Assignments</h3>
        <p>
          Assignments are agreed directly between models and clients. IDFR is not party to individual contracts.
        </p>
      </div>
      <div class="info__actions">
        <button class="btn btn--gold" type="button" data-close>
          <span>Close</span>
        </button>
      </div>
    </template>
@endsection