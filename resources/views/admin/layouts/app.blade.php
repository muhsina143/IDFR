<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Admin Dashboard') | IDFR Executive Portal</title>
  <meta name="theme-color" content="#050505" />
  <link rel="icon" type="image/png" href="{{ asset('images/logo/idfr-mark.png') }}" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="{{ asset('css/admin.css') }}" />

  <style>
    :root {
      --bg-primary: #050505;
      --bg-sidebar: #0B0B0B;
      --bg-card: #111111;
      --bg-secondary: #171717;
      --gold: #E5B83D;
      --gold-light: #F5C842;
      --gold-dark: #B38E26;
      --gold-glow: rgba(229, 184, 61, 0.15);
      --text-primary: #FFFFFF;
      --text-secondary: #A3A3A3;
      --text-muted: #666666;
      --border-subtle: #222222;
      --border-gold: rgba(229, 184, 61, 0.3);
      --font-sans: 'Jost', -apple-system, BlinkMacSystemFont, sans-serif;
      --font-serif: 'Playfair Display', Georgia, serif;
      --sidebar-w: 270px;
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
      overflow-x: hidden;
      font-size: 0.95rem;
      line-height: 1.6;
    }

    /* Sidebar */
    .admin-sidebar {
      width: var(--sidebar-w);
      background-color: var(--bg-sidebar);
      border-right: 1px solid var(--border-subtle);
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      z-index: 100;
      transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .admin-sidebar__brand {
      padding: 1.75rem 1.5rem;
      display: flex;
      align-items: center;
      gap: 1rem;
      border-bottom: 1px solid var(--border-subtle);
    }

    .admin-sidebar__logo {
      height: 38px;
      width: auto;
      object-fit: contain;
    }

    .admin-sidebar__brand-text {
      display: flex;
      flex-direction: column;
    }

    .admin-sidebar__brand-title {
      font-family: var(--font-serif);
      font-size: 1.15rem;
      letter-spacing: 0.12em;
      color: var(--gold);
      font-weight: 700;
      line-height: 1.2;
    }

    .admin-sidebar__brand-subtitle {
      font-size: 0.65rem;
      letter-spacing: 0.25em;
      text-transform: uppercase;
      color: var(--text-muted);
    }

    .admin-sidebar__nav {
      flex: 1;
      padding: 1.5rem 0.75rem;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 0.35rem;
    }

    .admin-nav__label {
      font-size: 0.7rem;
      letter-spacing: 0.15em;
      text-transform: uppercase;
      color: var(--text-muted);
      padding: 0.75rem 1rem 0.35rem;
      font-weight: 600;
    }

    .admin-nav__link {
      display: flex;
      align-items: center;
      gap: 0.85rem;
      padding: 0.75rem 1rem;
      border-radius: 6px;
      color: var(--text-secondary);
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      transition: all 0.2s ease;
      position: relative;
    }

    .admin-nav__link svg {
      width: 18px;
      height: 18px;
      stroke: currentColor;
      stroke-width: 1.8;
      fill: none;
      transition: stroke 0.2s ease;
    }

    .admin-nav__link:hover {
      background-color: var(--bg-secondary);
      color: var(--text-primary);
    }

    .admin-nav__link:hover svg {
      stroke: var(--gold);
    }

    .admin-nav__link.active {
      background: linear-gradient(90deg, rgba(229, 184, 61, 0.12), transparent);
      color: var(--gold);
      border-left: 3px solid var(--gold);
      padding-left: calc(1rem - 3px);
      font-weight: 600;
    }

    .admin-nav__link.active svg {
      stroke: var(--gold);
    }

    .admin-sidebar__footer {
      padding: 1.25rem 1.5rem;
      border-top: 1px solid var(--border-subtle);
    }

    .btn-logout {
      width: 100%;
      background: none;
      border: 1px solid var(--border-subtle);
      color: var(--text-secondary);
      padding: 0.65rem 1rem;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.65rem;
      font-family: var(--font-sans);
      font-size: 0.85rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .btn-logout:hover {
      background: rgba(220, 38, 38, 0.1);
      border-color: rgba(220, 38, 38, 0.4);
      color: #ef4444;
    }

    /* Main Container */
    .admin-layout {
      margin-left: var(--sidebar-w);
      flex: 1;
      display: flex;
      flex-direction: column;
      min-width: 0;
      min-height: 100vh;
    }

    /* Header */
    .admin-header {
      height: 70px;
      background-color: var(--bg-sidebar);
      border-bottom: 1px solid var(--border-subtle);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 2rem;
      position: sticky;
      top: 0;
      z-index: 90;
    }

    .admin-header__left {
      display: flex;
      align-items: center;
      gap: 1.25rem;
    }

    .admin-menu-toggle {
      display: none;
      background: none;
      border: 1px solid var(--border-subtle);
      color: var(--text-primary);
      padding: 0.45rem;
      border-radius: 6px;
      cursor: pointer;
    }

    .admin-header__title {
      font-family: var(--font-serif);
      font-size: 1.35rem;
      font-weight: 600;
      color: var(--text-primary);
      letter-spacing: 0.04em;
    }

    .admin-header__right {
      display: flex;
      align-items: center;
      gap: 1.25rem;
    }

    .admin-link-public {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.8rem;
      color: var(--text-muted);
      text-decoration: none;
      padding: 0.45rem 0.85rem;
      border: 1px solid var(--border-subtle);
      border-radius: 20px;
      transition: all 0.2s ease;
    }

    .admin-link-public:hover {
      border-color: var(--border-gold);
      color: var(--gold);
    }

    .admin-user-badge {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.4rem 0.75rem;
      background: var(--bg-card);
      border: 1px solid var(--border-subtle);
      border-radius: 30px;
    }

    .admin-user-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--gold-dark), var(--gold));
      color: #000;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 0.85rem;
    }

    .admin-user-meta {
      display: flex;
      flex-direction: column;
      line-height: 1.2;
    }

    .admin-user-name {
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--text-primary);
    }

    .admin-user-role {
      font-size: 0.65rem;
      color: var(--gold);
      text-transform: uppercase;
      letter-spacing: 0.1em;
    }

    /* Content Area */
    .admin-content {
      flex: 1;
      padding: 2rem;
      background: radial-gradient(circle at 100% 0%, rgba(229, 184, 61, 0.04) 0%, transparent 60%),
                  var(--bg-primary);
    }

    /* Alerts */
    .alert {
      padding: 1rem 1.25rem;
      border-radius: 8px;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 0.9rem;
    }

    .alert--success {
      background: rgba(34, 197, 94, 0.1);
      border: 1px solid rgba(34, 197, 94, 0.3);
      color: #4ade80;
    }

    .alert--error {
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid rgba(239, 68, 68, 0.3);
      color: #f87171;
    }

    .alert__close {
      background: none;
      border: none;
      color: currentColor;
      cursor: pointer;
      font-size: 1.2rem;
      line-height: 1;
      opacity: 0.7;
    }

    .alert__close:hover {
      opacity: 1;
    }

    /* Mobile Backdrop */
    .sidebar-backdrop {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.8);
      backdrop-filter: blur(4px);
      z-index: 95;
    }

    /* Responsive */
    @media (max-width: 992px) {
      .admin-sidebar {
        transform: translateX(-100%);
      }

      .admin-sidebar.open {
        transform: translateX(0);
      }

      .sidebar-backdrop.open {
        display: block;
      }

      .admin-layout {
        margin-left: 0;
      }

      .admin-menu-toggle {
        display: block;
      }

      .admin-header {
        padding: 0 1rem;
      }

      .admin-content {
        padding: 1.25rem;
      }

      .admin-link-public span {
        display: none;
      }
    }
  </style>
  @yield('styles')
