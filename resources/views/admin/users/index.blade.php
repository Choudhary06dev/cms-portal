@extends('layouts.sidebar')

@section('title', 'User Management — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">User Management</h2>
      <p class="text-light">Manage system users and their access</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-accent">
      <i data-feather="user-plus" class="me-2"></i>Add New User
    </a>
  </div>
</div>

<!-- FILTERS -->
<div class="card-glass mb-4">
  <div class="row g-3">
    <div class="col-md-3">
      <input type="text" class="form-control" placeholder="Search users..." id="searchInput">
    </div>
    <div class="col-md-2">
      <select class="form-select" id="roleFilter">
        <option value="">All Roles</option>
        @foreach($roles as $role)
        <option value="{{ $role->id }}">{{ $role->role_name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <select class="form-select" id="statusFilter">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
    </div>
    <div class="col-md-3">
      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm" id="applyFilters">
          <i data-feather="filter" class="me-1"></i>Apply
        </button>
        <button class="btn btn-outline-secondary btn-sm" id="clearFilters">
          <i data-feather="x" class="me-1"></i>Clear
        </button>
        <button class="btn btn-outline-primary btn-sm" id="exportBtn">
          <i data-feather="download" class="me-1"></i>Export
        </button>
      </div>
    </div>
  </div>
</div>

<!-- USERS TABLE -->
<div class="card-glass">
  <div class="table-responsive">
    <table class="table table-dark">
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        <tr>
          <td>{{ $user->id }}</td>
          <td>
            <div class="d-flex align-items-center">
              {{-- <div class="avatar-sm me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold;">
                {{ substr($user->username, 0, 1) }}
              </div> --}}
              <div>
                <div class="fw-bold">{{ $user->username }}</div>
                {{-- <small class="text-muted">ID: {{ $user->id }}</small> --}}
              </div>
            </div>
          </td>
          <td>{{ $user->email ?? 'N/A' }}</td>
          <td>
            <span class="badge bg-primary">{{ $user->role->role_name ?? 'No Role' }}</span>
          </td>
          <td>
            <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }}">
              {{ ucfirst($user->status) }}
            </span>
          </td>
          <td>
            <div class="btn-group" role="group">
              <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-info btn-sm" title="View Details">
                <i data-feather="eye"></i>
              </a>
              <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-warning btn-sm" title="Edit">
                <i data-feather="edit"></i>
              </a>
              <button class="btn btn-outline-danger btn-sm" onclick="deleteUser({{ $user->id }})" title="Delete">
                <i data-feather="trash-2"></i>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center py-4">
            <i data-feather="users" class="feather-lg mb-2"></i>
            <div>No users found</div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <!-- PAGINATION -->
  <div class="d-flex justify-content-center mt-3">
    <div>
      {{ $users->links() }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  feather.replace();

  // Restore filter values from URL parameters
  document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.get('search')) {
      document.getElementById('searchInput').value = urlParams.get('search');
    }
    if (urlParams.get('role_id')) {
      document.getElementById('roleFilter').value = urlParams.get('role_id');
    }
    if (urlParams.get('status')) {
      document.getElementById('statusFilter').value = urlParams.get('status');
    }
  });

  // Filter functionality
  document.getElementById('applyFilters')?.addEventListener('click', function() {
    const search = document.getElementById('searchInput').value;
    const role = document.getElementById('roleFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (role) params.append('role_id', role);
    if (status) params.append('status', status);
    
    window.location.href = '{{ route("admin.users.index") }}?' + params.toString();
  });

  document.getElementById('clearFilters')?.addEventListener('click', function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('roleFilter').value = '';
    document.getElementById('statusFilter').value = '';
    window.location.href = '{{ route("admin.users.index") }}';
  });

  // Export functionality
  document.getElementById('exportBtn')?.addEventListener('click', function() {
    window.location.href = '{{ route("admin.users.export") }}';
  });

  // Delete user function
  function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
      fetch(`/admin/users/${userId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification('User deleted successfully!', 'success');
          // Remove the row from table
          const row = document.querySelector(`button[onclick="deleteUser(${userId})"]`).closest('tr');
          if (row) {
            row.remove();
          }
          // Reload page after a short delay
          setTimeout(() => {
            location.reload();
          }, 1000);
        } else {
          showNotification('Error deleting user: ' + (data.message || 'Unknown error'), 'error');
        }
      })
      .catch(error => {
        console.error('Error deleting user:', error);
        showNotification('Error deleting user: ' + error.message, 'error');
      });
    }
  }

  function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification);
      }
    }, 5000);
  }
</script>
@endpush