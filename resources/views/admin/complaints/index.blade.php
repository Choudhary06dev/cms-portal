@extends('layouts.sidebar')

@section('title', 'Complaints Management — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2" >Complaints Management</h2>
      <p class="text-light" >Track and manage customer complaints</p>
    </div>
    <a href="{{ route('admin.complaints.create') }}" class="btn btn-accent">
      <i data-feather="plus" class="me-2"></i>Add Complaint
    </a>
  </div>
</div>

<!-- FILTERS -->
<div class="card-glass mb-4">
  <form method="GET" action="{{ route('admin.complaints.index') }}">
    <div class="row g-3">
      <div class="col-md-3">
        <input type="text" class="form-control" name="search" placeholder="Search complaints..." 
               value="{{ request('search') }}">
      </div>
      <div class="col-md-2">
        <select class="form-select" name="status">
          <option value="">All Status</option>
          <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
          <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
          <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
          <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
          <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
        </select>
      </div>
      <div class="col-md-2">
        <select class="form-select" name="priority">
          <option value="">All Priority</option>
          <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
          <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
          <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
          <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
        </select>
      </div>
      <div class="col-md-2">
        <select class="form-select" name="category">
          <option value="">All Categories</option>
          <option value="technical" {{ request('category') == 'technical' ? 'selected' : '' }}>Technical</option>
          <option value="service" {{ request('category') == 'service' ? 'selected' : '' }}>Service</option>
          <option value="billing" {{ request('category') == 'billing' ? 'selected' : '' }}>Billing</option>
          <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
        </select>
      </div>
      <div class="col-md-3">
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-outline-light btn-sm">
            <i class="fas fa-filter me-1"></i>Apply
          </button>
          <a href="{{ route('admin.complaints.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-times me-1"></i>Clear
          </a>
          <button type="button" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-download me-1"></i>Export
          </button>
        </div>
      </div>
    </div>
  </form>
</div>

<!-- COMPLAINTS TABLE -->
<div class="card-glass">
  <div class="table-responsive">
        <table class="table table-dark">
      <thead>
        <tr>
          <th >#</th>
          <th >Complaint</th>
          <th >Client</th>
          <th >Category</th>
          <th >Priority</th>
          <th >Status</th>
          <th >Assigned To</th>
          <th >Created</th>
          <th >Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($complaints as $complaint)
        <tr>
          <td >{{ $complaint->id }}</td>
          <td>
            <div style="color: #ffffff !important; font-weight: 600;">{{ $complaint->title }}</div>
            <div style="color: #94a3b8 !important; font-size: 0.8rem;">{{ Str::limit($complaint->description, 50) }}</div>
          </td>
          <td >{{ $complaint->client->client_name ?? 'N/A' }}</td>
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
          <td >{{ $complaint->assigned_to ?? 'Unassigned' }}</td>
          <td >{{ $complaint->created_at->format('M d, Y') }}</td>
          <td>
            <div class="btn-group" role="group" style="display:inline-flex; border-radius:6px; overflow:hidden;">
              <button title="View Details" onclick="viewComplaint({{ $complaint->id }})"
                  style="border:2px solid #17a2b8; background:none; padding:8px 14px; font-size:16px; cursor:pointer; border-right:1px solid #ddd; transition:all 0.2s ease-in-out;"
                  onmouseover="this.style.backgroundColor='#17a2b8'; this.querySelector('i').style.color='white';"
                  onmouseout="this.style.backgroundColor='transparent'; this.querySelector('i').style.color='#17a2b8';">
                  <i class="fas fa-eye" style="color:#17a2b8; font-size:14px;"></i>
              </button>
              <button title="Edit" onclick="editComplaint({{ $complaint->id }})"
                  style="border:2px solid #ffc107; background:none; padding:8px 14px; font-size:16px; cursor:pointer; border-right:1px solid #ddd; transition:all 0.2s ease-in-out;"
                  onmouseover="this.style.backgroundColor='#ffc107'; this.querySelector('i').style.color='white';"
                  onmouseout="this.style.backgroundColor='transparent'; this.querySelector('i').style.color='#ffc107';">
                  <i class="fas fa-edit" style="color:#ffc107; font-size:14px;"></i>
              </button>
              <button title="Delete" onclick="deleteComplaint({{ $complaint->id }})"
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
          <td colspan="9" class="text-center py-4" >
            <i data-feather="alert-circle" class="feather-lg mb-2"></i>
            <div>No complaints found</div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <!-- PAGINATION -->
  <div class="d-flex justify-content-between align-items-center mt-3">
    <div >
      Showing {{ $complaints->firstItem() ?? 0 }} to {{ $complaints->lastItem() ?? 0 }} of {{ $complaints->total() }} complaints
    </div>
    <div>
      {{ $complaints->links() }}
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .category-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  .category-technical { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
  .category-service { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
  .category-billing { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
  .category-other { background: rgba(107, 114, 128, 0.2); color: #6b7280; }
  
  .priority-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  .priority-low { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
  .priority-medium { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
  .priority-high { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
  .priority-urgent { background: rgba(139, 92, 246, 0.2); color: #8b5cf6; }
  
  .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  .status-new { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
  .status-assigned { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
  .status-in-progress { background: rgba(168, 85, 247, 0.2); color: #a855f7; }
  .status-resolved { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
  .status-closed { background: rgba(107, 114, 128, 0.2); color: #6b7280; }
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
                <button type="button" class="btn btn-warning" id="editComplaintBtn" style="display: none;">
                    <i class="fas fa-edit"></i> Edit Complaint
                </button>
                <button type="button" class="btn btn-danger" id="deleteComplaintBtn" style="display: none;">
                    <i class="fas fa-trash"></i> Delete Complaint
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
  let currentComplaintId = null;

  // Complaint Functions
  function viewComplaint(complaintId) {
    currentComplaintId = complaintId;
    
    // Show only view button
    document.getElementById('editComplaintBtn').style.display = 'none';
    document.getElementById('deleteComplaintBtn').style.display = 'none';
    
    // Show loading state
    const modalBody = document.getElementById('complaintModalBody');
    modalBody.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
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
                            <h6>Complaint Information</h6>
                            <p><strong>Title:</strong> ${complaint.title || 'N/A'}</p>
                            <p><strong>Description:</strong> ${complaint.description || 'N/A'}</p>
                            <p><strong>Category:</strong> 
                                <span class="badge bg-${complaint.category === 'technical' ? 'primary' : complaint.category === 'service' ? 'success' : complaint.category === 'billing' ? 'warning' : 'secondary'}">
                                    ${complaint.category ? complaint.category.charAt(0).toUpperCase() + complaint.category.slice(1) : 'N/A'}
                                </span>
                            </p>
                            <p><strong>Priority:</strong> 
                                <span class="badge bg-${complaint.priority === 'low' ? 'success' : complaint.priority === 'medium' ? 'warning' : complaint.priority === 'high' ? 'danger' : 'purple'}">
                                    ${complaint.priority ? complaint.priority.charAt(0).toUpperCase() + complaint.priority.slice(1) : 'N/A'}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Client & Assignment</h6>
                            <p><strong>Client:</strong> ${complaint.client ? complaint.client.client_name : 'N/A'}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-${complaint.status === 'new' ? 'primary' : complaint.status === 'assigned' ? 'warning' : complaint.status === 'in_progress' ? 'info' : complaint.status === 'resolved' ? 'success' : 'secondary'}">
                                    ${complaint.status ? complaint.status.charAt(0).toUpperCase() + complaint.status.slice(1) : 'N/A'}
                                </span>
                            </p>
                            <p><strong>Assigned To:</strong> ${complaint.assigned_to || 'Unassigned'}</p>
                            <p><strong>Created:</strong> ${complaint.created_at || 'N/A'}</p>
                        </div>
                    </div>
                `;
                
                document.getElementById('complaintModalLabel').textContent = 'Complaint Details';
                new bootstrap.Modal(document.getElementById('complaintModal')).show();
            } else {
                modalBody.innerHTML = '<div class="alert alert-danger">Error: ' + (data.message || 'Unknown error occurred') + '</div>';
                new bootstrap.Modal(document.getElementById('complaintModal')).show();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = '<div class="alert alert-danger">Error loading complaint details: ' + error.message + '</div>';
            new bootstrap.Modal(document.getElementById('complaintModal')).show();
        });
  }

  function editComplaint(complaintId) {
    // Redirect to edit page
    window.location.href = `/admin/complaints/${complaintId}/edit`;
  }

  function deleteComplaint(complaintId) {
    currentComplaintId = complaintId;
    
    // Set up the delete form action
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/admin/complaints/${complaintId}`;
    
    // Show delete confirmation modal
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
  }

  // Auto-submit filters on change
  document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.querySelector('form[method="GET"]');
    const filterSelects = document.querySelectorAll('select[name="status"], select[name="priority"], select[name="category"]');
    
    filterSelects.forEach(select => {
      select.addEventListener('change', function() {
        // Add a small delay to prevent multiple rapid requests
        setTimeout(() => {
          filterForm.submit();
        }, 100);
      });
    });
    
    // Add search functionality with debounce
    const searchInput = document.querySelector('input[name="search"]');
    let searchTimeout;
    
    if (searchInput) {
      searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          filterForm.submit();
        }, 500); // Wait 500ms after user stops typing
      });
    }
  });
</script>
@endpush