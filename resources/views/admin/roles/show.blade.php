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
<div class="card-glass">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-white fw-bold">Basic Information</h6>
                <table class="table table-borderless">
                  <tr>
                    <td><strong>Role Name:</strong></td>
                    <td>{{ $role->role_name }}</td>
                  </tr>
                  <tr>
                    <td><strong>Description:</strong></td>
                    <td>{{ $role->description ?? 'No description' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Users Count:</strong></td>
                    <td>
                      <span class="badge bg-primary">{{ $role->users->count() }} users</span>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Created:</strong></td>
                    <td>{{ $role->created_at->format('M d, Y H:i') }}</td>
                  </tr>
                  <tr>
                    <td><strong>Last Updated:</strong></td>
                    <td>{{ $role->updated_at->format('M d, Y H:i') }}</td>
                  </tr>
                </table>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-white fw-bold">Users with this Role</h6>
                @if($role->users->count() > 0)
                <div class="list-group">
                  @foreach($role->users as $user)
                  <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                      <strong>{{ $user->username }}</strong>
                      @if($user->email)
                      <br><small class="text-muted">{{ $user->email }}</small>
                      @endif
                    </div>
                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }}" style="color: #ffffff !important;">
                      {{ ucfirst($user->status) }}
                    </span>
                  </div>
                  @endforeach
                </div>
                @else
                <p class="text-muted">No users assigned to this role</p>
                @endif
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="card-glass" style="border: 1px solid white;">
                <div class="card-header">
                  <h6 class="card-title mb-0 text-white">
                    <i data-feather="shield" class="me-2"></i>Role Permissions
                  </h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-sm table-dark">
                      <thead class="table-dark">
                        <tr>
                          <th class="text-white">Module</th>
                          <th class="text-white text-center">View</th>
                          <th class="text-white text-center">Add</th>
                          <th class="text-white text-center">Edit</th>
                          <th class="text-white text-center">Delete</th>
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
                            <strong>{{ ucfirst($module) }}</strong>
                          </td>
                          <td class="text-center">
                            <span class="badge bg-{{ $permission && $permission->can_view ? 'success' : 'secondary' }}">
                              <i data-feather="{{ $permission && $permission->can_view ? 'check' : 'x' }}" class="me-1"></i>
                              {{ $permission && $permission->can_view ? 'Yes' : 'No' }}
                            </span>
                          </td>
                          <td class="text-center">
                            <span class="badge bg-{{ $permission && $permission->can_add ? 'success' : 'secondary' }}">
                              <i data-feather="{{ $permission && $permission->can_add ? 'check' : 'x' }}" class="me-1"></i>
                              {{ $permission && $permission->can_add ? 'Yes' : 'No' }}
                            </span>
                          </td>
                          <td class="text-center">
                            <span class="badge bg-{{ $permission && $permission->can_edit ? 'success' : 'secondary' }}">
                              <i data-feather="{{ $permission && $permission->can_edit ? 'check' : 'x' }}" class="me-1"></i>
                              {{ $permission && $permission->can_edit ? 'Yes' : 'No' }}
                            </span>
                          </td>
                          <td class="text-center">
                            <span class="badge bg-{{ $permission && $permission->can_delete ? 'success' : 'secondary' }}">
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
                      <strong>No permissions assigned to this role.</strong> This role currently has no specific permissions configured.
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>

          <div class="row mt-4">
            <div class="col-12">
              <div class="d-flex justify-content-start">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                  <i data-feather="arrow-left"></i> Back to Roles
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
