@extends('layouts.sidebar')

@section('title', 'Employees Management — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Employees Management</h2>
      <p class="text-light">Manage employee information and records</p>
    </div>
    <a href="{{ route('admin.employees.create') }}" class="btn btn-accent">
      <i data-feather="user-plus" class="me-2"></i>Add New Employee
    </a>
  </div>
</div>

<!-- FILTERS -->
<div class="card-glass mb-4">
  <div class="row g-3">
    <div class="col-md-3">
      <input type="text" class="form-control" placeholder="Search employees..." id="searchInput">
    </div>
    <div class="col-md-2">
      <input type="text" class="form-control" placeholder="Filter by department..." id="departmentFilter">
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

<!-- EMPLOYEES TABLE -->
<div class="card-glass">
  <div class="table-responsive">
    <table class="table table-dark" id="employeesTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Employee</th>
          <th>Department</th>
          <th>Position</th>
          <th>Phone</th>
          <th>Status</th>
          <th>Hire Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($employees as $employee)
        <tr>
          <td>{{ $employee->id }}</td>
          <td>
            <div class="d-flex align-items-center">
              <div class="avatar-sm me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold;">
                {{ substr($employee->user->username ?? 'E', 0, 1) }}
              </div>
              <div>
                <div class="fw-bold">{{ $employee->user->username ?? 'N/A' }}</div>
                <small class="text-muted">ID: {{ $employee->id }}</small>
              </div>
            </div>
          </td>
          <td>{{ $employee->department ?? 'N/A' }}</td>
          <td>{{ $employee->designation ?? 'N/A' }}</td>
          <td>{{ $employee->phone ?: 'N/A' }}</td>
          <td>
            <span class="badge {{ $employee->user->status === 'active' ? 'bg-success' : 'bg-danger' }}">
              {{ ucfirst($employee->user->status ?? 'inactive') }}
            </span>
          </td>
          <td>{{ $employee->created_at ? $employee->created_at->format('M d, Y') : 'N/A' }}</td>
          <td>
            <div class="btn-group" role="group">
              <a href="{{ route('admin.employees.show', $employee) }}" class="btn btn-outline-info btn-sm" title="View Details">
                <i data-feather="eye"></i>
              </a>
              <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-outline-warning btn-sm" title="Edit">
                <i data-feather="edit"></i>
              </a>
              <button class="btn btn-outline-danger btn-sm" onclick="deleteEmployee({{ $employee->id }})" title="Delete" data-employee-id="{{ $employee->id }}">
                <i data-feather="trash-2"></i>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center text-muted py-4">
            <i data-feather="users" class="feather-lg mb-2"></i>
            <div>No employees found</div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <!-- PAGINATION -->
  <div class="d-flex justify-content-center mt-4">
    <div>
      {{ $employees->links() }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  feather.replace();
  
  // Search functionality
  document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#employeesTable tbody tr');
    
    rows.forEach(row => {
      const username = row.cells[1].textContent.toLowerCase();
      const department = row.cells[2].textContent.toLowerCase();
      const position = row.cells[3].textContent.toLowerCase();
      
      if (username.includes(searchTerm) || department.includes(searchTerm) || position.includes(searchTerm)) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  });
  
  // Filter functionality
  document.getElementById('applyFilters').addEventListener('click', function() {
    const departmentFilter = document.getElementById('departmentFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    const rows = document.querySelectorAll('#employeesTable tbody tr');
    
    rows.forEach(row => {
      let showRow = true;
      
      // Department filter
      if (departmentFilter) {
        const department = row.cells[2].textContent.toLowerCase();
        if (!department.includes(departmentFilter.toLowerCase())) {
          showRow = false;
        }
      }
      
      // Status filter
      if (statusFilter) {
        const statusBadge = row.querySelector('.badge');
        if (!statusBadge || !statusBadge.textContent.toLowerCase().includes(statusFilter)) {
          showRow = false;
        }
      }
      
      // Search filter
      if (searchTerm) {
        const username = row.cells[1].textContent.toLowerCase();
        const department = row.cells[2].textContent.toLowerCase();
        const position = row.cells[3].textContent.toLowerCase();
        if (!username.includes(searchTerm) && !department.includes(searchTerm) && !position.includes(searchTerm)) {
          showRow = false;
        }
      }
      
      row.style.display = showRow ? '' : 'none';
    });
  });
  
  // Clear filters
  document.getElementById('clearFilters').addEventListener('click', function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('departmentFilter').value = '';
    document.getElementById('statusFilter').value = '';
    
    const rows = document.querySelectorAll('#employeesTable tbody tr');
    rows.forEach(row => {
      row.style.display = '';
    });
  });
  
  // Export functionality
  document.getElementById('exportBtn').addEventListener('click', function() {
    // Simple CSV export
    const rows = document.querySelectorAll('#employeesTable tbody tr:not([style*="display: none"])');
    let csv = 'ID,Employee,Department,Position,Status,Hire Date\n';
    
    rows.forEach(row => {
      const cells = row.cells;
      const id = cells[0].textContent;
      const employee = cells[1].textContent.split('\n')[0].trim();
      const department = cells[2].textContent;
      const position = cells[3].textContent;
      const status = cells[4].textContent;
      const hireDate = cells[5].textContent;
      
      csv += `"${id}","${employee}","${department}","${position}","${status}","${hireDate}"\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'employees.csv';
    a.click();
    window.URL.revokeObjectURL(url);
  });
  
  // Delete employee function
  function deleteEmployee(employeeId) {
    console.log('Deleting employee ID:', employeeId);
    
    if (!employeeId) {
      alert('Invalid employee ID');
      return;
    }
    
    // Check if employee exists by looking for the delete button
    const deleteButton = document.querySelector(`button[data-employee-id="${employeeId}"]`);
    if (!deleteButton) {
      alert('Employee not found. The page will be refreshed.');
      location.reload();
      return;
    }
    
    if (confirm('Are you sure you want to delete this employee? This action cannot be undone.')) {
      console.log('User confirmed deletion for employee ID:', employeeId);
      
      // Try using fetch with proper headers first
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      console.log('CSRF Token:', csrfToken);
      
      fetch(`/admin/employees/${employeeId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        credentials: 'same-origin'
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          location.reload();
        } else {
          alert('Error deleting employee: ' + (data.message || 'Unknown error'));
        }
      })
      .catch(error => {
        console.error('Error deleting employee:', error);
        alert('Error deleting employee');
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