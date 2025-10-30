<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ url('/') }}">CMS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.home') ? 'active' : '' }}" href="{{ route('frontend.home') }}">Home</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.about') ? 'active' : '' }}" href="{{ route('frontend.about') }}">About Us</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.contact') ? 'active' : '' }}" href="{{ route('frontend.contact') }}">Contact</a></li>
      </ul>
      <ul class="navbar-nav">
        @if(!Auth::guard('frontend')->check())
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.login') ? 'active' : '' }}" href="{{ route('frontend.login') }}">Login</a></li>
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.register') ? 'active' : '' }}" href="{{ route('frontend.register') }}">Register</a></li>
        @else
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              {{ Auth::guard('frontend')->user()->username ?? 'Account' }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="#">Profile</a></li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="POST" action="{{ route('frontend.logout') }}" class="px-3 py-1">
                  @csrf
                  <button class="btn btn-link p-0">Logout</button>
                </form>
              </li>
            </ul>
          </li>
        @endif
      </ul>
    </div>
  </div>
</nav>


