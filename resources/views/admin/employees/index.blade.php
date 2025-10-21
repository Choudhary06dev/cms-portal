@extends('layouts.sidebar')

@section('title', 'Employees — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2" style="color: #ffffff !important;">Employee Management</h2>
      <p class="text-light" style="color: #cbd5e1 !important;">Manage your team members and their information</p>
    </div>
    <button class="btn btn-accent" id="addEmployeeBtn" data-bs-toggle="modal" data-bs-target="#employeeModal">
      <i data-feather="user-plus" class="me-2"></i>Add Employee
    </button>
  </div>
</div>

<!-- FILTERS -->
<div class="card-glass mb-4">
  <div class="row g-3">
    <div class="col-md-3">
      <input type="text" class="form-control" id="searchInput" placeholder="Search employees..." 
             style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;">
    </div>
    <div class="col-md-2">
      <select class="form-select" id="departmentFilter" 
              style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;">
        <option value="">All Departments</option>
        @if(isset($departments))
          @foreach($departments as $dept)
            <option value="{{ $dept }}">{{ $dept }}</option>
          @endforeach
        @endif
      </select>
    </div>
    <div class="col-md-2">
      <select class="form-select" id="designationFilter"
              style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;">
        <option value="">All Designations</option>
        @if(isset($designations))
          @foreach($designations as $desig)
            <option value="{{ $desig }}">{{ $desig }}</option>
          @endforeach
        @endif
      </select>
    </div>
    <div class="col-md-2">
      <select class="form-select" id="statusFilter"
              style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;">
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
    <table class="table table-dark table-hover" id="employeesTable">
                        <thead>
                            <tr>
          <th style="color: #e2e8f0 !important;">#</th>
          <th style="color: #e2e8f0 !important;">Employee</th>
          <th style="color: #e2e8f0 !important;">Department</th>
          <th style="color: #e2e8f0 !important;">Designation</th>
          <th style="color: #e2e8f0 !important;">Biometric ID</th>
          <th style="color: #e2e8f0 !important;">Leave Quota</th>
          <th style="color: #e2e8f0 !important;">Status</th>
          <th style="color: #e2e8f0 !important;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
        <tr>
          <td style="color: #cbd5e1 !important;">{{ $employee->id }}</td>
          <td>
            <div class="d-flex align-items-center">
              <div class="avatar-sm me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold;">
                {{ substr($employee->user->full_name ?? 'U', 0, 1) }}
              </div>
              <div>
                <div style="color: #ffffff !important; font-weight: 600;">{{ $employee->user->full_name ?? 'Unknown' }}</div>
                <div style="color: #94a3b8 !important; font-size: 0.8rem;">{{ $employee->user->email ?? 'No email' }}</div>
              </div>
            </div>
          </td>
          <td style="color: #cbd5e1 !important;">{{ $employee->department }}</td>
          <td style="color: #cbd5e1 !important;">{{ $employee->designation }}</td>
          <td style="color: #cbd5e1 !important;">{{ $employee->biometric_id ?? 'N/A' }}</td>
          <td style="color: #cbd5e1 !important;">{{ $employee->leave_quota }} days</td>
          <td>
            <span class="badge {{ $employee->user->status === 'active' ? 'bg-success' : 'bg-danger' }}" 
                  style="font-size: 0.75rem; padding: 4px 8px;">
              {{ ucfirst($employee->user->status ?? 'inactive') }}
            </span>
          </td>
          <td>
            <div class="btn-group" role="group">
              <button class="btn btn-outline-info btn-sm" onclick="viewEmployee({{ $employee->id }})" title="View Details">
                <i data-feather="eye"></i>
              </button>
              <button class="btn btn-outline-warning btn-sm" onclick="editEmployee({{ $employee->id }})" title="Edit">
                <i data-feather="edit"></i>
              </button>
              <button class="btn btn-outline-danger btn-sm" onclick="deleteEmployee({{ $employee->id }})" title="Delete">
                <i data-feather="trash-2"></i>
              </button>
            </div>
                                    </td>
                                </tr>
                            @empty
        <tr>
          <td colspan="8" class="text-center py-4" style="color: #94a3b8 !important;">
            <i data-feather="users" class="feather-lg mb-2"></i>
            <div>No employees found</div>
          </td>
        </tr>
                            @endforelse
                        </tbody>
                    </table>
  </div>
  
  <!-- PAGINATION -->
  <div class="d-flex justify-content-between align-items-center mt-3">
    <div style="color: #94a3b8 !important;">
      Showing {{ $employees->firstItem() ?? 0 }} to {{ $employees->lastItem() ?? 0 }} of {{ $employees->total() }} employees
    </div>
    <div>
      {{ $employees->links() }}
    </div>
  </div>
</div>

<!-- Employee Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border: 1px solid rgba(59, 130, 246, 0.2);">
            <div class="modal-header" style="border-bottom: 1px solid rgba(59, 130, 246, 0.2);">
                <h5 class="modal-title text-white" id="employeeModalLabel">Add Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
            </div>
            <form id="employeeForm" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Personal Information -->
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label text-white">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" required 
                                   style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label text-white">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required
                                   style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label text-white">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label text-white">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                   style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <!-- Employee Information -->
                        <div class="col-md-6 mb-3">
                            <label for="department" class="form-label text-white">Department <span class="text-danger">*</span></label>
                            <select class="form-select" id="department" name="department" required
                                    style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;">
                                <option value="">Select Department</option>
                                <option value="IT">IT</option>
                                <option value="HR">HR</option>
                                <option value="Finance">Finance</option>
                                <option value="Operations">Operations</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Customer Service">Customer Service</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="designation" class="form-label text-white">Designation <span class="text-danger">*</span></label>
                            <select class="form-select" id="designation" name="designation" required
                                    style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;">
                                <option value="">Select Designation</option>
                                <option value="Manager">Manager</option>
                                <option value="Senior Developer">Senior Developer</option>
                                <option value="Developer">Developer</option>
                                <option value="Technician">Technician</option>
                                <option value="Analyst">Analyst</option>
                                <option value="Coordinator">Coordinator</option>
                                <option value="Assistant">Assistant</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="biometric_id" class="form-label text-white">Biometric ID</label>
                            <input type="text" class="form-control" id="biometric_id" name="biometric_id"
                                   style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="leave_quota" class="form-label text-white">Leave Quota <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="leave_quota" name="leave_quota" min="0" max="365" value="30" required
                                   style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <!-- Role and Password -->
                        <div class="col-md-6 mb-3">
                            <label for="role_id" class="form-label text-white">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role_id" name="role_id" required
                                    style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;">
                                <option value="">Select Role</option>
                                @if(isset($roles))
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ ucfirst($role->role_name) }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3" id="passwordField">
                            <label for="password" class="form-label text-white">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password"
                                   style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3" id="passwordConfirmationField" style="display: none;">
                            <label for="password_confirmation" class="form-label text-white">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                                   style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(59, 130, 246, 0.2);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" 
                            style="background: rgba(107, 114, 128, 0.8); border: 1px solid rgba(107, 114, 128, 0.3); color: #fff;">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn"
                            style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); border: none; color: #fff; font-weight: 600;">
                        <span id="submitText">Add Employee</span>
                        <span id="loadingSpinner" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Employee Details Modal -->