</head>
<body>
  <!-- Mobile Backdrop -->
  <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

  <!-- Sidebar -->
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar__brand">
      <img src="{{ asset('images/logo/idfr-mark.png') }}" alt="IDFR Mark" class="admin-sidebar__logo" />
      <div class="admin-sidebar__brand-text">
        <span class="admin-sidebar__brand-title">IDFR</span>
        <span class="admin-sidebar__brand-subtitle">Executive Portal</span>
      </div>
    </div>

    <nav class="admin-sidebar__nav">
      <span class="admin-nav__label">Overview</span>
      <a href="{{ route('admin.dashboard') }}" class="admin-nav__link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
        <span>Dashboard</span>
      </a>

      <span class="admin-nav__label">Runway Management</span>
      <a href="{{ route('admin.seasons') }}" class="admin-nav__link {{ request()->routeIs('admin.seasons*') && !request()->routeIs('admin.seasons.images*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        <span>Seasons</span>
      </a>
      <a href="{{ route('admin.season-images.index') }}" class="admin-nav__link {{ request()->routeIs('admin.season-images*') || request()->routeIs('admin.seasons.images*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
        <span>Season Hero Images</span>
      </a>
      <a href="{{ route('admin.gallery') }}" class="admin-nav__link {{ request()->routeIs('admin.gallery*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
        <span>Gallery</span>
      </a>
      <a href="{{ route('admin.about-statistics.index') }}" class="admin-nav__link {{ request()->routeIs('admin.about-statistics*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
        <span>About Statistics</span>
      </a>

      <span class="admin-nav__label">Talent & Showcase</span>
      <a href="{{ route('admin.models') }}" class="admin-nav__link {{ request()->routeIs('admin.models*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        <span>Models</span>
      </a>
      <a href="{{ route('admin.designers') }}" class="admin-nav__link {{ request()->routeIs('admin.designers*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
        <span>Designers</span>
      </a>
      <a href="{{ route('admin.professionals') }}" class="admin-nav__link {{ request()->routeIs('admin.professionals*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
        <span>Creative Professionals</span>
      </a>

      <span class="admin-nav__label">Brand Collaborations</span>

      <a href="{{ route('admin.partners.index') }}" class="admin-nav__link {{ request()->routeIs('admin.partners*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        <span>Our Partners</span>
      </a>
      <a href="{{ route('admin.sponsors.index') }}" class="admin-nav__link {{ request()->routeIs('admin.sponsors*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
        <span>Our Sponsors</span>
      </a>
      <a href="{{ route('admin.clients.index') }}" class="admin-nav__link {{ request()->routeIs('admin.clients*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        <span>Clients &amp; Testimonials</span>
      </a>
      <a href="{{ route('admin.moving-logos.index') }}" class="admin-nav__link {{ request()->routeIs('admin.moving-logos*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
        <span>Moving Logo Strip</span>
      </a>

      <span class="admin-nav__label">Administration</span>

      <a href="{{ route('admin.profile-enquiries.index') }}" class="admin-nav__link {{ request()->routeIs('admin.profile-enquiries*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
        <span>Profile Enquiries</span>
      </a>
      <a href="{{ route('admin.enquiries') }}" class="admin-nav__link {{ request()->routeIs('admin.enquiries*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
        <span>Contact Enquiries</span>
      </a>
      <a href="{{ route('admin.registrations.index') }}" class="admin-nav__link {{ request()->routeIs('admin.registrations*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
        <span>Registrations &amp; Profiles</span>
      </a>
      <a href="{{ route('admin.settings') }}" class="admin-nav__link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
        <span>Site Settings</span>
      </a>
    </nav>

    <div class="admin-sidebar__footer">
      <form action="{{ route('admin.logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn-logout">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
          <span>Sign Out</span>
        </button>
      </form>
    </div>
  </aside>

  <!-- Layout Container -->
  <div class="admin-layout">
    <header class="admin-header">
      <div class="admin-header__left">
        <button class="admin-menu-toggle" id="menuToggle" aria-label="Toggle Navigation">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </button>
        <h1 class="admin-header__title">@yield('page_title', 'Executive Dashboard')</h1>
      </div>

      <div class="admin-header__right">
        <a href="{{ route('idfr.home') }}" target="_blank" class="admin-link-public" title="View Public Portal">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
          <span>Live Site</span>
        </a>

        @if(Auth::guard('admin')->check())
          <div class="admin-user-badge">
            <div class="admin-user-avatar">
              {{ strtoupper(substr(Auth::guard('admin')->user()->name, 0, 1)) }}
            </div>
            <div class="admin-user-meta">
              <span class="admin-user-name">{{ Auth::guard('admin')->user()->name }}</span>
              <span class="admin-user-role">Administrator</span>
            </div>
          </div>
        @endif
      </div>
    </header>

    <main class="admin-content">
      @if(session('success'))
        <div class="alert alert--success">
          <span>{{ session('success') }}</span>
          <button class="alert__close" onclick="this.parentElement.remove()">×</button>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert--error">
          <span>{{ session('error') }}</span>
          <button class="alert__close" onclick="this.parentElement.remove()">×</button>
        </div>
      @endif

      @yield('content')
    </main>
  </div>

  <script>
    const menuToggle = document.getElementById('menuToggle');
    const adminSidebar = document.getElementById('adminSidebar');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');

    if (menuToggle && adminSidebar && sidebarBackdrop) {
      menuToggle.addEventListener('click', () => {
        adminSidebar.classList.toggle('open');
        sidebarBackdrop.classList.toggle('open');
      });

      sidebarBackdrop.addEventListener('click', () => {
        adminSidebar.classList.remove('open');
        sidebarBackdrop.classList.remove('open');
      });
    }
  </script>
  @yield('scripts')
</body>
</html>
