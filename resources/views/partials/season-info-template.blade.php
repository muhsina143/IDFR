{{-- 
  Season Info Modal Template Partial
  Renders the exact reference design from Reference Screenshot 1:
  - Gold "IDFR Fashion Show" label
  - Heading: "Season <em>{number}</em>" (gold italic number)
  - Intro paragraph
  - 4-column fact grid (.info__facts): Dates, Venue, Designers, Models
  - Event list (.info__list) with cards (.info__item) containing:
      - .info__item-num (01, 02, 03)
      - .info__item-meta (Date & time)
      - .info__item-name (Event title)
      - .info__item-desc (Event description)
  - Action buttons (.info__actions): Reserve Your Seat, Register to Walk
--}}
@props(['season' => null, 'templateId' => null])

@php
    $s = $season ?? null;
    $tplId = $templateId ?: ('program-season-' . ($s ? $s->number : 4));
    $number = $s ? $s->number : 4;
    $title = $s && $s->title ? $s->title : ('Season ' . $number);
    $description = $s && $s->description ? $s->description : 'Three runway nights celebrating emerging designers, established names and the next generation of Indian modelling talent. Season ' . $number . ' brings couture, resort, menswear and handloom collections to one stage in ' . ($s && $s->location ? $s->location : 'Kozhikode') . '.';
    $dates = $s && $s->dates ? $s->dates : '18 – 20 Dec 2026';
    $venue = $s && $s->location ? $s->location : 'IDFR Fashion House';
    
    // Designers count: either relation count, models count or default reference labels
    $designersCount = ($s && isset($s->designers_count) && $s->designers_count > 0) 
        ? $s->designers_count . ' Labels' 
        : ((isset($designers) && is_object($designers) && method_exists($designers, 'count') && $designers->count() > 0) ? $designers->count() . ' Labels' : '24 Labels');
        
    // Models count
    $modelsCount = ($s && isset($s->models_count) && $s->models_count > 0) 
        ? $s->models_count . ' Walking' 
        : ((isset($models) && is_object($models) && method_exists($models, 'count') && $models->count() > 0) ? $models->count() . ' Walking' : '60+ Walking');

    $events = ($s && is_object($s) && isset($s->schedule_events)) ? $s->schedule_events : [
        [
            'number' => '01',
            'meta' => '18 DECEMBER · 7:00 PM',
            'title' => 'Night One — New Faces',
            'description' => 'Debut collections walked by models from the Season ' . $number . ' open auditions.',
        ],
        [
            'number' => '02',
            'meta' => '19 DECEMBER · 7:00 PM',
            'title' => 'Night Two — Heritage & Handloom',
            'description' => 'Kerala handloom, Kanjeevaram and heritage textiles reimagined for today.',
        ],
        [
            'number' => '03',
            'meta' => '20 DECEMBER · 7:30 PM',
            'title' => 'Night Three — The Grand Finale',
            'description' => 'Couture showcases, awards and the closing walk of Season ' . $number . '.',
        ],
    ];
@endphp

<template id="{{ $tplId }}">
  <p class="info__label">IDFR Fashion Show</p>
  <h2 class="info__title" id="infoModalTitle">Season <em>{{ $number }}</em></h2>
  <p class="info__intro">{{ $description }}</p>
  <dl class="info__facts">
    <div>
      <dt>Dates</dt>
      <dd>{{ $dates }}</dd>
    </div>
    <div>
      <dt>Venue</dt>
      <dd>{{ $venue }}</dd>
    </div>
    <div>
      <dt>Designers</dt>
      <dd>{{ $designersCount }}</dd>
    </div>
    <div>
      <dt>Models</dt>
      <dd>{{ $modelsCount }}</dd>
    </div>
  </dl>
  <div class="info__list">
    @foreach ($events as $event)
      <div class="info__item">
        <span class="info__item-num">{{ $event['number'] }}</span>
        <p class="info__item-meta">{{ $event['meta'] }}</p>
        <h3 class="info__item-name">{{ $event['title'] }}</h3>
        <p class="info__item-desc">{{ $event['description'] }}</p>
      </div>
    @endforeach
  </div>
  <div class="info__actions">
    <a class="btn btn--gold" href="{{ route('idfr.tickets') }}?season={{ $number }}">
      <span>Reserve Your Seat</span>
      <svg class="icon" aria-hidden="true"><use href="#i-arrow-right" /></svg>
    </a>
    <button class="btn btn--ghost" type="button" data-auth="register" data-role="fresher">
      <span>Register to Walk</span>
      <svg class="icon" aria-hidden="true"><use href="#i-arrow-right" /></svg>
    </button>
  </div>
</template>

