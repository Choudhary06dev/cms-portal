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
  <form id="rolesFiltersForm" method="GET" action="{{ route('admin.roles.index') }}">
  <div class="row g-2 align-items-end">
    <div class="col-12 col-md-4">
      <input type="text" class="form-control" id="searchInput" name="search" placeholder="Search roles..." 
             value="{{ request('search') }}" oninput="handleRolesSearchInput()">
    </div>
  </div>
  </form>
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
      <tbody id="rolesTableBody">
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
  <div class="d-flex justify-content-center mt-3" id="rolesPagination">
    <div>
      {{ $roles->links() }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  feather.replace();

  // Debounced search input handler
  let rolesSearchTimeout = null;
  function handleRolesSearchInput() {
    if (rolesSearchTimeout) clearTimeout(rolesSearchTimeout);
    rolesSearchTimeout = setTimeout(() => {
      loadRoles();
    }, 500);
  }

  // Auto-submit for select filters
  function submitRolesFilters() {
    loadRoles();
  }

  // Load Roles via AJAX
  function loadRoles(url = null) {
    const form = document.getElementById('rolesFiltersForm');
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

    const tbody = document.getElementById('rolesTableBody');
    const paginationContainer = document.getElementById('rolesPagination');
    
    if (tbody) {
      tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
    }

    fetch(`{{ route('admin.roles.index') }}?${params.toString()}`, {
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
      
      const newTbody = doc.querySelector('#rolesTableBody');
      const newPagination = doc.querySelector('#rolesPagination');
      
      if (newTbody && tbody) {
        tbody.innerHTML = newTbody.innerHTML;
        feather.replace();
      }
      
      if (newPagination && paginationContainer) {
        paginationContainer.innerHTML = newPagination.innerHTML;
      }

      const newUrl = `{{ route('admin.roles.index') }}?${params.toString()}`;
      window.history.pushState({path: newUrl}, '', newUrl);
    })
    .catch(error => {
      console.error('Error loading roles:', error);
      if (tbody) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-danger">Error loading data. Please refresh the page.</td></tr>';
      }
    });
  }

  // Handle pagination clicks
  document.addEventListener('click', function(e) {
    const paginationLink = e.target.closest('#rolesPagination a');
    if (paginationLink && paginationLink.href && !paginationLink.href.includes('javascript:')) {
      e.preventDefault();
      loadRoles(paginationLink.href);
    }
  });

  // Handle browser back/forward buttons
  window.addEventListener('popstate', function(e) {
    if (e.state && e.state.path) {
      loadRoles(e.state.path);
    } else {
      loadRoles();
    }
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