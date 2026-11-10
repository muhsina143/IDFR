@extends('layouts.master')

@section('body_class', 'page-asd')
@section('favicon', asset('images/asdreams/asd-logo.png'))

@section('head_scripts')
@if(request()->routeIs('landing') || (request()->routeIs('asd.home') && !request()->filled('brand') && !request()->routeIs('asd.offers.*')))
<script>
  try {
    if (!sessionStorage.getItem("idfrSkipBrandsPop") && location.pathname !== '/asdreams') {
      document.documentElement.classList.add("brands-pop-first");
    }
  } catch (_) {}
</script>
@endif
@endsection

@section('preloader')
<div class="preloader" aria-hidden="true">
  <div class="preloader__inner">
    <img src="{{ asset('images/asdreams/asd-logo.png') }}" alt="" width="666" height="470" />
    <span class="preloader__line"></span>
    <span class="preloader__text">Powered By</span>
  </div>
</div>
@endsection

@section('layout_content')
  @unless(View::hasSection('no_header') || request()->routeIs('asd.offers.*') || request()->filled('brand'))
    @include('partials.asd-header')
  @endunless
  @yield('content')
  @unless(View::hasSection('no_footer') || request()->filled('brand'))
    @include('partials.asd-footer')
  @endunless
@endsection

@section('extra_modals')
  @include('partials.asd-modals')
@endsection
