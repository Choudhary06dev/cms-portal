<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Approvals Management — CMS Admin</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://unpkg.com/feather-icons"></script>
  <style>
    :root{
      --glass-bg: rgba(255,255,255,0.08);
      --accent: #3b82f6;
      --accent-hover: #2563eb;
      --muted: #64748b;
      --sidebar-bg: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
      --topbar-bg: linear-gradient(90deg, #3b82f6 0%, #1d4ed8 100%);
    }
    body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%); color:#f1f5f9; min-height:100vh; }
    .sidebar {
      min-height:100vh;
      width: 260px;
      background: var(--sidebar-bg);
      border-right: 1px solid rgba(59, 130, 246, 0.2);
      padding: 22px;
      position: fixed;
      left:0; top:0; bottom:0;
      box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
    }
    .brand { color: var(--accent); font-weight:700; font-size:18px; text-shadow: 0 0 10px rgba(59, 130, 246, 0.3); }
    .nav-link { color: #cbd5e1; border-radius:8px; transition: all 0.3s ease; }
    .nav-link:hover, .nav-link.active { background: linear-gradient(90deg, rgba(59, 130, 246, 0.15), rgba(37, 99, 235, 0.1)); color: #fff; transform: translateX(5px); }
    .content { margin-left: 280px; padding: 28px; }
    .topbar { 
      display:flex; 
      justify-content:space-between; 
      align-items:center; 
      gap:12px; 
      margin-bottom:18px; 
      background: var(--topbar-bg);
      padding: 16px 24px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(59, 130, 246, 0.2);
      border: 1px solid rgba(59, 130, 246, 0.1);
    }
    .card-glass { 
      background: var(--glass-bg); 
      border:1px solid rgba(59, 130, 246, 0.1); 
      border-radius:14px; 
      padding:18px; 
      box-shadow: 0 8px 30px rgba(15, 23, 42, 0.4);
      backdrop-filter: blur(10px);
    }
    .table thead th { 
      background: linear-gradient(90deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.05)); 
      color:#e2e8f0; 
      border-bottom: 2px solid rgba(59, 130, 246, 0.2);
    }
    .btn-accent { 
      background: linear-gradient(135deg, #3b82f6, #1d4ed8); 
      border:none; 
      color:#fff; 
      font-weight:700; 
      box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
      transition: all 0.3s ease;
    }
    .btn-accent:hover { 
      background: linear-gradient(135deg, #2563eb, #1e40af); 
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    }
    .btn-sm { padding: 6px 12px; font-size: 12px; }
    .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
    .status-pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
    .status-approved { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
    .status-rejected { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
    .priority-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
    .priority-high { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
    .priority-medium { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
    .priority-low { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
    @media (max-width: 991px){
      .sidebar { position: relative; width:100%; min-height:auto; }
      .content { margin-left:0; padding:12px; }
    }
  </style>
</head>
<body>

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="brand mb-4">CMS Admin</div>
    
    <div class="section-title">Main Menu</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <i data-feather="home" class="me-2"></i> Dashboard
    </a>
    
    <div class="section-title">Management</div>
    <a href="{{ route('admin.users.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
      <i data-feather="users" class="me-2"></i> Users
    </a>
    <a href="{{ route('admin.roles.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
      <i data-feather="shield" class="me-2"></i> Roles
    </a>
    <a href="{{ route('admin.employees.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
      <i data-feather="user-check" class="me-2"></i> Employees
    </a>
    <a href="{{ route('admin.clients.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
      <i data-feather="briefcase" class="me-2"></i> Clients
    </a>
    <a href="{{ route('admin.complaints.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.complaints.*') ? 'active' : '' }}">
      <i data-feather="alert-circle" class="me-2"></i> Complaints
    </a>
    <a href="{{ route('admin.spares.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.spares.*') ? 'active' : '' }}">
      <i data-feather="package" class="me-2"></i> Spare Parts
    </a>
    <a href="{{ route('admin.approvals.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.approvals.*') ? 'active' : '' }}">
      <i data-feather="check-circle" class="me-2"></i> Approvals
    </a>
    <a href="{{ route('admin.reports.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
      <i data-feather="bar-chart-2" class="me-2"></i> Reports
    </a>
    <a href="{{ route('admin.sla.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.sla.*') ? 'active' : '' }}">
      <i data-feather="clock" class="me-2"></i> SLA Rules
    </a>
  </aside>

  <!-- MAIN CONTENT -->
  <div class="content">
    <!-- TOPBAR -->
    <div class="topbar">
      <div>
        <h4 class="mb-0 text-white">Approvals Management</h4>
        <small class="text-blue-200">Manage spare parts approval requests</small>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('admin.approvals.create') }}" class="btn btn-accent btn-sm">
          <i data-feather="plus" class="me-1"></i> New Approval
        </a>
      </div>
    </div>

    <!-- APPROVALS TABLE -->
    <div class="card-glass">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">All Approval Requests</h5>
        <div class="d-flex gap-2">
          <input type="text" class="form-control form-control-sm" placeholder="Search approvals..." style="width: 200px;">
          <select class="form-select form-select-sm" style="width: 120px;">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>
          <select class="form-select form-select-sm" style="width: 120px;">
            <option value="">All Requesters</option>
            @foreach($employees as $employee)
            <option value="{{ $employee->id }}">{{ $employee->user->username }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-dark table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Complaint</th>
              <th>Requester</th>
              <th>Items Count</th>
              <th>Total Value</th>
              <th>Status</th>
              <th>Approver</th>
              <th>Requested</th>
              <th>Approved</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($approvals as $approval)
            <tr>
              <td>{{ $approval->id }}</td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="bg-info rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                    <i data-feather="alert-circle" class="text-white" style="width: 16px; height: 16px;"></i>
                  </div>
                  <div>
                    <strong>{{ $approval->complaint->title ?? 'N/A' }}</strong>
                    <br><small class="text-muted">#{{ $approval->complaint_id }}</small>
                  </div>
                </div>
              </td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px;">
                    <i data-feather="user" class="text-white" style="width: 12px; height: 12px;"></i>
                  </div>
                  {{ $approval->requester->user->username ?? 'N/A' }}
                </div>
              </td>
              <td>
                <span class="badge bg-info">{{ $approval->items_count ?? 0 }} items</span>
              </td>
              <td>
                <strong class="text-success">PKR {{ number_format($approval->getTotalValueRequestedAttribute(), 2) }}</strong>
              </td>
              <td>
                <span class="status-badge status-{{ $approval->status }}">
                  {{ $approval->getStatusDisplayAttribute() }}
                </span>
              </td>
              <td>
                @if($approval->approver)
                <div class="d-flex align-items-center">
                  <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px;">
                    <i data-feather="user-check" class="text-white" style="width: 12px; height: 12px;"></i>
                  </div>
                  {{ $approval->approver->user->username }}
                </div>
                @else
                <span class="text-muted">Pending</span>
                @endif
              </td>
              <td>{{ $approval->created_at->format('M d, Y') }}</td>
              <td>
                @if($approval->approved_at)
                {{ $approval->approved_at->format('M d, Y') }}
                @else
                <span class="text-muted">-</span>
                @endif
              </td>
              <td>
                <div class="d-flex gap-1">
                  <a href="{{ route('admin.approvals.show', $approval) }}" class="btn btn-outline-info btn-sm">
                    <i data-feather="eye"></i>
                  </a>
                  @if($approval->status === 'pending')
                  <a href="{{ route('admin.approvals.approve', $approval) }}" class="btn btn-outline-success btn-sm" onclick="return confirm('Approve this request?')">
                    <i data-feather="check"></i>
                  </a>
                  <a href="{{ route('admin.approvals.reject', $approval) }}" class="btn btn-outline-danger btn-sm" onclick="return confirm('Reject this request?')">
                    <i data-feather="x"></i>
                  </a>
                  @endif
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="10" class="text-center py-4">
                <div class="text-muted">
                  <i data-feather="check-circle" class="mb-2" style="width: 48px; height: 48px; opacity: 0.5;"></i>
                  <br>No approval requests found
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($approvals->hasPages())
      <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">
          Showing {{ $approvals->firstItem() }} to {{ $approvals->lastItem() }} of {{ $approvals->total() }} results
        </div>
        <div>
          {{ $approvals->links() }}
        </div>
      </div>
      @endif
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    feather.replace();
  </script>
</body>
</html>
