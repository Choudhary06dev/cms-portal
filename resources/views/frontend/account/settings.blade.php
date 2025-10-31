@extends('frontend.layouts.app')

@section('title', 'Settings')

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
          <li><a href="{{ route('frontend.profile') }}" class="text-decoration-none">View Profile</a></li>
        </ul>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="p-4 account-card mb-4">
        <div class="account-section-title">Preferences</div>
        <form method="POST" action="#">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Theme</label>
              <select class="form-select" disabled>
                <option selected>{{ $user->theme ?? 'auto' }}</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <input type="text" class="form-control" value="{{ $user->status ?? 'active' }}" disabled>
            </div>
          </div>
        </form>
      </div>
      <div class="p-4 account-card">
        <div class="account-section-title">Notifications</div>
        <p class="account-meta mb-0">Notification preferences can be added here later.</p>
      </div>
    </div>
  </div>
  </div>
@endsection


