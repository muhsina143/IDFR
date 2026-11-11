@extends('admin.layouts.app')

@section('title', 'ASDreamStudio — Add Studio Equipment')
@section('page_title', 'Add Equipment')

@section('content')
@include('admin.asdreams.studio-rental._nav')

<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.studio-equipment.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Equipment Included</span>
    </a>
    <h2 class="module-title">Add Studio <em>Equipment</em></h2>
    <p class="module-subtitle">Add an equipment item included with studio rental bookings.</p>
  </div>
</div>

<div class="admin-card" style="max-width: 820px;">
  <form action="{{ route('admin.asdreams.studio-equipment.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    @include('admin.asdreams.studio-rental.equipment._form')

    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
      <button type="submit" class="btn-gold">Save Equipment</button>
      <a href="{{ route('admin.asdreams.studio-equipment.index') }}" class="btn-subtle">Cancel</a>
    </div>
  </form>
</div>
@endsection

