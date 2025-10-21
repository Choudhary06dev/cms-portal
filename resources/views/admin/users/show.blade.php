@extends('layouts.sidebar')

@section('title', 'User Details')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">User Details: {{ $user->username }}</h5>
          <div class="btn-group">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">
              <i data-feather="edit"></i> Edit
            </a>
            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-{{ $user->status === 'active' ? 'warning' : 'success' }} btn-sm">
                <i data-feather="{{ $user->status === 'active' ? 'user-x' : 'user-check' }}"></i>
                {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
              </button>
            </form>
          </div>
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
                    @foreach($user->role->rolePermissions as $permission)
                    <tr>
                      <td>{{ ucfirst($permission->module_name) }}</td>
                      <td>
                        <span class="badge bg-{{ $permission->can_view ? 'success' : 'secondary' }}">
                          {{ $permission->can_view ? 'Yes' : 'No' }}
                        </span>
                      </td>
                      <td>
                        <span class="badge bg-{{ $permission->can_add ? 'success' : 'secondary' }}">
                          {{ $permission->can_add ? 'Yes' : 'No' }}
                        </span>
                      </td>
                      <td>
                        <span class="badge bg-{{ $permission->can_edit ? 'success' : 'secondary' }}">
                          {{ $permission->can_edit ? 'Yes' : 'No' }}
                        </span>
                      </td>
                      <td>
                        <span class="badge bg-{{ $permission->can_delete ? 'success' : 'secondary' }}">
                          {{ $permission->can_delete ? 'Yes' : 'No' }}
                        </span>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          @endif

          <div class="row mt-4">
            <div class="col-12">
              <div class="d-flex justify-content-between">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                  <i data-feather="arrow-left"></i> Back to Users
                </a>
                <div class="btn-group">
                  <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                    <i data-feather="edit"></i> Edit User
                  </a>
                  <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
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
