@extends('layouts.master')

@section('favicon', asset('images/logo/idfr-mark.png'))

@section('preloader')
<div class="preloader" aria-hidden="true">
  <div class="preloader__inner">
    <img src="{{ asset('images/logo/idfr-mark.png') }}" alt="" width="474" height="220" />
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
