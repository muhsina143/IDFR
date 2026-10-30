@extends('layouts.idfr')



@section('title', 'IDFR | Fashion Models & Fashion Shows')

@section('description', 'IDFR connects fashion models with creative professionals, modelling assignments and fashion show opportunities.')

@section('canonical', route('idfr.home'))



@section('preload')

@if(isset($heroImages) && $heroImages->isNotEmpty())

<link rel="preload" as="image" href="{{ $heroImages->first()->image_url }}" />

@elseif($currentSeason && $currentSeason->hero_image)

<link rel="preload" as="image" href="{{ $currentSeason->hero_image_url }}" />

@else

<link rel="preload" as="image" href="{{ asset('images/hero/hero-01.jpg') }}" />

@endif

@endsection



@section('content')

<main id="main">

      <!-- ============ HERO ============ -->

      <section

        class="hero"

        id="home"

        aria-roledescription="carousel"

        aria-label="IDFR Fashion Show {{ $currentSeason ? $currentSeason->title : 'Season 8' }} highlights"

      >

        <div class="hero__visual">

          <div class="hero__bg" aria-hidden="true">

            <span class="hero__spot"></span>

            <span class="hero__grid"></span>

            <span class="hero__floor"></span>

          </div>



          <p class="hero__vertical" aria-hidden="true">

            IDFR Presents <span></span> Runway {{ $currentSeason && $currentSeason->year ? $currentSeason->year : '2026' }}

          </p>



          <div class="hero__title-layer hero__title-layer--solid">

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

                  <span class="hero__title-a">Season</span><span class="hero__title-b">-8</span>

                @endif

              </h1>

            </div>

          </div>



          <div class="hero__stage" id="heroStage"></div>



          <div

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

                  <span class="hero__title-a">Season</span><span class="hero__title-b">-8</span>

                @endif

              </p>

            </div>

          </div>



          @php

            $firstHero = isset($heroImages) && $heroImages->isNotEmpty() ? $heroImages->first() : null;

            $secondHero = isset($heroImages) && $heroImages->count() > 1 ? $heroImages->slice(1, 1)->first() : $firstHero;

            $totalHero = isset($heroImages) ? $heroImages->count() : 0;

            $firstLookNum = $firstHero ? ($firstHero->look_number ?: 'Look 01') : 'Look 01';

            $firstTitle = $firstHero ? ($firstHero->caption ?: ($firstHero->image_title ?: 'Runway Look')) : 'Runway Look';

            $firstDesigner = $firstHero ? ($firstHero->designer_name ?: 'IDFR Runway') : 'IDFR Runway';

            $secondThumb = $secondHero ? $secondHero->image_url : ($firstHero ? $firstHero->image_url : asset('images/hero/hero-02.jpg'));

            $secondNum = $totalHero > 1 ? '02' : ($totalHero === 1 ? '01' : '00');

          @endphp



          <div class="hero__caption" id="heroCaption" aria-live="polite">

            <span class="hero__caption-num">{{ $firstLookNum }}</span>

            <p class="hero__caption-title">{{ $firstTitle }}</p>

            <p class="hero__caption-meta">Designed by {{ $firstDesigner }}</p>

          </div>



          <div class="hero__controls">

            <button

              class="hero__arrow"

              type="button"

              id="heroPrev"

              aria-label="Previous slide"

            >

              <svg class="icon" aria-hidden="true">

                <use href="#i-arrow-left" />

              </svg>

            </button>

            <span class="hero__count"

              ><span class="hero__count-current" id="heroCurrent"

                >{{ $totalHero > 0 ? '01' : '00' }}</span

              ></span

            >

            <span class="hero__progress" aria-hidden="true"

              ><span class="hero__progress-bar" id="heroProgress"></span

            ></span>

            <span class="hero__count hero__count--total" id="heroTotal"

              >{{ sprintf('%02d', $totalHero) }}</span

            >

            <button

              class="hero__arrow"

              type="button"

              id="heroNextBtn"

              aria-label="Next slide"

            >

              <svg class="icon" aria-hidden="true">

                <use href="#i-arrow-right" />

              </svg>

            </button>

          </div>



          <button

            class="hero__next"

            type="button"

            id="heroNextPreview"

            aria-label="Show next look"

          >

            <span class="hero__next-thumb"

              ><img

                id="heroNextImg"

                src="{{ $secondThumb }}"

                alt=""

                width="220"

                height="293"

            /></span>

            <span class="hero__next-meta"

              ><span>Next</span

              ><span class="hero__next-num" id="heroNextNum">{{ $secondNum }}</span></span

            >

          </button>

        </div>



        <div class="hero__info">

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

      </section>



      <!-- Brand marquee -->

      <div class="marquee" aria-hidden="true">

        <div class="marquee__track">

          <span>Fashion</span><i></i><span>Talent</span><i></i

          ><span>Opportunities</span><i></i><span>Discovery</span><i></i

          ><span>Modelling</span><i></i><span>Runway</span><i></i>

          <span>Fashion</span><i></i><span>Talent</span><i></i

          ><span>Opportunities</span><i></i><span>Discovery</span><i></i

          ><span>Modelling</span><i></i><span>Runway</span><i></i>

        </div>

      </div>



      <!-- ============ BRANDS ============ -->

      <section

        class="section asd-brands"

        id="asd-brands"

        aria-labelledby="asdBrandsTitle"

      >

        <div class="container">

          <div class="section-head">

            <div data-reveal="up">

              <p class="section-label">One Creative Family</p>

              <h2 class="section-title" id="asdBrandsTitle">

                Our <em>Brands</em>

              </h2>

            </div>

            <p class="section-intro" data-reveal="up" data-delay="150">

              Photography, runway, model training and beauty education — four

              houses working together to launch careers in fashion.

            </p>

          </div>



          <ul class="asd-brands__grid">

            <li class="asd-brand" data-reveal="up">

              <img

                class="photo asd-brand__img"

                src="{{ asset('images/asdreams/brands/asdreamstudio.jpg') }}"

                alt="Photographer focusing a camera through flowers"

                width="900"

                height="1350"

                loading="lazy"

              />

              <div class="asd-brand__body">

                <div class="asd-brand__logo">

                  <img

                    src="{{ asset('images/asdreams/asd-logo.png') }}"

                    alt=""

                    width="666"

                    height="470"

                    loading="lazy"

                  />

                </div>

                <h3 class="asd-brand__name"><span>AS</span>DreamStudio</h3>

                <p class="asd-brand__tagline">Photography &amp; Videography</p>

                <span class="asd-brand__rule" aria-hidden="true"></span>

                <ul class="asd-brand__tags">

                  <li>Fashion</li>

                  <li>Wedding</li>

                  <li>Events</li>

                  <li>Commercial</li>

                  <li>Products</li>

                  <li>Studio Rental</li>

                </ul>

                <a

                  class="btn btn--ghost btn--sm asd-brand__btn"

                  href="{{ route('asd.home') }}"

                  aria-label="Explore ASDreamStudio portfolio"

                  ><span>Explore</span

                  ><svg class="icon" aria-hidden="true">

                    <use href="#i-arrow-right" /></svg

                ></a>

              </div>

            </li>



            <li class="asd-brand" data-reveal="up" data-delay="100">

              <img

                class="photo asd-brand__img"

                src="{{ asset('images/hero/hero-04.jpg') }}"

                alt="Model in a flowing gown on the runway"

                width="1100"

                height="1321"

                loading="lazy"

              />

              <div class="asd-brand__body">

                <div class="asd-brand__logo asd-brand__logo--wide">

                  <img

                    src="{{ asset('images/logo/idfr-mark.png') }}"

                    alt="IDFR"

                    width="474"

                    height="220"

                    loading="lazy"

                  />

                </div>

                <h3 class="asd-brand__name asd-brand__name--caps">

                  Indian Designer Fashion Runway

                </h3>

                <span class="asd-brand__rule" aria-hidden="true"></span>

                <ul class="asd-brand__tags">

                  <li>Designers</li>

                  <li>Models</li>

                  <li>Fashion Shows</li>

                  <li>Brands</li>

                  <li>Events</li>

                  <li>Networking</li>

                </ul>

                <a

                  class="btn btn--ghost btn--sm asd-brand__btn"

                  href="{{ route('idfr.home') }}"

                  aria-label="Explore IDFR"

                  ><span>Explore</span

                  ><svg class="icon" aria-hidden="true">

                    <use href="#i-arrow-right" /></svg

                ></a>

              </div>

            </li>



            <li class="asd-brand" data-reveal="up" data-delay="200">

              <img

                class="photo asd-brand__img"

                src="{{ asset('images/models/model-01.jpg') }}"

                alt="Model posing in a black outfit"

                width="700"

                height="1050"

                loading="lazy"

              />

              <div class="asd-brand__body">

                <div class="asd-brand__logo">

                  <img

                    src="{{ asset('images/asdreams/brands/athuls-academy-logo.png') }}"

                    alt=""

                    width="110"

                    height="177"

                    loading="lazy"

                  />

                </div>

                <h3 class="asd-brand__name">Athul’s Academy</h3>

                <p class="asd-brand__tagline">Modelling &amp; Grooming</p>

                <span class="asd-brand__rule" aria-hidden="true"></span>

                <ul class="asd-brand__tags">

                  <li>Model Training</li>

                  <li>Grooming</li>

                  <li>Portfolio</li>

                  <li>Casting</li>

                  <li>Industry Opportunities</li>

                </ul>

                <a

                  class="btn btn--ghost btn--sm asd-brand__btn"

                  href="https://athulsacademy.com/"

                  target="_blank"

                  rel="noopener noreferrer"

                  aria-label="Explore Athul’s Academy"

                  ><span>Explore</span

                  ><svg class="icon" aria-hidden="true">

                    <use href="#i-arrow-right" /></svg

                ></a>

              </div>

            </li>



            <li class="asd-brand" data-reveal="up" data-delay="300">

              <img

                class="photo asd-brand__img"

                src="{{ asset('images/asdreams/brands/lakme-academy.jpg') }}"

                alt="Makeup artist applying eyeshadow"

                width="900"

                height="1300"

                loading="lazy"

              />

              <div class="asd-brand__body">

                <div class="asd-brand__logo">

                  <span

                    class="asd-lakme"

                    role="img"

                    aria-label="Lakmé Academy, Vidyaranyapura"

                  >

                    <span class="asd-lakme__word" aria-hidden="true"

                      >Lakmé</span

                    >

                    <span class="asd-lakme__sub" aria-hidden="true"

                      >Academy</span

                    >

                    <span class="asd-lakme__place" aria-hidden="true"

                      >Vidyaranyapura</span

                    >

                  </span>

                </div>

                <h3 class="visually-hidden">Lakmé Academy</h3>

                <p class="asd-brand__tagline">

                  <span>Makeup &bull; Hair</span> <span>Nails &bull; Skin</span>

                </p>

                <span class="asd-brand__rule" aria-hidden="true"></span>

                <ul class="asd-brand__tags">

                  <li>Professional Courses</li>

                  <li>Workshops</li>

                  <li>Practical Training</li>

                  <li>Career Development</li>

                </ul>

                <a

                  class="btn btn--ghost btn--sm asd-brand__btn"

                  href="https://www.lakme-academy.com/"

                  target="_blank"

                  rel="noopener noreferrer"

                  aria-label="Explore Lakmé Academy"

                  ><span>Explore</span

                  ><svg class="icon" aria-hidden="true">

                    <use href="#i-arrow-right" /></svg

                ></a>

              </div>

            </li>

          </ul>

        </div>

      </section>



      <!-- ============ ABOUT ============ -->

      <section

        class="section section--dark about"

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

            <h2

              class="section-title"

              id="aboutTitle"

              data-reveal="up"

              data-delay="100"

            >

              The IDFR <em>Story</em>

            </h2>

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

            @else

              <dl class="about__stats" data-reveal="up" data-delay="200">

                <div>

                  <dt>Seasons</dt>

                  <dd><span data-count="4" data-pad="2">0</span></dd>

                </div>

                <div>

                  <dt>Registered Models</dt>

                  <dd><span data-count="1200">0</span>+</dd>

                </div>

                <div>

                  <dt>Creative Talents</dt>

                  <dd><span data-count="350">0</span>+</dd>

                </div>

                <div>

                  <dt>Designers Showcased</dt>

                  <dd><span data-count="85">0</span>+</dd>

                </div>

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

                  $homeUser = Auth::guard('web')->user();

                @endphp

                <a

                  class="btn btn--gold"

                  href="{{ $homeUser->profileUrl() }}"

                >

                  <span>{{ $homeUser->role === 'model' ? 'Register as Model' : 'My Profile' }}</span>

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



      <!-- ============ SEASON 4 BANNER ============ -->

      <section

        class="season-banner"

        id="season-4-banner"

        aria-labelledby="seasonBannerTitle"

      >

        <img

          class="season-banner__bg photo"

          src="{{ asset('images/creative/portfolio/design-runway-spotlight.jpg') }}"

          alt=""

          width="1600"

          height="900"

          loading="lazy"

        />

        <div class="season-banner__veil" aria-hidden="true"></div>

        <span class="season-banner__frame" aria-hidden="true"></span>

        <div class="container season-banner__inner">

          <p class="season-banner__label" data-reveal="up">

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

          </p>

          <div class="season-banner__actions" data-reveal="up" data-delay="220">

            <a class="btn btn--gold" href="{{ route('idfr.tickets') }}"

              ><span>Reserve Your Seat</span

              ><svg class="icon" aria-hidden="true">

                <use href="#i-arrow-right" /></svg

            ></a>

          </div>

        </div>

      </section>



      <!-- ============ MODELS ============ -->

      <section

        class="section section--light models"

        id="models"

        aria-labelledby="modelsTitle"

      >

        <div class="container">

          <div class="section-head">

            <div>

              <p class="section-label" data-reveal="up">02 — Talent</p>

              <h2

                class="section-title"

                id="modelsTitle"

                data-reveal="up"

                data-delay="100"

              >

                Discover

                <em>Models</em>

              </h2>

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

            <a

              class="btn btn--outline"

              href="{{ route('idfr.models') }}"

              id="modelViewAll"

              ><span>View All</span

              ><svg class="icon" aria-hidden="true">

                <use href="#i-arrow-right" /></svg

            ></a>

          </div>

          <p class="models__empty" id="modelEmpty" hidden>

            No models match this filter yet — new profiles are added every week.

          </p>



          <div class="models__more" data-reveal="up">

            <p>1,200+ profiles on IDFR. Looking for campaign talent?</p>

            

          </div>

        </div>

      </section>



      <!-- ============ CREATIVE PROFESSIONALS ============ -->

      <!-- Each panel id doubles as its category link: index.html#creative-makeup-artist, etc. -->

      <section

        class="section section--dark creative-pros"

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

              <h2

                class="section-title"

                id="creativeTitle"

                data-reveal="up"

                data-delay="100"

              >

                Creative

                <em>Professionals</em>

              </h2>

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

              <button

                class="filter-btn is-active"

                type="button"

                role="tab"

                id="creativeTab-makeup-artist"

                aria-selected="true"

                aria-controls="creative-makeup-artist"

                data-category="makeup-artist"

                data-profession="Makeup Artist"

              >

                Makeup Artist

              </button>

              <button

                class="filter-btn"

                type="button"

                role="tab"

                id="creativeTab-hairstylist"

                aria-selected="false"

                aria-controls="creative-hairstylist"

                data-category="hairstylist"

                data-profession="Hairstylist"

                tabindex="-1"

              >

                Hairstylist

              </button>

              <button

                class="filter-btn"

                type="button"

                role="tab"

                id="creativeTab-photographer"

                aria-selected="false"

                aria-controls="creative-photographer"

                data-category="photographer"

                data-profession="Photographer"

                tabindex="-1"

              >

                Photographer

              </button>

              <button

                class="filter-btn"

                type="button"

                role="tab"

                id="creativeTab-videographer"

                aria-selected="false"

                aria-controls="creative-videographer"

                data-category="videographer"

                data-profession="Videographer"

                tabindex="-1"

              >

                Videographer

              </button>

              <button

                class="filter-btn"

                type="button"

                role="tab"

                id="creativeTab-fashion-designer"

                aria-selected="false"

                aria-controls="creative-fashion-designers"

                data-category="fashion-designer"

                data-profession="Fashion Designer"

                tabindex="-1"

              >

                Fashion Designers

              </button>

              <button

                class="filter-btn"

                type="button"

                role="tab"

                id="creativeTab-drone-pilot"

                aria-selected="false"

                aria-controls="creative-drone-pilot"

                data-category="drone-pilot"

                data-profession="Drone Pilot"

                tabindex="-1"

              >

                Drone Pilot

              </button>

              <button

                class="filter-btn"

                type="button"

                role="tab"

                id="creativeTab-photo-video-editor"

                aria-selected="false"

                aria-controls="creative-photo-video-editor"

                data-category="photo-video-editor"

                data-profession="Photo &amp; Video Editor"

                tabindex="-1"

              >

                Photo &amp; Video Editor

              </button>

              <button

                class="filter-btn"

                type="button"

                role="tab"

                id="creativeTab-jewellery-designer"

                aria-selected="false"

                aria-controls="creative-jewellery-designer"

                data-category="jewellery-designer"

                data-profession="Jewellery Designer"

                tabindex="-1"

              >

                Jewellery Designer

              </button>

            </div>

          </div>



          <!-- Cards are built from the creativeProsData template (PAGE CONTENT, end of this file) -->

          <div

            class="creative-pros__panel"

            role="tabpanel"

            id="creative-makeup-artist"

            aria-labelledby="creativeTab-makeup-artist"

            tabindex="0"

          >

            <div class="creative-pros__grid" data-category="makeup-artist"></div>

          </div>

          <div

            class="creative-pros__panel"

            role="tabpanel"

            id="creative-hairstylist"

            aria-labelledby="creativeTab-hairstylist"

            tabindex="0"

            hidden

          >

            <div class="creative-pros__grid" data-category="hairstylist"></div>

          </div>

          <div

            class="creative-pros__panel"

            role="tabpanel"

            id="creative-photographer"

            aria-labelledby="creativeTab-photographer"

            tabindex="0"

            hidden

          >

            <div class="creative-pros__grid" data-category="photographer"></div>

          </div>

          <div

            class="creative-pros__panel"

            role="tabpanel"

            id="creative-videographer"

            aria-labelledby="creativeTab-videographer"

            tabindex="0"

            hidden

          >

            <div class="creative-pros__grid" data-category="videographer"></div>

          </div>

          <div

            class="creative-pros__panel"

            role="tabpanel"

            id="creative-fashion-designers"

            aria-labelledby="creativeTab-fashion-designer"

            tabindex="0"

            hidden

          >

            <div

              class="creative-pros__grid"

              data-category="fashion-designer"

            ></div>

          </div>

          <div

            class="creative-pros__panel"

            role="tabpanel"

            id="creative-drone-pilot"

            aria-labelledby="creativeTab-drone-pilot"

            tabindex="0"

            hidden

          >

            <div class="creative-pros__grid" data-category="drone-pilot"></div>

          </div>

          <div

            class="creative-pros__panel"

            role="tabpanel"

            id="creative-photo-video-editor"

            aria-labelledby="creativeTab-photo-video-editor"

            tabindex="0"

            hidden

          >

            <div

              class="creative-pros__grid"

              data-category="photo-video-editor"

            ></div>

          </div>

          <div

            class="creative-pros__panel"

            role="tabpanel"

            id="creative-jewellery-designer"

            aria-labelledby="creativeTab-jewellery-designer"

            tabindex="0"

            hidden

          >

            <div

              class="creative-pros__grid"

              data-category="jewellery-designer"

            ></div>

          </div>



          <div class="creative-pros__view-all" data-reveal="up">

            <a

              class="btn btn--ghost"

              href="{{ route('idfr.professionals') }}"

              id="creativeViewAll"

              ><span>View All</span

              ><svg class="icon" aria-hidden="true">

                <use href="#i-arrow-right" /></svg

            ></a>

          </div>

        </div>

      </section>



      <!-- ============ GALLERY ============ -->

      <section

        class="section section--dark gallery"

        id="gallery"

        aria-labelledby="galleryTitle"

      >

        <div class="container">

          <div class="section-head">

            <div>

              <p class="section-label" data-reveal="up">03 — Archive</p>

              <h2

                class="section-title"

                id="galleryTitle"

                data-reveal="up"

                data-delay="100"

              >

                Fashion Show

                <em>Gallery</em>

              </h2>

            </div>

            <div

              class="filters filters--dark"

              role="group"

              aria-label="Filter gallery by season"

              id="galleryFilters"

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



          <div class="gallery__grid" id="galleryGrid"></div>

        </div>

      </section>



      <!-- ============ ACADEMY BANNER ============ -->

      <!-- <section class="academy" id="programs" aria-labelledby="academyTitle">

        <div class="academy__head container">

          <p class="section-label" data-reveal="up">04 — Programs</p>

          <h2

            class="section-title"

            id="academyTitle"

            data-reveal="up"

            data-delay="100"

          >

            Train for the

            <em>Spotlight</em>

          </h2>

        </div>

        <div class="academy__grid">

          <a

            class="academy-card"

            href="#modelling-academy"

            data-program="modelling-academy"

            data-reveal="fade"

          >

            <img

              class="academy-card__bg photo"

              src="{{ asset('images/banners/modelling-academy.jpg') }}"

              alt=""

              loading="lazy"

              width="1200"

              height="1500"

            />

            <span class="academy-card__num" aria-hidden="true">01</span>

            <span class="academy-card__content">

              <span class="academy-card__label">IDFR Programs</span>

              <span class="academy-card__title"

                >Modelling <em>Academy</em></span

              >

              <span class="academy-card__text"

                >Runway walk, posing, portfolio building and grooming programmes

                for aspiring and working models.</span

              >

              <span class="academy-card__cta"

                >Explore Programs

                <svg class="icon" aria-hidden="true">

                  <use href="#i-arrow-right" /></svg

              ></span>

            </span>

          </a>

          <a

            class="academy-card"

            href="#lakme-academy"

            data-program="lakme-academy"

            data-reveal="fade"

            data-delay="150"

          >

            <img

              class="academy-card__bg photo"

              src="{{ asset('images/banners/lakme-academy.jpg') }}"

              alt=""

              loading="lazy"

              width="1200"

              height="800"

            />

            <span class="academy-card__num" aria-hidden="true">02</span>

            <span class="academy-card__content">

              <span class="academy-card__label">Beauty &amp; Styling</span>

              <span class="academy-card__title">Lakme <em>Academy</em></span>

              <span class="academy-card__text"

                >Professional makeup artistry, hair design and fashion styling

                courses taught by industry experts.</span

              >

              <span class="academy-card__cta"

                >Explore Programs

                <svg class="icon" aria-hidden="true">

                  <use href="#i-arrow-right" /></svg

              ></span>

            </span>

          </a>

        </div>

      </section> -->



      <!-- ============ PARTNERS ============ -->

      <section

        class="section section--light partners"

        id="partners"

        aria-labelledby="partnersTitle"

      >

        <div class="container">

          <div class="section-head">

            <div>

              <p class="section-label" data-reveal="up">05 — Collaborations</p>

              <h2

                class="section-title"

                id="partnersTitle"

                data-reveal="up"

                data-delay="100"

              >

                Our

                <em>Partners</em>

              </h2>

            </div>

            <p class="section-intro" data-reveal="up" data-delay="200">

              The studios, ateliers and creative houses who help us build every

              season — from the first fitting to the final walk.

            </p>

          </div>

          <div class="partners__grid" id="partnersGrid"></div>

        </div>

      </section>



      <!-- ============ SPONSORS ============ -->

      <section

        class="section section--dark sponsors"

        id="sponsors"

        aria-labelledby="sponsorsTitle"

      >

        <div class="container">

          <div class="section-head">

            <div>

              <p class="section-label" data-reveal="up">06 — Support</p>

              <h2

                class="section-title"

                id="sponsorsTitle"

                data-reveal="up"

                data-delay="100"

              >

                Our

                <em>Sponsors</em>

              </h2>

            </div>

            <p class="section-intro" data-reveal="up" data-delay="200">

              Season 4 is made possible by brands who believe in championing new

              talent and Indian design.

            </p>

          </div>

          <div class="sponsors__grid" id="sponsorsGrid" data-reveal="up"></div>

          <div class="sponsors__cta" data-reveal="up">

            <p>Interested in sponsoring Season 5?</p>

            <a

              class="text-link text-link--light"

              href="#sponsor-enquiry"

              data-sponsor-enquiry

              >Become a Sponsor

              <svg class="icon" aria-hidden="true">

                <use href="#i-arrow-right" /></svg

            ></a>

          </div>

        </div>

      </section>



      <!-- ============ CLIENTS ============ -->

      <section

        class="section section--light clients"

        id="clients"

        aria-labelledby="clientsTitle"

      >

        <div class="container">

          <div class="section-head">

            <div>

              <p class="section-label" data-reveal="up">07 — Trusted By</p>

              <h2

                class="section-title"

                id="clientsTitle"

                data-reveal="up"

                data-delay="100"

              >

                Our

                <em>Clients</em>

              </h2>

            </div>

            <p class="section-intro" data-reveal="up" data-delay="200">

              Fashion houses, publications and agencies who cast their campaigns

              with IDFR talent. Select a client to read their story.

            </p>

          </div>



          <div class="clients__layout">

            <article

              class="client-feature"

              id="clientFeature"

              data-reveal="clip"

              aria-live="polite"

            >

              <div class="client-feature__media">

                <img

                  class="photo"

                  id="clientFeatureImg"

                  src="{{ asset('images/clients/client-featured.jpg') }}"

                  alt=""

                  loading="lazy"

                  width="1000"

                  height="1250"

                />

              </div>

              <div class="client-feature__body">

                <div class="client-feature__logo" id="clientFeatureLogo"></div>

                <blockquote

                  class="client-feature__quote"

                  id="clientFeatureQuote"

                ></blockquote>

                <p class="client-feature__person" id="clientFeaturePerson"></p>

              </div>

            </article>

            <div

              class="clients__grid"

              id="clientsGrid"

              role="group"

              aria-label="Select a client"

            ></div>

          </div>

        </div>

        <div class="logo-strip" aria-hidden="true">

          <div class="logo-strip__track" id="clientStrip"></div>

        </div>

      </section>

    </main>

