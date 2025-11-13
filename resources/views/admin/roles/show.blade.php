@extends('layouts.sidebar')

@section('title', 'Role Details — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Role Details: {{ $role->role_name }}</h2>
      <p class="text-light">View role information and permissions</p>
    </div>
   
  </div>
</div>

<!-- ROLE DETAILS -->
<div class="row">
  <!-- Basic Information -->
  <div class="col-md-6 mb-4">
    <div class="card-glass h-100">
      <div class="d-flex align-items-center mb-4" style="border-bottom: 2px solid rgba(59, 130, 246, 0.2); padding-bottom: 12px;">
        <i data-feather="shield" class="me-2 text-primary" style="width: 20px; height: 20px;"></i>
        <h5 class="text-white mb-0" style="font-size: 1.1rem; font-weight: 600;">Personal Information</h5>
      </div>
      
      <div class="info-item mb-3">
        <div class="d-flex align-items-start">
          <i data-feather="shield" class="me-3 text-muted" style="width: 18px; height: 18px; margin-top: 4px;"></i>
          <div class="flex-grow-1">
            <div class="text-muted small mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Role Name</div>
            <div class="text-white" style="font-size: 0.95rem; font-weight: 500;">{{ $role->role_name }}</div>
          </div>
        </div>
      </div>
      
      @if($role->description)
      <div class="info-item mb-3">
        <div class="d-flex align-items-start">
          <i data-feather="file-text" class="me-3 text-muted" style="width: 18px; height: 18px; margin-top: 4px;"></i>
          <div class="flex-grow-1">
            <div class="text-muted small mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Description</div>
            <div class="text-white" style="font-size: 0.95rem; font-weight: 500;">{{ $role->description }}</div>
          </div>
        </div>
      </div>
      @endif
      
      <div class="info-item mb-3">
        <div class="d-flex align-items-start">
          <i data-feather="users" class="me-3 text-muted" style="width: 18px; height: 18px; margin-top: 4px;"></i>
          <div class="flex-grow-1">
            <div class="text-muted small mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Users Count</div>
            <div>
              <span class="badge bg-primary" style="font-size: 0.85rem; padding: 6px 12px;">
                {{ $role->users->count() }} users
              </span>
            </div>
          </div>
        </div>
      </div>
      
      <div class="info-item mb-3">
        <div class="d-flex align-items-start">
          <i data-feather="calendar" class="me-3 text-muted" style="width: 18px; height: 18px; margin-top: 4px;"></i>
          <div class="flex-grow-1">
            <div class="text-muted small mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Created</div>
            <div class="text-white" style="font-size: 0.95rem; font-weight: 500;">{{ $role->created_at ? $role->created_at->timezone('Asia/Karachi')->format('M d, Y H:i:s') : 'N/A' }}</div>
          </div>
        </div>
      </div>
      
      <div class="info-item mb-3">
        <div class="d-flex align-items-start">
          <i data-feather="clock" class="me-3 text-muted" style="width: 18px; height: 18px; margin-top: 4px;"></i>
          <div class="flex-grow-1">
            <div class="text-muted small mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Last Updated</div>
            <div class="text-white" style="font-size: 0.95rem; font-weight: 500;">{{ $role->updated_at ? $role->updated_at->timezone('Asia/Karachi')->format('M d, Y H:i:s') : 'N/A' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Role Information -->
  <div class="col-md-6 mb-4">
    <div class="card-glass h-100">
      <div class="d-flex align-items-center mb-4" style="border-bottom: 2px solid rgba(59, 130, 246, 0.2); padding-bottom: 12px;">
        <i data-feather="users" class="me-2 text-primary" style="width: 20px; height: 20px;"></i>
        <h5 class="text-white mb-0" style="font-size: 1.1rem; font-weight: 600;">Users with this Role</h5>
      </div>
      
      @if($role->users->count() > 0)
        @foreach($role->users as $user)
        <div class="info-item mb-3">
          <div class="d-flex align-items-start">
            <i data-feather="user" class="me-3 text-muted" style="width: 18px; height: 18px; margin-top: 4px;"></i>
            <div class="flex-grow-1">
              <div class="text-muted small mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">{{ $user->username }}</div>
              <div class="text-white" style="font-size: 0.95rem; font-weight: 500;">
                {{ $user->name ?? 'N/A' }}
                @if($user->email)
                  <span class="text-muted ms-2" style="font-size: 0.85rem;">
                    <i data-feather="mail" style="width: 14px; height: 14px; vertical-align: middle;"></i>
                    {{ $user->email }}
                  </span>
                @endif
                <span class="badge ms-2 {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }}" style="font-size: 0.75rem; padding: 4px 8px; color: #ffffff !important;">
                  {{ ucfirst($user->status) }}
                </span>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      @else
        <div class="info-item mb-3">
          <div class="d-flex align-items-start">
            <i data-feather="alert-circle" class="me-3 text-muted" style="width: 18px; height: 18px; margin-top: 4px;"></i>
            <div class="flex-grow-1">
              <div class="text-muted" style="font-size: 0.9rem;">No users assigned to this role</div>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

<!-- ROLE PERMISSIONS -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card-glass">
      <div class="d-flex align-items-center mb-4" style="border-bottom: 2px solid rgba(59, 130, 246, 0.2); padding-bottom: 12px;">
        <i data-feather="shield" class="me-2 text-primary" style="width: 20px; height: 20px;"></i>
        <h5 class="text-white mb-0" style="font-size: 1.1rem; font-weight: 600;">Role Permissions</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-dark">
            <thead class="table-dark">
              <tr>
                <th class="text-white fw-bold" style="font-size: 0.875rem;">Module</th>
                <th class="text-white text-center fw-bold" style="font-size: 0.875rem;">View</th>
                <th class="text-white text-center fw-bold" style="font-size: 0.875rem;">Add</th>
                <th class="text-white text-center fw-bold" style="font-size: 0.875rem;">Edit</th>
                <th class="text-white text-center fw-bold" style="font-size: 0.875rem;">Delete</th>
              </tr>
            </thead>
            <tbody>
              @php
              $modules = ['users', 'roles', 'employees', 'clients', 'complaints', 'spares', 'approvals', 'reports', 'sla'];
              @endphp
              
              @foreach($modules as $module)
              @php
              $permission = $role->rolePermissions->where('module_name', $module)->first();
              @endphp
              <tr>
                <td class="text-white">
                  <strong style="font-size: 0.875rem;">{{ ucfirst($module) }}</strong>
                </td>
                <td class="text-center">
                  <span class="badge bg-{{ $permission && $permission->can_view ? 'success' : 'secondary' }}" style="font-size: 0.875rem;">
                    <i data-feather="{{ $permission && $permission->can_view ? 'check' : 'x' }}" class="me-1"></i>
                    {{ $permission && $permission->can_view ? 'Yes' : 'No' }}
                  </span>
                </td>
                <td class="text-center">
                  <span class="badge bg-{{ $permission && $permission->can_add ? 'success' : 'secondary' }}" style="font-size: 0.875rem;">
                    <i data-feather="{{ $permission && $permission->can_add ? 'check' : 'x' }}" class="me-1"></i>
                    {{ $permission && $permission->can_add ? 'Yes' : 'No' }}
                  </span>
                </td>
                <td class="text-center">
                  <span class="badge bg-{{ $permission && $permission->can_edit ? 'success' : 'secondary' }}" style="font-size: 0.875rem;">
                    <i data-feather="{{ $permission && $permission->can_edit ? 'check' : 'x' }}" class="me-1"></i>
                    {{ $permission && $permission->can_edit ? 'Yes' : 'No' }}
                  </span>
                </td>
                <td class="text-center">
                  <span class="badge bg-{{ $permission && $permission->can_delete ? 'success' : 'secondary' }}" style="font-size: 0.875rem;">
                    <i data-feather="{{ $permission && $permission->can_delete ? 'check' : 'x' }}" class="me-1"></i>
                    {{ $permission && $permission->can_delete ? 'Yes' : 'No' }}
                  </span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        @if($role->rolePermissions->count() == 0)
          <div class="alert alert-warning mt-3">
            <i data-feather="alert-triangle" class="me-2"></i>
            <strong style="font-size: 0.875rem;">No permissions assigned to this role.</strong> <span style="font-size: 0.875rem;">This role currently has no specific permissions configured.</span>
          </div>
        @endif
      </div>
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
  document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
  });
</script>
@endpush
@endsection
