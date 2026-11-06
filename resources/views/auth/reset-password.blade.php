@extends('layouts.idfr')

@section('title', 'Reset Your Password | IDFR')
@section('description', 'Reset your password for your IDFR Indian Designer Fashion Runway account.')

@section('styles')
<style>
  .reset-password-page {
    min-height: 85vh;
    padding: calc(var(--nav-h, 80px) + clamp(2rem, 5vw, 4rem)) 1.25rem 5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    background: radial-gradient(circle at 50% 20%, rgba(212, 175, 55, 0.05) 0%, rgba(10, 10, 12, 1) 70%);
  }

  .reset-password-card {
    width: 100%;
    max-width: 490px;
    background: rgba(18, 18, 22, 0.96);
    border: 1px solid rgba(212, 175, 55, 0.3);
    border-radius: 8px;
    padding: clamp(2rem, 5vw, 3rem);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(12px);
    position: relative;
    overflow: hidden;
  }

  .reset-password-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--gold-gradient, linear-gradient(90deg, #b8860b, #d4af37, #f3e5ab, #d4af37, #b8860b));
  }

  .reset-password-logo {
    width: 86px;
    display: block;
    margin: 0 auto 1.5rem auto;
  }

  .reset-password-title {
    font-family: var(--font-serif, 'Playfair Display', Georgia, serif);
    font-size: clamp(1.6rem, 1.3rem + 1vw, 2.1rem);
    font-weight: 500;
    text-align: center;
    color: #ffffff;
    margin: 0 0 0.5rem 0;
  }

  .reset-password-lead {
    font-size: 0.85rem;
    color: #a1a1aa;
    text-align: center;
    line-height: 1.55;
    margin: 0 0 1.75rem 0;
  }

  .reset-success-box {
    text-align: center;
    padding: 1rem 0 0.5rem;
  }

  .reset-success-icon {
    width: 52px;
    height: 52px;
    margin: 0 auto 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(52, 168, 83, 0.15);
    border: 1px solid rgba(52, 168, 83, 0.45);
    color: #70e090;
  }

  .reset-success-icon svg {
    width: 26px;
    height: 26px;
  }

  .reset-success-msg {
    font-size: 0.95rem;
    color: #f4f4f5;
    line-height: 1.6;
    margin-bottom: 2rem;
  }
</style>
@endsection

@section('content')
<main id="main">
  <section class="reset-password-page">
    <div class="reset-password-card">
      <img
        class="reset-password-logo"
        src="{{ asset('images/logo/idfr-mark.png') }}"
        alt="IDFR Logo"
        width="474"
        height="220"
      />

      @if(session('status'))
        <div class="reset-success-box">
          <div class="reset-success-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
          </div>
          <h1 class="reset-password-title">Password Reset Complete</h1>
          <p class="reset-success-msg">
            {{ session('status') }}
          </p>
          <a href="{{ route('idfr.home', ['auth' => 'login']) }}" class="btn btn--gold btn--block">
            <span>Go to Login</span>
            <svg class="icon" aria-hidden="true">
              <use href="#i-arrow-right" />
            </svg>
          </a>
        </div>
      @else
        <h1 class="reset-password-title">Reset Your Password</h1>
        <p class="reset-password-lead">
          Please enter your new password below.
        </p>

        @if($errors->any())
          <div class="auth-alert auth-alert--error" role="alert" style="margin-bottom: 1.25rem;">
            @foreach($errors->all() as $err)
              <div>{{ $err }}</div>
            @endforeach
          </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="auth-form" novalidate>
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">

          <div class="field">
            <label for="resetEmail">Email address</label>
            <input
              type="email"
              id="resetEmail"
              name="email"
              value="{{ old('email', $email) }}"
              required
              autocomplete="email"
              readonly
              style="opacity: 0.85; background: rgba(255, 255, 255, 0.04);"
            />
          </div>

          <div class="field">
            <label for="resetPassword">New Password</label>
            <input
              type="password"
              id="resetPassword"
              name="password"
              required
              minlength="6"
              autocomplete="new-password"
              placeholder="Minimum 6 characters"
              autofocus
            />
          </div>

          <div class="field">
            <label for="resetPasswordConfirmation">Confirm New Password</label>
            <input
              type="password"
              id="resetPasswordConfirmation"
              name="password_confirmation"
              required
              minlength="6"
              autocomplete="new-password"
              placeholder="Re-enter your new password"
            />
          </div>

          <button class="btn btn--gold btn--block" type="submit" style="margin-top: 0.5rem;">
            <span>Reset Password</span>
            <svg class="icon" aria-hidden="true">
              <use href="#i-arrow-right" />
            </svg>
          </button>

          <div class="auth-form__row" style="justify-content: center; margin-top: 0.75rem;">
            <a href="{{ route('idfr.home', ['auth' => 'login']) }}" class="text-link text-link--small">
              &larr; Back to Login
            </a>
          </div>
        </form>
      @endif
    </div>
  </section>
</main>
@endsection

