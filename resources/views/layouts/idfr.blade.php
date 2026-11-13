@extends('layouts.master')

@section('favicon', asset('images/asdreams/asd-logo.png'))

@section('preloader')
<div class="preloader" aria-hidden="true">
  <div class="preloader__inner">
    @include('partials.splash-logo')
    <span class="preloader__line"></span>
    <span class="preloader__text">Fashion Show — {{ $currentSeason ? $currentSeason->title : 'Season 4' }}</span>
  </div>
</div>
@endsection

@section('layout_content')
  @include('partials.idfr-header')
  @yield('content')
  @include('partials.idfr-footer')
@endsection