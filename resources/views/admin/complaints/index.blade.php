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
  <div class="row g-3">
    <div class="col-md-3">
      <input type="text" class="form-control" placeholder="Search complaints..." 
>
    </div>
    <div class="col-md-2">
      <select class="form-select" 
>
        <option value="">All Status</option>
        <option value="new">New</option>
        <option value="assigned">Assigned</option>
        <option value="in_progress">In Progress</option>
        <option value="resolved">Resolved</option>
        <option value="closed">Closed</option>
      </select>
    </div>
    <div class="col-md-2">
      <select class="form-select" 
>
        <option value="">All Priority</option>
        <option value="low">Low</option>
        <option value="medium">Medium</option>
        <option value="high">High</option>
        <option value="urgent">Urgent</option>
      </select>
    </div>
    <div class="col-md-2">
      <select class="form-select" 
>
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
          <td >{{ $complaint->client->name ?? 'N/A' }}</td>
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
            <div class="btn-group" role="group">
              <button class="btn btn-outline-info btn-sm" onclick="viewComplaint({{ $complaint->id }})" title="View Details">
                <i data-feather="eye"></i>
              </button>
              <button class="btn btn-outline-warning btn-sm" onclick="editComplaint({{ $complaint->id }})" title="Edit">
                <i data-feather="edit"></i>
              </button>
              <button class="btn btn-outline-danger btn-sm" onclick="deleteComplaint({{ $complaint->id }})" title="Delete">
                <i data-feather="trash-2"></i>
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

@push('scripts')
<script>
  feather.replace();

  // Complaint Functions
  function viewComplaint(complaintId) {
    alert('View complaint details functionality coming soon!');
  }

  function editComplaint(complaintId) {
    alert('Edit complaint functionality coming soon!');
  }

  function deleteComplaint(complaintId) {
    if (confirm('Are you sure you want to delete this complaint?')) {
      alert('Delete complaint functionality coming soon!');
    }
  }
</script>
@endpush