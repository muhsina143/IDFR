@extends('layouts.idfr')



@section('title', 'Discover Models | IDFR — Indian Designer Fashion Runway')

@section('description', 'Browse verified IDFR model profiles — new faces, experienced and kids models for runway, editorial and commercial assignments. Filter by category and view full profiles.')

@section('canonical', route('idfr.models'))



@section('content')

<main id="main">

      <!-- ============ MODELS ============ -->

      <section

        class="section section--light models models--page"

        id="models"

        aria-labelledby="modelsTitle"

      >

        <div class="container">

          <div class="section-head">

            <div>

              <p class="section-label" data-reveal="up">02 — Talent</p>

              <h1

                class="section-title"

                id="modelsTitle"

                data-reveal="up"

                data-delay="100"

              >

                Discover

                <em>Models</em>

              </h1>

            </div>

            <p class="section-intro" data-reveal="up" data-delay="200">

              Explore emerging and experienced modelling talent — verified

              profiles, ready for runway, editorial and commercial assignments.

            </p>

          </div>



          <div class="models__toolbar" data-reveal="up">

            <div

              class="filters"

              role="group"

              aria-label="Filter models"

              id="modelFilters"

            >

              <button

                class="filter-btn is-active"

                type="button"

                data-filter="all"

                aria-pressed="true"

              >

                All

              </button>

              <button

                class="filter-btn"

                type="button"

                data-filter="kids"

                aria-pressed="false"

              >

                Kids

              </button>

              <button

                class="filter-btn"

                type="button"

                data-filter="female"

                aria-pressed="false"

              >

                Female

              </button>

              <button

                class="filter-btn"

                type="button"

                data-filter="male"

                aria-pressed="false"

              >

                Male

              </button>

              <button

                class="filter-btn"

                type="button"

                data-filter="new"

                aria-pressed="false"

              >

                New Faces

              </button>

              <button

                class="filter-btn"

                type="button"

                data-filter="experienced"

                aria-pressed="false"

              >

                Experienced

              </button>

              <button

                class="filter-btn"

                type="button"

                data-filter="runway"

                aria-pressed="false"

              >

                Runway

              </button>

              <button

                class="filter-btn"

                type="button"

                data-filter="editorial"

                aria-pressed="false"

              >

                Editorial

              </button>

              <button

                class="filter-btn"

                type="button"

                data-filter="commercial"

                aria-pressed="false"

              >

                Commercial

              </button>

            </div>

            <p class="models__count" id="modelCount" aria-live="polite">

              Showing 12 models

            </p>

          </div>



          <div class="models__grid" id="modelGrid"></div>

          <div class="models__view-all">

            <button class="btn btn--outline" type="button" id="modelViewAll">

              <span>View All</span

              ><svg class="icon" aria-hidden="true">

                <use href="#i-arrow-right" />

              </svg>

            </button>

          </div>

          <p class="models__empty" id="modelEmpty" hidden>

            No models match this filter yet — new profiles are added every week.

          </p>



          <div class="models__more" data-reveal="up">

            <p>1,200+ profiles on IDFR. Looking for campaign talent?</p>

            

          </div>

        </div>

      </section>

    </main>

@endsection

@section('page_content_templates')

<!-- ============ PAGE CONTENT ============

         Content for the sections built by js/script.js. <template> content is not displayed;

         the script reads it and renders each section. Edit text, images and options here. -->



    <!-- Models (#modelGrid + profile modal): one article per model. data-kid="true" models are listed only

         under the "Kids" filter. The profile slider shows the main image, then each portfolio image. -->

    <template id="modelsData">

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

            <dt>Age</dt><dd data-age>{{ $m->age }}</dd>

            <dt>Height</dt><dd data-height>{{ $m->height }}</dd>

            <dt>Location</dt><dd data-location>{{ $m->location }}</dd>

            <dt>Experience</dt><dd data-experience>{{ $m->experience }}</dd>

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

          <p data-availability>{{ $m->availability }}</p>

          <p data-description>{{ $m->description }}</p>

        </article>

        @endforeach

      @endif

    </template>



    <!-- Model profile modal: booking enquiry form ("Contact / Enquire"). The heading becomes "Book <first name>". -->

    @include('partials.enquiry-modal-template')



    <!-- Info modal content: one template per program, opened by links with a matching data-program. -->

    <template id="program-season-4">

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

          <dd>24 Labels</dd>

        </div>

        <div>

          <dt>Models</dt>

          <dd>60+ Talent</dd>

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

    </template>

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