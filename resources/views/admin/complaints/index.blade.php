@extends('layouts.sidebar')

@section('title', 'Complaints Management — CMS Admin')

@section('content')
    <!-- PAGE HEADER -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white mb-2">Complaints Management</h2>
                <p class="text-light">Track and manage customer complaints</p>
            </div>
            <a href="{{ route('admin.complaints.create') }}" class="btn btn-accent">
                <i data-feather="plus" class="me-2"></i>Add Complaint
            </a>
        </div>
    </div>

    <!-- FILTERS -->
    <div class="card-glass mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="Search complaints...">
            </div>
            <div class="col-md-2">
                <select class="form-select">
                    <option value="">All Status</option>
                    <option value="new">New</option>
                    <option value="assigned">Assigned</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select">
                    <option value="">All Priority</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select">
                    <option value="">All Categories</option>
                    <option value="technical">Technical</option>
                    <option value="service">Service</option>
                    <option value="billing">Billing</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-light btn-sm">
                        <i data-feather="filter" class="me-1"></i>Apply
                    </button>
                    <button class="btn btn-outline-secondary btn-sm">
                        <i data-feather="x" class="me-1"></i>Clear
                    </button>
                    <button class="btn btn-outline-primary btn-sm">
                        <i data-feather="download" class="me-1"></i>Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- COMPLAINTS TABLE -->
    <div class="card-glass">
        <div class="table-responsive">
            <table class="table table-dark">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Complaint</th>
                        <th>Client</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complaints as $complaint)
                        <tr>
                            <td>{{ $complaint->id }}</td>
                            <td>
                                <div style="color: #ffffff !important; font-weight: 700; font-size: 1.1rem; line-height: 1.3;">{{ $complaint->title }}</div>
                                <div style="color: #64748b !important; font-size: 0.7rem; margin-top: 3px; opacity: 0.8;">
                                    {{ Str::limit($complaint->title, 25) }}</div>
                            </td>
                            <td>{{ $complaint->client->client_name ?? 'N/A' }}</td>
                            <td>
                                <span class="category-badge category-{{ strtolower($complaint->category) }}">
                                    {{ ucfirst($complaint->category) }}
                                </span>
                            </td>
                            <td>
                                <span class="priority-badge priority-{{ strtolower($complaint->priority) }}">
                                    {{ ucfirst($complaint->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ strtolower($complaint->status) }}">
                                    {{ ucfirst($complaint->status) }}
                                </span>
                            </td>
                            <td>{{ $complaint->assignedEmployee->user->username ?? 'Unassigned' }}</td>
                            <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-outline-info btn-sm"
                                        onclick="viewComplaint({{ $complaint->id }})" title="View Details">
                                        <i data-feather="eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm"
                                        onclick="editComplaint({{ $complaint->id }})" title="Edit">
                                        <i data-feather="edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm"
                                        onclick="deleteComplaint({{ $complaint->id }})" title="Delete">
                                        <i data-feather="trash-2"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i data-feather="alert-circle" class="feather-lg mb-2"></i>
                                <div>No complaints found</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="d-flex justify-content-center mt-3">
            <div>
                {{ $complaints->links() }}
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .category-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .category-technical {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .category-service {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .category-billing {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        .category-other {
            background: rgba(107, 114, 128, 0.2);
            color: #6b7280;
        }

        .priority-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .priority-low {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .priority-medium {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        .priority-high {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .priority-urgent {
            background: rgba(139, 92, 246, 0.2);
            color: #8b5cf6;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-new {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .status-assigned {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        .status-in-progress {
            background: rgba(168, 85, 247, 0.2);
            color: #a855f7;
        }

        .status-resolved {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .status-closed {
            background: rgba(107, 114, 128, 0.2);
            color: #6b7280;
        }

        /* Pagination styles are now centralized in components/pagination.blade.php */
    </style>
@endpush

<!-- Complaint Modal -->
<div class="modal fade" id="complaintModal" tabindex="-1" aria-labelledby="complaintModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="complaintModalLabel">Complaint Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="complaintModalBody">
                <!-- Complaint details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('admin.complaints.print-slip', $complaint) }}" class="btn btn-info" target="_blank">
                    <i data-feather="printer"></i> Print Slip
                </a>
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
                Are you sure you want to delete this complaint? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Complaint</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        feather.replace();

        // Complaint Functions
        function viewComplaint(complaintId) {
            currentComplaintId = complaintId;

            // Modal buttons removed - only show and print functionality

            // Show loading state
            const modalBody = document.getElementById('complaintModalBody');
            modalBody.innerHTML =
                '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Fetch complaint data
            fetch(`/admin/complaints/${complaintId}`, {
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
                        const complaint = data.complaint;

                        modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-white fw-bold mb-3">Complaint Information</h6>
                            <div class="mb-3">
                                <span class="text-muted">Title:</span>
                                <span class="text-white ms-2">${complaint.title || 'N/A'}</span>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted">Category:</span>
                                <span class="ms-2">
                                    <span class="badge bg-${complaint.category === 'technical' ? 'primary' : complaint.category === 'service' ? 'success' : complaint.category === 'billing' ? 'warning' : 'secondary'}">
                                        ${complaint.category ? complaint.category.charAt(0).toUpperCase() + complaint.category.slice(1) : 'N/A'}
                                    </span>
                                </span>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted">Priority:</span>
                                <span class="ms-2">
                                    <span class="badge bg-${complaint.priority === 'low' ? 'success' : complaint.priority === 'medium' ? 'warning' : complaint.priority === 'high' ? 'danger' : 'purple'}">
                                        ${complaint.priority ? complaint.priority.charAt(0).toUpperCase() + complaint.priority.slice(1) : 'N/A'}
                                    </span>
                                </span>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted">Status:</span>
                                <span class="ms-2">
                                    <span class="badge bg-${complaint.status === 'new' ? 'primary' : complaint.status === 'assigned' ? 'warning' : complaint.status === 'in_progress' ? 'info' : complaint.status === 'resolved' ? 'success' : 'secondary'}">
                                        ${complaint.status ? complaint.status.charAt(0).toUpperCase() + complaint.status.slice(1) : 'N/A'}
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-white fw-bold mb-3">Client & Assignment</h6>
                            <div class="mb-3">
                                <span class="text-muted">Client:</span>
                                <span class="text-white ms-2">${complaint.client ? complaint.client.client_name : 'N/A'}</span>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted">Assigned To:</span>
                                <span class="text-white ms-2">${complaint.assigned_employee ? complaint.assigned_employee.user.username : 'Unassigned'}</span>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted">Created:</span>
                                <span class="text-white ms-2">${complaint.created_at ? new Date(complaint.created_at).toLocaleDateString() : 'N/A'}</span>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted">Last Updated:</span>
                                <span class="text-white ms-2">${complaint.updated_at ? new Date(complaint.updated_at).toLocaleDateString() : 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-white fw-bold mb-3">Description</h6>
                            <div class="card" style="background-color: rgba(255, 255, 255, 0.1);">
                                <div class="card-body">
                                    <p class="text-white">${complaint.description || 'No description provided'}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                        document.getElementById('complaintModalLabel').textContent = 'Complaint Details';
                        new bootstrap.Modal(document.getElementById('complaintModal')).show();
                    } else {
                        modalBody.innerHTML = '<div class="alert alert-danger">Error: ' + (data.message ||
                            'Unknown error occurred') + '</div>';
                        new bootstrap.Modal(document.getElementById('complaintModal')).show();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = '<div class="alert alert-danger">Error loading complaint details: ' + error
                        .message + '</div>';
                    new bootstrap.Modal(document.getElementById('complaintModal')).show();
                });
        }

        function editComplaint(complaintId) {
            // Redirect to edit page
            window.location.href = `/admin/complaints/${complaintId}/edit`;
        }

        function deleteComplaint(complaintId) {
            if (confirm('Are you sure you want to delete this complaint? This action cannot be undone.')) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/complaints/${complaintId}`;

                // Add CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);

                // Add DELETE method
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                // Submit the form
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Modal buttons removed - no event listeners needed
    </script>
@endpush
