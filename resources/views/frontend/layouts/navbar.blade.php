<nav class="navbar navbar-expand-lg navbar-dark" style="background: transparent !important; background-color: transparent !important; box-shadow: none !important;">
  <div class="container-fluid px-4">
    <!-- Logo & Brand -->
    <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
      <div class="logo-wrapper me-2">
        <svg width="40" height="40" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="50" cy="50" r="45" fill="white" stroke="#003366" stroke-width="3"/>
          <path d="M50 20 L60 40 L80 45 L65 60 L68 80 L50 70 L32 80 L35 60 L20 45 L40 40 Z" fill="#003366"/>
          <circle cx="50" cy="50" r="15" fill="#ffd700"/>
          <text x="50" y="85" text-anchor="middle" font-size="10" fill="#003366" font-weight="bold">PAKISTAN</text>
        </svg>
      </div>
      <span class="fw-bold fs-5 text-white ms-2">MES</span>
    </a>

    <!-- Mobile Toggle -->
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- Main Navigation -->
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link px-3 text-white {{ request()->routeIs('frontend.home') ? 'active' : '' }}" href="{{ route('frontend.home') }}" style="font-weight: 500;">
            HOME
          </a>
        </li>
        @if(Auth::guard('frontend')->check())
        <li class="nav-item">
          <a class="nav-link px-3 text-white {{ request()->routeIs('frontend.dashboard') ? 'active' : '' }}" href="{{ route('frontend.dashboard') }}" style="font-weight: 500;">
            DASHBOARD
          </a>
        </li>
        @endif
      </ul>

      <!-- Auth Navigation -->
      <ul class="navbar-nav ms-3">
        @if(Auth::guard('frontend')->check())
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center px-3 text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-weight: 500;">
              <div class="user-avatar me-2">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                  <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                </svg>
              </div>
              <span>{{ Auth::guard('frontend')->user()->username ?? 'Account' }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userDropdown">
              <li>
                <a class="dropdown-item py-2" href="{{ route('frontend.profile') }}">
                  <svg width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                  </svg>
                  Profile
                </a>
              </li>
              <li>
                <a class="dropdown-item py-2" href="{{ route('frontend.settings') }}">
                  <svg width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                    <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                  </svg>
                  Settings
                </a>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="POST" action="{{ route('frontend.logout') }}" class="px-3 py-1">
                  @csrf
                  <button class="btn btn-danger w-100" type="submit">
                    <svg width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                      <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                    </svg>
                    Logout
                  </button>
                </form>
              </li>
            </ul>
          </li>
        @endif
      </ul>
    </div>
  </div>
</nav>

<style>
  .navbar,
  .navbar.navbar-dark,
  .navbar.navbar-expand-lg {
    transition: all 0.3s ease;
    z-index: 1030;
    margin: 0 !important;
    padding: 0.75rem 0;
    background-image: url('https://img.freepik.com/premium-photo/dark-blue-ocean-surface-seen-from-underwater_629685-6504.jpg') !important;
    background-size: cover !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
    background-color: transparent !important;
    border-bottom: none !important;
    position: absolute !important;
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
  }
  
  .navbar-brand {
    transition: transform 0.3s ease;
    color: #ffffff !important;
  }
  
  .navbar-brand span {
    color: #ffffff !important;
  }
  
  .navbar-brand:hover {
    transform: scale(1.05);
  }
  
  .logo-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .nav-link {
    position: relative;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    color: #ffffff !important;
  }
  
  .nav-link::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    bottom: 0;
    left: 50%;
    background-color: #ffd700;
    transition: all 0.3s ease;
    transform: translateX(-50%);
  }
  
  .nav-link:hover::after,
  .nav-link.active::after {
    width: 80%;
  }
  
  .nav-link.active {
    color: #ffd700 !important;
  }
  
  .nav-link:hover {
    color: #ffd700 !important;
  }
  
  .dropdown-menu {
    margin-top: 0.5rem;
    animation: fadeIn 0.3s ease;
  }
  
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .dropdown-item {
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
  }
  
  .dropdown-item:hover {
    background-color: #f8f9fa;
    padding-left: 1.5rem;
  }
  
  .user-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: linear-gradient(135deg, #003366 0%, #0066cc 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
  }
  
  .btn-light {
    background: white;
    color: #001f3f;
    border: none;
    font-weight: 600;
  }
  
  .btn-light:hover {
    background: #f8f9fa;
    color: #001f3f;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  }
  
  @media (max-width: 991px) {
    .nav-link::after {
      display: none;
    }
    
    .navbar-nav {
      padding-top: 1rem;
    }
    
    .nav-item {
      margin-bottom: 0.5rem;
    }
  }
</style>
