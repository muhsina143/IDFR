@extends('layouts.idfr')

@section('title', 'Model Profile | IDFR Fashion Models & Fashion Shows')
@section('description', 'Create your IDFR model profile — add portfolio photos, measurements, categories and project preferences so fashion brands and show directors can discover you.')
@section('canonical', route('profile.model'))
@section('preload')
<link rel="preload" as="image" href="{{ $model ? $model->photo_url : asset('images/models/model-01.jpg') }}" />
@endsection

@section('content')
<main id="main">
      <!-- ============ MODEL PROFILE ============ -->
      <section
        class="section section--dark profile"
        id="profile"
        data-spy="models"
        aria-labelledby="profileTitle"
      >
        <div class="container">
          <nav class="breadcrumb" aria-label="Breadcrumb" data-reveal="fade">
            <ol>
              <li><a href="{{ route('idfr.home') }}">Home</a></li>
              <li><a href="{{ route('idfr.home') }}#models">Models</a></li>
              <li aria-current="page">Model Profile</li>
            </ol>
          </nav>

          <div class="section-head">
            <div data-reveal="up">
              <p class="section-label">IDFR Runway Talent</p>
              <h1 class="section-title" id="profileTitle">
                Model <em>Profile</em>
              </h1>
            </div>
            <p class="section-intro" data-reveal="up" data-delay="150">
              Showcase your runway looks, stats and measurements so designers, casting scouts and show directors can discover you.
            </p>
          </div>

          <div class="profile__layout">
            <!-- Left: portfolio image gallery -->
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
                      src="{{ $model ? $model->photo_url : asset('images/models/model-01.jpg') }}"
                      alt="Portfolio look 1 — studio portrait"
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
                      src="{{ $model ? $model->getPortfolioUrl(0, 'images/models/model-03.jpg') : asset('images/models/model-03.jpg') }}"
                      alt="Portfolio look 2 — editorial portrait"
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
                      src="{{ $model ? $model->getPortfolioUrl(1, 'images/models/model-04.jpg') : asset('images/models/model-04.jpg') }}"
                      alt="Portfolio look 3 — commercial portrait"
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
                      src="{{ $model ? $model->photo_url : asset('images/models/model-01.jpg') }}"
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
                      src="{{ $model ? $model->getPortfolioUrl(0, 'images/models/model-03.jpg') : asset('images/models/model-03.jpg') }}"
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
                      src="{{ $model ? $model->getPortfolioUrl(1, 'images/models/model-04.jpg') : asset('images/models/model-04.jpg') }}"
                      alt=""
                      width="700"
                      height="933"
                      loading="lazy"
                    />
                  </button>
                </div>
              </div>
            </section>

            <!-- Right: model details form -->
            <section
              class="profile__details"
              aria-labelledby="profileFormTitle"
              data-reveal="up"
              data-delay="120"
            >
              <h2 class="profile__heading" id="profileFormTitle">
                Model Details
              </h2>
              <p class="profile__lead">
                Fill in your personal details, measurements and portfolio to publish your model profile on IDFR.
              </p>

              @if($model)
                @if($model->status === 'pending')
                  <div class="alert" style="margin-bottom: 20px; padding: 14px 18px; border-radius: 8px; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.35); color: #fbbf24; display: flex; align-items: flex-start; gap: 12px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0; margin-top:2px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <div>
                      <strong style="display:block; font-size:0.95rem; margin-bottom:2px;">Profile Submitted — Pending Admin Approval</strong>
                      <span style="font-size:0.85rem; opacity:0.9;">Your model profile and photos have been submitted for review. Once verified and accepted by the IDFR team, your profile will be published live on the public showcase.</span>
                    </div>
                  </div>
                @elseif($model->status === 'published')
                  <div class="alert" style="margin-bottom: 20px; padding: 14px 18px; border-radius: 8px; background: rgba(34, 197, 94, 0.12); border: 1px solid rgba(34, 197, 94, 0.35); color: #4ade80; display: flex; align-items: flex-start; gap: 12px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0; margin-top:2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <div>
                      <strong style="display:block; font-size:0.95rem; margin-bottom:2px;">Profile Approved &amp; Live</strong>
                      <span style="font-size:0.85rem; opacity:0.9;">Your profile is approved and currently featured live on the IDFR Models showcase.</span>
                    </div>
                  </div>
                @elseif($model->status === 'rejected')
                  <div class="alert" style="margin-bottom: 20px; padding: 14px 18px; border-radius: 8px; background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.35); color: #f87171; display: flex; align-items: flex-start; gap: 12px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0; margin-top:2px;"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                    <div>
                      <strong style="display:block; font-size:0.95rem; margin-bottom:2px;">Profile Not Approved</strong>
                      <span style="font-size:0.85rem; opacity:0.9;">Your profile was reviewed and not approved. Please review your details and photos below, update them as needed, and submit again for verification.</span>
                    </div>
                  </div>
                @endif
              @endif

              @if(session('success') || session('status'))
                <div class="alert alert--success" style="margin-bottom: 20px; padding: 14px 18px; border-radius: 8px; background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.3); color: #4ade80; display: flex; align-items: center; gap: 10px;">
                  <svg class="icon" aria-hidden="true" style="width: 18px; height: 18px; fill: currentColor;"><use href="#i-check" /></svg>
                  <span>{{ session('success') ?? session('status') }}</span>
                </div>
              @endif

              @if(session('error'))
                <div class="alert alert--error" style="margin-bottom: 20px; padding: 14px 18px; border-radius: 8px; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #f87171;">
                  {{ session('error') }}
                </div>
              @endif

              @if($errors->any())
                <div class="alert alert--error" style="margin-bottom: 20px; padding: 14px 18px; border-radius: 8px; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #f87171;">
                  {{ $errors->first() }}
                </div>
              @endif

              @php
                $savedGender = strtolower((string) old('gender', $model->gender ?? 'female'));
                $savedCategory = strtolower((string) old('category', $model->level ?? 'new-face'));
                $savedTypes = old('modelType', (array) ($model->types ?? ['commercial']));
                $savedOutstation = strtolower((string) old('outstation', $model->outstation ?? 'yes'));
                $savedInternational = strtolower((string) old('international', $model->international ?? 'no'));
                $savedSkills = old('skills', is_array($model->skills ?? null) ? implode(', ', $model->skills) : ($model->skills ?? 'Runway walk, Editorial posing'));
                $savedAbout = old('about', $model->description ?? '');

                $meas = is_array($model->measurements ?? null) ? $model->measurements : [];
                $savedBust = old('bust', $meas['Bust'] ?? ($meas['Chest'] ?? ($meas['bust'] ?? '')));
                $savedWaist = old('waist', $meas['Waist'] ?? ($meas['waist'] ?? ''));
                $savedHips = old('hips', $meas['Hips'] ?? ($meas['hips'] ?? ''));
                $savedShoe = old('shoe', $meas['Shoe'] ?? ($meas['shoe'] ?? ''));
              @endphp

              <form class="profile-form" id="profileForm" method="POST" action="{{ route('profile.model.save') }}" data-profile-kind="model" data-profile-label="model" enctype="multipart/form-data" data-validate data-ajax="true" data-server-errors='@json($errors->getMessages())' novalidate>
                @csrf
                <input type="file" name="profile_photo" id="pfFile_profile_photo" class="visually-hidden" tabindex="-1" aria-hidden="true" style="display:none;" />
                <input type="file" name="portfolio_image_1" id="pfFile_portfolio_1" class="visually-hidden" tabindex="-1" aria-hidden="true" style="display:none;" />
                <input type="file" name="portfolio_image_2" id="pfFile_portfolio_2" class="visually-hidden" tabindex="-1" aria-hidden="true" style="display:none;" />
                <div class="field profile-form__full">
                  <label for="pfName">Name</label>
                  <input
                    type="text"
                    id="pfName" maxlength="150" data-rules="required|min:2|max:150|name" data-label="Name"
                    name="name"
                    autocomplete="name"
                    required
                    minlength="2"
                    placeholder="Your full name"
                    value="{{ old('name', $model->name ?? ($user->name ?? '')) }}"
                  />
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field">
                  <label for="pfPhone">Phone</label>
                  <input
                    type="tel"
                    id="pfPhone" data-rules="phone" data-label="Phone"
                    name="phone"
                    autocomplete="tel"
                    placeholder="+971 54 178 6786"
                    value="{{ old('phone', $model->phone ?? ($user->phone ?? '')) }}"
                  />
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field">
                  <label for="pfEmail">Email</label>
                  <input
                    type="email"
                    id="pfEmail" data-rules="email|max:150" data-label="Email"
                    name="email"
                    autocomplete="email"
                    placeholder="you@example.com"
                    value="{{ old('email', $model->email ?? ($user->email ?? '')) }}"
                  />
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field">
                  <label for="pfGender">Gender</label>
                  <select id="pfGender" name="gender" required data-rules="select" data-label="Gender">
                    <option value="">Select gender</option>
                    <option value="male" {{ $savedGender === 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ $savedGender === 'female' ? 'selected' : '' }}>Female</option>
                  </select>
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field">
                  <label for="pfPlace" id="pfPlaceLabel">Place</label>
                  <div class="combo" id="placeCombo">
                    <input
                      type="text"
                      id="pfPlace" data-rules="required|min:2|max:100" data-label="Place"
                      name="place"
                      role="combobox"
                      aria-autocomplete="list"
                      aria-expanded="false"
                      aria-controls="pfPlaceList"
                      autocomplete="off"
                      spellcheck="false"
                      required
                      placeholder="Search a city"
                      value="{{ old('place', $model->location ?? '') }}"
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
                  <label for="pfAge">Age</label>
                  <div class="unit">
                    <input
                      type="number"
                      id="pfAge" data-rules="required|numeric|min:14|max:70" data-label="Age"
                      name="age"
                      inputmode="numeric"
                      required
                      min="16"
                      max="60"
                      step="1"
                      placeholder="e.g. 22"
                      value="{{ old('age', $model->age ?? '') }}"
                    />
                    <span class="unit__label" aria-hidden="true">yrs</span>
                  </div>
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field">
                  <label for="pfHeight">Height</label>
                  <div class="unit">
                    <input
                      type="number"
                      id="pfHeight" data-rules="required|numeric|min:100|max:230" data-label="Height"
                      name="height"
                      inputmode="numeric"
                      required
                      min="120"
                      max="215"
                      step="1"
                      placeholder="e.g. 172"
                      aria-describedby="pfHeightHint"
                      value="{{ old('height', $model->height ?? '') }}"
                    />
                    <span class="unit__label" aria-hidden="true">cm</span>
                  </div>
                  <span class="field__hint" id="pfHeightHint"
                    >In centimetres</span
                  >
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <fieldset class="field">
                  <legend>Category</legend>
                  <div class="choices choices--fit">
                    <label class="choice choice--radio"
                      ><input
                        type="radio"
                        name="category"
                        value="experienced"
                        {{ in_array($savedCategory, ['experienced', 'exp'], true) ? 'checked' : '' }}
                        required
                      /><span>Experienced</span></label
                    >
                    <label class="choice choice--radio"
                      ><input
                        type="radio"
                        name="category"
                        value="new-face"
                        {{ in_array($savedCategory, ['new-face', 'new face', 'newface'], true) ? 'checked' : '' }}
                      /><span>New Face</span></label
                    >
                  </div>
                  <span class="field__error" aria-live="polite"></span>
                </fieldset>

                <div class="field">
                  <label for="pfExperience">Experience</label>
                  <div class="unit">
                    <input
                      type="number"
                      id="pfExperience" data-rules="required|numeric|min:0|max:50" data-label="Experience"
                      name="experience"
                      inputmode="decimal"
                      required
                      min="0"
                      max="40"
                      step="0.5"
                      placeholder="e.g. 2"
                      value="{{ old('experience', $model->experience ?? '') }}"
                    />
                    <span class="unit__label" aria-hidden="true">yrs</span>
                  </div>
                  <span class="field__error" aria-live="polite"></span>
                </div>

                                <fieldset class="field profile-form__full">
                  <legend>
                    Measurements &amp; Sizes
                    <span class="field__note">Helps designers and stylists prepare runway fits</span>
                  </legend>
                  <div class="choices" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px; margin-top: 8px;">
                    <div>
                      <label for="pfBust" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--gold); display: block; margin-bottom: 4px;">Bust / Chest</label>
                      <input type="text" id="pfBust" name="bust" placeholder='e.g. 34"' value="{{ $savedBust }}" />
                    </div>
                    <div>
                      <label for="pfWaist" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--gold); display: block; margin-bottom: 4px;">Waist</label>
                      <input type="text" id="pfWaist" name="waist" placeholder='e.g. 26"' value="{{ $savedWaist }}" />
                    </div>
                    <div>
                      <label for="pfHips" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--gold); display: block; margin-bottom: 4px;">Hips</label>
                      <input type="text" id="pfHips" name="hips" placeholder='e.g. 36"' value="{{ $savedHips }}" />
                    </div>
                    <div>
                      <label for="pfShoe" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--gold); display: block; margin-bottom: 4px;">Shoe Size</label>
                      <input type="text" id="pfShoe" name="shoe" placeholder='e.g. UK 6' value="{{ $savedShoe }}" />
                    </div>
                  </div>
                </fieldset>
