@extends('frontend.layouts.app')

@section('title', 'Navy Complaint Management System - Login')

@push('styles')
<style>
  body {
    margin: 0;
    font-family: 'Inter', Arial, sans-serif;
    background: url('{{ asset("images/navy-background.jpg") }}') no-repeat center center/cover;
    background-attachment: fixed;
    position: relative;
    min-height: 100vh;
  }

  /* Overlay for people saluting flag */
  body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('https://images.unsplash.com/photo-1581833971358-2c8b550f87b3?w=1920&q=80') no-repeat center left/cover;
    opacity: 0.3;
    z-index: 0;
    pointer-events: none;
  }

  /* Dark overlay for better readability */
  body::after {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(0, 31, 63, 0.7) 0%, rgba(0, 51, 102, 0.75) 50%, rgba(0, 77, 153, 0.7) 100%);
    z-index: 1;
    pointer-events: none;
  }

  /* Hide default navbar and footer */
  .nav-spacer {
    display: none !important;
  }

  /* Hide default footer from layout */
  body > footer:not(.home-footer) {
    display: none !important;
  }

  main {
    padding: 0 !important;
    margin: 0 !important;
    position: relative;
    z-index: 2;
    min-height: 100vh;
  }

  /* Custom navbar for home page */
  .home-navbar {
    text-align: center;
    padding: 20px;
    font-size: 18px;
    color: #fff;
    letter-spacing: 2px;
    position: relative;
    z-index: 10;
    width: 100%;
  }

  .home-navbar a {
    margin: 0 25px;
    color: #fff;
    text-decoration: none;
    font-weight: bold;
    transition: opacity 0.3s ease;
  }

  .home-navbar a:hover {
    opacity: 0.8;
  }

  .container {
    width: 90%;
    max-width: 1200px;
    margin: 40px auto;
    display: flex;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(2px);
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    position: relative;
    z-index: 10;
  }

  .left-section {
    flex: 2;
    min-height: 550px;
    background: url('https://images.unsplash.com/photo-1581833971358-2c8b550f87b3?w=1920&q=80') no-repeat center center/cover;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }

  .left-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, rgba(0, 31, 63, 0.3), rgba(0, 51, 102, 0.2));
    z-index: 1;
  }

  .image-slider {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 15px;
    background: rgba(255, 255, 255, 0.5);
    padding: 15px;
    border-radius: 15px;
    backdrop-filter: blur(5px);
    z-index: 2;
  }

  .image-slider img {
    width: 90px;
    height: 60px;
    border-radius: 10px;
    object-fit: cover;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  }

  .right-section {
    flex: 1;
    background: #fff;
    padding: 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .logo {
    text-align: center;
    margin-bottom: 25px;
  }

  .logo img {
    width: 140px;
    height: auto;
    display: block;
    margin: 0 auto;
  }

  .logo svg {
    width: 140px;
    height: 140px;
    display: block;
    margin: 0 auto;
  }

  .heading {
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    margin-top: 15px;
    color: #003366;
    text-transform: uppercase;
    letter-spacing: 1px;
    line-height: 1.4;
  }

  .form {
    margin-top: 30px;
  }

  .form-group {
    margin-top: 25px;
  }

  .form-group label {
    display: block;
    font-weight: 600;
    color: #334155;
    margin-bottom: 8px;
    font-size: 14px;
  }

  input[type="email"],
  input[type="password"],
  input[type="text"] {
    width: 100%;
    padding: 12px;
    margin-top: 8px;
    border-radius: 8px;
    border: 1px solid #ddd;
    font-size: 16px;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
  }

  input[type="email"]:focus,
  input[type="password"]:focus,
  input[type="text"]:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
  }

  .remember {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
    margin-top: 12px;
  }

  .remember label {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #495057;
    cursor: pointer;
    margin: 0;
  }

  .remember input[type="checkbox"] {
    width: auto;
    margin: 0;
    cursor: pointer;
  }

  .remember a {
    color: #007bff;
    text-decoration: none;
  }

  .remember a:hover {
    text-decoration: underline;
  }

  .sign-btn {
    width: 100%;
    margin-top: 30px;
    padding: 12px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
  }

  .sign-btn:hover {
    background: #0056b3;
    transform: translateY(-2px);
  }

  .sign-btn:active {
    transform: translateY(0);
  }

  .home-footer {
    text-align: center;
    color: white;
    margin: 30px 0;
    font-size: 14px;
    position: relative;
    z-index: 10;
    padding: 20px 0;
    width: 100%;
  }

  .subtitle {
    text-align: center;
    color: #6c757d;
    margin-top: 10px;
    font-size: 0.95rem;
    margin-bottom: 25px;
  }

  /* Responsive */
  @media (max-width: 991.98px) {
    .container {
      flex-direction: column;
      width: 95%;
      margin: 20px auto;
    }

    .left-section {
      min-height: 300px;
    }

    .right-section {
      padding: 30px;
    }

    .navbar a {
      margin: 0 15px;
      font-size: 16px;
    }
  }

  @media (max-width: 576px) {
    .navbar {
      padding: 15px;
    }

    .navbar a {
      margin: 0 10px;
      font-size: 14px;
    }

    .right-section {
      padding: 25px;
    }

    .heading {
      font-size: 16px;
    }

    .image-slider {
      gap: 10px;
      padding: 10px;
    }

    .image-slider img {
      width: 70px;
      height: 50px;
    }
  }
