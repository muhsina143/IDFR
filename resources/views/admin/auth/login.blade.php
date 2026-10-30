<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Portal | IDFR Indian Designer Fashion Runway</title>
  <meta name="theme-color" content="#050505" />
  <link rel="icon" type="image/png" href="{{ asset('images/logo/idfr-mark.png') }}" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet" />

  <style>
    :root {
      --bg-primary: #050505;
      --bg-card: #0B0B0B;
      --bg-input: #111111;
      --gold: #E5B83D;
      --gold-light: #F5C842;
      --gold-dark: #B38E26;
      --gold-glow: rgba(229, 184, 61, 0.15);
      --text-primary: #FFFFFF;
      --text-secondary: #A3A3A3;
      --text-muted: #666666;
      --border-subtle: #222222;
      --border-gold: rgba(229, 184, 61, 0.4);
      --font-sans: 'Jost', -apple-system, BlinkMacSystemFont, sans-serif;
      --font-serif: 'Playfair Display', Georgia, serif;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      background-color: var(--bg-primary);
      color: var(--text-primary);
      font-family: var(--font-sans);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      position: relative;
      overflow-x: hidden;
    }

    /* Ambient Background Glow */
    .ambient-glow {
      position: absolute;
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, rgba(229, 184, 61, 0.08) 0%, transparent 70%);
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      pointer-events: none;
      z-index: 0;
    }

    .login-container {
      width: 100%;
      max-width: 440px;
      background-color: var(--bg-card);
      border: 1px solid var(--border-subtle);
      border-radius: 12px;
      padding: 2.75rem 2.25rem;
      position: relative;
      z-index: 10;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6), 0 0 1px rgba(229, 184, 61, 0.3);
      backdrop-filter: blur(10px);
    }

    .login-header {
      text-align: center;
      margin-bottom: 2.25rem;
    }

    .login-logo {
      height: 48px;
      width: auto;
      object-fit: contain;
      margin-bottom: 1.25rem;
      filter: drop-shadow(0 2px 10px rgba(229, 184, 61, 0.2));
    }

    .login-badge {
      display: inline-block;
      font-size: 0.65rem;
      letter-spacing: 0.25em;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 0.5rem;
      font-weight: 600;
    }

    .login-title {
      font-family: var(--font-serif);
      font-size: 1.75rem;
      font-weight: 600;
      color: var(--text-primary);
      letter-spacing: 0.03em;
    }

    .login-subtitle {
      font-size: 0.85rem;
      color: var(--text-secondary);
      margin-top: 0.35rem;
    }

    .alert {
      padding: 0.85rem 1rem;
      border-radius: 6px;
      margin-bottom: 1.5rem;
      font-size: 0.85rem;
      line-height: 1.4;
    }

    .alert--error {
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid rgba(239, 68, 68, 0.3);
      color: #f87171;
    }

    .alert--success {
      background: rgba(34, 197, 94, 0.1);
      border: 1px solid rgba(34, 197, 94, 0.3);
      color: #4ade80;
    }

    .form-group {
      margin-bottom: 1.35rem;
    }

    .form-label {
      display: block;
      font-size: 0.8rem;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      color: var(--text-secondary);
      margin-bottom: 0.5rem;
      font-weight: 500;
    }

    .input-wrapper {
      position: relative;
    }

    .form-control {
      width: 100%;
      background-color: var(--bg-input);
      border: 1px solid var(--border-subtle);
      border-radius: 6px;
      padding: 0.8rem 1rem;
      font-family: var(--font-sans);
      font-size: 0.95rem;
      color: var(--text-primary);
      outline: none;
      transition: all 0.2s ease;
    }

    .form-control:focus {
      border-color: var(--gold);
      box-shadow: 0 0 0 3px var(--gold-glow);
    }

    .form-control.is-invalid {
      border-color: #ef4444;
    }

    .password-toggle {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--text-muted);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 4px;
      transition: color 0.2s ease;
    }

    .password-toggle:hover {
      color: var(--text-primary);
    }

    .form-options {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.75rem;
      font-size: 0.85rem;
    }

    .checkbox-label {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      color: var(--text-secondary);
      cursor: pointer;
      user-select: none;
    }

    .checkbox-label input[type="checkbox"] {
      accent-color: var(--gold);
      width: 16px;
      height: 16px;
      cursor: pointer;
    }

    .btn-submit {
      width: 100%;
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      border: none;
      border-radius: 6px;
      color: #000;
      font-family: var(--font-sans);
      font-size: 0.95rem;
      font-weight: 600;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      padding: 0.85rem 1.5rem;
      cursor: pointer;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      box-shadow: 0 4px 15px rgba(229, 184, 61, 0.25);
    }

    .btn-submit:hover {
      background: linear-gradient(135deg, var(--gold-light) 0%, var(--gold) 100%);
      box-shadow: 0 6px 20px rgba(229, 184, 61, 0.4);
      transform: translateY(-1px);
    }

    .btn-submit:active {
      transform: translateY(0);
    }

    .btn-submit[disabled] {
      opacity: 0.7;
      cursor: not-allowed;
      transform: none;
    }

    .spinner {
      width: 18px;
      height: 18px;
      border: 2px solid rgba(0, 0, 0, 0.2);
      border-top-color: #000;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
      display: none;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .login-footer {
      text-align: center;
      margin-top: 2rem;
      padding-top: 1.5rem;
      border-top: 1px solid var(--border-subtle);
    }

    .link-back {
      font-size: 0.8rem;
      color: var(--text-muted);
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      transition: color 0.2s ease;
    }

    .link-back:hover {
      color: var(--gold);
    }
  </style>
</head>
<body>
  <div class="ambient-glow"></div>

  <div class="login-container">
    <div class="login-header">
      <img src="{{ asset('images/logo/idfr-logo.png') }}" alt="IDFR Logo" class="login-logo" />
      <span class="login-badge">Executive Security Portal</span>
      <h1 class="login-title">Admin Portal</h1>
      <p class="login-subtitle">Sign in to manage the Indian Designer Fashion Runway platform</p>
    </div>

    @if(session('error'))
      <div class="alert alert--error">
        {{ session('error') }}
      </div>
    @endif

    @if(session('success'))
      <div class="alert alert--success">
        {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div class="alert alert--error">
        @foreach($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form action="{{ route('admin.login.submit') }}" method="POST" id="loginForm">
      @csrf

      <div class="form-group">
        <label for="email" class="form-label">Administrator Email</label>
        <div class="input-wrapper">
          <input
            type="email"
            name="email"
            id="email"
            class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
            value="{{ old('email') }}"
            placeholder="admin@idfr.example"
            required
            autocomplete="username"
            autofocus
          />
        </div>
      </div>

      <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <div class="input-wrapper">
          <input
            type="password"
            name="password"
            id="password"
            class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
            placeholder="••••••••••••"
            required
            autocomplete="current-password"
          />
          <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
            <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
          </button>
        </div>
      </div>

      <div class="form-options">
        <label class="checkbox-label">
          <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} />
          <span>Keep me authenticated</span>
        </label>
      </div>

      <button type="submit" class="btn-submit" id="btnSubmit">
        <span class="spinner" id="btnSpinner"></span>
        <span id="btnText">Access Portal</span>
      </button>
    </form>

    <div class="login-footer">
      <a href="{{ route('idfr.home') }}" class="link-back">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        <span>Return to IDFR Public Site</span>
      </a>
    </div>
  </div>

  <script>
    // Password visibility toggle
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    if (toggleBtn && passwordInput) {
      toggleBtn.addEventListener('click', () => {
        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
        eyeIcon.innerHTML = isPassword
          ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>'
          : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
      });
    }

    // Submit loading state
    const form = document.getElementById('loginForm');
    const btnSubmit = document.getElementById('btnSubmit');
    const btnSpinner = document.getElementById('btnSpinner');
    const btnText = document.getElementById('btnText');

    if (form && btnSubmit) {
      form.addEventListener('submit', () => {
        btnSubmit.setAttribute('disabled', 'true');
        btnSpinner.style.display = 'inline-block';
        btnText.textContent = 'Authenticating...';
      });
    }
  </script>
</body>
</html>
