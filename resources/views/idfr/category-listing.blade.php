@extends('layouts.idfr')

@section('title', $categoryModel->name . ' | IDFR Creative Professionals')
@section('description', $categoryModel->description ?: 'Discover talented ' . $categoryModel->name . 's on IDFR — browse portfolios, specialisations, and contact verified professionals.')
@section('canonical', route('idfr.professionals.category', $categoryModel->slug))
@section('preload')
<link rel="preload" as="image" href="{{ $categoryModel->photo_url }}" />
@endsection

@section('content')
<main id="main">
  <section class="section section--dark creative-pros creative-pros--page" id="categoryListingSection" data-spy="creative">
    <div class="container">
      <nav class="breadcrumb" aria-label="Breadcrumb" data-reveal="fade">
        <ol>
          <li><a href="{{ route('idfr.home') }}">Home</a></li>
          <li><a href="{{ route('idfr.professionals') }}">Creative Professionals</a></li>
          <li aria-current="page">{{ $categoryModel->name }}</li>
        </ol>
      </nav>

      <div class="section-head" style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1.5rem;">
        <div>
          <p class="section-label" data-reveal="up">Creative Professionals Showcase</p>
          <h1 class="section-title" id="creativeCategoryTitle" data-reveal="up" data-delay="100">
            {!! $categoryModel->hero_title !!}
          </h1>
          <p class="section-intro" data-reveal="up" data-delay="150" style="max-width: 650px;">
            {{ $categoryModel->description }}
          </p>
        </div>

        <div data-reveal="up" data-delay="200">
          <button
            class="btn btn--gold"
            type="button"
            data-auth="register"
            data-role="{{ $categoryModel->slug }}"
            style="padding: 0.85rem 1.75rem; font-size: 0.85rem;"
          >
            <span>Register as {{ $categoryModel->name }}</span>
            <svg class="icon" aria-hidden="true">
              <use href="#i-arrow-right" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Category Filter Navigation Pills -->
      <div class="creative-pros__tabs-wrap" data-reveal="up" style="margin-bottom: 2.5rem;">
        <div class="filters filters--dark creative-pros__tabs" role="tablist">
          <a
            href="{{ route('idfr.professionals') }}"
            class="filter-btn"
            style="text-decoration: none;"
          >
            All Categories
          </a>
          @foreach($categories as $cat)
            <a
              href="{{ route('idfr.professionals.category', $cat->slug) }}"
              class="filter-btn {{ $cat->slug === $categoryModel->slug ? 'is-active' : '' }}"
              style="text-decoration: none;"
            >
              {{ $cat->name }}
            </a>
          @endforeach
        </div>
      </div>

      <!-- Professionals Grid -->
      <div class="creative-pros__panel" role="tabpanel" tabindex="0">
        @if($professionals->isEmpty())
          <div style="text-align: center; padding: 4rem 1.5rem; background: rgba(255,255,255,0.02); border: 1px dashed var(--border-subtle); border-radius: 8px;">
            <p style="color: var(--gold); font-family: var(--font-serif); font-size: 1.25rem; margin-bottom: 0.5rem;">
              No Approved {{ $categoryModel->name }} Profiles Yet
            </p>
            <p style="color: var(--text-muted); font-size: 0.9rem; max-width: 500px; margin: 0 auto 1.5rem;">
              Be the first {{ strtolower($categoryModel->name) }} to publish your portfolio and work with fashion houses, runway shows, and clients across IDFR.
            </p>
            <button
              class="btn btn--gold btn--sm"
              type="button"
              data-auth="register"
              data-role="{{ $categoryModel->slug }}"
            >
              <span>Create {{ $categoryModel->name }} Profile</span>
            </button>
          </div>
        @else
          <div class="creative-pros__grid" data-category="{{ $categoryModel->slug }}"></div>
        @endif
      </div>
    </div>
  </section>
</main>
@endsection

@section('page_content_templates')
<template id="creativeProsData">
  @if(isset($professionals) && $professionals->isNotEmpty())
    @foreach($professionals as $pro)
    <article data-id="{{ $pro->slug }}" data-db-id="{{ $pro->id }}" data-pro-type="{{ $pro->category === 'fashion-designer' ? 'designer' : 'creative_professional' }}" data-category="{{ $pro->category }}" data-brand="IDFR">
      <h3>{{ $pro->name }}</h3>
      <img src="{{ $pro->photo_url }}" alt="{{ $pro->name }}" width="700" height="875" />
      <dl>
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

