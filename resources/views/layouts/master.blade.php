<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'IDFR | Fashion Models & Fashion Shows')</title>
    <meta
      name="description"
      content="@yield('description', 'IDFR connects fashion models with creative professionals, modelling assignments and fashion show opportunities.')"
    />
    <meta
      name="keywords"
      content="@yield('keywords', 'IDFR, fashion models, modelling marketplace, fashion show, creative professionals, model portfolio, Season 4, Kerala fashion')"
    />
    <meta name="author" content="IDFR" />
    <meta name="theme-color" content="#000000" />
    @hasSection('canonical')
      <link rel="canonical" href="@yield('canonical')" />
    @endif

    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="IDFR" />
    <meta property="og:title" content="@yield('title', 'IDFR | Fashion Models & Fashion Shows')" />
    <meta
      property="og:description"
      content="@yield('description', 'Discover modelling talent, connect with creative professionals and experience the IDFR Fashion Show — Season 4.')"
    />
    <meta property="og:url" content="@yield('canonical', url()->current())" />
    <meta
      property="og:image"
      content="@yield('og_image', asset('images/logo/idfr-logo-original.png'))"
    />
    <meta property="og:locale" content="en_IN" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta
      name="twitter:title"
      content="@yield('title', 'IDFR | Fashion Models & Fashion Shows')"
    />
    <meta
      name="twitter:description"
      content="@yield('description', 'IDFR connects fashion models with creative professionals, modelling assignments and fashion show opportunities.')"
    />
    <meta
      name="twitter:image"
      content="@yield('og_image', asset('images/logo/idfr-logo-original.png'))"
    />

    <link rel="icon" type="image/png" href="@yield('favicon', asset('images/logo/idfr-mark.png'))" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&display=swap"
      rel="stylesheet"
    />
    @yield('preload')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />

    @php
      $currentUserData = null;
      if (Auth::guard('web')->check()) {
        $u = Auth::guard('web')->user();
        $currentUserData = [
          'id' => $u->id,
          'name' => $u->name,
          'email' => $u->email,
          'phone' => $u->phone,
          'role' => $u->role,
          'role_label' => $u->roleLabel(),
          'profile_url' => $u->profileUrl(),
          'profile_completion' => $u->profileCompletion(),
        ];
      }
    @endphp
    <script>
      document.documentElement.classList.add("js");
      window.__IDFR_USER__ = {!! json_encode($currentUserData) !!};
    </script>
    @yield('head_scripts')
  </head>

  <body class="@yield('body_class') {{ Auth::guard('web')->check() ? 'is-authenticated' : '' }}">
    <a class="skip-link" href="#main">Skip to content</a>

    @include('partials.svg-sprite')

    @yield('preloader')

    @yield('layout_content')

    @include('partials.auth-modals')

    @yield('extra_modals')

    @yield('page_content_templates')

    <script src="{{ asset('js/script.js') }}" defer></script>
    @stack('scripts')
  </body>
</html>
