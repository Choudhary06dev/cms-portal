@extends('layouts.sidebar')

@section('title', 'SLA Rules Management — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
      <div>
      <h2 class="text-white mb-2" >SLA Rules Management</h2>
      <p class="text-light" >Manage Service Level Agreement rules and compliance</p>
      </div>
    <a href="{{ route('admin.sla.create') }}" class="btn btn-accent">
      <i data-feather="plus" class="me-2"></i>Add SLA Rule
        </a>
      </div>
    </div>

<!-- FILTERS -->
<div class="card-glass mb-4">
  <div class="row g-3">
    <div class="col-md-4">
      <input type="text" class="form-control" placeholder="Search SLA rules..." 
>
    </div>
    <div class="col-md-3">
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
      <select class="form-select" 
>
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-outline-light btn-sm w-100">
        <i data-feather="filter" class="me-1"></i>Filter
      </button>
    </div>
        </div>
      </div>

<!-- SLA RULES TABLE -->
<div class="card-glass">
      <div class="table-responsive">
        <table class="table table-dark">
          <thead>
            <tr>
          <th >ID</th>
          <th >Rule Name</th>
          <th >Priority</th>
          <th >Response Time</th>
          <th >Resolution Time</th>
          <th >Escalation Time</th>
          <th >Notify To</th>
          <th >Status</th>
          <th >Created</th>
          <th >Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($slaRules as $rule)
            <tr>
          <td >{{ $rule->id }}</td>
          <td>
            <div style="color: #ffffff !important; font-weight: 600;">{{ $rule->rule_name }}</div>
            <div style="color: #94a3b8 !important; font-size: 0.8rem;">{{ $rule->description ?? 'No description' }}</div>
              </td>
              <td>
            <span class="priority-badge priority-{{ strtolower($rule->priority) }}">
              {{ ucfirst($rule->priority) }}
                </span>
              </td>
          <td >{{ $rule->response_time }} hours</td>
          <td >{{ $rule->resolution_time }} hours</td>
          <td >{{ $rule->escalation_time }} hours</td>
          <td >{{ $rule->notify_to ?? 'N/A' }}</td>
          <td>
            <span class="status-badge status-{{ $rule->status ?? 'active' }}">
              {{ ucfirst($rule->status ?? 'active') }}
                </span>
              </td>
          <td >{{ $rule->created_at->format('M d, Y') }}</td>
              <td>
            <div class="btn-group" role="group">
              <button class="btn btn-outline-info btn-sm" onclick="viewRule({{ $rule->id }})" title="View Details">
                    <i data-feather="eye"></i>
              </button>
              <button class="btn btn-outline-warning btn-sm" onclick="editRule({{ $rule->id }})" title="Edit">
                    <i data-feather="edit"></i>
              </button>
              <button class="btn btn-outline-danger btn-sm" onclick="deleteRule({{ $rule->id }})" title="Delete">
                      <i data-feather="trash-2"></i>
                    </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
          <td colspan="10" class="text-center py-4" >
            <i data-feather="clock" class="feather-lg mb-2"></i>
            <div>No SLA rules found</div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

  <!-- PAGINATION -->
      <div class="d-flex justify-content-center mt-3">
        <div>
          {{ $slaRules->links() }}
        </div>
      </div>
    </div>
@endsection

@push('styles')
<style>
  .priority-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  .priority-low { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
  .priority-medium { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
  .priority-high { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
  .priority-urgent { background: rgba(139, 92, 246, 0.2); color: #8b5cf6; }
  
  .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  .status-active { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
  .status-inactive { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
</style>
@endpush

@push('scripts')
  <script>
    feather.replace();

  // SLA Rule Functions
  function viewRule(ruleId) {
    alert('View SLA rule details functionality coming soon!');
  }

  function editRule(ruleId) {
    alert('Edit SLA rule functionality coming soon!');
  }

  function deleteRule(ruleId) {
    if (confirm('Are you sure you want to delete this SLA rule?')) {
      alert('Delete SLA rule functionality coming soon!');
    }
  }
  </script>
@endpush