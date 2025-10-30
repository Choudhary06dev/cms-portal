@extends('frontend.layouts.app')

@section('title', 'Login')

@section('content')
  <div class="auth-page">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="p-4 auth-card">
        <div class="auth-header">
          <div class="auth-badge">CMS</div>
          <h1 class="auth-title">Welcome back</h1>
          <div class="auth-subtitle">Sign in to continue</div>
        </div>
        <div class="auth-divider"></div>
        <form method="POST" action="{{ route('frontend.login.post') }}">
          @csrf
          @if ($errors->any())
            <div class="alert alert-danger">
              {{ $errors->first() }}
            </div>
          @endif
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3 form-check">
            <input type="checkbox" name="remember" id="remember" class="form-check-input">
            <label for="remember" class="form-check-label small">Remember me</label>
          </div>
          <div class="d-flex justify-content-between align-items-center auth-links">
            <button type="submit" class="btn btn-primary">Login</button>
            <a href="{{ route('frontend.register') }}" class="small">Create account</a>
          </div>
        </form>
        <div class="auth-footer">Having issues? Contact support from the Contact page.</div>
      </div>
    </div>
  </div>
  </div>
@endsection


