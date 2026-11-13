@extends('layouts.asdreams')

@section('title', 'Team Members | ASDreamStudio')
@section('description', 'Meet the ASDreamStudio crew — photographers, filmmakers and stylists behind the studio. Placeholder profiles until the roster is confirmed.')
@section('canonical', route('asd.team'))

@section('content')
<main id="main">
      <section
        class="section team-page"
        id="team"
        aria-labelledby="teamTitle"
      >
        <div class="container">
          <nav class="breadcrumb" aria-label="Breadcrumb">
            <ol>
              <li><a href="{{ route('asd.home') }}#home">Home</a></li>
              <li><a href="{{ route('asd.home') }}">ASDreamStudio</a></li>
              <li aria-current="page">Team Members</li>
            </ol>
          </nav>

          <div class="section-head">
            <div data-reveal="up">
              <p class="section-label">The Studio</p>
              <h1 class="section-title" id="teamTitle">
                Team <em>Members</em>
              </h1>
            </div>
            <p class="section-intro" data-reveal="up" data-delay="150">
              One Visionary. One Exceptional Team. One Creative Vision.
            </p>
          </div>

          @if($founders->isNotEmpty())
            <div class="team-group team-group--founders" aria-labelledby="foundersTitle">
              <div class="team-group__head" data-reveal="up">
                <p class="section-label">THE VISIONARIES</p>
                <h2 class="section-title team-group__title" id="foundersTitle">
                  Our <em>Founders</em>
                </h2>
              </div>

              <div class="team-leadership-grid team-leadership-grid--founders">
                @foreach($founders as $index => $founder)
                  <article class="team-leadership-card team-leadership-card--founder" data-reveal="up" data-delay="{{ ($index % 3) * 90 }}">
                    <figure class="team-leadership-card__media">
                      <img
                        src="{{ $founder->photo_url }}"
                        alt="{{ $founder->name }}"
                        width="600"
                        height="800"
                      />
                      <span class="team-leadership-card__badge">Founder</span>
                    </figure>
                    <div class="team-leadership-card__body">
                      <h3 class="team-leadership-card__name">{{ $founder->name }}</h3>
                      @if($founder->designation)
                        <p class="team-leadership-card__designation">{{ $founder->designation }}</p>
                      @endif
                      @if($founder->bio)
                        <blockquote class="team-leadership-card__bio">{{ $founder->bio }}</blockquote>
                      @endif
                      @if($founder->instagram || $founder->linkedin)
                        <div class="team-leadership-card__socials">
                          @if($founder->instagram)
                            <a href="{{ $founder->instagram }}" target="_blank" rel="noopener" aria-label="{{ $founder->name }} Instagram">Instagram</a>
                          @endif
                          @if($founder->linkedin)
                            <a href="{{ $founder->linkedin }}" target="_blank" rel="noopener" aria-label="{{ $founder->name }} LinkedIn">LinkedIn</a>
                          @endif
                        </div>
                      @endif
                    </div>
                  </article>
                @endforeach
              </div>
            </div>
          @endif

          @if($coFounders->isNotEmpty())
            <div class="team-group team-group--cofounders" aria-labelledby="coFoundersTitle">
              <div class="team-group__head" data-reveal="up">
                <p class="section-label">Leadership</p>
                <h2 class="section-title team-group__title" id="coFoundersTitle">
                  Our <em>Co-Founders</em>
                </h2>
              </div>

              <div class="team-leadership-grid team-leadership-grid--cofounders">
                @foreach($coFounders as $index => $coFounder)
                  <article class="team-leadership-card team-leadership-card--cofounder" data-reveal="up" data-delay="{{ ($index % 3) * 90 }}">
                    <figure class="team-leadership-card__media">
                      <img
                        src="{{ $coFounder->photo_url }}"
                        alt="{{ $coFounder->name }}"
                        width="600"
                        height="800"
                      />
                      <span class="team-leadership-card__badge team-leadership-card__badge--outline">Co-Founder</span>
                    </figure>
                    <div class="team-leadership-card__body">
                      <h3 class="team-leadership-card__name">{{ $coFounder->name }}</h3>
                      @if($coFounder->designation)
                        <p class="team-leadership-card__designation">{{ $coFounder->designation }}</p>
                      @endif
                      @if($coFounder->bio)
                        <blockquote class="team-leadership-card__bio">{{ $coFounder->bio }}</blockquote>
                      @endif
                      @if($coFounder->instagram || $coFounder->linkedin)
                        <div class="team-leadership-card__socials">
                          @if($coFounder->instagram)
                            <a href="{{ $coFounder->instagram }}" target="_blank" rel="noopener" aria-label="{{ $coFounder->name }} Instagram">Instagram</a>
                          @endif
                          @if($coFounder->linkedin)
                            <a href="{{ $coFounder->linkedin }}" target="_blank" rel="noopener" aria-label="{{ $coFounder->name }} LinkedIn">LinkedIn</a>
                          @endif
                        </div>
                      @endif
                    </div>
                  </article>
                @endforeach
              </div>
            </div>
          @endif

          @if($teamMembers->isNotEmpty())
            <div class="team-group team-group--members" aria-labelledby="studioCrewTitle">
              @if($founders->isNotEmpty() || $coFounders->isNotEmpty())
                <div class="team-group__head team-group__head--left" data-reveal="up">
                  <p class="section-label">The Crew</p>
                  <h2 class="section-title team-group__title" id="studioCrewTitle">
                    Creative <em>Team</em>
                  </h2>
                </div>
              @endif

              <ul class="models__grid">
                @foreach($teamMembers as $index => $member)
                  <li class="model-card" style="--i: {{ $index }}" data-reveal="up" data-delay="{{ ($index % 4) * 70 }}">
                    <figure class="model-card__media">
                      <img
                        src="{{ $member->photo_url }}"
                        alt="{{ $member->name }}"
                        width="600"
                        height="800"
                      />
                    </figure>
                    <h2 class="model-card__name">{{ $member->name }}</h2>
                    @if($member->designation)
                      <p class="model-card__meta">{{ $member->designation }}</p>
                    @endif
                    @if($member->bio)
                      <p class="model-card__bio">{{ $member->bio }}</p>
                    @endif
                  </li>
                @endforeach
              </ul>
            </div>
          @endif

          @if($founders->isEmpty() && $coFounders->isEmpty() && $teamMembers->isEmpty())
            <div class="team-page__empty" data-reveal="up">
              <p class="team-page__empty-title">No Team Members Yet</p>
              <p class="model-card__meta">Roster coming soon</p>
            </div>
          @endif
        </div>
      </section>
    </main>