</style>
@endpush

@section('content')
<div class="home-navbar">
    <a href="{{ route('frontend.home') }}">HOME</a>
    <a href="{{ route('frontend.about') }}">ABOUT US</a>
    <a href="{{ route('frontend.contact') }}">CONTACT</a>
</div>

<div class="container">
    <div class="left-section">
        <div class="image-slider">
            <img src="{{ asset('images/navy-img1.jpg') }}" alt="img1" onerror="this.src='https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=200&q=80'" />
            <img src="{{ asset('images/navy-img2.jpg') }}" alt="img2" onerror="this.src='https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=200&q=80'" />
            <img src="{{ asset('images/navy-img3.jpg') }}" alt="img3" onerror="this.src='https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=200&q=80'" />
        </div>
    </div>

    <div class="right-section">
        <div class="logo">
            <img src="{{ asset('images/navy-logo.png') }}" alt="Navy Logo" onerror="this.outerHTML='<div class=\'logo\'><svg width=\'140\' height=\'140\' viewBox=\'0 0 100 100\' fill=\'none\' xmlns=\'http://www.w3.org/2000/svg\'><circle cx=\'50\' cy=\'50\' r=\'45\' fill=\'white\' stroke=\'#003366\' stroke-width=\'2\'/><path d=\'M50 20 L60 40 L80 45 L65 60 L68 80 L50 70 L32 80 L35 60 L20 45 L40 40 Z\' fill=\'#003366\'/><circle cx=\'50\' cy=\'50\' r=\'15\' fill=\'#ffd700\'/><text x=\'50\' y=\'85\' text-anchor=\'middle\' font-size=\'8\' fill=\'#003366\' font-weight=\'bold\'>PAKISTAN</text></svg></div>';">
        </div>
        <div class="heading">NAVY COMPLAINT MANAGEMENT SYSTEM</div>
        <p class="subtitle">Nice to see you again</p>

        <form method="POST" action="{{ route('frontend.login.post') }}" class="form">
            @csrf
            @if ($errors->any())
                <div style="padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem; background: #fee; color: #c33; border: 1px solid #fcc; font-size: 14px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="form-group">
                <label>Login</label>
                <input type="text" name="username" placeholder="Email or phone number" value="{{ old('username') }}" required autofocus />
            </div>

            <div class="form-group">
                <label>Password</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" placeholder="Enter password" required style="padding-right: 40px;" />
                    <i data-feather="eye" id="togglePassword" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; width: 18px; height: 18px; color: #6c757d;"></i>
                </div>
            </div>

            <div class="remember">
                <label>
                    <input type="checkbox" name="remember" id="remember" />
                    Remember me
                </label>
                <a href="#">Forgot password?</a>
            </div>

            <button type="submit" class="sign-btn">Sign In</button>
        </form>
    </div>
</div>

<footer class="home-footer">
    © 2025 Navy All Rights Reserved
</footer>

@push('scripts')
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
@endpush
@endsection
