@extends('admin.layouts.app')

@section('title', 'ASDreamStudio — Add Moving Client Logo')
@section('page_title', 'Add Moving Logo')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.moving-logos.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Moving Client Logos</span>
    </a>
    <h2 class="module-title">Add Moving <em>Client Logo</em></h2>
    <p class="module-subtitle">Upload a logo image to appear in the continuously scrolling marquee strip on ASDreams.</p>
  </div>
</div>

<div class="admin-card" style="max-width: 780px;">
  <form action="{{ route('admin.asdreams.moving-logos.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    @include('admin.asdreams.moving-logos._form')

    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
      <button type="submit" class="btn-gold">Save Logo</button>
      <a href="{{ route('admin.asdreams.moving-logos.index') }}" class="btn-subtle">Cancel</a>
    </div>
  </form>
</div>
@endsection

