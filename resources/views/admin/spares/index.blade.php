<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Spare Parts Management — CMS Admin</title>
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
    .status-active { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
    .status-inactive { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
    .status-discontinued { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
    .stock-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
    .stock-in { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
    .stock-low { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
    .stock-out { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
    .category-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
    .category-electrical { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
    .category-plumbing { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
    .category-kitchen { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
    .category-general { background: rgba(139, 92, 246, 0.2); color: #8b5cf6; }
    .category-tools { background: rgba(107, 114, 128, 0.2); color: #6b7280; }
    .category-consumables { background: rgba(20, 184, 166, 0.2); color: #14b8a6; }
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
        <h4 class="mb-0 text-white">Spare Parts Management</h4>
        <small class="text-blue-200">Manage inventory and spare parts</small>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('admin.spares.create') }}" class="btn btn-accent btn-sm">
          <i data-feather="plus" class="me-1"></i> Add Spare Part
        </a>
      </div>
    </div>

    <!-- SPARES TABLE -->
    <div class="card-glass">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">All Spare Parts</h5>
        <div class="d-flex gap-2">
          <input type="text" class="form-control form-control-sm" placeholder="Search spares..." style="width: 200px;">
          <select class="form-select form-select-sm" style="width: 120px;">
            <option value="">All Categories</option>
            <option value="electrical">Electrical</option>
            <option value="plumbing">Plumbing</option>
            <option value="kitchen">Kitchen</option>
            <option value="general">General</option>
            <option value="tools">Tools</option>
            <option value="consumables">Consumables</option>
          </select>
          <select class="form-select form-select-sm" style="width: 120px;">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="discontinued">Discontinued</option>
          </select>
          <select class="form-select form-select-sm" style="width: 120px;">
            <option value="">All Stock</option>
            <option value="in_stock">In Stock</option>
            <option value="low_stock">Low Stock</option>
            <option value="out_of_stock">Out of Stock</option>
          </select>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-dark table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Spare Name</th>
              <th>Category</th>
              <th>Part Number</th>
              <th>Current Stock</th>
              <th>Min Level</th>
              <th>Unit Price</th>
              <th>Total Value</th>
              <th>Status</th>
              <th>Stock Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($spares as $spare)
            <tr>
              <td>{{ $spare->id }}</td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                    <i data-feather="package" class="text-white" style="width: 16px; height: 16px;"></i>
                  </div>
                  <div>
                    <strong>{{ $spare->spare_name }}</strong>
                    @if($spare->description)
                    <br><small class="text-muted">{{ Str::limit($spare->description, 30) }}</small>
                    @endif
                  </div>
                </div>
              </td>
              <td>
                <span class="category-badge category-{{ $spare->category }}">
                  {{ ucfirst($spare->category) }}
                </span>
              </td>
              <td>{{ $spare->part_number ?: 'N/A' }}</td>
              <td>
                <strong>{{ $spare->current_stock }}</strong>
              </td>
              <td>{{ $spare->min_stock_level }}</td>
              <td>PKR {{ number_format($spare->unit_price, 2) }}</td>
              <td>
                <strong class="text-success">PKR {{ number_format($spare->getTotalValueAttribute(), 2) }}</strong>
              </td>
              <td>
                <span class="status-badge status-{{ $spare->status }}">
                  {{ ucfirst($spare->status) }}
                </span>
              </td>
              <td>
                <span class="stock-badge stock-{{ $spare->getStockStatusAttribute() }}">
                  {{ $spare->getStockStatusDisplayAttribute() }}
                </span>
              </td>
              <td>
                <div class="d-flex gap-1">
                  <a href="{{ route('admin.spares.show', $spare) }}" class="btn btn-outline-info btn-sm">
                    <i data-feather="eye"></i>
                  </a>
                  <a href="{{ route('admin.spares.edit', $spare) }}" class="btn btn-outline-warning btn-sm">
                    <i data-feather="edit"></i>
                  </a>
                  <form action="{{ route('admin.spares.destroy', $spare) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                      <i data-feather="trash-2"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="11" class="text-center py-4">
                <div class="text-muted">
                  <i data-feather="package" class="mb-2" style="width: 48px; height: 48px; opacity: 0.5;"></i>
                  <br>No spare parts found
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($spares->hasPages())
      <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">
          Showing {{ $spares->firstItem() }} to {{ $spares->lastItem() }} of {{ $spares->total() }} results
        </div>
        <div>
          {{ $spares->links() }}
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
