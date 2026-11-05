@extends('layouts.idfr')

@section('title', 'Form Preview: ' . $categoryModel->name . ' | IDFR Admin')
@section('description', 'Live administrative preview of dynamic profile form for ' . $categoryModel->name)

@section('content')
<main id="main">
  <!-- Admin Preview Banner -->
  <div style="background: linear-gradient(90deg, #1e1b18, #2a241b); border-bottom: 1px solid var(--gold); padding: 12px 20px; position: sticky; top: 0; z-index: 1000; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
    <div style="display: flex; align-items: center; gap: 12px;">
      <span class="badge" style="background: var(--gold); color: #000; font-weight: 700; padding: 4px 10px; border-radius: 4px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em;">
        Live Admin Preview
      </span>
      <span style="color: #fff; font-size: 0.9rem; font-weight: 500;">
        Category: <strong>{{ $categoryModel->name }}</strong> ({{ $fields->count() }} active fields)
      </span>
    </div>
    <div style="display: flex; align-items: center; gap: 12px;">
      <a href="{{ route('admin.dynamic-forms.categories.fields', $categoryModel) }}" style="color: var(--gold); font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line></svg>
        <span>Configure Fields</span>
      </a>
      <a href="{{ route('admin.dynamic-forms.index') }}" style="color: #cbd5e1; font-size: 0.85rem; text-decoration: none;">
        Exit Preview &rarr;
      </a>
    </div>
  </div>

  <section class="section section--dark profile" id="profile" data-spy="creative" aria-labelledby="profileTitle">
    <div class="container">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <ol>
          <li><a href="{{ route('idfr.home') }}">Home</a></li>
          <li><a href="{{ route('idfr.professionals') }}">Creative Professionals</a></li>
          <li><a href="#">{{ $categoryModel->name }}</a></li>
          <li aria-current="page">{{ $categoryModel->name }} Profile (Preview)</li>
        </ol>
      </nav>

      <div class="section-head">
        <div>
          <p class="section-label">Dynamic Form System</p>
          <h1 class="section-title" id="profileTitle">
            {!! $categoryModel->hero_title !!}
          </h1>
        </div>
        <p class="section-intro">
          {{ $categoryModel->description }}
        </p>
      </div>

      <div class="profile__layout">
        <!-- Gallery Preview Mock -->
        <section class="profile__gallery" aria-label="Portfolio images">
          <div class="pg" id="profileGallery" aria-roledescription="carousel">
            <div class="pg__stage">
              <figure class="pg__slide is-active" aria-roledescription="slide" aria-label="1 of 3">
                <img
                  class="photo"
                  src="{{ $profile && $profile->photo_url ? $profile->photo_url : asset($categoryModel->default_photo ?? 'images/creative/makeup-artist/ananya-menon.jpg') }}"
                  alt="{{ $categoryModel->name }} showcase"
                  width="700"
                  height="933"
                />
              </figure>
            </div>
          </div>
        </section>

        <!-- Dynamic Form Details -->
        <section class="profile__details" aria-labelledby="profileFormTitle">
          <h2 class="profile__heading" id="profileFormTitle">
            {{ $categoryModel->form_title }}
          </h2>
          <p class="profile__lead">
            {{ $categoryModel->form_lead }}
          </p>

          @include('profiles.partials.dynamic-form', [
            'categoryModel' => $categoryModel,
            'profile' => $profile,
            'fields' => $fields,
            'user' => $user,
            'saveRoute' => '#',
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
  <option>Bengaluru</option>
  <option>Chennai</option>
  <option>Mumbai</option>
  <option>Dubai</option>
</template>
@endsection

@push('scripts')
<script src="{{ asset('js/creative_profiles.js') }}" defer></script>
@endpush

