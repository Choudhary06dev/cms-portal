@extends('layouts.sidebar')

@section('title', 'Role Management — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Role Management</h2>
      <p class="text-light">Manage user roles and permissions</p>
    </div>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-accent">
      <i data-feather="plus-circle" class="me-2"></i>Add New Role
    </a>
  </div>
</div>

<!-- FILTERS -->
<div class="card-glass mb-4">
  <div class="row g-3">
    <div class="col-md-4">
      <input type="text" class="form-control" placeholder="Search roles..." id="searchInput">
    </div>
    <div class="col-md-3">
      <select class="form-select" id="statusFilter">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
    </div>
    <div class="col-md-3">
      <div class="d-flex gap-2">
        <button class="btn btn-outline-light btn-sm" id="applyFilters">
          <i data-feather="filter" class="me-1"></i>Apply
        </button>
        <button class="btn btn-outline-secondary btn-sm" id="clearFilters">
          <i data-feather="x" class="me-1"></i>Clear
        </button>
        
      </div>
    </div>
  </div>
</div>

<!-- ROLES TABLE -->
<div class="card-glass">
  <div class="table-responsive">
    <table class="table table-dark">
      <thead class="table-dark">
        <tr>
          <th class="text-white">ID</th>
          <th class="text-white">Role Name</th>
          <th class="text-white">Description</th>
          <th class="text-white">Users Count</th>
          <th class="text-white">Permissions</th>
          <th class="text-white">Created</th>
          <th class="text-white">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($roles as $role)
        <tr>
          <td>{{ $role->id }}</td>
          <td>
            <div class="d-flex align-items-center">
              {{-- <div class="avatar-sm me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold;">
                {{ substr($role->role_name, 0, 1) }}
              </div> --}}
              <div>
                <div class="fw-bold">{{ $role->role_name }}</div>
                {{-- <div class="text-muted small">{{ $role->description ?? 'No description' }}</div> --}}
              </div>
            </div>
          </td>
          <td>{{ $role->description ?? 'N/A' }}</td>
          <td>
            <span class="badge bg-info">{{ $role->users_count ?? 0 }} users</span>
          </td>
          <td>
            <span class="badge bg-warning">{{ $role->role_permissions_count ?? 0 }} permissions</span>
          </td>
          <td>{{ $role->created_at->format('M d, Y') }}</td>
          <td>
            <div class="btn-group" role="group">
              <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-outline-info btn-sm" title="View Details">
                <i data-feather="eye"></i>
              </a>
              <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-outline-warning btn-sm" title="Edit">
                <i data-feather="edit"></i>
              </a>
              <a href="{{ route('admin.roles.permissions', $role) }}" class="btn btn-outline-primary btn-sm" title="Permissions">
                <i data-feather="shield"></i>
              </a>
              <button class="btn btn-outline-danger btn-sm" onclick="deleteRole({{ $role->id }})" title="Delete">
                <i data-feather="trash-2"></i>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center py-4">
            <i data-feather="shield" class="feather-lg mb-2"></i>
            <div class="text-muted">No roles found</div>
            <small class="text-muted">Create your first role to get started</small>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <!-- PAGINATION -->
  <div class="d-flex justify-content-center mt-3">
    <div>
      {{ $roles->links() }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  feather.replace();

  // Filter functionality
  document.getElementById('applyFilters')?.addEventListener('click', function() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (status) params.append('status', status);
    
    window.location.href = '{{ route("admin.roles.index") }}?' + params.toString();
  });

  document.getElementById('clearFilters')?.addEventListener('click', function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    window.location.href = '{{ route("admin.roles.index") }}';
  });

  // Export functionality
  

  // Delete role function
  function deleteRole(roleId) {
    if (confirm('Are you sure you want to delete this role? This action cannot be undone.')) {
      // Show loading state
      const deleteBtn = event.target.closest('button');
      const originalContent = deleteBtn.innerHTML;
      deleteBtn.innerHTML = '<i data-feather="loader" class="spinner"></i>';
      deleteBtn.disabled = true;
      
      fetch(`/admin/roles/${roleId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      })
      .then(response => {
        if (!response.ok) {
          return response.json().then(data => {
            throw new Error(data.message || `HTTP error! status: ${response.status}`);
          });
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          // Show success message
          alert('Role deleted successfully!');
          location.reload();
        } else {
          alert('Error deleting role: ' + (data.message || 'Unknown error'));
          // Restore button
          deleteBtn.innerHTML = originalContent;
          deleteBtn.disabled = false;
          feather.replace();
        }
      })
      .catch(error => {
        console.error('Error deleting role:', error);
        alert('Error deleting role: ' + error.message);
        // Restore button
        deleteBtn.innerHTML = originalContent;
        deleteBtn.disabled = false;
        feather.replace();
      });
    }
  }
</script>
@endpush