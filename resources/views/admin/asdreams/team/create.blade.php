@extends('admin.layouts.app')

@section('title', 'Add New Team Member')
@section('page_title', 'Create Team Member')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.team.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Team</span>
    </a>
    <h2 class="module-title">Add Team <em>Member</em></h2>
    <p class="module-subtitle">Add a Founder, Co-Founder, photographer, cinematographer, or creative artist to the studio roster.</p>
  </div>
</div>

<div class="admin-card" style="max-width: 780px;">
  <form action="{{ route('admin.asdreams.team.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    @include('admin.asdreams.team._form')

    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
      <button type="submit" class="btn-gold">Add Team Member</button>
      <a href="{{ route('admin.asdreams.team.index') }}" class="btn-subtle">Cancel</a>
    </div>
  </form>
</div>
@endsection
