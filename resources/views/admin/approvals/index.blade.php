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
      <button class="btn btn-accent" id="bulkApproveBtn" type="button" disabled>
        <i data-feather="check-circle" class="me-2"></i>Bulk Approve
      </button>
    </div>
  </div>
</div>

<!-- FILTERS -->
<div class="card-glass mb-4">
  <form method="GET" action="{{ route('admin.approvals.index') }}">
  <div class="row g-3">
    <div class="col-md-3">
        <input type="text" class="form-control" name="search" placeholder="Search approvals..." 
               value="{{ request('search') }}">
    </div>
    <div class="col-md-2">
        <select class="form-select" name="status">
        <option value="">All Status</option>
          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
          <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
      </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" name="requested_by">
        <option value="">All Requesters</option>
          @foreach($employees as $employee)
          <option value="{{ $employee->id }}" {{ request('requested_by') == $employee->id ? 'selected' : '' }}>
            {{ $employee->user->username }}
          </option>
          @endforeach
      </select>
    </div>
      <div class="col-md-5">
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-outline-secondary btn-sm">
            <i data-feather="filter" class="me-1"></i>Apply
          </button>
          <a href="{{ route('admin.approvals.index') }}" class="btn btn-outline-secondary btn-sm">
            <i data-feather="x" class="me-1"></i>Clear
          </a>
          <button type="button" class="btn btn-outline-primary btn-sm">
            <i data-feather="download" class="me-1"></i>Export
          </button>
        </div>
      </div>
    </div>
    
    <!-- Date Range Filter -->
    <div class="row g-3 mt-2">
      <div class="col-md-3">
        <input type="date" class="form-control" name="date_from" 
               value="{{ request('date_from') }}" placeholder="From Date">
      </div>
      <div class="col-md-3">
        <input type="date" class="form-control" name="date_to" 
               value="{{ request('date_to') }}" placeholder="To Date">
      </div>
      <div class="col-md-6">
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-outline-info btn-sm">
            <i data-feather="calendar" class="me-1"></i>Filter by Date
          </button>
        </div>
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
          <th>
            <input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes()">
          </th>
          <th>#</th>
          <th>Request</th>
          <th>Type</th>
          <th>Requested By</th>
          <th>Status</th>
          <th>Requested</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($approvals as $approval)
        <tr>
          <td>
            @if($approval->status === 'pending')
              <input type="checkbox" class="approval-checkbox" value="{{ $approval->id }}" onchange="updateBulkActions()">
            @endif
          </td>
          <td>{{ $approval->id }}</td>
          <td>
            <div class="text-white fw-bold">{{ $approval->complaint->title ?? 'No complaint title' }}</div>
            <div class="text-muted small">{{ Str::limit($approval->remarks ?? 'No remarks', 50) }}</div>
          </td>
          <td>
            <span class="type-badge type-spare">
              {{ $approval->items->count() > 0 ? 'Spare Parts (' . $approval->items->count() . ' items)' : 'Spare Parts' }}
            </span>
          </td>
          <td>{{ $approval->requestedBy->user->username ?? 'N/A' }}</td>
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
          <td colspan="8" class="text-center py-4">
            <i data-feather="check-circle" class="feather-lg mb-2"></i>
            <div>No approval requests found</div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <!-- PAGINATION -->
  <div class="d-flex justify-content-center mt-3">
    <div>
      {{ $approvals->links() }}
    </div>
  </div>
</div>

