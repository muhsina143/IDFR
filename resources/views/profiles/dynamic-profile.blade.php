@extends('layouts.idfr')

@section('title', $categoryModel->name . ' Profile | IDFR Creative Professionals')
@section('description', 'Create your IDFR ' . strtolower($categoryModel->name) . ' profile — add portfolio work, specialisations and contact details so clients can discover you.')
@section('canonical', route('profile.category', $categoryModel->slug))
@section('preload')
<link rel="preload" as="image" href="{{ $profile && $profile->photo_url ? $profile->photo_url : $categoryModel->photo_url }}" />
@endsection

@section('content')
<main id="main">
  <!-- ============ CREATIVE PROFESSIONAL DYNAMIC PROFILE ============ -->
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
          <li><a href="{{ route('idfr.professionals.category', $categoryModel->slug) }}">{{ $categoryModel->name }}</a></li>
          <li aria-current="page">{{ $categoryModel->name }} Profile</li>
        </ol>
      </nav>

      <div class="section-head">
        <div data-reveal="up">
          <p class="section-label">Creative Professionals</p>
          <h1 class="section-title" id="profileTitle">
            {!! $categoryModel->hero_title !!}
          </h1>
        </div>
        <p class="section-intro" data-reveal="up" data-delay="150">
          {{ $categoryModel->description }}
        </p>
      </div>

      <div class="profile__layout">
        <!-- Gallery Section -->
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
                  src="{{ $profile && $profile->photo_url ? $profile->photo_url : $categoryModel->photo_url }}"
                  alt="{{ $categoryModel->name }} Showcase"
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
                  src="{{ $profile ? $profile->getPortfolioUrl(0, $categoryModel->photo_url) : $categoryModel->photo_url }}"
                  alt="{{ $categoryModel->name }} Portfolio Work 1"
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
                  src="{{ $profile ? $profile->getPortfolioUrl(1, $categoryModel->photo_url) : $categoryModel->photo_url }}"
                  alt="{{ $categoryModel->name }} Portfolio Work 2"
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
                  src="{{ $profile && $profile->photo_url ? $profile->photo_url : $categoryModel->photo_url }}"
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
                  src="{{ $profile ? $profile->getPortfolioUrl(0, $categoryModel->photo_url) : $categoryModel->photo_url }}"
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
                  src="{{ $profile ? $profile->getPortfolioUrl(1, $categoryModel->photo_url) : $categoryModel->photo_url }}"
                  alt=""
                  width="700"
                  height="933"
                  loading="lazy"
                />
              </button>
            </div>
          </div>
        </section>

        <!-- Form Section -->
        <section
          class="profile__details"
          aria-labelledby="profileFormTitle"
          data-reveal="up"
          data-delay="120"
        >
          <h2 class="profile__heading" id="profileFormTitle">
            {{ $categoryModel->form_title }}
          </h2>
          <p class="profile__lead">
            {{ $categoryModel->form_lead }}
          </p>

          @php
            $currentStatus = ($profile ?? $designer ?? null)?->status;
          @endphp
          @if($currentStatus === 'pending')
            <div class="alert" style="margin-bottom: 20px; padding: 14px 18px; border-radius: 8px; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.35); color: #fbbf24; display: flex; align-items: flex-start; gap: 12px;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0; margin-top:2px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              <div>
                <strong style="display:block; font-size:0.95rem; margin-bottom:2px;">Profile Submitted — Pending Admin Approval</strong>
                <span style="font-size:0.85rem; opacity:0.9;">Your {{ $categoryModel->name }} profile and portfolio photos have been submitted for review. Once verified and approved by the IDFR team, your profile will be published live on the directory.</span>
              </div>
            </div>
          @elseif($currentStatus === 'published')
            <div class="alert" style="margin-bottom: 20px; padding: 14px 18px; border-radius: 8px; background: rgba(34, 197, 94, 0.12); border: 1px solid rgba(34, 197, 94, 0.35); color: #4ade80; display: flex; align-items: flex-start; gap: 12px;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0; margin-top:2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
              <div>
                <strong style="display:block; font-size:0.95rem; margin-bottom:2px;">Profile Approved &amp; Live</strong>
                <span style="font-size:0.85rem; opacity:0.9;">Your profile is approved and currently featured live on the IDFR {{ $categoryModel->name }} directory.</span>
              </div>
            </div>
          @elseif($currentStatus === 'rejected')
            <div class="alert" style="margin-bottom: 20px; padding: 14px 18px; border-radius: 8px; background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.35); color: #f87171; display: flex; align-items: flex-start; gap: 12px;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0; margin-top:2px;"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
              <div>
                <strong style="display:block; font-size:0.95rem; margin-bottom:2px;">Profile Not Approved</strong>
                <span style="font-size:0.85rem; opacity:0.9;">Your profile was reviewed and not approved. Please review your details and uploaded works below, update any missing information, and submit again for verification.</span>
              </div>
            </div>
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

          @include('profiles.partials.dynamic-form', [
            'categoryModel' => $categoryModel,
            'profile' => $profile ?? $designer ?? null,
            'fields' => $fields,
            'user' => $user,
            'saveRoute' => route('profile.category.save', ['category' => $categoryModel->slug]),
          ])
        </section>
      </div>
    </div>
  </section>
</main>
@endsection

@section('page_content_templates')
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
@endsection

@push('scripts')
<script src="{{ asset('js/creative_profiles.js') }}" defer></script>
@endpush

