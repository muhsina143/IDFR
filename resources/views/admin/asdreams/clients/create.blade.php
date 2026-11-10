@extends('admin.layouts.app')

@section('title', 'ASDreamStudio — Add Client')
@section('page_title', 'Add New Client')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.clients.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Our Clients</span>
    </a>
    <h2 class="module-title">Add New <em>Client</em></h2>
    <p class="module-subtitle">Create a new client card and featured story for the ASDreams Our Clients showcase.</p>
  </div>
</div>

<div class="admin-card" style="max-width: 880px;">
  <form action="{{ route('admin.asdreams.clients.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    @include('admin.asdreams.clients._form')

    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
      <button type="submit" class="btn-gold">Save Client</button>
      <a href="{{ route('admin.asdreams.clients.index') }}" class="btn-subtle">Cancel</a>
    </div>
  </form>
</div>
@endsection

