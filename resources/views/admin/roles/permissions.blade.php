@extends('layouts.sidebar')

@section('title', 'Manage Role Permissions')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5 class="card-title mb-0">
                <i data-feather="shield" class="me-2"></i>Manage Permissions: {{ $role->role_name }}
              </h5>
              <p class="text-muted mb-0">Configure what this role can access in the system</p>
            </div>
          
          </div>
        </div>
        <div class="card-body">
          @if(session('error'))
            <div class="alert alert-danger">
              <i data-feather="alert-circle" class="me-2"></i>{{ session('error') }}
            </div>
          @endif
          
          @if(session('success'))
            <div class="alert alert-success">
              <i data-feather="check-circle" class="me-2"></i>{{ session('success') }}
            </div>
          @endif
          
          <form action="{{ route('admin.roles.update-permissions', $role) }}" method="POST">
            @csrf
            
            <div class="row">
              @php
              $modules = ['users', 'roles', 'employees', 'clients', 'complaints', 'spares', 'approvals', 'reports', 'sla'];
              @endphp
              
              @foreach($modules as $module)
              @php
              $hasPermission = $role->rolePermissions->where('module_name', $module)->first();
              @endphp
              <div class="col-md-6 col-lg-4 mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" 
                         id="{{ $module }}" 
                         name="permissions[]"
                         value="{{ $module }}" 
                         {{ ($role->id === 1 || $hasPermission) ? 'checked' : '' }}
                         {{ $role->id === 1 ? 'disabled' : '' }}>
                  <label class="form-check-label" for="{{ $module }}">
                    {{ ucfirst($module) }}
                    @if($role->id === 1)
                      <span class="badge bg-success ms-2">Auto</span>
                    @endif
                  </label>
                </div>
              </div>
              @endforeach
            </div>

            <!-- Summary Section -->
            <div class="row mt-4">
              <div class="col-12">
                <div class="alert alert-info">
                  <h6 class="alert-heading">
                    <i data-feather="info" class="me-2"></i>Permission Summary
                  </h6>
                  <p class="mb-2">
                    @if($role->id === 1)
                      <strong>Admin Role:</strong> This role automatically has <strong>all {{ count($modules) }} permissions</strong> assigned.
                    @else
                      This role currently has <strong>{{ $role->rolePermissions->count() }}</strong> permission sets configured.
                    @endif
                  </p>
                  <small class="text-muted">
                    Users with this role will inherit all the permissions you configure here. 
                    Make sure to review each module carefully before saving.
                  </small>
                </div>
              </div>
            </div>

            <!-- Debug Section (remove in production) -->
            @if(config('app.debug'))
            <div class="row mt-3">
              <div class="col-12">
                <div class="alert alert-warning">
                  <h6 class="alert-heading">Debug Information</h6>
                  <p><strong>Role ID:</strong> {{ $role->id }}</p>
                  <p><strong>Role Name:</strong> {{ $role->role_name }}</p>
                  <p><strong>Current Permissions:</strong> {{ $role->rolePermissions->count() }}</p>
                  <p><strong>Form Action:</strong> {{ route('admin.roles.update-permissions', $role) }}</p>
                  
                  @if($role->rolePermissions->count() > 0)
                    <h6>Current Permissions:</h6>
                    @foreach($role->rolePermissions as $perm)
                      <p>- {{ ucfirst($perm->module_name) }}</p>
                    @endforeach
                  @else
                    <p><strong>No permissions found for this role.</strong></p>
                  @endif
                </div>
              </div>
            </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mt-4">
              <div class="d-flex gap-2">
                <a href="{{ route('admin.roles.index', $role) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                  <i data-feather="save" class="me-1"></i>Update Permissions
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  feather.replace();
});
</script>
@endsection
