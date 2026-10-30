@extends('layouts.idfr')

@section('title', 'About IDFR | Vision &amp; Mission of Indian Designer Fashion Runway')
@section('description', "About IDFR — Indian Designer Fashion Runway. Our story, our vision and our mission: empowering models, supporting designers and creating opportunities across India's fashion industry.")
@section('canonical', route('idfr.about'))

@section('content')
<main id="main">
      <!-- ============ ABOUT ============ -->
      <section
        class="section section--dark about about--page"
        id="about"
        aria-labelledby="aboutTitle"
      >
        <span class="about__word" aria-hidden="true">IDFR</span>
        <div class="container about__grid">
          <div class="about__media">
            <span
              class="about__frame"
              aria-hidden="true"
              data-reveal="fade"
            ></span>

            <figure class="about__fig about__fig--founder" data-reveal="clip">
              <img
                class="photo"
                src="{{ asset('images/founders/tt5.webp') }}"
                alt="Portrait of Name1, Founder of IDFR"
                loading="lazy"
                width="800"
                height="1000"
              />
              <figcaption class="about__tag">
                <span class="about__tag-label">Founder</span>
                <strong>Name1</strong>
                <span>Founder &amp; Creative Director</span>
              </figcaption>
            </figure>

            <figure
              class="about__fig about__fig--cofounder"
              data-reveal="up"
              data-delay="200"
            >
              <img
                class="photo"
                src="{{ asset('images/founders/tt6.jpg') }}"
                alt="Portrait of Name2, Co-Founder of IDFR"
                loading="lazy"
                width="700"
                height="933"
              />
              <figcaption class="about__tag">
                <span class="about__tag-label">Co-Founder</span>
                <strong>Name2</strong>
                <span>Co-Founder &amp; Head of Talent</span>
              </figcaption>
            </figure>

            <figure
              class="about__fig about__fig--moment"
              data-reveal="left"
              data-delay="350"
            >
              <img
                class="photo"
                src="{{ asset('images/founders/show-moment.jpg') }}"
                alt="Stage lights at the IDFR Season 3 finale"
                loading="lazy"
                width="900"
                height="600"
              />
              <figcaption class="about__tag about__tag--small">
                <span class="about__tag-label">Season 3</span>
                <strong>The Grand Finale</strong>
              </figcaption>
            </figure>

            <!-- <span class="about__badge" aria-hidden="true">
                        <svg viewBox="0 0 120 120">
                            <defs>
                                <path id="badgeCircle" d="M60 60m-46 0a46 46 0 1 1 92 0a46 46 0 1 1-92 0" />
                            </defs><text>
                                <textPath href="#badgeCircle">Indian Designer Fashion Runway · Est. 2022 ·</textPath>
                            </text>
                        </svg>
                        <svg class="about__badge-star">
                            <use href="#i-star" />
                        </svg>
                    </span> -->
          </div>

          <div class="about__content">
            <p class="section-label" data-reveal="up">01 — About IDFR</p>
            <h1
              class="section-title"
              id="aboutTitle"
              data-reveal="up"
              data-delay="100"
            >
              The IDFR <em>Story</em>
            </h1>
            <span class="gold-line" data-reveal="line" data-delay="200"></span>

            <p class="about__lead" data-reveal="up" data-delay="150">
              Fashion is a stage. Every talent deserves its spotlight.
            </p>
            <p data-reveal="up" data-delay="200">
              IDFR — Indian Designer Fashion Runway — was founded to bridge the
              gap between modelling talent and the opportunities that shape
              careers. Whether you are a fresh face taking your very first step
              or an experienced professional refining your portfolio, IDFR gives
              you a stage to be seen.
            </p>
            <p data-reveal="up" data-delay="250">
              Through our seasonal fashion shows, verified digital profiles and
              a growing network of fashion brands, photographers and
              designers, models on IDFR receive assignments from hundreds of
              industry professionals across India.
            </p>
            <p data-reveal="up" data-delay="300">
              Season after season, we continue to build a community where
              creativity, confidence and opportunity walk the same runway.
            </p>

            @if(isset($statistics) && $statistics->isNotEmpty())
              <dl class="about__stats" data-reveal="up" data-delay="200">
                @foreach($statistics as $stat)
                  <div>
                    <dt>{{ $stat->title }}</dt>
                    <dd>{{ $stat->prefix }}<span @if($stat->is_numeric) data-count="{{ $stat->clean_number }}" @if($stat->pad_length > 0) data-pad="{{ $stat->pad_length }}" @endif>0</span>@else>{{ $stat->formatted_value }}</span>@endif{{ $stat->suffix }}</dd>
                  </div>
                @endforeach
              </dl>
            @endif

            <div class="about__team" data-reveal="up" data-delay="250">
              <figure class="team-mini">
                <img
                  class="photo"
                  src="{{ asset('images/founders/team-01.jpg') }}"
                  alt="Rohan Das, Casting Director"
                  loading="lazy"
                  width="120"
                  height="120"
                />
                <figcaption>
                  <strong>Rohan Das</strong><span>Casting Director</span>
                </figcaption>
              </figure>
              <figure class="team-mini">
                <img
                  class="photo"
                  src="{{ asset('images/founders/team-02.jpg') }}"
                  alt="Ishita Rao, Show Producer"
                  loading="lazy"
                  width="120"
                  height="120"
                />
                <figcaption>
                  <strong>Ishita Rao</strong><span>Show Producer</span>
                </figcaption>
              </figure>
            </div>

            <div class="about__cta" data-reveal="up" data-delay="300">
              @if(Auth::guard('web')->check())
                @php
                  $aboutUser = Auth::guard('web')->user();
                @endphp
                <a
                  class="btn btn--gold"
                  href="{{ $aboutUser->profileUrl() }}"
                >
                  <span>{{ $aboutUser->role === 'model' ? 'Register as Model' : 'My Profile' }}</span>
                  <svg class="icon" aria-hidden="true">
                    <use href="#i-arrow-right" />
                  </svg>
                </a>
                <a
                  class="text-link"
                  href="{{ route('idfr.models') }}"
                >
                  Hire Talent
                  <svg class="icon" aria-hidden="true">
                    <use href="#i-arrow-right" />
                  </svg>
                </a>
              @else
                <button
                  class="btn btn--gold"
                  type="button"
                  data-auth="register"
                  data-role="fresher"
                >
                  <span>Become an IDFR Model</span>
                  <svg class="icon" aria-hidden="true">
                    <use href="#i-arrow-right" />
                  </svg>
                </button>
                
              @endif
            </div>
          </div>
        </div>
      </section>

      <!-- ============ VISION & MISSION ============ -->
      <section
        class="section section--light vm"
        id="vision-mission"
        data-spy="about"
        aria-labelledby="vmTitle"
      >
        <div class="container">
          <div class="section-head">
            <div>
              <p class="section-label" data-reveal="up">02 — Our Purpose</p>
              <h2
                class="section-title"
                id="vmTitle"
                data-reveal="up"
                data-delay="100"
              >
                Vision &amp;
                <em>Mission</em>
              </h2>
            </div>
            <p class="section-intro" data-reveal="up" data-delay="200">
              Why IDFR exists, and the promise we make to every model, designer
              and brand who walks the runway with us.
            </p>
          </div>

          <!-- Vision -->
          <article class="vm__row" aria-labelledby="vmVisionTitle">
            <div class="vm__media">
              <span
                class="vm__frame"
                aria-hidden="true"
                data-reveal="fade"
              ></span>
              <figure class="vm__fig vm__fig--main" data-reveal="clip">
                <img
                  class="photo"
                  src="{{ asset('images/hero/hero-06.jpg') }}"
                  alt="Model in an off-shoulder velvet evening gown"
                  loading="lazy"
                  width="1100"
                  height="1567"
                />
              </figure>
              <figure
                class="vm__fig vm__fig--inset"
                data-reveal="up"
                data-delay="250"
              >
                <img
                  class="photo"
                  src="{{ asset('images/gallery/s3-03.jpg') }}"
                  alt="Confetti falling over the audience at the IDFR finale"
                  loading="lazy"
                  width="1000"
                  height="667"
                />
                <figcaption class="about__tag about__tag--small">
                  <span class="about__tag-label">Season 3</span>
                  <strong>The Grand Finale</strong>
                </figcaption>
              </figure>
            </div>

            <div class="vm__content">
              <span class="vm__word" aria-hidden="true" data-reveal="fade"
                >Vision</span
              >
              <p class="vm__kicker" data-reveal="up">
                <span>01</span>Our Vision
              </p>
              <h3
                class="vm__title"
                id="vmVisionTitle"
                data-reveal="up"
                data-delay="100"
              >
                A runway where every talent is <em>seen.</em>
              </h3>
              <span
                class="gold-line"
                data-reveal="line"
                data-delay="150"
              ></span>
              <p class="vm__lead" data-reveal="up" data-delay="150">
                To be India's most trusted stage for fashion — where talent, not
                connections, decides who walks the runway.
              </p>
              <p data-reveal="up" data-delay="200">
                We picture a fashion industry in which a first-time model from a
                small town and an established designer from a metro share the
                same spotlight, the same respect and the same chance to be
                discovered — and where Indian craft and creativity are
                celebrated on runways around the world.
              </p>
              <ul class="vm__points" data-reveal="up" data-delay="250">
                <li>
                  <svg class="icon" aria-hidden="true">
                    <use href="#i-diamond" /></svg
                  >A stage open to every talent, from fresh faces to seasoned
                  professionals
                </li>
                <li>
                  <svg class="icon" aria-hidden="true">
                    <use href="#i-diamond" /></svg
                  >Indian designers and handcrafted collections on a global
                  runway
                </li>
                <li>
                  <svg class="icon" aria-hidden="true">
                    <use href="#i-diamond" /></svg
                  >An industry built on trust, fairness and respect
                </li>
              </ul>
            </div>
          </article>

          <!-- Mission -->
          <article
            class="vm__row vm__row--reverse"
            aria-labelledby="vmMissionTitle"
          >
            <div class="vm__media">
              <span
                class="vm__frame"
                aria-hidden="true"
                data-reveal="fade"
              ></span>
              <figure class="vm__fig vm__fig--main" data-reveal="clip">
                <img
                  class="photo"
                  src="{{ asset('images/hero/hero-04.jpg') }}"
                  alt="Model twirling in a flowing designer gown"
                  loading="lazy"
                  width="1100"
                  height="1321"
                />
              </figure>
              <figure
                class="vm__fig vm__fig--inset"
                data-reveal="up"
                data-delay="250"
              >
                <img
                  class="photo"
                  src="{{ asset('images/gallery/s2-05.jpg') }}"
                  alt="Designer garments displayed in the atelier before the show"
                  loading="lazy"
                  width="1000"
                  height="667"
                />
                <figcaption class="about__tag about__tag--small">
                  <span class="about__tag-label">Designers</span>
                  <strong>Atelier Preview</strong>
                </figcaption>
              </figure>
            </div>

            <div class="vm__content">
              <span class="vm__word" aria-hidden="true" data-reveal="fade"
                >Mission</span
              >
              <p class="vm__kicker" data-reveal="up">
                <span>02</span>Our Mission
              </p>
              <h3
                class="vm__title"
                id="vmMissionTitle"
                data-reveal="up"
                data-delay="100"
              >
                Opening doors,
                <em>season after season.</em>
              </h3>
              <span
                class="gold-line"
                data-reveal="line"
                data-delay="150"
              ></span>
              <p class="vm__lead" data-reveal="up" data-delay="150">
                We turn talent into careers by connecting models, designers and
                the fashion industry on one professional platform.
              </p>
              <ol class="vm__pillars">
                <li data-reveal="up" data-delay="150">
                  <span class="vm__pillar-num">01</span>
                  <h4>Empower models</h4>
                  <p>
                    Verified profiles, portfolio guidance, grooming and runway
                    training that help every model walk with confidence.
                  </p>
                </li>
                <li data-reveal="up" data-delay="250">
                  <span class="vm__pillar-num">02</span>
                  <h4>Support designers</h4>
                  <p>
                    A professional runway, styling and production teams, and an
                    audience of buyers, press and brands for every collection.
                  </p>
                </li>
                <li data-reveal="up" data-delay="350">
                  <span class="vm__pillar-num">03</span>
                  <h4>Create opportunities</h4>
                  <p>
                    Direct connections to fashion designers, photographers and
                    brands for real, paid assignments across India.
                  </p>
                </li>
                <li data-reveal="up" data-delay="450">
                  <span class="vm__pillar-num">04</span>
                  <h4>Raise industry standards</h4>
                  <p>
                    Transparent castings, safe sets and fair pay — so fashion
                    stays a place where everyone can thrive.
                  </p>
                </li>
              </ol>
            </div>
          </article>
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