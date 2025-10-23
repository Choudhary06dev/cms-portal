@extends('layouts.sidebar')

@section('title', 'Approvals Management — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2" >Approvals Management</h2>
      <p class="text-light" >Manage approval workflows and requests</p>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-secondary">
        <i data-feather="refresh-cw" class="me-2"></i>Refresh
      </button>
      <button class="btn btn-accent" onclick="showBulkApproveModal()">
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
          <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
      </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" name="type">
        <option value="">All Types</option>
          <option value="spare" {{ request('type') == 'spare' ? 'selected' : '' }}>Spare Parts</option>
          <option value="leave" {{ request('type') == 'leave' ? 'selected' : '' }}>Leave Request</option>
          <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense</option>
          <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }}>Purchase Order</option>
      </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" name="priority">
        <option value="">All Priorities</option>
          <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
          <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
          <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
          <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
      </select>
    </div>
      <div class="col-md-3">
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
          <th >#</th>
          <th >Request</th>
          <th >Type</th>
          <th >Requested By</th>
          <th >Priority</th>
          <th >Status</th>
          <th >Amount</th>
          <th >Requested</th>
          <th >Actions</th>
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
          <td >{{ $approval->id }}</td>
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
            <span class="priority-badge priority-medium">
              Medium
            </span>
          </td>
          <td>
            <span class="status-badge status-{{ strtolower($approval->status) }}">
              {{ ucfirst($approval->status) }}
            </span>
          </td>
          <td >${{ number_format($approval->items->sum(function($item) { return $item->spare->unit_price * $item->quantity_requested; }) ?? 0, 2) }}</td>
          <td >{{ $approval->created_at->format('M d, Y') }}</td>
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
          <td colspan="9" class="text-center py-4" >
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
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="approvalDetailsModalLabel">Approval Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="approvalDetailsModalBody">
        <div class="text-center">
          <div class="spinner-border" role="status">
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
  .type-leave { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
  .type-expense { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
  .type-purchase { background: rgba(139, 92, 246, 0.2); color: #8b5cf6; }
  
  .priority-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  .priority-low { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
  .priority-medium { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
  .priority-high { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
  .priority-urgent { background: rgba(139, 92, 246, 0.2); color: #8b5cf6; }
  
  .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  .status-pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
  .status-approved { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
  .status-rejected { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
  .status-cancelled { background: rgba(107, 114, 128, 0.2); color: #6b7280; }
  
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

  // Approval Functions
  function viewApproval(approvalId) {
    console.log('Viewing approval ID:', approvalId);
    
    // Show loading state
    const modalBody = document.getElementById('approvalDetailsModalBody');
    modalBody.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('approvalDetailsModal'));
    modal.show();
    
    // Fetch approval details
    fetch(`/admin/approvals/${approvalId}`, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        displayApprovalDetails(data.approval);
      } else {
        modalBody.innerHTML = '<div class="alert alert-danger">Error loading approval details</div>';
      }
    })
    .catch(error => {
      console.error('Error loading approval details:', error);
      modalBody.innerHTML = '<div class="alert alert-danger">Error loading approval details</div>';
    });
  }
  
  function displayApprovalDetails(approval) {
    console.log('Real approval data received:', approval);
    console.log('Items array:', approval.items);
    console.log('Items length:', approval.items ? approval.items.length : 'undefined');
    
    const modalBody = document.getElementById('approvalDetailsModalBody');
    
    modalBody.innerHTML = `
      <div class="row">
        <div class="col-md-6">
          <h6 class="text-white fw-bold mb-3">Approval Information</h6>
          <div class="mb-3">
            <span class="text-muted">Approval ID:</span>
            <span class="text-white ms-2">#${approval.id}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Status:</span>
            <span class="ms-2">
              <span class="status-badge status-${approval.status.toLowerCase()}">${approval.status.charAt(0).toUpperCase() + approval.status.slice(1)}</span>
            </span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Requested By:</span>
            <span class="text-white ms-2">${approval.requested_by_name || 'N/A'}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Requested At:</span>
            <span class="text-white ms-2">${approval.created_at || 'N/A'}</span>
          </div>
        </div>
        
        <div class="col-md-6">
          <h6 class="text-white fw-bold mb-3">Complaint Information</h6>
          <div class="mb-3">
            <span class="text-muted">Complaint ID:</span>
            <span class="text-white ms-2">#${approval.complaint_id || 'N/A'}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Client:</span>
            <span class="text-white ms-2">${approval.client_name || 'N/A'}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Complaint Title:</span>
            <span class="text-white ms-2">${approval.complaint_title || 'N/A'}</span>
          </div>
        </div>
      </div>
      
      <div class="row mt-4">
        <div class="col-12">
          <h6 class="text-white fw-bold mb-3">Requested Items</h6>
          <div class="alert alert-success mb-3">
            <small><strong>Real Data Debug:</strong> Items: ${approval.items ? approval.items.length : 0} | 
            ${approval.items && approval.items.length > 0 ? 
              'First item: ' + approval.items[0].spare_name + ' (Qty: ' + approval.items[0].quantity_requested + ', Price: $' + approval.items[0].unit_price + ')' : 
              'No items found'
            }</small>
          </div>
          <div class="table-responsive">
            <table class="table table-striped" style="background-color: rgba(255, 255, 255, 0.1); color: #ffffff;">
              <thead>
                <tr style="background-color: rgba(255, 255, 255, 0.2);">
                  <th style="color: #ffffff; border-color: rgba(255, 255, 255, 0.3);">Item</th>
                  <th style="color: #ffffff; border-color: rgba(255, 255, 255, 0.3);">Quantity</th>
                  <th style="color: #ffffff; border-color: rgba(255, 255, 255, 0.3);">Unit Price</th>
                  <th style="color: #ffffff; border-color: rgba(255, 255, 255, 0.3);">Total</th>
                </tr>
              </thead>
              <tbody>
                ${approval.items && approval.items.length > 0 ? approval.items.map(item => `
                  <tr style="border-color: rgba(255, 255, 255, 0.3);">
                    <td style="color: #ffffff; border-color: rgba(255, 255, 255, 0.3);">${item.spare_name || 'N/A'}</td>
                    <td style="color: #ffffff; border-color: rgba(255, 255, 255, 0.3);">${item.quantity_requested || 0}</td>
                    <td style="color: #ffffff; border-color: rgba(255, 255, 255, 0.3);">$${item.unit_price || 0}</td>
                    <td style="color: #ffffff; border-color: rgba(255, 255, 255, 0.3);">$${((item.quantity_requested || 0) * (item.unit_price || 0)).toFixed(2)}</td>
                  </tr>
                `).join('') : '<tr style="border-color: rgba(255, 255, 255, 0.3);"><td colspan="4" class="text-center text-white" style="color: #ffffff; border-color: rgba(255, 255, 255, 0.3);">No items found</td></tr>'}
              </tbody>
            </table>
          </div>
        </div>
      </div>
      
      ${approval.remarks ? `
        <div class="row mt-4">
          <div class="col-12">
            <h6 class="text-white fw-bold mb-3">Remarks</h6>
            <p class="text-light">${approval.remarks}</p>
          </div>
        </div>
      ` : ''}
      
      ${approval.approved_by_name ? `
        <div class="row mt-4">
          <div class="col-12">
            <h6 class="text-white fw-bold mb-3">Approval Information</h6>
            <div class="mb-3">
              <span class="text-muted">Approved By:</span>
              <span class="text-white ms-2">${approval.approved_by_name}</span>
            </div>
            <div class="mb-3">
              <span class="text-muted">Approved At:</span>
              <span class="text-white ms-2">${approval.approved_at || 'N/A'}</span>
            </div>
          </div>
        </div>
      ` : ''}
    `;
  }

  function approveRequest(approvalId) {
    console.log('Approving request ID:', approvalId);
    if (confirm('Are you sure you want to approve this request?')) {
      // Implement single approval
      processSingleApproval(approvalId, 'approve');
    }
  }

  function rejectRequest(approvalId) {
    console.log('Rejecting request ID:', approvalId);
    if (confirm('Are you sure you want to reject this request?')) {
      // Implement single rejection
      processSingleApproval(approvalId, 'reject');
    }
  }

  // Bulk operations
  function showBulkApproveModal() {
    const selectedCheckboxes = document.querySelectorAll('.approval-checkbox:checked');
    if (selectedCheckboxes.length === 0) {
      alert('Please select at least one approval request to approve.');
      return;
    }
    
    document.getElementById('selectedCount').textContent = selectedCheckboxes.length;
    const modal = new bootstrap.Modal(document.getElementById('bulkApproveModal'));
    modal.show();
  }

  function toggleAllCheckboxes() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.approval-checkbox');
    
    checkboxes.forEach(checkbox => {
      checkbox.checked = selectAll.checked;
    });
    
    updateBulkActions();
  }

  function updateBulkActions() {
    const selectedCheckboxes = document.querySelectorAll('.approval-checkbox:checked');
    const selectAll = document.getElementById('selectAll');
    
    // Update select all checkbox state
    const totalCheckboxes = document.querySelectorAll('.approval-checkbox').length;
    selectAll.checked = selectedCheckboxes.length === totalCheckboxes;
    selectAll.indeterminate = selectedCheckboxes.length > 0 && selectedCheckboxes.length < totalCheckboxes;
  }

  function processBulkApprove() {
    const selectedCheckboxes = document.querySelectorAll('.approval-checkbox:checked');
    const approvalIds = Array.from(selectedCheckboxes).map(cb => cb.value);
    const remarks = document.getElementById('bulkRemarks').value;
    
    console.log('Selected approval IDs:', approvalIds);
    console.log('Remarks:', remarks);
    
    if (approvalIds.length === 0) {
      alert('Please select at least one approval request.');
      return;
    }
    
    // Disable button to prevent double submission
    const confirmBtn = document.getElementById('confirmBulkApprove');
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    
    // Send bulk approval request
    fetch('{{ route("admin.approvals.bulk-action") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        action: 'approve',
        approval_ids: approvalIds,
        remarks: remarks
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Bulk approval completed successfully!');
        location.reload();
      } else {
        alert('Error: ' + (data.message || 'Unknown error occurred'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error processing bulk approval: ' + error.message);
    })
    .finally(() => {
      confirmBtn.disabled = false;
      confirmBtn.innerHTML = '<i data-feather="check-circle" class="me-2"></i>Approve Selected';
    });
  }

  function processSingleApproval(approvalId, action) {
    console.log('Processing single approval:', { approvalId, action });
    
    const url = '{{ route("admin.approvals.bulk-action") }}';
    console.log('Request URL:', url);
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    console.log('CSRF Token:', csrfToken);
    
    const requestData = {
      action: action,
      approval_ids: [approvalId]
    };
    console.log('Request data:', requestData);
    
    fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: JSON.stringify(requestData)
    })
    .then(response => {
      console.log('Response status:', response.status);
      console.log('Response URL:', response.url);
      console.log('Response headers:', response.headers);
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      return response.text().then(text => {
        console.log('Raw response text:', text);
        try {
          return JSON.parse(text);
        } catch (e) {
          console.error('Failed to parse JSON:', e);
          console.error('Response was:', text);
          throw new Error('Invalid JSON response: ' + text.substring(0, 100));
        }
      });
    })
    .then(data => {
      console.log('Response data:', data);
      if (data.success) {
        alert(`Request ${action}d successfully!`);
        location.reload();
      } else {
        alert('Error: ' + (data.message || 'Unknown error occurred'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert(`Error ${action}ing request: ` + error.message);
    });
  }
</script>
@endpush