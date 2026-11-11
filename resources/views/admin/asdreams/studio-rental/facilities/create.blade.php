@extends('admin.layouts.app')

@section('title', 'ASDreamStudio — Add Studio Facility')
@section('page_title', 'Add Facility')

@section('content')
@include('admin.asdreams.studio-rental._nav')

<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.studio-facilities.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Facilities Provided</span>
    </a>
    <h2 class="module-title">Add Studio <em>Facility</em></h2>
    <p class="module-subtitle">Create a new facility item displayed inside the Book Studio for Rent panel.</p>
  </div>
</div>

<div class="admin-card" style="max-width: 780px;">
  <form action="{{ route('admin.asdreams.studio-facilities.store') }}" method="POST">
    @csrf

    @include('admin.asdreams.studio-rental.facilities._form')

    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
      <button type="submit" class="btn-gold">Save Facility</button>
      <a href="{{ route('admin.asdreams.studio-facilities.index') }}" class="btn-subtle">Cancel</a>
    </div>
  </form>
</div>
@endsection

