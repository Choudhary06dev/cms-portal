@extends('frontend.layouts.app')

@section('title', 'Login - NAVY COMPLAINT MANAGEMENT SYSTEM')

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

  /* Login Page with Split Layout */
  .navy-login-page {
    min-height: calc(100vh - 80px);
    display: flex;
    position: relative;
    overflow: hidden;
  }

  /* Left Side - Navy Imagery */
  .navy-login-left {
    flex: 1;
    background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy-primary) 50%, var(--navy-light) 100%);
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    overflow: hidden;
  }

  .navy-login-left::before {
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

  .navy-login-imagery {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 600px;
  }

  .navy-login-main-image {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    margin-bottom: 1.5rem;
    background: 
      linear-gradient(135deg, rgba(0, 31, 63, 0.9) 0%, rgba(0, 51, 102, 0.85) 50%, rgba(0, 77, 153, 0.9) 100%),
      url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800"><defs><pattern id="waves" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M0 50 Q25 30 50 50 T100 50" stroke="rgba(255,255,255,0.1)" fill="none" stroke-width="2"/></pattern></defs><rect width="1200" height="800" fill="url(%23waves)"/></svg>');
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    position: relative;
    overflow: hidden;
  }

  .navy-login-main-image::before {
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

  .navy-login-main-image-content {
    position: relative;
    z-index: 2;
    text-align: center;
    width: 100%;
    padding: 2rem;
  }

  .navy-login-flag-icon {
    font-size: 6rem;
    margin-bottom: 1rem;
    display: block;
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
  }

  .navy-login-main-text {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
  }

  .navy-login-sub-text {
    font-size: 1rem;
    opacity: 0.95;
    text-shadow: 0 1px 5px rgba(0, 0, 0, 0.3);
  }

  .navy-login-thumbnails {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
  }

  .navy-login-thumbnail {
    width: 100%;
    height: 120px;
    border-radius: 8px;
    object-fit: cover;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    position: relative;
    overflow: hidden;
  }

  .navy-login-thumbnail::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at center, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    pointer-events: none;
  }

  .navy-login-thumbnail:hover {
    transform: translateY(-5px);
  }

  /* Right Side - Login Form */
  .navy-login-right {
    flex: 1;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem;
  }

  .navy-login-card {
    width: 100%;
    max-width: 450px;
    background: #ffffff;
    border-radius: 16px;
    padding: 3rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
  }

  .navy-login-emblem {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, var(--navy-primary), var(--navy-light));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 5px 20px rgba(0, 51, 102, 0.3);
  }

  .navy-login-emblem svg {
    width: 50px;
    height: 50px;
  }

  .navy-login-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--navy-primary);
    text-align: center;
    margin-bottom: 0.5rem;
  }

  .navy-login-subtitle {
    color: #6c757d;
    text-align: center;
    margin-bottom: 2rem;
    font-size: 1rem;
  }

  .navy-form-label {
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.5rem;
    display: block;
  }

  .navy-form-control {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    background: #f8fafc;
    font-size: 1rem;
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
    margin: 1.5rem 0;
  }

  .navy-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
  }

  .navy-checkbox-label {
    font-size: 0.9rem;
    color: #495057;
    cursor: pointer;
    margin: 0;
  }

  .navy-forgot-password {
    color: var(--navy-primary);
    text-decoration: none;
    font-size: 0.9rem;
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
    padding: 0.875rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    margin-top: 1rem;
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
    font-size: 0.85rem;
    margin-top: 1.5rem;
  }

  /* Responsive */
  @media (max-width: 991.98px) {
    .navy-login-page {
      flex-direction: column;
      min-height: auto;
    }

    .navy-login-left {
      min-height: 300px;
      padding: 2rem 1rem;
    }

    .navy-login-right {
      padding: 2rem 1rem;
    }

    .navy-login-card {
      padding: 2rem;
    }

    .navy-login-thumbnails {
      grid-template-columns: repeat(3, 1fr);
      gap: 0.5rem;
    }

    .navy-login-thumbnail {
      height: 80px;
    }
  }
</style>

<!-- Login Page with Split Layout -->
<div class="navy-login-page">
  <!-- Left Side - Navy Imagery -->
  <div class="navy-login-left">
    <div class="navy-login-imagery">
      <div class="navy-login-main-image">
        <div class="navy-login-main-image-content">
          <span class="navy-login-flag-icon">🇵🇰</span>
          <div class="navy-login-main-text">PAKISTAN NAVY</div>
          <div class="navy-login-sub-text">Serving with Excellence</div>
        </div>
      </div>
      <div class="navy-login-thumbnails">
        <div class="navy-login-thumbnail" style="background: linear-gradient(135deg, rgba(0, 31, 63, 0.8), rgba(0, 51, 102, 0.8));">
          <div style="font-size: 3rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));">🚢</div>
        </div>
        <div class="navy-login-thumbnail" style="background: linear-gradient(135deg, rgba(0, 51, 102, 0.8), rgba(0, 77, 153, 0.8));">
          <div style="font-size: 3rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));">⚓</div>
        </div>
        <div class="navy-login-thumbnail" style="background: linear-gradient(135deg, rgba(0, 77, 153, 0.8), rgba(0, 102, 204, 0.8));">
          <div style="font-size: 3rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));">🛡️</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Side - Login Form -->
  <div class="navy-login-right">
    <div class="navy-login-card">
      <div class="navy-login-emblem">
        <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="50" cy="50" r="45" fill="white" stroke="#003366" stroke-width="2"/>
          <path d="M50 20 L60 40 L80 45 L65 60 L68 80 L50 70 L32 80 L35 60 L20 45 L40 40 Z" fill="#003366"/>
          <circle cx="50" cy="50" r="15" fill="#ffd700"/>
          <text x="50" y="85" text-anchor="middle" font-size="8" fill="#003366" font-weight="bold">NAVY</text>
        </svg>
      </div>
      <h1 class="navy-login-title">NAVY COMPLAINT MANAGEMENT SYSTEM</h1>
      <p class="navy-login-subtitle">Nice to see you again</p>
      
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
  </div>

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
