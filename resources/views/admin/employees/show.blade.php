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
          <h6 class="text-white fw-bold mb-3" style="font-size: 1rem; font-weight: 700;">Basic Information</h6>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Name:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $employee->name ?? 'N/A' }}</span>
          </div>
          
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Email:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $employee->email ?? 'N/A' }}</span>
          </div>
          
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Phone:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $employee->phone ?? 'N/A' }}</span>
          </div>
          
          <h6 class="text-white fw-bold mb-3 mt-4" style="font-size: 1rem; font-weight: 700;">Additional Information</h6>
          
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Sector:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $employee->sector ? $employee->sector->name : 'N/A' }}</span>
          </div>
          
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">City:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $employee->city ? $employee->city->name : 'N/A' }}</span>
          </div>
          
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Leave Quota:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $employee->leave_quota ?? 'N/A' }} days</span>
          </div>
          
          @if($employee->address)
          <div class="mb-3 mt-4">
            <h6 class="text-white fw-bold mb-3" style="font-size: 1rem; font-weight: 700;">Address</h6>
            <p class="text-light" style="font-size: 0.875rem;">{{ $employee->address }}</p>
          </div>
          @endif
        </div>
        
        <div class="col-md-6">
          <h6 class="text-white fw-bold mb-3" style="font-size: 1rem; font-weight: 700;">Work Information</h6>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Department:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $employee->department ?? 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Designation:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $employee->designation ?? '' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Status:</span>
            <span class="badge {{ $employee->status === 'active' ? 'bg-success' : 'bg-danger' }} ms-2" style="color: #ffffff !important; font-size: 0.875rem;">
              {{ ucfirst($employee->status ?? 'inactive') }}
            </span>
            @if($employee->status === 'inactive' && $employee->updated_at)
              <span class="text-muted ms-2" style="font-size: 0.8rem;">
                (Inactive since: {{ $employee->updated_at->setTimezone('Asia/Karachi')->format('M d, Y H:i:s') }})
              </span>
            @endif
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Hire Date:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $employee->date_of_hire ? $employee->date_of_hire->format('M d, Y') : 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">City:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $employee->city ? $employee->city->name : 'N/A' }}</span>
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