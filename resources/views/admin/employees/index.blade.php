@extends('layouts.sidebar')

@section('title', 'Employees Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Employees Management</h5>
                    <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Employee
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                    <tr>
                                        <td>{{ $employee->id }}</td>
                                        <td>{{ $employee->user->username ?? 'N/A' }}</td>
                                        <td>{{ $employee->user->email ?? 'N/A' }}</td>
                                        <td>{{ $employee->department }}</td>
                                        <td>{{ $employee->designation }}</td>
                                        <td>
                                            <span class="badge bg-{{ $employee->user->status === 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($employee->user->status ?? 'inactive') }}
                                            </span>
                                        </td>
                                        <td>
     <div class="btn-group" role="group" style="display:inline-flex; border-radius:6px; overflow:hidden;">
         <button title="View Details" onclick="viewEmployee({{ $employee->id }})"
             style="border:2px solid #17a2b8; background:none; padding:8px 14px; font-size:16px; cursor:pointer; border-right:1px solid #ddd; transition:all 0.2s ease-in-out;"
             onmouseover="this.style.backgroundColor='#17a2b8'; this.querySelector('i').style.color='white';"
             onmouseout="this.style.backgroundColor='transparent'; this.querySelector('i').style.color='#17a2b8';">
             <i class="fas fa-eye" style="color:#17a2b8; font-size:14px;"></i>
         </button>

         <button title="Edit" onclick="editEmployee({{ $employee->id }})"
             style="border:2px solid #ffc107; background:none; padding:8px 14px; font-size:16px; cursor:pointer; border-right:1px solid #ddd; transition:all 0.2s ease-in-out;"
             onmouseover="this.style.backgroundColor='#ffc107'; this.querySelector('i').style.color='white';"
             onmouseout="this.style.backgroundColor='transparent'; this.querySelector('i').style.color='#ffc107';">
             <i class="fas fa-edit" style="color:#ffc107; font-size:14px;"></i>
         </button>

         <button title="Delete" onclick="deleteEmployee({{ $employee->id }})"
             style="border:2px solid #dc3545; background:none; padding:8px 14px; font-size:16px; cursor:pointer; transition:all 0.2s ease-in-out;"
             onmouseover="this.style.backgroundColor='#dc3545'; this.querySelector('i').style.color='white';"
             onmouseout="this.style.backgroundColor='transparent'; this.querySelector('i').style.color='#dc3545';">
             <i class="fas fa-trash" style="color:#dc3545; font-size:14px;"></i>
         </button>
     </div>
</td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No employees found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($employees->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $employees->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Employee Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="employeeModalLabel">Employee Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="employeeModalBody">
                <!-- Employee details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" id="editEmployeeBtn" style="display: none;">
                    <i class="fas fa-edit"></i> Edit Employee
                </button>
                <button type="button" class="btn btn-danger" id="deleteEmployeeBtn" style="display: none;">
                    <i class="fas fa-trash"></i> Delete Employee
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this employee? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Employee</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let currentEmployeeId = null;

function viewEmployee(employeeId) {
    currentEmployeeId = employeeId;
    
    // Show only view button
    document.getElementById('editEmployeeBtn').style.display = 'none';
    document.getElementById('deleteEmployeeBtn').style.display = 'none';
    
    // Show loading state
    const modalBody = document.getElementById('employeeModalBody');
    modalBody.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Fetch employee data
    fetch(`/admin/employees/${employeeId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        credentials: 'same-origin'
    })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                const employee = data.employee;
                
                modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Personal Information</h6>
                            <p><strong>Username:</strong> ${employee.user.username || 'N/A'}</p>
                            <p><strong>Email:</strong> ${employee.user.email || 'N/A'}</p>
                            <p><strong>Phone:</strong> ${employee.user.phone || 'N/A'}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-${employee.user.status === 'active' ? 'success' : 'danger'}">
                                    ${employee.user.status ? employee.user.status.charAt(0).toUpperCase() + employee.user.status.slice(1) : 'Inactive'}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Employee Information</h6>
                            <p><strong>Department:</strong> ${employee.department || 'N/A'}</p>
                            <p><strong>Designation:</strong> ${employee.designation || 'N/A'}</p>
                            <p><strong>Biometric ID:</strong> ${employee.biometric_id || 'N/A'}</p>
                            <p><strong>Leave Quota:</strong> ${employee.leave_quota || 'N/A'} days</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Role Information</h6>
                            <p><strong>Role:</strong> ${employee.user.role ? employee.user.role.role_name : 'N/A'}</p>
                        </div>
                    </div>
                `;
                
                document.getElementById('employeeModalLabel').textContent = 'Employee Details';
                new bootstrap.Modal(document.getElementById('employeeModal')).show();
            } else {
                modalBody.innerHTML = '<div class="alert alert-danger">Error: ' + (data.message || 'Unknown error occurred') + '</div>';
                new bootstrap.Modal(document.getElementById('employeeModal')).show();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = '<div class="alert alert-danger">Error loading employee details: ' + error.message + '</div>';
            new bootstrap.Modal(document.getElementById('employeeModal')).show();
        });
}

function editEmployee(employeeId) {
    // Redirect to edit page
    window.location.href = `/admin/employees/${employeeId}/edit`;
}

function deleteEmployee(employeeId) {
    currentEmployeeId = employeeId;
    
    // Set up the delete form action
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/admin/employees/${employeeId}`;
    
    // Show delete confirmation modal
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection
