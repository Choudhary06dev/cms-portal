@extends('frontend.layouts.app')

@section('title', 'Home - NAVY COMPLAINT MANAGEMENT SYSTEM')

@section('content')
<style>
  /* Navy Theme Colors */
  :root {
    --navy-primary: #003366;
    --navy-dark: #001f3f;
    --navy-light: #004d99;
    --navy-accent: #0066cc;
    --navy-gold: #ffd700;
  }

  body {
    background: #f5f5f5;
    font-family: 'Inter', sans-serif;
  }

  /* Hero Section with Split Layout */
  .navy-hero {
    min-height: 80vh;
    display: flex;
    position: relative;
    overflow: hidden;
  }

  /* Left Side - Navy Imagery */
  .navy-hero-left {
    flex: 1.5;
    min-width: 60%;
    background: 
      linear-gradient(135deg, rgba(0, 31, 63, 0.4) 0%, rgba(0, 51, 102, 0.35) 50%, rgba(0, 77, 153, 0.4) 100%),
      url('https://e1.pxfuel.com/desktop-wallpaper/492/540/desktop-wallpaper-join-pak-navy-as-a-civilian.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    overflow: hidden;
  }

  .navy-hero-left::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
      radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
      radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.08) 0%, transparent 50%);
    pointer-events: none;
  }

  .navy-imagery-container {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 500px;
  }

  .navy-main-image {
    width: 100%;
    height: 280px;
    object-fit: cover;
    border-radius: 10px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
    margin-bottom: 1rem;
    background: 
      linear-gradient(135deg, rgba(0, 31, 63, 0.5) 0%, rgba(0, 51, 102, 0.45) 50%, rgba(0, 77, 153, 0.5) 100%),
      url('https://e1.pxfuel.com/desktop-wallpaper/492/540/desktop-wallpaper-join-pak-navy-as-a-civilian.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    position: relative;
    overflow: hidden;
  }

  .navy-main-image::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
      radial-gradient(circle at 30% 40%, rgba(255, 255, 255, 0.15) 0%, transparent 50%),
      radial-gradient(circle at 70% 60%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    pointer-events: none;
  }

  .navy-main-image-content {
    position: relative;
    z-index: 2;
    text-align: center;
    width: 100%;
    padding: 1.5rem;
  }

  .navy-flag-icon {
    font-size: 4rem;
    margin-bottom: 0.75rem;
    display: block;
    filter: drop-shadow(0 3px 6px rgba(0, 0, 0, 0.3));
  }

  .navy-main-text {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 0.4rem;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
  }

  .navy-sub-text {
    font-size: 0.85rem;
    opacity: 0.95;
    text-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
  }

  .navy-thumbnails {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
  }

  .navy-thumbnail {
    width: 100%;
    height: 90px;
    border-radius: 6px;
    object-fit: cover;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    position: relative;
    overflow: hidden;
  }

  .navy-thumbnail::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at center, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    pointer-events: none;
  }

  .navy-thumbnail:hover {
    transform: translateY(-5px);
  }

  /* Right Side - Content Card */
  .navy-hero-right {
    flex: 0.5;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
  }

  .navy-content-card {
    width: 100%;
    max-width: 450px;
    background: #ffffff;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
  }

  .navy-emblem {
    width: 60px;
    height: 60px;
    margin: 0 auto 1rem;
    background: linear-gradient(135deg, var(--navy-primary), var(--navy-light));
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(0, 51, 102, 0.3);
  }

  .navy-emblem svg {
    width: 38px;
    height: 38px;
  }

  .navy-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--navy-primary);
    text-align: center;
    margin-bottom: 0.4rem;
    line-height: 1.3;
  }

  .navy-subtitle {
    color: #6c757d;
    text-align: center;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
  }

  .navy-features {
    list-style: none;
    padding: 0;
    margin: 2rem 0;
  }

  .navy-features li {
    padding: 0.75rem 0;
    display: flex;
    align-items: center;
    color: #495057;
  }

  .navy-features li::before {
    content: '✓';
    color: var(--navy-accent);
    font-weight: bold;
    margin-right: 0.75rem;
    font-size: 1.2rem;
  }

  .navy-form-label {
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.4rem;
    display: block;
    font-size: 0.9rem;
  }

  .navy-form-control {
    width: 100%;
    padding: 0.7rem 0.9rem;
    border: 2px solid #e5e7eb;
    border-radius: 6px;
    background: #f8fafc;
    font-size: 0.9rem;
    transition: all 0.3s ease;
  }

  .navy-form-control:focus {
    outline: none;
    border-color: var(--navy-primary);
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
  }

  .navy-password-toggle {
    position: relative;
  }

  .navy-password-toggle-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #6c757d;
  }

  .navy-remember-me {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 1rem 0;
  }

  .navy-checkbox {
    width: 16px;
    height: 16px;
    cursor: pointer;
  }

  .navy-checkbox-label {
    font-size: 0.85rem;
    color: #495057;
    cursor: pointer;
    margin: 0;
  }

  .navy-forgot-password {
    color: var(--navy-primary);
    text-decoration: none;
    font-size: 0.85rem;
    transition: color 0.3s ease;
  }

  .navy-forgot-password:hover {
    color: var(--navy-dark);
    text-decoration: underline;
  }

  .navy-login-btn {
    width: 100%;
    background: var(--navy-primary);
    color: white;
    border: none;
    padding: 0.75rem 1.25rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    margin-top: 0.75rem;
  }

  .navy-login-btn:hover {
    background: var(--navy-dark);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 51, 102, 0.3);
    color: white;
  }

  .navy-login-footer {
    text-align: center;
    color: #6c757d;
    font-size: 0.8rem;
    margin-top: 1rem;
  }

  /* Stats Section */
  .navy-stats {
    background: white;
    padding: 2rem 0;
    margin-top: 0;
  }

  .navy-stat-card {
    text-align: center;
    padding: 1.5rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease;
  }

  .navy-stat-card:hover {
    transform: translateY(-3px);
  }

  .navy-stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--navy-primary);
    margin-bottom: 0.4rem;
  }

  .navy-stat-label {
    color: #6c757d;
    font-size: 0.85rem;
    font-weight: 500;
  }

  /* Responsive */
  @media (max-width: 991.98px) {
    .navy-hero {
      flex-direction: column;
      min-height: auto;
    }

    .navy-hero-left {
      min-height: 400px;
      padding: 2rem 1rem;
    }

    .navy-hero-right {
      padding: 2rem 1rem;
    }

    .navy-content-card {
      padding: 2rem;
    }

    .navy-thumbnails {
      grid-template-columns: repeat(3, 1fr);
      gap: 0.5rem;
    }

    .navy-thumbnail {
      height: 80px;
    }
  }
