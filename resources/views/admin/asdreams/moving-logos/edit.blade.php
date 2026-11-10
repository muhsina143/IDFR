@extends('admin.layouts.app')

@section('title', 'ASDreamStudio — Edit Moving Client Logo')
@section('page_title', 'Edit Moving Logo')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.moving-logos.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Moving Client Logos</span>
    </a>
    <h2 class="module-title">Edit <em>{{ $movingLogo->name }}</em></h2>
    <p class="module-subtitle">Update this moving logo image, display order, or strip visibility.</p>
  </div>
</div>

<div class="admin-card" style="max-width: 780px;">
  <form action="{{ route('admin.asdreams.moving-logos.update', $movingLogo) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @include('admin.asdreams.moving-logos._form', ['movingLogo' => $movingLogo])

    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
      <button type="submit" class="btn-gold">Update Logo</button>
      <a href="{{ route('admin.asdreams.moving-logos.index') }}" class="btn-subtle">Cancel</a>
    </div>
  </form>
</div>
@endsection

