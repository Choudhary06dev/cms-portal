@extends('layouts.sidebar')

@section('title', 'Role Details')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Role Details: {{ $role->role_name }}</h5>
          <div class="btn-group">
            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-warning btn-sm">
              <i data-feather="edit"></i> Edit
            </a>
            <a href="{{ route('admin.roles.permissions', $role) }}" class="btn btn-info btn-sm">
              <i data-feather="shield"></i> Permissions
            </a>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-muted">Basic Information</h6>
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
                <h6 class="text-muted">Users with this Role</h6>
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
                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }}">
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
              <h6 class="text-muted">Role Permissions</h6>
              <div class="table-responsive">
                <table class="table table-sm table-striped">
                  <thead>
                    <tr>
                      <th>Module</th>
                      <th>View</th>
                      <th>Add</th>
                      <th>Edit</th>
                      <th>Delete</th>
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
                      <td><strong>{{ ucfirst($module) }}</strong></td>
                      <td>
                        <span class="badge bg-{{ $permission && $permission->can_view ? 'success' : 'secondary' }}">
                          {{ $permission && $permission->can_view ? 'Yes' : 'No' }}
                        </span>
                      </td>
                      <td>
                        <span class="badge bg-{{ $permission && $permission->can_add ? 'success' : 'secondary' }}">
                          {{ $permission && $permission->can_add ? 'Yes' : 'No' }}
                        </span>
                      </td>
                      <td>
                        <span class="badge bg-{{ $permission && $permission->can_edit ? 'success' : 'secondary' }}">
                          {{ $permission && $permission->can_edit ? 'Yes' : 'No' }}
                        </span>
                      </td>
                      <td>
                        <span class="badge bg-{{ $permission && $permission->can_delete ? 'success' : 'secondary' }}">
                          {{ $permission && $permission->can_delete ? 'Yes' : 'No' }}
                        </span>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="row mt-4">
            <div class="col-12">
              <div class="d-flex justify-content-between">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                  <i data-feather="arrow-left"></i> Back to Roles
                </a>
                <div class="btn-group">
                  <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-warning">
                    <i data-feather="edit"></i> Edit Role
                  </a>
                  <a href="{{ route('admin.roles.permissions', $role) }}" class="btn btn-info">
                    <i data-feather="shield"></i> Manage Permissions
                  </a>
                  <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role? This will affect all users with this role.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                      <i data-feather="trash-2"></i> Delete
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
