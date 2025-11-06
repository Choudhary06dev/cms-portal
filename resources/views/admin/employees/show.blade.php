@extends('layouts.sidebar')

@section('title', 'Employee Details — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Employee Details</h2>
      <p class="text-light">View employee information and records</p>
    </div>
  </div>
</div>

<!-- EMPLOYEE DETAILS -->
<div class="d-flex justify-content-center">
  <div class="card-glass" style="max-width: 900px; width: 100%;">
    <div class="row">
      <div class="col-12">
        <div class="row">
          <div class="col-md-6">
          <h6 class="text-white fw-bold mb-3">Basic Information</h6>
          <div class="mb-3">
            <span class="text-muted">Name:</span>
            <span class="text-white ms-2">{{ $employee->name ?? 'N/A' }}</span>
          </div>
          
          <div class="mb-3">
            <span class="text-muted">Email:</span>
            <span class="text-white ms-2">{{ $employee->email ?? 'N/A' }}</span>
          </div>
          
          <div class="mb-3">
            <span class="text-muted">Phone:</span>
            <span class="text-white ms-2">{{ $employee->phone ?? 'N/A' }}</span>
          </div>
          
          <h6 class="text-white fw-bold mb-3 mt-4">Additional Information</h6>
          
          <div class="mb-3">
            <span class="text-muted">Sector:</span>
            <span class="text-white ms-2">{{ $employee->sector ? $employee->sector->name : 'N/A' }}</span>
          </div>
          
          <div class="mb-3">
            <span class="text-muted">City:</span>
            <span class="text-white ms-2">{{ $employee->city ? $employee->city->name : 'N/A' }}</span>
          </div>
          
          <div class="mb-3">
            <span class="text-muted">Leave Quota:</span>
            <span class="text-white ms-2">{{ $employee->leave_quota ?? 'N/A' }} days</span>
          </div>
          
          @if($employee->address)
          <div class="mb-3 mt-4">
            <h6 class="text-white fw-bold mb-3">Address</h6>
            <p class="text-light">{{ $employee->address }}</p>
          </div>
          @endif
        </div>
        
        <div class="col-md-6">
          <h6 class="text-white fw-bold mb-3">Work Information</h6>
          <div class="mb-3">
            <span class="text-muted">Department:</span>
            <span class="text-white ms-2">{{ $employee->department ?? 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Designation:</span>
            <span class="text-white ms-2">{{ $employee->designation ?? '' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Status:</span>
            <span class="badge {{ $employee->status === 'active' ? 'bg-success' : 'bg-danger' }} ms-2" style="color: #ffffff !important;">
              {{ ucfirst($employee->status ?? 'inactive') }}
            </span>
            @if($employee->status === 'inactive' && $employee->updated_at)
              <span class="text-muted ms-2" style="font-size: 0.875rem;">
                (Inactive since: {{ $employee->updated_at->setTimezone('Asia/Karachi')->format('M d, Y H:i:s') }})
              </span>
            @endif
          </div>
          <div class="mb-3">
            <span class="text-muted">Hire Date:</span>
            <span class="text-white ms-2">{{ $employee->date_of_hire ? $employee->date_of_hire->format('M d, Y') : 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">City:</span>
            <span class="text-white ms-2">{{ $employee->city ? $employee->city->name : 'N/A' }}</span>
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