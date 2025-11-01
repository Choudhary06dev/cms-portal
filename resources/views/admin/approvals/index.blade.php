@extends('layouts.sidebar')

@section('title', 'Approvals Management — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Approvals Management</h2>
      <p class="text-light">Manage approval workflows and requests</p>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-secondary" onclick="refreshPage()">
        <i data-feather="refresh-cw" class="me-2"></i>Refresh
      </button>
    </div>
  </div>
</div>

<!-- FILTERS -->
<div class="card-glass mb-4">
  <form id="approvalsFiltersForm" method="GET" action="{{ route('admin.approvals.index') }}">
  <div class="row g-2 align-items-end">
    <div class="col-12 col-md-3">
        <input type="text" class="form-control" id="searchInput" name="search" placeholder="Search approvals..." 
               value="{{ request('search') }}" oninput="handleApprovalsSearchInput()">
    </div>
    <div class="col-6 col-md-2">
        <select class="form-select" name="status" onchange="submitApprovalsFilters()">
        <option value="" {{ request('status') ? '' : 'selected' }}>All Status</option>
          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
          <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
      </select>
    </div>
    <div class="col-6 col-md-3">
        <select class="form-select" name="requested_by" onchange="submitApprovalsFilters()">
        <option value="" {{ request('requested_by') ? '' : 'selected' }}>All Complaints</option>
          @foreach($employees as $employee)
          <option value="{{ $employee->id }}" {{ request('requested_by') == $employee->id ? 'selected' : '' }}>
            {{ $employee->name }}
          </option>
          @endforeach
      </select>
    </div>
    <div class="col-6 col-md-2">
      <input type="date" class="form-control" name="date_from" 
             value="{{ request('date_from') }}" placeholder="From Date" onchange="submitApprovalsFilters()">
    </div>
    <div class="col-6 col-md-2">
      <input type="date" class="form-control" name="date_to" 
             value="{{ request('date_to') }}" placeholder="To Date" onchange="submitApprovalsFilters()">
    </div>
  </div>
  </form>
  </div>
</div>

<!-- APPROVALS TABLE -->
<div class="card-glass">
  <div class="table-responsive">
        <table class="table table-dark">
      <thead>
        <tr>
          <th>#</th>
          <th>Complaint</th>
          <th>Product Name</th>
          <th>Quantity-Required</th>
          <th>Quantity Approved</th>
          <th>Assigned to</th>
          <th>Status</th>
          <th>Complaint-Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="approvalsTableBody">
        @forelse($approvals as $approval)
        <tr>
          <td>{{ $approval->id }}</td>
          <td>
            <div class="text-white fw-bold">{{ $approval->complaint->title ?? 'No complaint title' }}</div>
            <div class="text-muted small">{{ Str::limit($approval->remarks ?? 'No remarks', 50) }}</div>
          </td>
          <td>
            {{ optional($approval->items->first())->spare_name ?? 'N/A' }}
            @if($approval->items->count() > 1)
              <span class="text-muted small">(+{{ $approval->items->count() - 1 }} more)</span>
            @endif
          </td>
          <td>{{ $approval->items->sum('quantity_requested') }}</td>
          <td>{{ $approval->items->sum('quantity_approved') }}</td>
          <td>{{ $approval->complaint->assignedEmployee->name ?? 'N/A' }}</td>
          <td>
            <span class="status-badge status-{{ strtolower($approval->status) }}">
              {{ ucfirst($approval->status) }}
            </span>
          </td>
          <td>{{ $approval->created_at->format('M d, Y') }}</td>
          <td>
            <div class="btn-group" role="group">
              <button class="btn btn-outline-info btn-sm" onclick="viewApproval({{ $approval->id }})" title="View Details">
                <i data-feather="eye"></i>
              </button>
              @if($approval->status === 'pending')
              <button class="btn btn-outline-success btn-sm" onclick="approveRequest({{ $approval->id }})" title="Approve">
                <i data-feather="check"></i>
              </button>
              <button class="btn btn-outline-danger btn-sm" onclick="rejectRequest({{ $approval->id }})" title="Reject">
                <i data-feather="x"></i>
              </button>
              @endif
            </div>
          </td>
        </tr>
          @empty
        <tr>
          <td colspan="9" class="text-center py-4">
            <i data-feather="check-circle" class="feather-lg mb-2"></i>
            <div>No approval requests found</div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <!-- PAGINATION -->
  <div class="d-flex justify-content-center mt-3" id="approvalsPagination">
    <div>
      {{ $approvals->links() }}
    </div>
  </div>
</div>

<!-- Approval Details Modal -->
<div class="modal fade" id="approvalDetailsModal" tabindex="-1" aria-labelledby="approvalDetailsModalLabel" aria-hidden="true">
<div class="modal-dialog modal-xl">
  <div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title text-white" id="approvalDetailsModalLabel">Approval Details</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
    <div class="modal-body" id="approvalDetailsModalBody">
        <div class="text-center">
          <div class="spinner-border text-white" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .type-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  .type-spare { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
  
  .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  .status-pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
  .status-approved { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
  .status-rejected { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
  
  /* Table text styling for all themes */
  .table td {
    color: #1e293b !important;
  }
  
  .theme-dark .table td,
  .theme-night .table td {
    color: #f1f5f9 !important;
  }
  
  .table .text-muted {
    color: #64748b !important;
  }
  
  .theme-dark .table .text-muted,
  .theme-night .table .text-muted {
    color: #94a3b8 !important;
  }
  
</style>
@endpush

@push('scripts')
<script>
  feather.replace();

  // Global variables
  let currentApprovalId = null;
  let isProcessing = false;

  // Utility Functions
  function refreshPage() {
    console.log('Refreshing page...');
    location.reload();
  }

  // Debounced search input handler
  let approvalsSearchTimeout = null;
  function handleApprovalsSearchInput() {
    if (approvalsSearchTimeout) clearTimeout(approvalsSearchTimeout);
    approvalsSearchTimeout = setTimeout(() => {
      loadApprovals();
    }, 500);
  }

  // Auto-submit for select filters
  function submitApprovalsFilters() {
    loadApprovals();
  }

  // Load Approvals via AJAX
  function loadApprovals(url = null) {
    const form = document.getElementById('approvalsFiltersForm');
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

    const tbody = document.getElementById('approvalsTableBody');
    const paginationContainer = document.getElementById('approvalsPagination');
    
    if (tbody) {
      tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4"><div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
    }

    fetch(`{{ route('admin.approvals.index') }}?${params.toString()}`, {
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
      
      const newTbody = doc.querySelector('#approvalsTableBody');
      const newPagination = doc.querySelector('#approvalsPagination');
      
      if (newTbody && tbody) {
        tbody.innerHTML = newTbody.innerHTML;
        feather.replace();
      }
      
      if (newPagination && paginationContainer) {
        paginationContainer.innerHTML = newPagination.innerHTML;
      }

      const newUrl = `{{ route('admin.approvals.index') }}?${params.toString()}`;
      window.history.pushState({path: newUrl}, '', newUrl);
    })
    .catch(error => {
      console.error('Error loading approvals:', error);
      if (tbody) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-danger">Error loading data. Please refresh the page.</td></tr>';
      }
    });
  }

  // Handle pagination clicks
  document.addEventListener('click', function(e) {
    const paginationLink = e.target.closest('#approvalsPagination a');
    if (paginationLink && paginationLink.href && !paginationLink.href.includes('javascript:')) {
      e.preventDefault();
      loadApprovals(paginationLink.href);
    }
  });

  // Handle browser back/forward buttons
  window.addEventListener('popstate', function(e) {
    if (e.state && e.state.path) {
      loadApprovals(e.state.path);
    } else {
      loadApprovals();
    }
  });

  // Approval Functions
  function viewApproval(approvalId) {
    currentApprovalId = approvalId;
    const modal = new bootstrap.Modal(document.getElementById('approvalDetailsModal'));
    modal.show();
    
    // Show loading spinner
    document.getElementById('approvalDetailsModalBody').innerHTML = `
      <div class="text-center">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    `;
    
    // Fetch approval details via AJAX
    fetch(`/admin/approvals/${approvalId}`, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        displayApprovalDetails(data.approval);
      } else {
        showError('Failed to load approval details');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showError('Error loading approval details');
    });
  }

  function displayApprovalDetails(approval) {
    const modalBody = document.getElementById('approvalDetailsModalBody');
    modalBody.innerHTML = `
      <div class="row" style="color: var(--text-primary);">
        <div class="col-md-6">
          <h6 class="fw-bold mb-3" style="color: var(--text-primary);">Approval Information</h6>
          <div style="color: var(--text-primary);">
            <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Approval ID:</strong> <span style="color: var(--text-secondary);">#${approval.id}</span></p>
            <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Status:</strong> <span class="badge bg-${approval.status === 'pending' ? 'warning' : approval.status === 'approved' ? 'success' : 'danger'}">${approval.status.charAt(0).toUpperCase() + approval.status.slice(1)}</span></p>
            <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Requested By:</strong> <span style="color: var(--text-secondary);">${approval.requested_by_name}</span></p>
            <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Approved By:</strong> <span style="color: var(--text-secondary);">${approval.approved_by_name || 'Not Approved'}</span></p>
            <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Created:</strong> <span style="color: var(--text-secondary);">${approval.created_at}</span></p>
            <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Approved:</strong> <span style="color: var(--text-secondary);">${approval.approved_at || 'Not Approved'}</span></p>
            <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Remarks:</strong> <span style="color: var(--text-secondary);">${approval.remarks || 'No remarks'}</span></p>
          </div>
        </div>
        <div class="col-md-6">
          <h6 class="fw-bold mb-3" style="color: var(--text-primary);">Complaint Information</h6>
          <div style="color: var(--text-primary);">
            <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Complaint ID:</strong> <span style="color: var(--text-secondary);">#${approval.complaint_id}</span></p>
            <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Client:</strong> <span style="color: var(--text-secondary);">${approval.client_name}</span></p>
            <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Title:</strong> <span style="color: var(--text-secondary);">${approval.complaint_title}</span></p>
          </div>
        </div>
      </div>
      
      <div class="row mt-4">
        <div class="col-12">
          <h6 class="fw-bold mb-3" style="color: var(--text-primary);">Requested Items (${approval.items.length})</h6>
          <div class="table-responsive">
            <table class="table table-striped" style="color: var(--text-primary);">
              <thead>
                <tr>
                  <th style="color: var(--text-primary);">Spare Part</th>
                  <th style="color: var(--text-primary);">Quantity Requested</th>
                  <th style="color: var(--text-primary);">Quantity Approved</th>
                </tr>
              </thead>
              <tbody>
                ${approval.items.map(item => `
                  <tr style="color: var(--text-primary);">
                    <td style="color: var(--text-secondary);">${item.spare_name}</td>
                    <td style="color: var(--text-secondary);">${item.quantity_requested}</td>
                    <td style="max-width:160px;">
                      ${approval.status === 'pending' 
                        ? `<input type="number" min="0" class="form-control form-control-sm" name="items[${item.id}][quantity_approved]" value="${item.quantity_approved ?? item.quantity_requested}">`
                        : `<span style='color: var(--text-secondary);'>${item.quantity_approved ?? item.quantity_requested}</span>`}
                    </td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
          ${approval.status === 'pending' ? `
            <div class="d-flex justify-content-end mt-2">
              <button class="btn btn-success" onclick="submitApprovedQuantities(${approval.id})">
                Approve Selected Quantities
              </button>
            </div>
          ` : ''}
        </div>
      </div>
    `;
  }

  function submitApprovedQuantities(approvalId) {
    const inputs = document.querySelectorAll('#approvalDetailsModalBody input[name^="items["]');
    const payload = { items: {}, remarks: '' };
    inputs.forEach(input => {
      const match = input.name.match(/items\[(\d+)\]\[quantity_approved\]/);
      if (match) {
        const itemId = match[1];
        payload.items[itemId] = { quantity_approved: parseInt(input.value || '0', 10) };
      }
    });

    fetch(`/admin/approvals/${approvalId}/approve`, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        showSuccess('Approval approved successfully!');
        location.reload();
      } else {
        showError(data.message || 'Failed to approve request');
      }
    })
    .catch(err => {
      console.error(err);
      showError('Error approving request');
    });
  }

  function approveRequest(approvalId) {
    if (confirm('Are you sure you want to approve this request?')) {
      const remarks = prompt('Enter approval remarks (optional):');
      
      fetch(`/admin/approvals/${approvalId}/approve`, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          remarks: remarks || ''
        })
      })
      .then(response => {
        return response.json().then(data => {
          if (data.success || response.ok) {
            showSuccess('Approval approved successfully!');
            location.reload();
          } else {
            showError(data.message || 'Failed to approve request');
          }
        });
      })
      .catch(error => {
        console.error('Error:', error);
        showError('Error approving request');
      });
    }
  }

  function rejectRequest(approvalId) {
    const remarks = prompt('Please enter rejection reason (required):');
    if (remarks === null) return; // User cancelled
    
    if (!remarks.trim()) {
      showError('Rejection reason is required');
      return;
    }
    
    if (confirm('Are you sure you want to reject this request?')) {
      fetch(`/admin/approvals/${approvalId}/reject`, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          remarks: remarks
        })
      })
      .then(response => {
        return response.json().then(data => {
          if (data.success || response.ok) {
            showSuccess('Approval rejected successfully!');
            location.reload();
          } else {
            showError(data.message || 'Failed to reject request');
          }
        });
      })
      .catch(error => {
        console.error('Error:', error);
        showError('Error rejecting request');
      });
    }
  }

  // Utility Functions
  function showSuccess(message) {
    // Create and show success alert
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
      <i data-feather="check-circle" class="me-2"></i>
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    feather.replace();
    
    // Auto remove after 5 seconds
    setTimeout(() => {
      if (alertDiv.parentNode) {
        alertDiv.parentNode.removeChild(alertDiv);
      }
    }, 5000);
  }

  function showError(message) {
    // Create and show error alert
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed';
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
      <i data-feather="alert-circle" class="me-2"></i>
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    feather.replace();
    
    // Auto remove after 5 seconds
    setTimeout(() => {
      if (alertDiv.parentNode) {
        alertDiv.parentNode.removeChild(alertDiv);
      }
    }, 5000);
  }

</script>
@endpush
