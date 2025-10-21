@extends('layouts.sidebar')

@section('title', 'User Details')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">User Details: {{ $user->username }}</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-muted">Basic Information</h6>
                <table class="table table-borderless">
                  <tr>
                    <td><strong>Username:</strong></td>
                    <td>{{ $user->username }}</td>
                  </tr>
                  <tr>
                    <td><strong>Email:</strong></td>
                    <td>{{ $user->email ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Phone:</strong></td>
                    <td>{{ $user->phone ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Status:</strong></td>
                    <td>
                      <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }}">
                        {{ ucfirst($user->status) }}
                      </span>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-muted">Role & Permissions</h6>
                <table class="table table-borderless">
                  <tr>
                    <td><strong>Role:</strong></td>
                    <td>
                      <span class="badge bg-info">{{ $user->role->role_name ?? 'No Role' }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Role Description:</strong></td>
                    <td>{{ $user->role->description ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Created:</strong></td>
                    <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                  </tr>
                  <tr>
                    <td><strong>Last Updated:</strong></td>
                    <td>{{ $user->updated_at->format('M d, Y H:i') }}</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>

          @if($user->role)
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h6 class="card-title mb-0">
                    <i data-feather="shield" class="me-2"></i>Role Permissions
                  </h6>
                </div>
                <div class="card-body">
                  @if($user->role->rolePermissions->count() > 0)
                    <div class="table-responsive">
                      <table class="table table-sm table-striped">
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
                          @foreach($user->role->rolePermissions as $permission)
                          <tr>
                            <td>
                              <strong>{{ ucfirst($permission->module_name) }}</strong>
                            </td>
                            <td class="text-center">
                              <span class="badge bg-{{ $permission->can_view ? 'success' : 'secondary' }}">
                                <i data-feather="{{ $permission->can_view ? 'check' : 'x' }}" class="me-1"></i>
                                {{ $permission->can_view ? 'Yes' : 'No' }}
                              </span>
                            </td>
                            <td class="text-center">
                              <span class="badge bg-{{ $permission->can_add ? 'success' : 'secondary' }}">
                                <i data-feather="{{ $permission->can_add ? 'check' : 'x' }}" class="me-1"></i>
                                {{ $permission->can_add ? 'Yes' : 'No' }}
                              </span>
                            </td>
                            <td class="text-center">
                              <span class="badge bg-{{ $permission->can_edit ? 'success' : 'secondary' }}">
                                <i data-feather="{{ $permission->can_edit ? 'check' : 'x' }}" class="me-1"></i>
                                {{ $permission->can_edit ? 'Yes' : 'No' }}
                              </span>
                            </td>
                            <td class="text-center">
                              <span class="badge bg-{{ $permission->can_delete ? 'success' : 'secondary' }}">
                                <i data-feather="{{ $permission->can_delete ? 'check' : 'x' }}" class="me-1"></i>
                                {{ $permission->can_delete ? 'Yes' : 'No' }}
                              </span>
                            </td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  @else
                    <div class="alert alert-info">
                      <i data-feather="info" class="me-2"></i>
                      No specific permissions assigned to this role.
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
          @endif

          <div class="row mt-4">
            <div class="col-12">
              <div class="d-flex justify-content-start">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                  <i data-feather="arrow-left"></i> Back to Users
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
