@extends('admin.layouts.app')

@section('title', 'ASDreamStudio — Edit Client')
@section('page_title', 'Edit Client')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.clients.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Our Clients</span>
    </a>
    <h2 class="module-title">Edit <em>{{ $client->name }}</em></h2>
    <p class="module-subtitle">Update this client card, featured showcase photo, logo, story, order, or visibility.</p>
  </div>
</div>

<div class="admin-card" style="max-width: 880px;">
  <form action="{{ route('admin.asdreams.clients.update', $client) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @include('admin.asdreams.clients._form', ['client' => $client])

    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
      <button type="submit" class="btn-gold">Update Client</button>
      <a href="{{ route('admin.asdreams.clients.index') }}" class="btn-subtle">Cancel</a>
    </div>
  </form>
</div>
@endsection

