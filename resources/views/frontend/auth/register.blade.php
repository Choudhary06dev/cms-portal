@extends('frontend.layouts.app')

@section('title', 'Register')

@section('content')
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="p-4 bg-white rounded shadow-sm">
        <h1 class="mb-3">Register</h1>
        <form method="POST" action="{{ route('frontend.register.post') }}">
          @csrf
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
          <div class="d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-primary">Create account</button>
            <a href="{{ route('frontend.login') }}" class="small">Have an account? Login</a>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection


