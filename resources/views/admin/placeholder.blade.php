@extends('admin.layouts.app')

@section('title', $info['title'])
@section('page_title', $info['title'])

@section('styles')
<style>
  .placeholder-container {
    background-color: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: 12px;
    padding: 4rem 2rem;
    text-align: center;
    max-width: 680px;
    margin: 2rem auto;
    position: relative;
    overflow: hidden;
  }

  .placeholder-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 300px;
    height: 100px;
    background: radial-gradient(circle, rgba(229, 184, 61, 0.15) 0%, transparent 70%);
  }

  .placeholder-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 1.5rem;
    background: rgba(229, 184, 61, 0.1);
    border: 1px solid var(--border-gold);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gold);
  }

  .placeholder-icon svg {
    width: 30px;
    height: 30px;
    stroke: currentColor;
    stroke-width: 1.8;
    fill: none;
  }

  .placeholder-badge {
    display: inline-block;
    padding: 0.35rem 0.85rem;
    background: rgba(229, 184, 61, 0.12);
    border: 1px solid rgba(229, 184, 61, 0.3);
    border-radius: 20px;
    color: var(--gold);
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 1.25rem;
  }

  .placeholder-title {
    font-family: var(--font-serif);
    font-size: 2rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.75rem;
    letter-spacing: 0.03em;
  }

  .placeholder-desc {
    color: var(--text-secondary);
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 2rem;
  }

  .btn-return {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
    color: #000;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    text-decoration: none;
    transition: all 0.2s ease;
  }

  .btn-return:hover {
    background: linear-gradient(135deg, var(--gold-light) 0%, var(--gold) 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(229, 184, 61, 0.3);
  }
</style>
@endsection

@section('content')
  <div class="placeholder-container">
    <div class="placeholder-icon">
      <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
    </div>
    
    <span class="placeholder-badge">{{ $info['badge'] }}</span>
    <h2 class="placeholder-title">{{ $info['title'] }}</h2>
    <p class="placeholder-desc">{{ $info['desc'] }}</p>

    <a href="{{ route('admin.dashboard') }}" class="btn-return">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Return to Dashboard</span>
    </a>
  </div>
@endsection
