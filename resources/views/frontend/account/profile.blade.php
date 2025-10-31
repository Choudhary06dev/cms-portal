@extends('frontend.layouts.app')
@section('title', 'Profile')
@section('content')
  <div class="account-page">
  <div class="row g-4">
    <div class="col-lg-4">
      <div class="p-4 account-card account-sidebar">
        <div class="account-header">
          <div class="account-avatar">{{ strtoupper(substr($user->username,0,2)) }}</div>
          <div>
            <div class="h5 m-0">{{ $user->username }}</div>
            <div class="account-meta">{{ $user->email }}</div>
          </div>
        </div>
        <ul class="list-clean">
          <li><strong>Status:</strong> {{ ucfirst($user->status ?? 'active') }}</li>
          <li><strong>Theme:</strong> {{ $user->theme ?? 'auto' }}</li>
          <li><strong>Member since:</strong> {{ $user->created_at?->format('M d, Y') }}</li>
        </ul>
        <div class="mt-3">
          <a href="{{ route('frontend.settings') }}" class="btn btn-primary w-100">Open Settings</a>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="p-4 account-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="account-section-title">Basic Information</div>
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" value="{{ $user->username }}" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="{{ $user->email }}" disabled>
          </div>
        </div>
      </div>
      <div class="p-4 account-card">
        <div class="account-section-title">Security</div>
        <p class="account-meta mb-3">Keep your account secure. Password change can be added here later.</p>
        <div class="d-flex gap-2">
          <a href="{{ route('frontend.settings') }}" class="btn btn-primary">Manage Settings</a>
          <form method="POST" action="{{ route('frontend.logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-danger">Logout</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  </div>
@endsection