@endsection



@section('extra_modals')

<div

      class="modal shoot-book sponsor-enquiry"

      id="sponsorEnquiryModal"

      role="dialog"

      aria-modal="true"

      aria-labelledby="sponsorEnquiryTitle"

      hidden

    >

      <div class="modal__backdrop" data-close></div>

      <div class="modal__panel auth-modal__panel shoot-book__panel">

        <button

          class="modal__close"

          type="button"

          data-close

          aria-label="Close sponsorship enquiry"

        >

          <svg class="icon" aria-hidden="true">

            <use href="#i-close" />

          </svg>

        </button>



        <div data-sponsor-form>

          <p class="info__label">IDFR · Season 5</p>

          <h2

            class="auth-modal__title shoot-book__title"

            id="sponsorEnquiryTitle"

          >

            Sponsorship <em>Enquiry</em>

          </h2>

          <p class="shoot-book__lead">

            Tell us about your interest in sponsoring an IDFR fashion show.

            Fields marked <span aria-hidden="true">*</span

            ><span class="visually-hidden">with an asterisk</span> are required.

            We’ll follow up at

            <a href="mailto:info@idfr.example">info@idfr.example</a>

            with the relevant sponsorship details.

          </p>



          <form class="profile-form" id="sponsorEnquiryForm" novalidate>

            <div class="field">

              <label for="sponsorName"

                >Name / Company Name <span aria-hidden="true">*</span></label

              >

              <input

                type="text"

                id="sponsorName"

                name="name"

                autocomplete="organization"

                required

                minlength="2"

                maxlength="120"

                placeholder="Your name or company"

                data-autofocus

              />

              <span class="field__error" aria-live="polite"></span>

            </div>



            <div class="field">

              <label for="sponsorEmail"

                >Email Address <span aria-hidden="true">*</span></label

              >

              <input

                type="email"

                id="sponsorEmail"

                name="email"

                autocomplete="email"

                inputmode="email"

                required

                maxlength="120"

                placeholder="you@company.com"

              />

              <span class="field__error" aria-live="polite"></span>

            </div>



            <div class="field">

              <label for="sponsorPhone"

                >Phone Number <span aria-hidden="true">*</span></label

              >

              <input

                type="tel"

                id="sponsorPhone"

                name="phone"

                autocomplete="tel"

                inputmode="tel"

                required

                maxlength="20"

                placeholder="+91 98765 43210"

              />

              <span class="field__error" aria-live="polite"></span>

            </div>



            <div class="field">

              <label for="sponsorType"

                >Type of Sponsorship <span aria-hidden="true">*</span></label

              >

              <select id="sponsorType" name="type" required>

                <option value="" selected disabled>Select a sponsorship type</option>

                <option value="Title Sponsor">Title Sponsor</option>

                <option value="Associate Sponsors">Associate Sponsors</option>

              </select>

              <span class="field__error" aria-live="polite"></span>

            </div>



            <div class="field profile-form__full">

              <label for="sponsorMessage"

                >Message <span class="field__note">Optional</span></label

              >

              <textarea

                id="sponsorMessage"

                name="message"

                rows="4"

                maxlength="1000"

                placeholder="Share any additional details, goals or questions about sponsoring IDFR"

              ></textarea>

              <span class="field__error" aria-live="polite"></span>

            </div>



            <div class="profile-form__actions profile-form__full">

              <button class="btn btn--gold" type="submit">

                <span>Send Me Details</span

                ><svg class="icon" aria-hidden="true">

                  <use href="#i-arrow-right" />

                </svg>

              </button>

            </div>

          </form>

        </div>



        <div class="shoot-book__success" data-sponsor-success hidden>

          <span class="shoot-book__mark" aria-hidden="true"

            ><svg class="icon">

              <use href="#i-check" /></svg

          ></span>

          <h2

            class="shoot-book__success-title"

            id="sponsorEnquirySuccessTitle"

            tabindex="-1"

          >

            Enquiry <em>sent</em>

          </h2>

          <p class="shoot-book__message" role="status">

            Thank you for your interest in sponsoring IDFR. Your enquiry is ready

            to send to

            <strong>info@idfr.example</strong>. Our team will follow up with the

            relevant sponsorship details.

          </p>

          <p class="shoot-book__note">

            If your email app did not open, please write to

            <a href="mailto:info@idfr.example?subject=Sponsorship%20Enquiry"

              >info@idfr.example</a

            >

            with your sponsorship enquiry.

          </p>

          <button class="btn btn--gold" type="button" data-close>

            <span>Close</span>

          </button>

        </div>

      </div>

    </div>

