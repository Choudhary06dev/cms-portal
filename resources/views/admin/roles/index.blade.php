@extends('layouts.admin')

@section('title', 'Role Management')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Role Management</h5>
          <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
            <i data-feather="plus"></i> Add New Role
          </a>
        </div>
        <div class="card-body">
          <!-- Filters -->
          <div class="row mb-3">
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
              <button class="btn btn-outline-info" onclick="exportRoles()">
                <i data-feather="download"></i> Export
              </button>
            </div>
            <div class="col-md-2">
              <button class="btn btn-outline-secondary" onclick="clearFilters()">Clear</button>
            </div>
          </div>

          <!-- Roles Table -->
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Role Name</th>
                  <th>Description</th>
                  <th>Users Count</th>
                  <th>Permissions</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($roles as $role)
                <tr>
                  <td>{{ $role->id }}</td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-2">
                        <div class="avatar-content bg-info text-white">
                          {{ substr($role->role_name, 0, 1) }}
                        </div>
                      </div>
                      <strong>{{ $role->role_name }}</strong>
                    </div>
                  </td>
                  <td>{{ $role->description ?? 'No description' }}</td>
                  <td>
                    <span class="badge bg-primary">{{ $role->users_count ?? 0 }} users</span>
                  </td>
                  <td>
                    <span class="badge bg-success">{{ $role->rolePermissions->count() }} permissions</span>
                  </td>
                  <td>{{ $role->created_at->format('M d, Y') }}</td>
                  <td>
                    <div class="btn-group" role="group">
                      <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-outline-info btn-sm">
                        <i data-feather="eye"></i>
                      </a>
                      <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-outline-warning btn-sm">
                        <i data-feather="edit"></i>
                      </a>
                      <a href="{{ route('admin.roles.permissions', $role) }}" class="btn btn-outline-secondary btn-sm">
                        <i data-feather="shield"></i>
                      </a>
                      <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure? This will affect all users with this role.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                          <i data-feather="trash-2"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="7" class="text-center">No roles found</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="d-flex justify-content-center mt-3">
            {{ $roles->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Search functionality
  const searchInput = document.getElementById('searchInput');
  const statusFilter = document.getElementById('statusFilter');

  function filterTable() {
    const searchTerm = searchInput.value.toLowerCase();
    const statusValue = statusFilter.value;
    
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
      const roleName = row.cells[1].textContent.toLowerCase();
      const description = row.cells[2].textContent.toLowerCase();
      
      const matchesSearch = roleName.includes(searchTerm) || description.includes(searchTerm);
      const matchesStatus = !statusValue; // Add status logic if needed
      
      row.style.display = matchesSearch && matchesStatus ? '' : 'none';
    });
  }

  searchInput.addEventListener('input', filterTable);
  statusFilter.addEventListener('change', filterTable);
});

function clearFilters() {
  document.getElementById('searchInput').value = '';
  document.getElementById('statusFilter').value = '';
  filterTable();
}

function exportRoles() {
  // Implement export functionality
  alert('Export functionality will be implemented');
}
</script>
@endsection