<!-- Bulk Approve Modal -->
<div class="modal fade" id="bulkApproveModal" tabindex="-1" aria-labelledby="bulkApproveModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bulkApproveModalLabel">Bulk Approve Requests</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="bulkRemarks" class="form-label">Approval Remarks (Optional)</label>
          <textarea class="form-control" id="bulkRemarks" rows="3" placeholder="Enter any remarks for the bulk approval..."></textarea>
        </div>
        <div class="alert alert-info">
          <i data-feather="info" class="me-2"></i>
          <span id="selectedCount">0</span> approval(s) will be processed.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger me-2" onclick="processBulkReject()">
          <i data-feather="x-circle" class="me-2"></i>Reject Selected
        </button>
        <button type="button" class="btn btn-success" id="confirmBulkApprove" onclick="processBulkApprove()">
          <i data-feather="check-circle" class="me-2"></i>Approve Selected
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Approval Details Modal -->
<div class="modal fade" id="approvalDetailsModal" tabindex="-1" aria-labelledby="approvalDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content" style="background: rgba(0, 0, 0, 0.9); border: 1px solid rgba(59, 130, 246, 0.3);">
      <div class="modal-header" style="background: rgba(0, 0, 0, 0.9); border-bottom: 1px solid rgba(59, 130, 246, 0.3);">
        <h5 class="modal-title text-white" id="approvalDetailsModalLabel">Approval Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
      </div>
      <div class="modal-body" id="approvalDetailsModalBody" style="background: rgba(0, 0, 0, 0.9);">
        <div class="text-center">
          <div class="spinner-border text-white" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="background: rgba(0, 0, 0, 0.9); border-top: 1px solid rgba(59, 130, 246, 0.3);">
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
  
  /* Bulk button styling */
  #bulkApproveBtn {
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: relative !important;
    z-index: 1000 !important;
    pointer-events: auto !important;
    cursor: pointer !important;
  }
  
  #bulkApproveBtn:disabled,
  #bulkApproveBtn.disabled {
    opacity: 0.5 !important;
    cursor: not-allowed !important;
    display: inline-block !important;
    visibility: visible !important;
    pointer-events: none !important;
  }
  
  #bulkApproveBtn:disabled:hover,
  #bulkApproveBtn.disabled:hover {
    opacity: 0.5 !important;
  }
  
  /* Force button to be clickable */
  #bulkApproveBtn:not(:disabled) {
    pointer-events: auto !important;
    cursor: pointer !important;
    opacity: 1 !important;
  }
  
  
  /* Force button to be clickable */
  #bulkApproveBtn {
    position: relative !important;
    z-index: 1000 !important;
  }
</style>
@endpush