</style>

<!-- Hero Section with Split Layout -->
<section class="navy-hero">
  <!-- Left Side - Navy Imagery -->
  <div class="navy-hero-left">
    <div class="navy-imagery-container">
      <div class="navy-main-image">
        <div class="navy-main-image-content">
          <span class="navy-flag-icon">🇵🇰</span>
          <div class="navy-main-text">PAKISTAN NAVY</div>
          <div class="navy-sub-text">Serving with Excellence</div>
        </div>
      </div>
      <div class="navy-thumbnails">
        <div class="navy-thumbnail" style="background: linear-gradient(135deg, rgba(0, 31, 63, 0.8), rgba(0, 51, 102, 0.8));">
          <div style="font-size: 2.2rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));">🚢</div>
        </div>
        <div class="navy-thumbnail" style="background: linear-gradient(135deg, rgba(0, 51, 102, 0.8), rgba(0, 77, 153, 0.8));">
          <div style="font-size: 2.2rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));">⚓</div>
        </div>
        <div class="navy-thumbnail" style="background: linear-gradient(135deg, rgba(0, 77, 153, 0.8), rgba(0, 102, 204, 0.8));">
          <div style="font-size: 2.2rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));">🛡️</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Side - Content Card -->
  <div class="navy-hero-right">
    <div class="navy-content-card">
      <div class="navy-emblem">
        <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="50" cy="50" r="45" fill="white" stroke="#003366" stroke-width="2"/>
          <path d="M50 20 L60 40 L80 45 L65 60 L68 80 L50 70 L32 80 L35 60 L20 45 L40 40 Z" fill="#003366"/>
          <circle cx="50" cy="50" r="15" fill="#ffd700"/>
          <text x="50" y="85" text-anchor="middle" font-size="8" fill="#003366" font-weight="bold">NAVY</text>
        </svg>
      </div>
      <h1 class="navy-title">NAVY COMPLAINT MANAGEMENT SYSTEM</h1>
      <p class="navy-subtitle">Nice to see you again</p>
      
      <form method="POST" action="{{ route('frontend.login.post') }}">
        @csrf
        @if ($errors->any())
          <div class="alert alert-danger" style="padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem;">
            {{ $errors->first() }}
          </div>
        @endif
        
        <div class="mb-3">
          <label class="navy-form-label">Email or phone number</label>
          <input type="text" name="username" class="navy-form-control" value="{{ old('username') }}" required autofocus>
        </div>
        
        <div class="mb-3">
          <label class="navy-form-label">Password</label>
          <div class="navy-password-toggle">
            <input type="password" name="password" id="password" class="navy-form-control" required>
            <i data-feather="eye" class="navy-password-toggle-icon" id="togglePassword" style="width: 18px; height: 18px;"></i>
          </div>
        </div>
        
        <div class="navy-remember-me">
          <input type="checkbox" name="remember" id="remember" class="navy-checkbox">
          <label for="remember" class="navy-checkbox-label">Remember me</label>
          <a href="#" class="navy-forgot-password ms-auto">Forgot password?</a>
        </div>
        
        <button type="submit" class="navy-login-btn">Sign in</button>
      </form>
      
      <div class="navy-login-footer">
        Don't have an account? <a href="{{ route('frontend.register') }}" class="navy-forgot-password">Create account</a>
      </div>
    </div>
  </div>
</section>

<!-- Stats Section -->
<section class="navy-stats">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-3 col-6">
        <div class="navy-stat-card">
          <div class="navy-stat-number">5K+</div>
          <div class="navy-stat-label">Complaints Resolved</div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="navy-stat-card">
          <div class="navy-stat-number">98%</div>
          <div class="navy-stat-label">Satisfaction Rate</div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="navy-stat-card">
          <div class="navy-stat-number">50%</div>
          <div class="navy-stat-label">Faster Resolution</div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="navy-stat-card">
          <div class="navy-stat-number">24/7</div>
          <div class="navy-stat-label">System Availability</div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
  // Password toggle functionality
  document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    if (togglePassword && passwordInput) {
      togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Update icon
        if (typeof feather !== 'undefined') {
          feather.replace();
        }
      });
    }
    
    // Initialize feather icons
    if (typeof feather !== 'undefined') {
      feather.replace();
    }
  });
</script>

@endsection
