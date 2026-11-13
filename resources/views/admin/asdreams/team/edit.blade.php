@extends('admin.layouts.app')

@section('title', 'Edit Team Member')
@section('page_title', 'Edit Team Member')

@section('content')
@php
  $member = $teamMember ?? $team;
@endphp
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.team.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Team</span>
    </a>
    <h2 class="module-title">Edit Team Member: <em>{{ $member->name }}</em></h2>
    <p class="module-subtitle">Update member type, designation, bio, display order, portrait photo, and visibility status.</p>
  </div>
</div>

<div class="admin-card" style="max-width: 780px;">
  <form action="{{ route('admin.asdreams.team.update', $member) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @include('admin.asdreams.team._form')

    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
      <button type="submit" class="btn-gold">Update Member</button>
      <a href="{{ route('admin.asdreams.team.index') }}" class="btn-subtle">Cancel</a>
    </div>
  </form>
</div>
@endsection