@push('scripts')
<script>
  feather.replace();

  // Global variables
  let selectedApprovals = [];
  let currentApprovalId = null;
  let isProcessing = false;

  // Utility Functions
  function refreshPage() {
    console.log('Refreshing page...');
    location.reload();
  }

  // Handle bulk button click
  function handleBulkButtonClick(e) {
    console.log('Bulk button clicked');
    console.log('Button disabled state:', this.disabled);
    
    if (this.disabled) {
      e.preventDefault();
      e.stopPropagation();
      console.log('Button is disabled, preventing click');
      showError('Please select at least one approval first');
      return false;
    }
  }

  // Handle checkbox change
  function handleCheckboxChange() {
    console.log('Checkbox changed:', this.checked, this.value);
    updateBulkActions();
  }

  // Force show bulk button
  function forceShowBulkButton() {
    const bulkButton = document.getElementById('bulkApproveBtn');
    if (bulkButton) {
      bulkButton.style.display = 'inline-block';
      bulkButton.style.visibility = 'visible';
      bulkButton.style.opacity = '1';
      bulkButton.style.position = 'relative';
      bulkButton.style.zIndex = '1000';
      console.log('Bulk button forced to be visible');
    }
  }

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
                  <th style="color: var(--text-primary);">Quantity</th>
                </tr>
              </thead>
              <tbody>
                ${approval.items.map(item => `
                  <tr style="color: var(--text-primary);">
                    <td style="color: var(--text-secondary);">${item.spare_name}</td>
                    <td style="color: var(--text-secondary);">${item.quantity_requested}</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    `;
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

  // Bulk Operations Functions
  function toggleAllCheckboxes() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.approval-checkbox');
    
    checkboxes.forEach(checkbox => {
      checkbox.checked = selectAll.checked;
    });
    
    updateBulkActions();
  }

  function updateBulkActions() {
    try {
      const checkboxes = document.querySelectorAll('.approval-checkbox:checked');
      selectedApprovals = Array.from(checkboxes).map(cb => cb.value);
      
      console.log('Selected approvals count:', selectedApprovals.length);
      console.log('Selected approvals:', selectedApprovals);
      
      const bulkButton = document.getElementById('bulkApproveBtn');
      const selectedCount = document.getElementById('selectedCount');
      
      if (selectedCount) {
        selectedCount.textContent = selectedApprovals.length;
      }
      
      if (bulkButton) {
        if (selectedApprovals.length === 0) {
          bulkButton.disabled = true;
          bulkButton.setAttribute('disabled', 'disabled');
          bulkButton.classList.add('disabled');
          bulkButton.style.opacity = '0.5';
          bulkButton.style.cursor = 'not-allowed';
          bulkButton.style.pointerEvents = 'none';
        } else {
          bulkButton.disabled = false;
          bulkButton.removeAttribute('disabled');
          bulkButton.classList.remove('disabled');
          bulkButton.style.opacity = '1';
          bulkButton.style.cursor = 'pointer';
          bulkButton.style.pointerEvents = 'auto';
        }
        console.log('Bulk button disabled:', bulkButton.disabled);
        console.log('Bulk button element:', bulkButton);
        console.log('Bulk button styles:', {
          display: bulkButton.style.display,
          visibility: bulkButton.style.visibility,
          opacity: bulkButton.style.opacity,
          cursor: bulkButton.style.cursor,
          pointerEvents: bulkButton.style.pointerEvents
        });
      } else {
        console.error('Bulk button not found');
      }
    } catch (error) {
      console.error('Error in updateBulkActions:', error);
    }
  }

  function showBulkApproveModal() {
    console.log('showBulkApproveModal called');
    console.log('Selected approvals:', selectedApprovals);
    
    if (isProcessing) {
      showError('Please wait, another operation is in progress');
      return;
    }
    
    // Re-check selected approvals
    const checkboxes = document.querySelectorAll('.approval-checkbox:checked');
    const currentSelected = Array.from(checkboxes).map(cb => cb.value);
    
    console.log('Current selected checkboxes:', currentSelected);
    console.log('Current selected length:', currentSelected.length);
    
    if (currentSelected.length === 0) {
      showError('Please select at least one approval to process');
      return;
    }
    
    // Update selected approvals
    selectedApprovals = currentSelected;
    console.log('Updated selectedApprovals:', selectedApprovals);
    
    const modalElement = document.getElementById('bulkApproveModal');
    if (!modalElement) {
      console.error('Bulk approve modal not found');
      showError('Modal not found');
      return;
    }
    
    // Update selected count in modal
    const selectedCount = document.getElementById('selectedCount');
    if (selectedCount) {
      selectedCount.textContent = selectedApprovals.length;
      console.log('Updated selected count:', selectedApprovals.length);
    }
    
    // Clear remarks field
    const remarksField = document.getElementById('bulkRemarks');
    if (remarksField) {
      remarksField.value = '';
    }
    
    try {
      const modal = new bootstrap.Modal(modalElement);
      modal.show();
      console.log('Modal shown successfully');
    } catch (error) {
      console.error('Error showing modal:', error);
      showError('Error opening modal');
    }
  }

  function processBulkApprove() {
    console.log('processBulkApprove called');
    
    if (isProcessing) {
      showError('Please wait, another operation is in progress');
      return;
    }
    
    const remarks = document.getElementById('bulkRemarks').value;
    
    if (selectedApprovals.length === 0) {
      showError('No approvals selected');
      return;
    }
    
    if (confirm(`Are you sure you want to approve ${selectedApprovals.length} selected approval(s)?`)) {
      isProcessing = true;
      
      // Disable buttons during processing
      const approveBtn = document.getElementById('confirmBulkApprove');
      const rejectBtn = document.querySelector('button[onclick="processBulkReject()"]');
      
      if (approveBtn) {
        approveBtn.disabled = true;
        approveBtn.innerHTML = '<i data-feather="loader" class="me-2"></i>Processing...';
        feather.replace();
      }
      
      if (rejectBtn) {
        rejectBtn.disabled = true;
      }
      
      console.log('Sending bulk approve request:', {
        action: 'approve',
        approval_ids: selectedApprovals,
        remarks: remarks
      });
      
      fetch('/admin/approvals/bulk-action', {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          action: 'approve',
          approval_ids: selectedApprovals,
          remarks: remarks
        })
      })
      .then(response => {
        console.log('Response status:', response.status);
        return response.json();
      })
      .then(data => {
        console.log('Response data:', data);
        isProcessing = false;
        
        if (data.success) {
          showSuccess(data.message || 'Approvals processed successfully!');
          bootstrap.Modal.getInstance(document.getElementById('bulkApproveModal')).hide();
          setTimeout(() => {
            location.reload();
          }, 1000);
        } else {
          showError(data.message || 'Failed to process bulk approval');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        isProcessing = false;
        showError('Error processing bulk approval: ' + error.message);
      })
      .finally(() => {
        // Re-enable buttons
        if (approveBtn) {
          approveBtn.disabled = false;
          approveBtn.innerHTML = '<i data-feather="check-circle" class="me-2"></i>Approve Selected';
          feather.replace();
        }
        
        if (rejectBtn) {
          rejectBtn.disabled = false;
        }
      });
    }
  }

  function processBulkReject() {
    console.log('processBulkReject called');
    
    if (isProcessing) {
      showError('Please wait, another operation is in progress');
      return;
    }
    
    const remarks = prompt('Please enter rejection reason for all selected approvals:');
    if (remarks === null) return; // User cancelled
    
    if (!remarks.trim()) {
      showError('Rejection reason is required');
      return;
    }
    
    if (selectedApprovals.length === 0) {
      showError('No approvals selected');
      return;
    }
    
    if (confirm(`Are you sure you want to reject ${selectedApprovals.length} selected approval(s)?`)) {
      isProcessing = true;
      
      // Disable buttons during processing
      const approveBtn = document.getElementById('confirmBulkApprove');
      const rejectBtn = document.querySelector('button[onclick="processBulkReject()"]');
      
      if (rejectBtn) {
        rejectBtn.disabled = true;
        rejectBtn.innerHTML = '<i data-feather="loader" class="me-2"></i>Processing...';
        feather.replace();
      }
      
      if (approveBtn) {
        approveBtn.disabled = true;
      }
      
      console.log('Sending bulk reject request:', {
        action: 'reject',
        approval_ids: selectedApprovals,
        remarks: remarks
      });
      
      fetch('/admin/approvals/bulk-action', {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          action: 'reject',
          approval_ids: selectedApprovals,
          remarks: remarks
        })
      })
      .then(response => {
        console.log('Response status:', response.status);
        return response.json();
      })
      .then(data => {
        console.log('Response data:', data);
        isProcessing = false;
        
        if (data.success) {
          showSuccess(data.message || 'Approvals rejected successfully!');
          bootstrap.Modal.getInstance(document.getElementById('bulkApproveModal')).hide();
          setTimeout(() => {
            location.reload();
          }, 1000);
        } else {
          showError(data.message || 'Failed to process bulk rejection');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        isProcessing = false;
        showError('Error processing bulk rejection: ' + error.message);
      })
      .finally(() => {
        // Re-enable buttons
        if (rejectBtn) {
          rejectBtn.disabled = false;
          rejectBtn.innerHTML = '<i data-feather="x-circle" class="me-2"></i>Reject Selected';
          feather.replace();
        }
        
        if (approveBtn) {
          approveBtn.disabled = false;
        }
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

  // Initialize on page load
  document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing bulk actions');
    
    try {
      // Wait a bit for DOM to be fully ready
      setTimeout(() => {
        initializeBulkActions();
        setupBulkButtonClick();
      }, 500);
      
    } catch (error) {
      console.error('Error initializing bulk actions:', error);
    }
  });

  // Setup bulk button click handler
  function setupBulkButtonClick() {
    console.log('Setting up bulk button click handler...');
    
    const bulkButton = document.getElementById('bulkApproveBtn');
    if (!bulkButton) {
      console.error('Bulk button not found!');
      return;
    }
    
    console.log('Bulk button found:', bulkButton);
    
    // Remove all existing event listeners
    const newButton = bulkButton.cloneNode(true);
    bulkButton.parentNode.replaceChild(newButton, bulkButton);
    
    // Get the new button reference
    const freshButton = document.getElementById('bulkApproveBtn');
    
    // Add click event listener
    freshButton.addEventListener('click', function(e) {
      console.log('BULK BUTTON CLICKED!');
      console.log('Event:', e);
      console.log('Button:', this);
      console.log('Disabled:', this.disabled);
      
      e.preventDefault();
      e.stopPropagation();
      
      if (this.disabled || selectedApprovals.length === 0) {
        console.log('Button is disabled or no approvals selected, showing error');
        alert('Please select at least one approval to process');
        return false;
      }
      
      console.log('Opening bulk approve modal...');
      showBulkApproveModal();
    });
    
    // Force enable for testing
    freshButton.disabled = false;
    freshButton.removeAttribute('disabled');
    freshButton.classList.remove('disabled');
    freshButton.style.pointerEvents = 'auto';
    freshButton.style.cursor = 'pointer';
    freshButton.style.opacity = '1';
    
    console.log('Bulk button setup complete');
    
    
    feather.replace();
  }

  // Initialize bulk actions
  function initializeBulkActions() {
    console.log('Initializing bulk actions...');
    
    // Update bulk actions
    updateBulkActions();
    
    // Setup checkboxes
    setupCheckboxes();
  }

  // Setup checkboxes
  function setupCheckboxes() {
    function initializeCheckboxes() {
      const checkboxes = document.querySelectorAll('.approval-checkbox');
      console.log('Found checkboxes:', checkboxes.length);
      
      if (checkboxes.length === 0) {
        console.log('No checkboxes found, will retry after a short delay');
        setTimeout(initializeCheckboxes, 1000);
        return;
      }
      
      checkboxes.forEach((checkbox, index) => {
        // Remove existing listener
        checkbox.removeEventListener('change', handleCheckboxChange);
        
        // Add new listener
        checkbox.addEventListener('change', function() {
          console.log('Checkbox changed:', this.checked, this.value);
          updateBulkActions();
        });
      });
      
      console.log('Checkboxes initialized successfully');
    }
    
    // Initialize checkboxes
    initializeCheckboxes();
  }
</script>
@endpush
