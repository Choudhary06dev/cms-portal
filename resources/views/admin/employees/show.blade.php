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
<div class="card-glass">
  <div class="row">
    <div class="col-md-4">
      <div class="text-center mb-4">
        <div class="avatar-lg mx-auto mb-3" style="width: 120px; height: 120px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 48px;">
          {{ substr($employee->name ?? 'E', 0, 1) }}
        </div>
        <h4 class="text-white">{{ $employee->name ?? 'N/A' }}</h4>
        <p class="text-light">{{ $employee->designation ?? 'N/A' }}</p>
        <span class="badge {{ $employee->status === 'active' ? 'bg-success' : 'bg-danger' }}">
          {{ ucfirst($employee->status ?? 'inactive') }}
        </span>
      </div>
    </div>
    
    <div class="col-md-8">
      <div class="row">
        <div class="col-md-6">
          <h6 class="text-white fw-bold mb-3">Basic Information</h6>
          <div class="mb-3">
            <span class="text-muted">Name:</span>
            <span class="text-white ms-2">{{ $employee->name ?? 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Employee ID:</span>
            <span class="text-white ms-2">{{ $employee->emp_id ?? 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Phone:</span>
            <span class="text-white ms-2">{{ $employee->phone ?? 'N/A' }}</span>
          </div>
        </div>
        
        <div class="col-md-6">
          <h6 class="text-white fw-bold mb-3">Work Information</h6>
          <div class="mb-3">
            <span class="text-muted">Department:</span>
            <span class="text-white ms-2">{{ $employee->department ?? 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Designation:</span>
            <span class="text-white ms-2">{{ $employee->designation ?? 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Status:</span>
            <span class="badge {{ $employee->status === 'active' ? 'bg-success' : 'bg-danger' }} ms-2">
              {{ ucfirst($employee->status ?? 'inactive') }}
            </span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Hire Date:</span>
            <span class="text-white ms-2">{{ $employee->date_of_hire ? $employee->date_of_hire->format('M d, Y') : 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">City:</span>
            <span class="text-white ms-2">{{ $employee->city ? $employee->city->name : 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Sector:</span>
            <span class="text-white ms-2">{{ $employee->sector ? $employee->sector->name : 'N/A' }}</span>
          </div>
        </div>
      </div>
      
      <div class="row mt-4">
        <div class="col-md-6">
          <h6 class="text-white fw-bold mb-3">Additional Information</h6>
          <div class="mb-3">
            <span class="text-muted">Employee ID:</span>
            <span class="text-white ms-2">{{ $employee->emp_id ?? 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Leave Quota:</span>
            <span class="text-white ms-2">{{ $employee->leave_quota ?? 'N/A' }} days</span>
          </div>
        </div>
        
        @if($employee->address)
        <div class="col-md-6">
          <h6 class="text-white fw-bold mb-3">Address</h6>
          <p class="text-light">{{ $employee->address }}</p>
        </div>
        @endif
      </div>
    </div>
  </div>
  
  <hr class="my-4">
  
  <div class="d-flex gap-2">
    <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
      <i data-feather="arrow-left" class="me-2"></i>Back to Employees
    </a>
  </div>
</div>
@endsection

@push('scripts')
<script>
  feather.replace();
</script>
@endpush