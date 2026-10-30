@extends('layouts.idfr')

@section('title', 'Jewellery Designer Profile | IDFR Creative Professionals')
@section('description', 'Create your IDFR jewellery designer profile — add portfolio work, specialisations and contact details so clients can discover you.')
@section('canonical', route('profile.jewellery-designer'))
@section('preload')
<link rel="preload" as="image" href="{{ $profile ? $profile->photo_url : asset('images/creative/fashion-designer/maya-joseph.jpg') }}" />
@endsection

@section('content')
<main id="main">
      <!-- ============ MODEL PROFILE ============ -->
      <section
        class="section section--dark profile"
        id="profile"
        data-spy="creative"
        aria-labelledby="profileTitle"
      >
        <div class="container">
          <nav class="breadcrumb" aria-label="Breadcrumb" data-reveal="fade">
            <ol>
              <li><a href="{{ route('idfr.home') }}">Home</a></li>
              <li><a href="{{ route('idfr.professionals') }}">Creative Professionals</a></li>
              <li><a href="{{ route('idfr.professionals') }}#creative-jewellery-designer">Jewellery Designer</a></li>
              <li aria-current="page">Jewellery Designer Profile</li>
            </ol>
          </nav>

          <div class="section-head">
            <div data-reveal="up">
              <p class="section-label">Creative Professionals</p>
              <h1 class="section-title" id="profileTitle">
                Jewellery <em>Designer</em>
              </h1>
            </div>
            <p class="section-intro" data-reveal="up" data-delay="150">
              Present bridal sets, custom pieces and runway accessories to the IDFR fashion community.
            </p>
          </div>

          <div class="profile__layout">
            <section
              class="profile__gallery"
              aria-label="Portfolio images"
              data-reveal="up"
            >
              <div
                class="pg"
                id="profileGallery"
                aria-roledescription="carousel"
              >
                <div class="pg__stage">
                  
                  <figure
                    class="pg__slide is-active"
                    aria-roledescription="slide"
                    aria-label="1 of 3"
                    
                  >
                    <img
                      class="photo"
                      src="{{ $profile ? $profile->photo_url : asset('images/creative/fashion-designer/maya-joseph.jpg') }}"
                      alt="Jewellery designer reviewing pieces"
                      width="700"
                      height="933"
                      
                    />
                    <button
                      class="pg__edit"
                      type="button"
                      aria-label="Replace portfolio image 1"
                      data-pg-edit
                    >
                      <svg class="icon" aria-hidden="true">
                        <use href="#i-edit" />
                      </svg>
                    </button>
                  </figure>
                  <figure
                    class="pg__slide"
                    aria-roledescription="slide"
                    aria-label="2 of 3"
                    aria-hidden="true"
                  >
                    <img
                      class="photo"
                      src="{{ $profile ? $profile->getPortfolioUrl(0, 'images/asdreams/gallery/engagement-04.jpg') : asset('images/asdreams/gallery/engagement-04.jpg') }}"
                      alt="Engagement rings resting on white flowers"
                      width="700"
                      height="933"
                      loading="lazy"
                    />
                    <button
                      class="pg__edit"
                      type="button"
                      aria-label="Replace portfolio image 2"
                      data-pg-edit
                    >
                      <svg class="icon" aria-hidden="true">
                        <use href="#i-edit" />
                      </svg>
                    </button>
                  </figure>
                  <figure
                    class="pg__slide"
                    aria-roledescription="slide"
                    aria-label="3 of 3"
                    aria-hidden="true"
                  >
                    <img
                      class="photo"
                      src="{{ $profile ? $profile->getPortfolioUrl(1, 'images/creative/portfolio/design-sketches.jpg') : asset('images/creative/portfolio/design-sketches.jpg') }}"
                      alt="Jewellery design sketches on a studio desk"
                      width="700"
                      height="933"
                      loading="lazy"
                    />
                    <button
                      class="pg__edit"
                      type="button"
                      aria-label="Replace portfolio image 3"
                      data-pg-edit
                    >
                      <svg class="icon" aria-hidden="true">
                        <use href="#i-edit" />
                      </svg>
                    </button>
                  </figure>

                  <button
                    class="pg__nav pg__nav--prev"
                    type="button"
                    aria-label="Previous image"
                  >
                    <svg class="icon" aria-hidden="true">
                      <use href="#i-arrow-left" />
                    </svg>
                  </button>
                  <button
                    class="pg__nav pg__nav--next"
                    type="button"
                    aria-label="Next image"
                  >
                    <svg class="icon" aria-hidden="true">
                      <use href="#i-arrow-right" />
                    </svg>
                  </button>

                  <p class="pg__counter" aria-hidden="true">
                    <span class="pg__current">01</span> /
                    <span class="pg__total">03</span>
                  </p>
                  <p class="visually-hidden pg__status" aria-live="polite"></p>
                </div>

                <div
                  class="pg__thumbs"
                  role="group"
                  aria-label="Choose an image"
                >
                  
                  <button
                    class="pg__thumb is-active"
                    type="button"
                    aria-label="Show image 1"
                    aria-current="true"
                  >
                    <img
                      class="photo"
                      src="{{ $profile ? $profile->photo_url : asset('images/creative/fashion-designer/maya-joseph.jpg') }}"
                      alt=""
                      width="700"
                      height="933"
                      loading="lazy"
                    />
                  </button>
                  <button
                    class="pg__thumb"
                    type="button"
                    aria-label="Show image 2"
                    aria-current="false"
                  >
                    <img
                      class="photo"
                      src="{{ $profile ? $profile->getPortfolioUrl(0, 'images/asdreams/gallery/engagement-04.jpg') : asset('images/asdreams/gallery/engagement-04.jpg') }}"
                      alt=""
                      width="700"
                      height="933"
                      loading="lazy"
                    />
                  </button>
                  <button
                    class="pg__thumb"
                    type="button"
                    aria-label="Show image 3"
                    aria-current="false"
                  >
                    <img
                      class="photo"
                      src="{{ $profile ? $profile->getPortfolioUrl(1, 'images/creative/portfolio/design-sketches.jpg') : asset('images/creative/portfolio/design-sketches.jpg') }}"
                      alt=""
                      width="700"
                      height="933"
                      loading="lazy"
                    />
                  </button>
                </div>
              </div>
            </section>

            <section
              class="profile__details"
              aria-labelledby="profileFormTitle"
              data-reveal="up"
              data-delay="120"
            >
              
              <h2 class="profile__heading" id="profileFormTitle">
                Jewellery Designer Details
              </h2>
              <p class="profile__lead">
                Describe materials, categories and design specialisations for your profile.
              </p>

              @if(session('success'))
                <div class="alert alert--success" style="margin-bottom: 1.5rem; padding: 1rem 1.25rem; border-radius: var(--radius-sm, 8px); background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.4); color: #86efac;">
                  {{ session('success') }}
                </div>
              @endif

              @if($errors->any())
                <div class="alert alert--danger" style="margin-bottom: 1.5rem; padding: 1rem 1.25rem; border-radius: var(--radius-sm, 8px); background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5;">
                  <ul style="margin: 0; padding-left: 1.25rem;">
                    @foreach($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <form
                class="profile-form"
                id="profileForm"
                data-profile-kind="creative"
                data-profile-label="jewellery designer"
                action="{{ route('profile.jewellery-designer.save') }}"
                method="POST"
                enctype="multipart/form-data"
                novalidate
              >
                @csrf
                <input type="file" name="profile_photo" id="pfFile_profile_photo" class="visually-hidden" tabindex="-1" aria-hidden="true" style="display:none;" />
                <input type="file" name="portfolio_image_1" id="pfFile_portfolio_1" class="visually-hidden" tabindex="-1" aria-hidden="true" style="display:none;" />
                <input type="file" name="portfolio_image_2" id="pfFile_portfolio_2" class="visually-hidden" tabindex="-1" aria-hidden="true" style="display:none;" />
                <input type="hidden" name="category" value="jewellery-designer" />
                <input type="hidden" name="brand" value="{{ old('brand', $profile->brand ?? 'IDFR') }}" />

                <div class="field profile-form__full">
                  <label for="pfName">Name</label>
                  <input
                    type="text"
                    id="pfName"
                    name="name"
                    autocomplete="name"
                    required
                    minlength="2"
                    placeholder="Your full name"
                    value="{{ old('name', $profile->name ?? $user->name ?? '') }}"
                  />
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field">
                  <label for="pfProfession">Profession</label>
                  <input
                    type="text"
                    id="pfProfession"
                    name="profession"
                    readonly
                    value="Jewellery Designer"
                  />
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field">
                  <label for="pfPlace" id="pfPlaceLabel">Location</label>
                  <div class="combo" id="placeCombo">
                    <input
                      type="text"
                      id="pfPlace"
                      name="place"
                      role="combobox"
                      aria-autocomplete="list"
                      aria-expanded="false"
                      aria-controls="pfPlaceList"
                      autocomplete="off"
                      spellcheck="false"
                      required
                      placeholder="Search a city"
                      value="{{ old('place', $profile->place ?? '') }}"
                    />
                    <ul
                      class="combo__list"
                      id="pfPlaceList"
                      role="listbox"
                      aria-labelledby="pfPlaceLabel"
                      hidden
                    ></ul>
                  </div>
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field">
                  <label for="pfExperience">Experience</label>
                  <div class="unit">
                    <input
                      type="number"
                      id="pfExperience"
                      name="experience"
                      inputmode="decimal"
                      required
                      min="0"
                      max="40"
                      step="0.5"
                      placeholder="e.g. 2"
                      value="{{ old('experience', $profile->experience ?? '') }}"
                    />
                    <span class="unit__label" aria-hidden="true">yrs</span>
                  </div>
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field">
                  <label for="pfPhone">Phone</label>
                  <input
                    type="tel"
                    id="pfPhone"
                    name="phone"
                    autocomplete="tel"
                    required
                    placeholder="+91 …"
                    value="{{ old('phone', $profile->phone ?? $user->phone ?? '') }}"
                  />
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field profile-form__full">
                  <label for="pfEmail">Email</label>
                  <input
                    type="email"
                    id="pfEmail"
                    name="email"
                    autocomplete="email"
                    required
                    placeholder="you@example.com"
                    value="{{ old('email', $profile->email ?? $user->email ?? '') }}"
                  />
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field profile-form__full">
                  <label for="pfAbout">About</label>
                  <textarea
                    id="pfAbout"
                    name="about"
                    rows="4"
                    required
                    minlength="20"
                    placeholder="Short professional bio"
                  >{{ old('about', $profile->about ?? '') }}</textarea>
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field profile-form__full">
                  <label for="pfEquipment">Materials &amp; Categories</label>
                  <textarea
                    id="pfEquipment"
                    name="materials"
                    rows="2"
                    required
                    minlength="3"
                    placeholder="e.g. Gold, silver, polki, temple jewellery, contemporary"
                  >{{ old('materials', $profile->equipment ?? '') }}</textarea>
                  <span class="field__error" aria-live="polite"></span>
                </div>

                @php
                  $selectedServices = old('service', $profile->services ?? []);
                  if (!is_array($selectedServices)) {
                      $selectedServices = is_string($selectedServices) ? json_decode($selectedServices, true) ?? [] : [];
                  }
                @endphp
                <fieldset class="field profile-form__full" id="pfServices">
                  <legend>
                    Jewellery Design Specialisations
                    <span class="field__note">Select all that apply</span>
                  </legend>
                  <div class="choices choices--grid">
                    @php
                      $jewelleryServices = [
                        'bridal-jewellery-sets' => 'Bridal jewellery sets',
                        'custom-engagement-rings' => 'Custom engagement rings',
                        'temple-inspired-pieces' => 'Temple-inspired pieces',
                        'runway-accessories' => 'Runway accessories',
                        'restyling-family-gold' => 'Restyling family gold',
                        'silver-contemporary-lines' => 'Silver & contemporary lines',
                        'necklace-maang-tikka-sets' => 'Necklace & maang tikka sets',
                        'bespoke-commissions' => 'Bespoke commissions',
                      ];
                    @endphp
                    @foreach($jewelleryServices as $serviceValue => $serviceLabel)
                      <label class="choice choice--check">
                        <input
                          type="checkbox"
                          name="service[]"
                          value="{{ $serviceValue }}"
                          {{ in_array($serviceValue, $selectedServices) ? 'checked' : '' }}
                        /><span>{{ $serviceLabel }}</span>
                      </label>
                    @endforeach
                  </div>
                  <span class="field__error" aria-live="polite"></span>
                </fieldset>

                @php
                  $outstationVal = old('outstation', $profile->outstation ?? 'yes');
                @endphp
                <fieldset class="field profile-form__full">
                  <legend>Available for Outstation Projects</legend>
                  <div class="choices choices--row">
                    <label class="choice choice--radio"
                      ><input
                        type="radio"
                        name="outstation"
                        value="yes"
                        required
                        {{ $outstationVal === 'yes' ? 'checked' : '' }}
                      /><span>Yes</span></label
                    >
                    <label class="choice choice--radio"
                      ><input
                        type="radio"
                        name="outstation"
                        value="no"
                        {{ $outstationVal === 'no' ? 'checked' : '' }}
                      /><span>No</span></label
                    >
                  </div>
                  <span class="field__error" aria-live="polite"></span>
                </fieldset>

                <div class="profile-form__actions profile-form__full">
                  <button class="btn btn--ghost" type="reset">
                    <span>Reset</span>
                  </button>
                  <button class="btn btn--gold" type="submit">
                    <span>Save Profile</span
                    ><svg class="icon" aria-hidden="true">
                      <use href="#i-arrow-right" />
                    </svg>
                  </button>
                </div>
              </form>
            </section>
          </div>
        </div>
      </section>
    </main>
@endsection
@section('page_content_templates')
<!-- ============ PAGE CONTENT ============
         Content for the sections built by js/script.js. <template> content is not displayed;
         the script reads it and renders each section. Edit text, images and options here. -->

    <!-- Place field (#placeCombo): the places offered in the searchable list, in this order. -->
    <template id="placesData">
      <option>Kozhikode</option>
      <option>Kochi</option>
      <option>Thiruvananthapuram</option>
      <option>Thrissur</option>
      <option>Kannur</option>
      <option>Kollam</option>
      <option>Kottayam</option>
      <option>Palakkad</option>
      <option>Malappuram</option>
      <option>Alappuzha</option>
      <option>Wayanad</option>
      <option>Bengaluru</option>
      <option>Mysuru</option>
      <option>Mangaluru</option>
      <option>Chennai</option>
      <option>Coimbatore</option>
      <option>Madurai</option>
      <option>Hyderabad</option>
      <option>Mumbai</option>
      <option>Pune</option>
      <option>Goa</option>
      <option>New Delhi</option>
      <option>Gurugram</option>
      <option>Noida</option>
      <option>Jaipur</option>
      <option>Ahmedabad</option>
      <option>Chandigarh</option>
      <option>Kolkata</option>
      <option>Lucknow</option>
      <option>Indore</option>
      <option>Bhubaneswar</option>
      <option>Dubai</option>
    </template>

    <!-- Info modal content: one template per program, opened by links with a matching data-program. -->
    <template id="program-casting">
      <p class="info__label">Opportunities</p>
      <h2 class="info__title" id="infoModalTitle">Casting <em>Calls</em></h2>
      <p class="info__intro">
        Hundreds of fashion brands and studios post assignments on IDFR every month.
        Here is a preview of current open calls — register to apply and receive
        new briefs first.
      </p>
      <div class="info__list">
        <div class="info__item">
          <span class="info__item-num">01</span>
          <p class="info__item-meta">Kochi · Female, 20–28 · Editorial</p>
          <h3 class="info__item-name">Bridal Couture Campaign</h3>
          <p class="info__item-desc">
            Two-day shoot for a festive bridal lookbook. Height 5'7" and above.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">02</span>
          <p class="info__item-meta">Bengaluru · Male, 22–30 · Commercial</p>
          <h3 class="info__item-name">Menswear Lookbook</h3>
          <p class="info__item-desc">
            Autumn–winter suiting lookbook with an e-commerce shoot.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">03</span>
          <p class="info__item-meta">Chennai · Female, 22–32 · Commercial</p>
          <h3 class="info__item-name">Fine Jewellery Ad Film</h3>
          <p class="info__item-desc">
            Close-up beauty work for a national jewellery brand.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">04</span>
          <p class="info__item-meta">Kozhikode · All genders, 18+ · Runway</p>
          <h3 class="info__item-name">Season 4 Runway Auditions</h3>
          <p class="info__item-desc">
            Open auditions for IDFR Fashion Show Season 4. Closes 15 November
            2026.
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
          <span>Apply as Model</span
          ><svg class="icon" aria-hidden="true">
            <use href="#i-arrow-right" />
          </svg>
        </button>
        
      </div>
    </template>
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
@push('scripts')
<script src="{{ asset('js/creative_profiles.js') }}" defer></script>
@endpush