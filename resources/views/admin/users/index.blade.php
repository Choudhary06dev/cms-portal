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
  <form id="usersFiltersForm" method="GET" action="{{ route('admin.users.index') }}">
  <div class="row g-2 align-items-end">
    <div class="col-12 col-md-4">
      <input type="text" class="form-control" id="searchInput" name="search" placeholder="Search users..." 
             value="{{ request('search') }}" oninput="handleUsersSearchInput()">
    </div>
    <div class="col-6 col-md-3">
      <select class="form-select" name="role_id" onchange="submitUsersFilters()">
        <option value="" {{ request('role_id') ? '' : 'selected' }}>All Roles</option>
        @foreach($roles as $role)
        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->role_name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-6 col-md-3">
      <select class="form-select" name="status" onchange="submitUsersFilters()">
        <option value="" {{ request('status') ? '' : 'selected' }}>All Status</option>
        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
      </select>
    </div>
  </div>
  </form>
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
          <th>City</th>
          <th>Sector</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="usersTableBody">
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
            @php
              $roleName = strtolower($user->role->role_name ?? '');
            @endphp
            @if(in_array($roleName, ['director', 'admin']))
              <span class="badge bg-info">All Cities</span>
            @else
              {{ $user->city->name ?? 'N/A' }}
            @endif
          </td>
          <td>
            @php
              $roleName = strtolower($user->role->role_name ?? '');
            @endphp
            @if(in_array($roleName, ['director', 'admin']))
              <span class="badge bg-info">All Sectors</span>
            @elseif($roleName === 'garrison_engineer')
              <span class="badge bg-info">All Sectors</span>
            @else
              {{ $user->sector->name ?? 'N/A' }}
            @endif
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
          <td colspan="8" class="text-center py-4">
            <i data-feather="users" class="feather-lg mb-2"></i>
            <div>No users found</div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <!-- PAGINATION -->
  <div class="d-flex justify-content-center mt-3" id="usersPagination">
    <div>
      {{ $users->links() }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  feather.replace();

  // Debounced search input handler
  let usersSearchTimeout = null;
  function handleUsersSearchInput() {
    if (usersSearchTimeout) clearTimeout(usersSearchTimeout);
    usersSearchTimeout = setTimeout(() => {
      loadUsers();
    }, 500);
  }

  // Auto-submit for select filters
  function submitUsersFilters() {
    loadUsers();
  }

  // Load Users via AJAX
  function loadUsers(url = null) {
    const form = document.getElementById('usersFiltersForm');
    if (!form) return;
    
    const formData = new FormData(form);
    const params = new URLSearchParams();
    
    if (url) {
      const urlObj = new URL(url, window.location.origin);
      urlObj.searchParams.forEach((value, key) => {
        params.append(key, value);
      });
    } else {
      for (const [key, value] of formData.entries()) {
        if (value) {
          params.append(key, value);
        }
      }
    }

    const tbody = document.getElementById('usersTableBody');
    const paginationContainer = document.getElementById('usersPagination');
    
    if (tbody) {
      tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4"><div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
    }

    fetch(`{{ route('admin.users.index') }}?${params.toString()}`, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'text/html',
      },
      credentials: 'same-origin'
    })
    .then(response => response.text())
    .then(html => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      
      const newTbody = doc.querySelector('#usersTableBody');
      const newPagination = doc.querySelector('#usersPagination');
      
      if (newTbody && tbody) {
        tbody.innerHTML = newTbody.innerHTML;
        feather.replace();
      }
      
      if (newPagination && paginationContainer) {
        paginationContainer.innerHTML = newPagination.innerHTML;
      }

      const newUrl = `{{ route('admin.users.index') }}?${params.toString()}`;
      window.history.pushState({path: newUrl}, '', newUrl);
    })
    .catch(error => {
      console.error('Error loading users:', error);
      if (tbody) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger">Error loading data. Please refresh the page.</td></tr>';
      }
    });
  }

  // Handle pagination clicks
  document.addEventListener('click', function(e) {
    const paginationLink = e.target.closest('#usersPagination a');
    if (paginationLink && paginationLink.href && !paginationLink.href.includes('javascript:')) {
      e.preventDefault();
      loadUsers(paginationLink.href);
    }
  });

  // Handle browser back/forward buttons
  window.addEventListener('popstate', function(e) {
    if (e.state && e.state.path) {
      loadUsers(e.state.path);
    } else {
      loadUsers();
    }
  });

  // Export functionality
  

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