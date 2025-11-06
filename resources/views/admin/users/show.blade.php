@extends('layouts.sidebar')

@section('title', 'User Details — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">User Details</h2>
      <p class="text-light">View user information and details</p>
    </div>
      <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-{{ $user->status === 'active' ? 'danger' : 'success' }}">
          <i data-feather="{{ $user->status === 'active' ? 'user-x' : 'user-check' }}" class="me-2"></i>
          {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
        </button>
      </form>
    </div>
  </div>
</div>

<!-- USER DETAILS -->
<div class="card-glass">
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <div class="mb-4">
          <h6 class="text-muted fw-bold">Basic Information</h6>
          <div class="mb-2">
            <span class="text-muted">Username:</span>
            <span class="text-white">{{ $user->username }}</span>
          </div>
          <div class="mb-2">
            <span class="text-muted">Full Name:</span>
            <span class="text-white">{{ $user->full_name ?? 'Not provided' }}</span>
          </div>
          <div class="mb-2">
            <span class="text-muted">Email:</span>
            <span class="text-white">{{ $user->email ?? 'Not provided' }}</span>
          </div>
          <div class="mb-2">
            <span class="text-muted">Phone:</span>
            <span class="text-white">{{ $user->phone ?? 'Not provided' }}</span>
          </div>
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="mb-4">
          <h6 class="text-muted fw-bold">Account Information</h6>
          <div class="mb-2">
            <span class="text-muted">Role:</span>
            <span class="badge bg-primary">{{ $user->role->role_name ?? 'No Role' }}</span>
          </div>
          <div class="mb-2">
            <span class="text-muted">Status:</span>
            <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }}" style="color: #ffffff !important;">{{ ucfirst($user->status) }}</span>
          </div>
          <div class="mb-2">
            <span class="text-muted">Created:</span>
            <span class="text-white">{{ $user->created_at->format('M d, Y') }}</span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Additional Information -->
    @if($user->address || $user->city || $user->country)
    <div class="row">
      <div class="col-12">
        <h6 class="text-muted fw-bold">Address Information</h6>
        @if($user->address)
        <div class="mb-2">
          <span class="text-muted">Address:</span>
          <span class="text-white">{{ $user->address }}</span>
        </div>
        @endif
        @if($user->city)
        <div class="mb-2">
          <span class="text-muted">City:</span>
          <span class="text-white">{{ $user->city }}</span>
        </div>
        @endif
        @if($user->country)
        <div class="mb-2">
          <span class="text-muted">Country:</span>
          <span class="text-white">{{ $user->country }}</span>
        </div>
        @endif
      </div>
    </div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
  feather.replace();
</script>
@endpush