<div class="field profile-form__full">
                  <label for="pfSkills">Skills</label>
                  <textarea
                    id="pfSkills" data-rules="required|min:2|max:500" data-label="Skills"
                    name="skills"
                    rows="3"
                    required
                    minlength="3"
                    placeholder="e.g. Runway walk, editorial posing, Bharatanatyam, Malayalam & English"
                  >{{ $savedSkills }}</textarea>
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <fieldset class="field profile-form__full" id="pfTypes">
                  <legend>
                    Model Type
                    <span class="field__note">Select all that apply</span>
                  </legend>
                  <div class="choices choices--grid">
                    @foreach([
                      'commercial' => 'Commercial Model',
                      'editorial' => 'Editorial Model',
                      'parts' => 'Parts Model',
                      'fitness' => 'Fitness Model',
                      'promotional' => 'Promotional Model',
                      'glamour' => 'Glamour Model',
                      'print' => 'Print Model',
                      'runway' => 'Runway/Ramp Model',
                      'video' => 'Video Shoot Model',
                      'actor' => 'Actor',
                    ] as $typeKey => $typeTitle)
                      <label class="choice choice--check"
                        ><input
                          type="checkbox"
                          name="modelType[]" data-rules="group_min:1" data-label="Model Type"
                          value="{{ $typeKey }}"
                          {{ in_array($typeKey, (array) $savedTypes, true) ? 'checked' : '' }}
                        /><span>{{ $typeTitle }}</span></label
                      >
                    @endforeach
                  </div>
                  <span class="field__error" aria-live="polite"></span>
                </fieldset>

                <fieldset class="field profile-form__full">
                  <legend>Ready for Outstation Projects</legend>
                  <div class="choices choices--row">
                    <label class="choice choice--radio"
                      ><input
                        type="radio"
                        name="outstation"
                        value="yes"
                        {{ $savedOutstation === 'yes' ? 'checked' : '' }}
                        required
                      /><span>Yes</span></label
                    >
                    <label class="choice choice--radio"
                      ><input
                        type="radio"
                        name="outstation"
                        value="no"
                        {{ $savedOutstation === 'no' ? 'checked' : '' }}
                      /><span>No</span></label
                    >
                  </div>
                  <span class="field__error" aria-live="polite"></span>
                </fieldset>

                <fieldset class="field profile-form__full">
                  <legend>Ready for International Projects</legend>
                  <div class="choices choices--row">
                    <label class="choice choice--radio"
                      ><input
                        type="radio"
                        name="international"
                        value="yes"
                        {{ $savedInternational === 'yes' ? 'checked' : '' }}
                        required
                      /><span>Yes</span></label
                    >
                    <label class="choice choice--radio"
                      ><input
                        type="radio"
                        name="international"
                        value="no"
                        {{ $savedInternational === 'no' ? 'checked' : '' }}
                      /><span>No</span></label
                    >
                  </div>
                  <span class="field__error" aria-live="polite"></span>
                </fieldset>
                <div class="field profile-form__full">
                  <label for="pfAbout">About / Biography</label>
                  <textarea
                    id="pfAbout"
                    name="about"
                    rows="4"
                    placeholder="Tell designers, brands and casting directors about your style, runway experience or artistic passions..."
                  >{{ $savedAbout }}</textarea>
                  <span class="field__error" aria-live="polite"></span>
                </div>

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
