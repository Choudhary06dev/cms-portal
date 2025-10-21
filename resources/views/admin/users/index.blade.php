@extends('layouts.sidebar')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">User Management</h5>
          <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
            <i data-feather="plus"></i> Add New User
          </a>
        </div>
        <div class="card-body">
          <!-- Filters -->
          <div class="row mb-3">
            <div class="col-md-4">
              <input type="text" class="form-control" placeholder="Search users..." id="searchInput">
            </div>
            <div class="col-md-3">
              <select class="form-select" id="statusFilter">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="col-md-3">
              <select class="form-select" id="roleFilter">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <button class="btn btn-outline-secondary" onclick="clearFilters()">Clear</button>
            </div>
          </div>

          <!-- Users Table -->
          <div class="table-responsive">
            <table class="table table-striped">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Last Login</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($users as $user)
                <tr>
                  <td>{{ $user->id }}</td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-2">
                        <div class="avatar-content bg-primary text-white">
                          {{ substr($user->username, 0, 1) }}
                        </div>
                      </div>
                      {{ $user->username }}
                    </div>
                  </td>
                  <td>{{ $user->email ?? 'N/A' }}</td>
                  <td>
                    <span class="badge bg-info">{{ $user->role->role_name ?? 'No Role' }}</span>
                  </td>
                  <td>
                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }}">
                      {{ ucfirst($user->status) }}
                    </span>
                  </td>
                  <td>{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</td>
                  <td>
                    <div class="btn-group" role="group">
                      <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-info btn-sm">
                        <i data-feather="eye"></i>
                      </a>
                      <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-warning btn-sm">
                        <i data-feather="edit"></i>
                      </a>
                      <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-{{ $user->status === 'active' ? 'warning' : 'success' }} btn-sm">
                          <i data-feather="{{ $user->status === 'active' ? 'user-x' : 'user-check' }}"></i>
                        </button>
                      </form>
                      <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                  <td colspan="7" class="text-center">No users found</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="d-flex justify-content-center mt-3">
            {{ $users->links() }}
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
  const roleFilter = document.getElementById('roleFilter');

  function filterTable() {
    const searchTerm = searchInput.value.toLowerCase();
    const statusValue = statusFilter.value;
    const roleValue = roleFilter.value;
    
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
      const username = row.cells[1].textContent.toLowerCase();
      const email = row.cells[2].textContent.toLowerCase();
      const role = row.cells[3].textContent.toLowerCase();
      const status = row.cells[4].textContent.toLowerCase();
      
      const matchesSearch = username.includes(searchTerm) || email.includes(searchTerm);
      const matchesStatus = !statusValue || status.includes(statusValue);
      const matchesRole = !roleValue || role.includes(roleValue);
      
      row.style.display = matchesSearch && matchesStatus && matchesRole ? '' : 'none';
    });
  }

  searchInput.addEventListener('input', filterTable);
  statusFilter.addEventListener('change', filterTable);
  roleFilter.addEventListener('change', filterTable);
});

function clearFilters() {
  document.getElementById('searchInput').value = '';
  document.getElementById('statusFilter').value = '';
  document.getElementById('roleFilter').value = '';
  filterTable();
}
</script>
@endsection
