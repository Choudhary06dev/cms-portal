@extends('layouts.sidebar')

@section('title', 'Stock Approval — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Stock Approval</h2>
      <p class="text-light">View and manage stock issued complaints</p>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-secondary" onclick="refreshPage()">
        <i data-feather="refresh-cw" class="me-2"></i>Refresh
      </button>
    </div>
  </div>
</div>

<!-- FILTERS -->
<div class="card-glass mb-4" style="display: inline-block; width: fit-content;">
  <form id="stockApprovalFiltersForm" method="GET" action="{{ route('admin.stock-approval.index') }}" onsubmit="event.preventDefault(); submitStockApprovalFilters(event); return false;">
  <div class="row g-2 align-items-end">
    <div class="col-auto">
      <label class="form-label small text-muted mb-1" style="font-size: 0.8rem;">Search</label>
      <input type="text" class="form-control" id="searchInput" name="search" placeholder="Complaint ID, Product..." 
             value="{{ request('search') }}" autocomplete="off" style="font-size: 0.9rem; width: 200px;">
    </div>
    <div class="col-auto">
      <label class="form-label small text-muted mb-1" style="font-size: 0.8rem;">From Date</label>
      <input type="date" class="form-control" name="complaint_date" 
             value="{{ request('complaint_date') }}" placeholder="Select Date" autocomplete="off" style="font-size: 0.9rem; width: 150px;">
    </div>
    <div class="col-auto">
      <label class="form-label small text-muted mb-1" style="font-size: 0.8rem;">To Date</label>
      <input type="date" class="form-control" name="date_to" 
             value="{{ request('date_to') }}" placeholder="End Date" autocomplete="off" style="font-size: 0.9rem; width: 150px;">
    </div>
    <div class="col-auto">
      <label class="form-label small text-muted mb-1" style="font-size: 0.8rem;">Category</label>
      <select class="form-select" name="category" autocomplete="off" style="font-size: 0.9rem; width: 140px;">
        <option value="" {{ request('category') ? '' : 'selected' }}>All</option>
        @if(isset($categories) && $categories->count() > 0)
          @foreach($categories as $cat)
            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
          @endforeach
        @else
          <option value="electric">Electric</option>
          <option value="technical">Technical</option>
          <option value="service">Service</option>
          <option value="billing">Billing</option>
          <option value="water">Water Supply</option>
          <option value="sanitary">Sanitary</option>
          <option value="plumbing">Plumbing</option>
          <option value="kitchen">Kitchen</option>
          <option value="other">Other</option>
        @endif
      </select>
    </div>
    <div class="col-auto">
      <label class="form-label small text-muted mb-1" style="font-size: 0.8rem;">&nbsp;</label>
      <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetStockApprovalFilters()" style="font-size: 0.9rem; padding: 0.35rem 0.8rem;">
        <i data-feather="refresh-cw" class="me-1" style="width: 14px; height: 14px;"></i>Reset
      </button>
    </div>
  </div>
  </form>
</div>

<!-- STOCK APPROVAL TABLE -->
<div class="card-glass">
  <div class="table-responsive">
    <table class="table table-dark table-sm">
      <thead>
        <tr>
          <th>#</th>
          <th>Date</th>
          <th>Category</th>
          <th>Product Name</th>
          <th>Total Quantity</th>
          <th>Required Quantity</th>
          <th>Approval Quantity</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($stockApprovals as $stockApproval)
        @php
          $statusColors = [
            'approved' => ['bg' => '#16a34a', 'text' => '#ffffff', 'border' => '#15803d'],
            'rejected' => ['bg' => '#dc2626', 'text' => '#ffffff', 'border' => '#b91c1c'],
            'pending' => ['bg' => '#f59e0b', 'text' => '#ffffff', 'border' => '#d97706'],
            'request_for_stock' => ['bg' => '#3b82f6', 'text' => '#ffffff', 'border' => '#2563eb'],
          ];
          
          $status = $stockApproval->status ?? 'pending';
          $statusDisplay = ucfirst(str_replace('_', ' ', $status));
          $statusColor = $statusColors[$status] ?? $statusColors['pending'];
          
          $categoryDisplay = [
            'electric' => 'Electric',
            'technical' => 'Technical',
            'service' => 'Service',
            'billing' => 'Billing',
            'water' => 'Water Supply',
            'sanitary' => 'Sanitary',
            'plumbing' => 'Plumbing',
            'kitchen' => 'Kitchen',
            'other' => 'Other',
          ];
          // Prefer spare category; fallback to complaint category
          $category = $stockApproval->spare_category ?? $stockApproval->complaint_category ?? 'N/A';
          $catDisplay = $category ? ($categoryDisplay[strtolower($category)] ?? ucfirst($category)) : 'N/A';
          
          // Get complaint date for display (handle both Eloquent and DB::table results)
          try {
            if (isset($stockApproval->complaint) && $stockApproval->complaint && isset($stockApproval->complaint->created_at)) {
              $complaintDate = \Carbon\Carbon::parse($stockApproval->complaint->created_at)->format('M d, Y');
            } elseif (!empty($stockApproval->issue_date)) {
              $complaintDate = \Carbon\Carbon::parse($stockApproval->issue_date)->format('M d, Y');
            } elseif (!empty($stockApproval->created_at)) {
              $complaintDate = \Carbon\Carbon::parse($stockApproval->created_at)->format('M d, Y');
            } else {
              $complaintDate = 'N/A';
            }
          } catch (\Exception $e) {
            $complaintDate = 'N/A';
          }
        @endphp
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $complaintDate }}</td>
          <td>{{ $catDisplay }}</td>
          <td>{{ $stockApproval->product_name ?? 'No item' }}</td>
          <td>
            @php $avail = (int)($stockApproval->available_stock ?? 0); @endphp
            <span class="badge {{ $avail > 0 ? 'bg-success' : 'bg-danger' }}" style="font-size: 12px;">
              {{ $avail }}
            </span>
          </td>
          <td>
            @php $req = (int)($stockApproval->requested_stock ?? 0); @endphp
            <span class="badge bg-info" style="font-size: 12px;">
              {{ $req }}
            </span>
          </td>
          <td>
            <span class="badge bg-warning text-dark" style="font-size: 12px;">
              {{ $stockApproval->approval_stock }}
            </span>
          </td>
          <td>
            <div class="status-chip" style="background-color: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }}; border-color: {{ $statusColor['border'] }}; width: 140px; height: 28px; justify-content: center;">
              <span style="font-size: 11px; font-weight: 700; color: white !important;">{{ $statusDisplay }}</span>
            </div>
          </td>
          <td>
            <div class="btn-group" role="group">
              @if($stockApproval->complaint_id)
                <a href="{{ route('admin.complaints.show', $stockApproval->complaint_id) }}" 
                   class="btn btn-outline-success btn-sm view-complaint-link" 
                   title="View Complaint" 
                   style="padding: 3px 8px;"
                   data-no-ajax="true"
                   onclick="event.preventDefault(); event.stopPropagation(); window.location.href=this.href; return false;">
                  <i data-feather="eye" style="width: 16px; height: 16px;"></i>
                </a>
              @endif
              @if($status !== 'approved')
                <button type="button" class="btn btn-outline-success btn-sm" onclick="quickUpdateStatus({{ $stockApproval->id }}, 'approved', {{ $stockApproval->approval_stock }})" title="Approve" style="padding: 3px 8px;">
                  <i data-feather="check" style="width: 16px; height: 16px;"></i>
                </button>
              @endif
              @if($status !== 'rejected')
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="quickUpdateStatus({{ $stockApproval->id }}, 'rejected', {{ $stockApproval->approval_stock }})" title="Reject" style="padding: 3px 8px;">
                  <i data-feather="x" style="width: 16px; height: 16px;"></i>
                </button>
              @endif
              @if($status !== 'request_for_stock')
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="quickUpdateStatus({{ $stockApproval->id }}, 'request_for_stock', {{ $stockApproval->approval_stock }})" title="Request for Stock" style="padding: 3px 8px;">
                  <i data-feather="shopping-cart" style="width: 16px; height: 16px;"></i>
                </button>
              @endif
              <button type="button" class="btn btn-outline-warning btn-sm" onclick="updateStockStatus({{ $stockApproval->id }}, '{{ $status }}', {{ $stockApproval->approval_stock }})" title="Edit" style="padding: 3px 8px;">
                <i data-feather="edit" style="width: 16px; height: 16px;"></i>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9" class="text-center py-4">
            <i data-feather="check-circle" class="feather-lg mb-2"></i>
            <div>No stock issued records found</div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <!-- PAGINATION -->
  <div class="d-flex justify-content-center mt-3">
    <div>
      {{ $stockApprovals->links() }}
    </div>
  </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="updateStatusModalLabel">
          <i data-feather="edit" class="me-2"></i>Update Stock Status
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateStatusForm">
          <input type="hidden" id="stockApprovalId" name="stock_approval_id">
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" id="stockStatus" name="status" required>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
              <option value="request_for_stock">Request for Stock</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Approval Stock</label>
            <input type="number" class="form-control" id="approvalStock" name="approval_stock" min="0" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="submitStatusUpdate()">Update Status</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  feather.replace();

  function refreshPage() {
    location.reload();
  }

  function resetStockApprovalFilters() {
    const form = document.getElementById('stockApprovalFiltersForm');
    if (!form) return;
    
    form.querySelectorAll('input[type="text"], input[type="date"], select').forEach(input => {
      if (input.type === 'select-one') {
        input.selectedIndex = 0;
      } else {
        input.value = '';
      }
    });
    
    window.location.href = '{{ route('admin.stock-approval.index') }}';
  }

  function submitStockApprovalFilters(e) {
    if (e) e.preventDefault();
    if (e) e.stopPropagation();
    
    const form = document.getElementById('stockApprovalFiltersForm');
    if (!form) return;
    
    form.submit();
  }

  function quickUpdateStatus(id, status, approvalStock) {
    if (!confirm(`Are you sure you want to ${status === 'approved' ? 'approve' : status === 'rejected' ? 'reject' : 'request stock for'} this record?`)) {
      return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    fetch(`/admin/stock-approval/${id}/update-status`, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({
        status: status,
        approval_stock: parseInt(approvalStock)
      }),
      credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Status updated successfully!');
        location.reload();
      } else {
        alert('Error: ' + (data.message || 'Failed to update status'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error updating status: ' + error.message);
    });
  }

  function updateStockStatus(id, currentStatus, currentApprovalStock) {
    document.getElementById('stockApprovalId').value = id;
    document.getElementById('stockStatus').value = currentStatus;
    document.getElementById('approvalStock').value = currentApprovalStock || 0;
    
    const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
    modal.show();
  }

  function submitStatusUpdate() {
    const form = document.getElementById('updateStatusForm');
    const formData = new FormData(form);
    const id = formData.get('stock_approval_id');
    const status = formData.get('status');
    const approvalStock = formData.get('approval_stock');
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    fetch(`/admin/stock-approval/${id}/update-status`, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({
        status: status,
        approval_stock: parseInt(approvalStock)
      }),
      credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Status updated successfully!');
        bootstrap.Modal.getInstance(document.getElementById('updateStatusModal')).hide();
        location.reload();
      } else {
        alert('Error: ' + (data.message || 'Failed to update status'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error updating status: ' + error.message);
    });
  }

  // Prevent global click handlers from intercepting complaint links
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.view-complaint-link').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        window.location.href = this.href;
        return false;
      }, true);
    });
  });

  // Auto-submit on filter change
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('stockApprovalFiltersForm');
    if (form) {
      const searchInput = form.querySelector('#searchInput');
      if (searchInput) {
        let searchTimeout = null;
        searchInput.addEventListener('input', function() {
          clearTimeout(searchTimeout);
          searchTimeout = setTimeout(() => {
            submitStockApprovalFilters(null);
          }, 500);
        });
      }
      
      const dateInput = form.querySelector('input[name="complaint_date"]');
      if (dateInput) {
        dateInput.addEventListener('change', submitStockApprovalFilters);
      }
      
      const endDateInput = form.querySelector('input[name="date_to"]');
      if (endDateInput) {
        endDateInput.addEventListener('change', submitStockApprovalFilters);
      }
      
      const categorySelect = form.querySelector('select[name="category"]');
      if (categorySelect) {
        categorySelect.addEventListener('change', submitStockApprovalFilters);
      }
    }
  });
</script>
@endpush
