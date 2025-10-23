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
      <button class="btn btn-accent">
        <i data-feather="check-circle" class="me-2"></i>Bulk Approve
      </button>
    </div>
  </div>
</div>

<!-- FILTERS -->
<div class="card-glass mb-4">
  <div class="row g-3">
    <div class="col-md-3">
      <input type="text" class="form-control" placeholder="Search approvals..." 
>
    </div>
    <div class="col-md-2">
      <select class="form-select" 
>
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="rejected">Rejected</option>
        <option value="cancelled">Cancelled</option>
      </select>
    </div>
    <div class="col-md-2">
      <select class="form-select" 
>
        <option value="">All Types</option>
        <option value="spare">Spare Parts</option>
        <option value="leave">Leave Request</option>
        <option value="expense">Expense</option>
        <option value="purchase">Purchase Order</option>
      </select>
    </div>
    <div class="col-md-2">
      <select class="form-select" 
>
        <option value="">All Priorities</option>
        <option value="low">Low</option>
        <option value="medium">Medium</option>
        <option value="high">High</option>
        <option value="urgent">Urgent</option>
      </select>
    </div>
    <div class="col-md-3">
      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm">
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

<!-- APPROVALS TABLE -->
<div class="card-glass">
  <div class="table-responsive">
        <table class="table table-dark">
      <thead>
        <tr>
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
          <td >{{ $approval->id }}</td>
          <td>
            <div class="text-white fw-bold">{{ $approval->title }}</div>
            <div class="text-muted small">{{ Str::limit($approval->description, 50) }}</div>
          </td>
          <td>
            <span class="type-badge type-{{ strtolower($approval->type) }}">
              {{ ucfirst($approval->type) }}
            </span>
          </td>
          <td >{{ $approval->requested_by ?? 'N/A' }}</td>
          <td>
            <span class="priority-badge priority-{{ strtolower($approval->priority) }}">
              {{ ucfirst($approval->priority) }}
            </span>
          </td>
          <td>
            <span class="status-badge status-{{ strtolower($approval->status) }}">
              {{ ucfirst($approval->status) }}
            </span>
          </td>
          <td >${{ number_format($approval->amount ?? 0, 2) }}</td>
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
    alert('View approval details functionality coming soon!');
  }

  function approveRequest(approvalId) {
    if (confirm('Are you sure you want to approve this request?')) {
      alert('Approve request functionality coming soon!');
    }
  }

  function rejectRequest(approvalId) {
    if (confirm('Are you sure you want to reject this request?')) {
      alert('Reject request functionality coming soon!');
    }
  }
</script>
@endpush