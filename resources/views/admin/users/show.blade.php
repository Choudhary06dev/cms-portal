@extends('layouts.sidebar')

@section('title', 'User Details — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">User Details</h2>
      <p class="text-light">View user information and records</p>
    </div>
  </div>
</div>

<!-- USER DETAILS -->
<div class="d-flex justify-content-center">
  <div class="card-glass" style="max-width: 900px; width: 100%;">
    <div class="row">
      <div class="col-12">
      <div class="row">
        <div class="col-md-6">
          <h6 class="text-white fw-bold mb-3">Basic Information</h6>
          <div class="mb-3">
            <span class="text-muted">Username:</span>
            <span class="text-white ms-2">{{ $user->username ?? 'N/A' }}</span>
          </div>
          
          <div class="mb-3">
            <span class="text-muted">Phone:</span>
            <span class="text-white ms-2">{{ $user->phone ?? 'N/A' }}</span>
          </div>
          
         

          @if($user->city)
          <div class="mb-3">
            <span class="text-muted">City:</span>
            <span class="text-white ms-2">{{ $user->city->name ?? 'N/A' }}</span>
          </div>
          @endif
          @if($user->sector)
          <div class="mb-3">
            <span class="text-muted">Sector:</span>
            <span class="text-white ms-2">{{ $user->sector->name ?? 'N/A' }}</span>
          </div>
          @endif
        </div>
        
        <div class="col-md-6">
          <h6 class="text-white fw-bold mb-3">Account Information</h6>
          <div class="mb-3">
            <span class="text-muted">Role:</span>
            <span class="badge ms-2" style="background-color: rgba(59, 130, 246, 0.15); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.4);">{{ $user->role->role_name ?? 'No Role' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Status:</span>
            <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }} ms-2" style="color: #ffffff !important;">
              {{ ucfirst($user->status ?? 'inactive') }}
            </span>
            @if($user->status === 'inactive' && $user->updated_at)
              <span class="text-muted ms-2" style="font-size: 0.875rem;">
                (Inactive since: {{ $user->updated_at->setTimezone('Asia/Karachi')->format('M d, Y H:i:s') }})
              </span>
            @endif
          </div>
          <div class="mb-3">
            <span class="text-muted">Created:</span>
            <span class="text-white ms-2">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</span>
          </div>
        
          @if($user->country)
          <div class="mb-3">
            <span class="text-muted">Country:</span>
            <span class="text-white ms-2">{{ $user->country }}</span>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
  
    <hr class="my-4">
    
  
  </div>
</div>
@endsection

@push('scripts')
<script>
  feather.replace();
</script>
@endpush
