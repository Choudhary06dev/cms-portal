@extends('frontend.layouts.app')

@section('title', 'Register')

@section('content')
  <div class="auth-page">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="p-4 auth-card">
        <div class="auth-header">
          <div class="auth-badge">CMS</div>
          <h1 class="auth-title">Create an account</h1>
          <div class="auth-subtitle">It only takes a minute</div>
        </div>
        <div class="auth-divider"></div>
        <form method="POST" action="{{ route('frontend.register.post') }}">
          @csrf
          @if ($errors->any())
            <div class="alert alert-danger">
              {{ $errors->first() }}
            </div>
          @endif
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
          </div>
          <div class="d-flex justify-content-between align-items-center auth-links">
            <button type="submit" class="btn btn-primary">Create account</button>
            <a href="{{ route('frontend.login') }}" class="small">Have an account? Login</a>
          </div>
        </form>
        <div class="auth-footer">By continuing you agree to our terms and privacy policy.</div>
      </div>
    </div>
  </div>
  </div>
@endsection


