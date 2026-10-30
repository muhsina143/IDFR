@extends('layouts.master')

@section('body_class', 'page-asd')
@section('favicon', asset('images/asdreams/asd-logo.png'))

@section('head_scripts')
<script>
  try {
    if (!sessionStorage.getItem("idfrSkipBrandsPop")) {
      document.documentElement.classList.add("brands-pop-first");
    }
  } catch (_) {}
</script>
@endsection

@section('preloader')
<div class="preloader" aria-hidden="true">
  <div class="preloader__inner">
    <img src="{{ asset('images/asdreams/asd-logo.png') }}" alt="" width="666" height="470" />
    <span class="preloader__line"></span>
    <span class="preloader__text">Photography &amp; Videography</span>
  </div>
</div>
@endsection

@section('layout_content')
  @include('partials.asd-header')
  @yield('content')
  @include('partials.asd-footer')
@endsection

@section('extra_modals')
  @include('partials.asd-modals')
@endsection
