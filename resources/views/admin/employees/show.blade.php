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
    <div class="d-flex gap-2">
      <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
        <i data-feather="arrow-left" class="me-2"></i>Back
      </a>
     
    </div>
  </div>
</div>

<!-- EMPLOYEE PROFILE CARD -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card-glass">
      <div class="row align-items-center">
        <div class="col-md-3 text-center mb-3 mb-md-0">
          <div class="employee-avatar mx-auto" style="width: 120px; height: 120px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 3rem; font-weight: bold; box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);">
            {{ strtoupper(substr($employee->name ?? 'E', 0, 1)) }}
          </div>
        </div>
        <div class="col-md-9">
          <h3 class="text-white mb-2" style="font-size: 1.75rem; font-weight: 700;">{{ $employee->name ?? 'N/A' }}</h3>
          <div class="d-flex flex-wrap gap-3 mb-3">
            <span class="badge bg-primary" style="font-size: 0.9rem; padding: 8px 16px;">
              <i data-feather="briefcase" class="me-1" style="width: 14px; height: 14px;"></i>
              {{ $employee->designation ?? 'N/A' }}
            </span>
            <span class="badge {{ $employee->status === 'active' ? 'bg-success' : 'bg-danger' }}" style="font-size: 0.9rem; padding: 8px 16px;">
              <i data-feather="{{ $employee->status === 'active' ? 'check-circle' : 'x-circle' }}" class="me-1" style="width: 14px; height: 14px;"></i>
              {{ ucfirst($employee->status ?? 'inactive') }}
            </span>
            @if($employee->department)
            <span class="badge bg-info" style="font-size: 0.9rem; padding: 8px 16px;">
              <i data-feather="building" class="me-1" style="width: 14px; height: 14px;"></i>
              {{ $employee->department }}
            </span>
            @endif
          </div>
          @if($employee->phone)
          <div class="text-light">
            <i data-feather="phone" class="me-2" style="width: 16px; height: 16px;"></i>
            <span>{{ $employee->phone }}</span>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<!-- EMPLOYEE DETAILS -->
<div class="row">
  <!-- Basic Information -->
  <div class="col-md-6 mb-4">
    <div class="card-glass h-100">
      <div class="d-flex align-items-center mb-4" style="border-bottom: 2px solid rgba(59, 130, 246, 0.2); padding-bottom: 12px;">
        <i data-feather="user" class="me-2 text-primary" style="width: 20px; height: 20px;"></i>
        <h5 class="text-white mb-0" style="font-size: 1.1rem; font-weight: 600;">Basic Information</h5>
      </div>
      
      <div class="info-item mb-3">
        <div class="d-flex align-items-start">
          <i data-feather="user" class="me-3 text-muted" style="width: 18px; height: 18px; margin-top: 4px;"></i>
          <div class="flex-grow-1">
            <div class="text-muted small mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Name</div>
            <div class="text-white" style="font-size: 0.95rem; font-weight: 500;">{{ $employee->name ?? 'N/A' }}</div>
          </div>
        </div>
      </div>
      
      @if($employee->phone)
      <div class="info-item mb-3">
        <div class="d-flex align-items-start">
          <i data-feather="phone" class="me-3 text-muted" style="width: 18px; height: 18px; margin-top: 4px;"></i>
          <div class="flex-grow-1">
            <div class="text-muted small mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Phone</div>
            <div class="text-white" style="font-size: 0.95rem; font-weight: 500;">{{ $employee->phone }}</div>
          </div>
        </div>
      </div>
      @endif
      
      @if($employee->address)
      <div class="info-item mb-3">
        <div class="d-flex align-items-start">
          <i data-feather="map-pin" class="me-3 text-muted" style="width: 18px; height: 18px; margin-top: 4px;"></i>
          <div class="flex-grow-1">
            <div class="text-muted small mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Address</div>
            <div class="text-white" style="font-size: 0.95rem; font-weight: 500;">{{ $employee->address }}</div>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
  
  <!-- Work Information -->
  <div class="col-md-6 mb-4">
    <div class="card-glass h-100">
      <div class="d-flex align-items-center mb-4" style="border-bottom: 2px solid rgba(59, 130, 246, 0.2); padding-bottom: 12px;">
        <i data-feather="briefcase" class="me-2 text-primary" style="width: 20px; height: 20px;"></i>
        <h5 class="text-white mb-0" style="font-size: 1.1rem; font-weight: 600;">Work Information</h5>
      </div>
      
      @if($employee->department)
      <div class="info-item mb-3">
        <div class="d-flex align-items-start">
          <i data-feather="building" class="me-3 text-muted" style="width: 18px; height: 18px; margin-top: 4px;"></i>
          <div class="flex-grow-1">
            <div class="text-muted small mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Department</div>
            <div class="text-white" style="font-size: 0.95rem; font-weight: 500;">{{ $employee->department }}</div>
          </div>
        </div>
      </div>
      @endif
      
      @if($employee->designation)
      <div class="info-item mb-3">
        <div class="d-flex align-items-start">
          <i data-feather="award" class="me-3 text-muted" style="width: 18px; height: 18px; margin-top: 4px;"></i>
          <div class="flex-grow-1">
            <div class="text-muted small mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Designation</div>
            <div class="text-white" style="font-size: 0.95rem; font-weight: 500;">{{ $employee->designation }}</div>
          </div>
        </div>
      </div>
      @endif
      
      <div class="info-item mb-3">
        <div class="d-flex align-items-start">
          <i data-feather="activity" class="me-3 text-muted" style="width: 18px; height: 18px; margin-top: 4px;"></i>
          <div class="flex-grow-1">
            <div class="text-muted small mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</div>
            <div>
              <span class="badge {{ $employee->status === 'active' ? 'bg-success' : 'bg-danger' }}" style="font-size: 0.85rem; padding: 6px 12px;">
                {{ ucfirst($employee->status ?? 'inactive') }}
              </span>
              @if($employee->status === 'inactive' && $employee->updated_at)
                <span class="text-muted ms-2 small" style="font-size: 0.8rem;">
                  (Since: {{ $employee->updated_at->setTimezone('Asia/Karachi')->format('M d, Y') }})
                </span>
              @endif
            </div>
          </div>
        </div>
      </div>
      
      @if($employee->date_of_hire)
      <div class="info-item mb-3">
        <div class="d-flex align-items-start">
          <i data-feather="calendar" class="me-3 text-muted" style="width: 18px; height: 18px; margin-top: 4px;"></i>
          <div class="flex-grow-1">
            <div class="text-muted small mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Hire Date</div>
            <div class="text-white" style="font-size: 0.95rem; font-weight: 500;">{{ $employee->date_of_hire->format('M d, Y') }}</div>
          </div>
        </div>
      </div>
      @endif
      
    </div>
  </div>
</div>

@push('styles')
<style>
  .info-item {
    padding: 12px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  }
  
  .info-item:last-child {
    border-bottom: none;
  }
  
  .employee-avatar {
    transition: transform 0.3s ease;
  }
  
  .employee-avatar:hover {
    transform: scale(1.05);
  }
  
  .card-glass {
    transition: box-shadow 0.3s ease;
  }
  
  .card-glass:hover {
    box-shadow: 0 12px 40px rgba(15, 23, 42, 0.5);
  }
</style>
@endpush

@push('scripts')
<script>
  feather.replace();
</script>
@endpush
@endsection