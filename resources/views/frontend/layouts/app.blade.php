@php
  $title = trim($__env->yieldContent('title')) ?: 'CMS';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title }}</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('css/frontend.css') }}">
    <style>
      html,body{margin:0;padding:0;font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif}
    /* Fixed navbar spacer */
    .nav-spacer{height:42px}
    @media (max-width: 991.98px){ .nav-spacer{height:80px} }
    </style>
  @stack('styles')
</head>
<body>
  @include('frontend.layouts.navbar')
    <div class="nav-spacer"></div>

    <main>
      @yield('content')
  </main>

  @include('frontend.layouts.footer')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/feather-icons"></script>
  <script>
    // Initialize feather icons
    if (typeof feather !== 'undefined') {
      feather.replace();
    }
  </script>
  @stack('scripts')
</body>
</html>