@endsection
@section('page_content_templates')
<!-- ============ PAGE CONTENT ============
         Content for the sections built by js/script.js. <template> content is not displayed;
         the script reads it and renders each section. Edit text, images and options here. -->

    <!-- Info modal content: one template per program, opened by links with a matching data-program. -->
    <template id="program-season-4">
      <p class="info__label">IDFR Fashion Show</p>
      <h2 class="info__title" id="infoModalTitle">Season <em>4</em></h2>
      <p class="info__intro">
        Three runway nights celebrating emerging designers, established names
        and the next generation of Indian modelling talent. Season 4 brings
        couture, resort, menswear and handloom collections to one stage in
        Kozhikode.
      </p>
      <dl class="info__facts">
        <div>
          <dt>Dates</dt>
          <dd>18 – 20 Dec 2026</dd>
        </div>
        <div>
          <dt>Venue</dt>
          <dd>IDFR Fashion House</dd>
        </div>
        <div>
          <dt>Designers</dt>
          <dd>24 Labels</dd>
        </div>
        <div>
          <dt>Models</dt>
          <dd>60+ Walking</dd>
        </div>
      </dl>
      <div class="info__list">
        <div class="info__item">
          <span class="info__item-num">01</span>
          <p class="info__item-meta">18 December · 7:00 PM</p>
          <h3 class="info__item-name">Night One — New Faces</h3>
          <p class="info__item-desc">
            Debut collections walked by models from the Season 4 open auditions.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">02</span>
          <p class="info__item-meta">19 December · 7:00 PM</p>
          <h3 class="info__item-name">Night Two — Heritage &amp; Handloom</h3>
          <p class="info__item-desc">
            Kerala handloom, Kanjeevaram and heritage textiles reimagined for
            today.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">03</span>
          <p class="info__item-meta">20 December · 7:30 PM</p>
          <h3 class="info__item-name">Night Three — The Grand Finale</h3>
          <p class="info__item-desc">
            Couture showcases, awards and the closing walk of Season 4.
          </p>
        </div>
      </div>
      <div class="info__actions">
        <a class="btn btn--gold" href="{{ route('idfr.tickets') }}"
          ><span>Reserve Your Seat</span
          ><svg class="icon" aria-hidden="true">
            <use href="#i-arrow-right" /></svg
        ></a>
        <button
          class="btn btn--ghost"
          type="button"
          data-auth="register"
          data-role="fresher"
        >
          <span>Register to Walk</span
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
    <template id="program-modelling-academy">
      <p class="info__label">IDFR Programs</p>
      <h2 class="info__title" id="infoModalTitle">
        Modelling <em>Academy</em>
      </h2>
      <p class="info__intro">
        Structured training led by working runway coaches, photographers and
        casting directors. Every graduate receives a professional portfolio
        shoot and an IDFR profile listing.
      </p>
      <dl class="info__facts">
        <div>
          <dt>Batches</dt>
          <dd>Weekday & Weekend</dd>
        </div>
        <div>
          <dt>Location</dt>
          <dd>Kozhikode & Kochi</dd>
        </div>
        <div>
          <dt>Next Intake</dt>
          <dd>5 Jan 2027</dd>
        </div>
      </dl>
      <div class="info__list">
        <div class="info__item">
          <span class="info__item-num">01</span>
          <p class="info__item-meta">8 Weeks · Beginners</p>
          <h3 class="info__item-name">Runway Foundation</h3>
          <p class="info__item-desc">
            Posture, walk technique, turns and runway etiquette.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">02</span>
          <p class="info__item-meta">4 Weeks · All levels</p>
          <h3 class="info__item-name">Portfolio &amp; Posing Masterclass</h3>
          <p class="info__item-desc">
            Studio and outdoor posing with a professional portfolio shoot.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">03</span>
          <p class="info__item-meta">6 Weeks · All levels</p>
          <h3 class="info__item-name">
            Grooming &amp; Personality Development
          </h3>
          <p class="info__item-desc">
            Skincare, styling, communication and audition confidence.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">04</span>
          <p class="info__item-meta">12 Weeks · Advanced</p>
          <h3 class="info__item-name">Professional Model Track</h3>
          <p class="info__item-desc">
            Complete programme including a guaranteed IDFR Season 5 audition.
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