<div class="modal fade" id="employeeDetailsModal" tabindex="-1" aria-labelledby="employeeDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border: 1px solid rgba(59, 130, 246, 0.2);">
            <div class="modal-header" style="border-bottom: 1px solid rgba(59, 130, 246, 0.2);">
                <h5 class="modal-title text-white" id="employeeDetailsModalLabel">Employee Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
            </div>
            <div class="modal-body" id="employeeDetailsContent">
                <!-- Employee details will be loaded here -->
            </div>
            <div class="modal-footer" style="border-top: 1px solid rgba(59, 130, 246, 0.2);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        style="background: rgba(107, 114, 128, 0.8); border: 1px solid rgba(107, 114, 128, 0.3); color: #fff;">Close</button>
                <button type="button" class="btn btn-primary" id="editEmployeeBtn"
                        style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); border: none; color: #fff; font-weight: 600;">Edit Employee</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
  feather.replace();

  // Employee Modal Functions
  function viewEmployee(employeeId) {
    // Load employee details via AJAX
    fetch(`/admin/employees/${employeeId}`)
      .then(response => response.json())
      .then(data => {
        document.getElementById('employeeDetailsContent').innerHTML = `
          <div class="row">
            <div class="col-md-4">
              <div class="text-center mb-3">
                <div class="avatar-lg mx-auto mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; font-weight: bold;">
                  ${data.user.full_name.charAt(0)}
                </div>
                <h5 style="color: #ffffff !important;">${data.user.full_name}</h5>
                <p style="color: #94a3b8 !important;">${data.designation}</p>
              </div>
            </div>
            <div class="col-md-8">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label style="color: #94a3b8 !important; font-size: 0.8rem;">Email</label>
                  <div style="color: #ffffff !important;">${data.user.email || 'N/A'}</div>
                </div>
                <div class="col-md-6 mb-3">
                  <label style="color: #94a3b8 !important; font-size: 0.8rem;">Phone</label>
                  <div style="color: #ffffff !important;">${data.user.phone || 'N/A'}</div>
                </div>
                <div class="col-md-6 mb-3">
                  <label style="color: #94a3b8 !important; font-size: 0.8rem;">Department</label>
                  <div style="color: #ffffff !important;">${data.department}</div>
                </div>
                <div class="col-md-6 mb-3">
                  <label style="color: #94a3b8 !important; font-size: 0.8rem;">Biometric ID</label>
                  <div style="color: #ffffff !important;">${data.biometric_id || 'N/A'}</div>
                </div>
                <div class="col-md-6 mb-3">
                  <label style="color: #94a3b8 !important; font-size: 0.8rem;">Leave Quota</label>
                  <div style="color: #ffffff !important;">${data.leave_quota} days</div>
                </div>
                <div class="col-md-6 mb-3">
                  <label style="color: #94a3b8 !important; font-size: 0.8rem;">Status</label>
                  <span class="badge ${data.user.status === 'active' ? 'bg-success' : 'bg-danger'}">
                    ${data.user.status.charAt(0).toUpperCase() + data.user.status.slice(1)}
                  </span>
                </div>
            </div>
        </div>
    </div>
        `;
        document.getElementById('editEmployeeBtn').onclick = () => editEmployee(employeeId);
        new bootstrap.Modal(document.getElementById('employeeDetailsModal')).show();
      })
      .catch(error => {
        console.error('Error loading employee details:', error);
        alert('Error loading employee details');
      });
  }

  function editEmployee(employeeId) {
    // Load employee data for editing
    fetch(`/admin/employees/${employeeId}/edit-data`)
      .then(response => response.json())
      .then(data => {
        // Populate form with employee data
        document.getElementById('employeeForm').action = `/admin/employees/${employeeId}`;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('employeeModalLabel').textContent = 'Edit Employee';
        document.getElementById('submitText').textContent = 'Update Employee';
        
        // Populate form fields
        document.getElementById('username').value = data.user.username;
        document.getElementById('full_name').value = data.user.full_name;
        document.getElementById('email').value = data.user.email || '';
        document.getElementById('phone').value = data.user.phone || '';
        document.getElementById('department').value = data.department;
        document.getElementById('designation').value = data.designation;
        document.getElementById('biometric_id').value = data.biometric_id || '';
        document.getElementById('leave_quota').value = data.leave_quota;
        document.getElementById('role_id').value = data.user.role_id;
        
        // Hide password fields for edit
        document.getElementById('passwordField').style.display = 'none';
        document.getElementById('passwordConfirmationField').style.display = 'none';
        
        new bootstrap.Modal(document.getElementById('employeeModal')).show();
      })
      .catch(error => {
        console.error('Error loading employee data:', error);
        alert('Error loading employee data');
      });
  }

  function deleteEmployee(employeeId) {
    if (confirm('Are you sure you want to delete this employee?')) {
      fetch(`/admin/employees/${employeeId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json',
        },
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

  // Add Employee Button
  document.getElementById('addEmployeeBtn').addEventListener('click', function() {
    // Reset form for new employee
    document.getElementById('employeeForm').action = '/admin/employees';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('employeeModalLabel').textContent = 'Add Employee';
    document.getElementById('submitText').textContent = 'Add Employee';
    document.getElementById('employeeForm').reset();
    
    // Show password fields for new employee
    document.getElementById('passwordField').style.display = 'block';
    document.getElementById('passwordConfirmationField').style.display = 'block';
  });

  // Form submission
  document.getElementById('employeeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    
    // Show loading state
    submitBtn.disabled = true;
    submitText.style.display = 'none';
    loadingSpinner.style.display = 'inline-block';
    
    fetch(this.action, {
      method: this.method || 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('employeeModal')).hide();
        location.reload();
      } else {
        // Handle validation errors
        if (data.errors) {
          Object.keys(data.errors).forEach(field => {
            const input = document.getElementById(field);
            if (input) {
              input.classList.add('is-invalid');
              input.nextElementSibling.textContent = data.errors[field][0];
            }
          });
        } else {
          alert('Error: ' + (data.message || 'Unknown error'));
        }
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error saving employee');
    })
    .finally(() => {
      // Reset loading state
      submitBtn.disabled = false;
      submitText.style.display = 'inline';
      loadingSpinner.style.display = 'none';
    });
  });

  // Filter functionality
  document.getElementById('applyFilters').addEventListener('click', function() {
    const search = document.getElementById('searchInput').value;
    const department = document.getElementById('departmentFilter').value;
    const designation = document.getElementById('designationFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (department) params.append('department', department);
    if (designation) params.append('designation', designation);
    if (status) params.append('status', status);
    
    window.location.href = '{{ route("admin.employees.index") }}?' + params.toString();
  });

  document.getElementById('clearFilters').addEventListener('click', function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('departmentFilter').value = '';
    document.getElementById('designationFilter').value = '';
    document.getElementById('statusFilter').value = '';
    window.location.href = '{{ route("admin.employees.index") }}';
  });

  // Export functionality
  document.getElementById('exportBtn').addEventListener('click', function() {
    window.location.href = '{{ route("admin.employees.export") }}';
  });
</script>
@endpush