@endsection



@section('page_content_templates')

<!-- ============ PAGE CONTENT ============

         Content for the sections built by js/script.js. <template> content is not displayed;

         the script reads it and renders each section. Edit text, images and options here. -->



    <!-- Hero slider (#heroStage): one figure per look, loaded dynamically from database -->

    <template id="heroSlidesData">

      @if(isset($heroImages) && $heroImages->isNotEmpty())

        @foreach($heroImages as $index => $img)

        <figure

          data-designer="{{ $img->designer_name ?: 'IDFR Runway' }}"

          data-look="{{ $img->look_number ? (str_starts_with(strtolower($img->look_number), 'look') ? $img->look_number : 'Look ' . sprintf('%02d', (int)$img->look_number)) : 'Look ' . sprintf('%02d', $index + 1) }}"

        >

          <img

            src="{{ $img->image_url }}"

            alt="{{ $img->alt_text ?: ($img->caption ?: ($img->image_title ?: 'Look ' . sprintf('%02d', $index + 1))) }}"

          />

          <figcaption>{{ $img->caption ?: ($img->image_title ?: 'Look ' . sprintf('%02d', $index + 1)) }}</figcaption>

        </figure>

        @endforeach

      @elseif($currentSeason && $currentSeason->hero_image)

        <figure data-designer="IDFR Runway" data-look="Look 01">

          <img

            src="{{ $currentSeason->hero_image_url }}"

            alt="{{ $currentSeason->title }}"

          />

          <figcaption>{{ $currentSeason->title }}</figcaption>

        </figure>

      @endif

    </template>



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

    </template>



    <!-- Gallery (#galleryGrid): one figure per photo; data-season must match a filter button's data-season.

         width/height must be the image file's real pixel size: the bento grid uses them to keep the aspect ratio. -->

    <template id="galleryData">

      @if(isset($galleryImages) && $galleryImages->count() > 0)

        @foreach($galleryImages as $item)

          <figure data-season="{{ $item->season_name }}">

            <img

              src="{{ $item->image_url }}"

              alt="{{ $item->alt_text ?: $item->title }}"

              width="{{ $item->width ?: 1000 }}"

              height="{{ $item->height ?: 1500 }}"

            />

            <figcaption>{{ $item->title }}</figcaption>

          </figure>

        @endforeach

      @endif

    </template>



    <!-- Partners (#partnersGrid) -->

    <template id="partnersData">

      @foreach($partners as $partner)

      <article>

        <h3>{{ $partner->name }}</h3>

        <p data-type>{{ $partner->type }}</p>

        <p data-desc>{{ $partner->description }}</p>

        <img src="{{ $partner->image_url }}" alt="{{ $partner->name }}" />

        <span

          @if($partner->logo_url)

          data-logo="image"

          data-src="{{ $partner->logo_url }}"

          @else

          data-logo="{{ $partner->logo_style ?: 'circle' }}"

          data-mark="{{ $partner->logo_mark ?: substr($partner->name, 0, 1) }}"

          data-word="{{ $partner->logo_word ?: strtoupper($partner->name) }}"

          data-sub="{{ $partner->logo_sub ?: '' }}"

          @endif

        ></span>

      </article>

      @endforeach

    </template>



    <!-- Sponsors (#sponsorsGrid) -->

    <template id="sponsorsData">

      @foreach($sponsors as $sponsor)

      <article>

        <h3>{{ $sponsor->name }}</h3>

        <p data-tier>{{ $sponsor->tier ?: ($sponsor->is_title ? 'Title Sponsor' : 'Associate Sponsor') }}</p>

        <span

          @if($sponsor->logo_url)

          data-logo="image"

          data-src="{{ $sponsor->logo_url }}"

          @else

          data-logo="{{ $sponsor->logo_style ?: 'crown' }}"

          data-mark="{{ $sponsor->logo_mark ?: substr($sponsor->name, 0, 1) }}"

          data-word="{{ $sponsor->logo_word ?: strtoupper($sponsor->name) }}"

          data-sub="{{ $sponsor->logo_sub ?: '' }}"

          @endif

        ></span>

      </article>

      @endforeach

    </template>



    <!-- Clients (#clientsGrid + featured testimonial) -->

    <template id="clientsData">

      @foreach($clients as $client)

      <article data-featured="{{ $client->is_featured ? 'true' : 'false' }}">

        <h3>{{ $client->name }}</h3>

        <p data-industry>{{ $client->category ?: 'Client' }}</p>

        <img src="{{ $client->image_url }}" alt="{{ $client->name }}" />

        <span

          @if($client->logo_url)

          data-logo="image"

          data-src="{{ $client->logo_url }}"

          @else

          data-logo="{{ $client->logo_style ?: 'arch' }}"

          data-mark="{{ $client->logo_mark ?: substr($client->name, 0, 1) }}"

          data-word="{{ $client->logo_word ?: strtoupper($client->name) }}"

          data-sub="{{ $client->logo_sub ?: '' }}"

          @endif

        ></span>

        <blockquote>

          {{ $client->testimonial_text }}

        </blockquote>

        <p data-person>{{ $client->author_name }}</p>

        <p data-role>{{ $client->author_title }}</p>

      </article>

      @endforeach

    </template>



    <!-- Moving Client Logos (#clientStrip) -->

    <template id="movingLogosData">

      @foreach($movingLogos as $ml)

      <span data-name="{{ $ml->name }}"

        @if($ml->logo_url)

        data-logo="image"

        data-src="{{ $ml->logo_url }}"

        @else

        data-logo="{{ $ml->logo_style ?: 'circle' }}"

        data-mark="{{ $ml->logo_mark ?: substr($ml->name, 0, 1) }}"

        data-word="{{ $ml->logo_word ?: strtoupper($ml->name) }}"

        data-sub="{{ $ml->logo_sub ?: '' }}"

        @endif

      ></span>

      @endforeach

    </template>



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