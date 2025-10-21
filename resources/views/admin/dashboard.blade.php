<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Dashboard — CMS Admin</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://unpkg.com/feather-icons"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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
    .stat-card {
      background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.05));
      border: 1px solid rgba(59, 130, 246, 0.2);
      border-radius: 12px;
      padding: 20px;
      text-align: center;
      transition: all 0.3s ease;
    }
    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(59, 130, 246, 0.2);
    }
    .stat-number {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--accent);
      margin-bottom: 8px;
    }
    .stat-label {
      color: #94a3b8;
      font-size: 0.9rem;
      font-weight: 500;
    }
    .chart-container {
      background: var(--glass-bg);
      border: 1px solid rgba(59, 130, 246, 0.1);
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
    }
    .table thead th { 
      background: linear-gradient(90deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.05)); 
      color:#e2e8f0; 
      border-bottom: 2px solid rgba(59, 130, 246, 0.2);
    }
    .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
    .status-new { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
    .status-assigned { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
    .status-in_progress { background: rgba(168, 85, 247, 0.2); color: #a855f7; }
    .status-resolved { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
    .status-closed { background: rgba(107, 114, 128, 0.2); color: #6b7280; }
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
        <h4 class="mb-0 text-white">Dashboard Overview</h4>
        <small class="text-blue-200">Real-time complaint management system</small>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-light btn-sm" onclick="refreshDashboard()">
          <i data-feather="refresh-cw" class="me-1"></i> Refresh
        </button>
      </div>
    </div>

    <!-- STATISTICS CARDS -->
    <div class="row mb-4">
      <div class="col-md-3 mb-3">
        <div class="stat-card">
          <div class="stat-number">{{ $stats['total_complaints'] }}</div>
          <div class="stat-label">Total Complaints</div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="stat-card">
          <div class="stat-number">{{ $stats['pending_complaints'] }}</div>
          <div class="stat-label">Pending</div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="stat-card">
          <div class="stat-number">{{ $stats['resolved_complaints'] }}</div>
          <div class="stat-label">Resolved</div>
        </div>
            </div>
      <div class="col-md-3 mb-3">
        <div class="stat-card">
          <div class="stat-number">{{ $stats['overdue_complaints'] }}</div>
          <div class="stat-label">Overdue</div>
        </div>
      </div>
    </div>

    <!-- ADDITIONAL STATS -->
    <div class="row mb-4">
      <div class="col-md-2 mb-3">
        <div class="stat-card">
          <div class="stat-number">{{ $stats['total_users'] }}</div>
          <div class="stat-label">Users</div>
        </div>
      </div>
      <div class="col-md-2 mb-3">
        <div class="stat-card">
          <div class="stat-number">{{ $stats['total_employees'] }}</div>
          <div class="stat-label">Employees</div>
        </div>
      </div>
      <div class="col-md-2 mb-3">
        <div class="stat-card">
          <div class="stat-number">{{ $stats['total_clients'] }}</div>
          <div class="stat-label">Clients</div>
        </div>
      </div>
      <div class="col-md-2 mb-3">
        <div class="stat-card">
          <div class="stat-number">{{ $stats['low_stock_items'] }}</div>
          <div class="stat-label">Low Stock</div>
        </div>
      </div>
      <div class="col-md-2 mb-3">
        <div class="stat-card">
          <div class="stat-number">{{ $stats['pending_approvals'] }}</div>
          <div class="stat-label">Pending Approvals</div>
        </div>
      </div>
      <div class="col-md-2 mb-3">
        <div class="stat-card">
          <div class="stat-number">{{ $slaPerformance['sla_percentage'] }}%</div>
          <div class="stat-label">SLA Performance</div>
        </div>
      </div>
    </div>

    <!-- CHARTS ROW -->
    <div class="row mb-4">
      <div class="col-md-6">
        <div class="chart-container">
          <h5 class="mb-3">Complaints by Status</h5>
          <div id="complaintsStatusChart"></div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="chart-container">
          <h5 class="mb-3">Complaints by Type</h5>
          <div id="complaintsTypeChart"></div>
        </div>
      </div>
        </div>

    <!-- MONTHLY TRENDS CHART -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="chart-container">
          <h5 class="mb-3">Monthly Trends</h5>
          <div id="monthlyTrendsChart"></div>
        </div>
      </div>
            </div>

    <!-- TABLES ROW -->
    <div class="row">
      <div class="col-md-6">
        <div class="card-glass">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Recent Complaints</h5>
            <a href="{{ route('admin.complaints.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
          </div>
          <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                <tr>
                  <th>Ticket</th>
                  <th>Client</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Priority</th>
                </tr>
                </thead>
              <tbody>
                @forelse($recentComplaints as $complaint)
                <tr>
                  <td>{{ $complaint->getTicketNumberAttribute() }}</td>
                  <td>{{ $complaint->client->client_name }}</td>
                  <td>{{ $complaint->getComplaintTypeDisplayAttribute() }}</td>
                  <td><span class="status-badge status-{{ $complaint->status }}">{{ $complaint->getStatusDisplayAttribute() }}</span></td>
                  <td><span class="priority-badge priority-{{ $complaint->priority }}">{{ $complaint->getPriorityDisplayAttribute() }}</span></td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center py-3">No recent complaints</td>
                </tr>
                @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

      <div class="col-md-6">
        <div class="card-glass">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Pending Approvals</h5>
            <a href="{{ route('admin.approvals.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
          </div>
          <div class="table-responsive">
            <table class="table table-dark table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Complaint</th>
                  <th>Requester</th>
                  <th>Items</th>
                  <th>Value</th>
                </tr>
              </thead>
              <tbody>
                @forelse($pendingApprovals as $approval)
                <tr>
                  <td>{{ $approval->id }}</td>
                  <td>{{ $approval->complaint->getTicketNumberAttribute() }}</td>
                  <td>{{ $approval->requestedBy->user->full_name }}</td>
                  <td>{{ $approval->items->count() }}</td>
                  <td>PKR {{ number_format($approval->getTotalValueRequestedAttribute(), 2) }}</td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center py-3">No pending approvals</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          </div>
        </div>
      </div>

    <!-- LOW STOCK ALERTS -->
    @if($lowStockItems->count() > 0)
    <div class="row mt-4">
      <div class="col-12">
        <div class="card-glass">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 text-warning">
              <i data-feather="alert-triangle" class="me-2"></i>Low Stock Alerts
            </h5>
            <a href="{{ route('admin.spares.index') }}" class="btn btn-outline-warning btn-sm">Manage Stock</a>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover">
            <thead>
                <tr>
                  <th>Item</th>
                  <th>Category</th>
                  <th>Current Stock</th>
                  <th>Threshold</th>
                  <th>Status</th>
                </tr>
            </thead>
              <tbody>
                @foreach($lowStockItems as $item)
                <tr>
                  <td>{{ $item->item_name }}</td>
                  <td>{{ ucfirst($item->category) }}</td>
                  <td>{{ $item->stock_quantity }}</td>
                  <td>{{ $item->threshold_level }}</td>
                  <td>
                    @if($item->stock_quantity <= 0)
                      <span class="badge bg-danger">Out of Stock</span>
                    @else
                      <span class="badge bg-warning">Low Stock</span>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
          </table>
          </div>
        </div>
      </div>
        </div>
    @endif
      </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  feather.replace();

    // Complaints by Status Chart
    var complaintsStatusOptions = {
      series: [
        @foreach($complaintsByStatus as $status => $count)
        {{ $count }},
        @endforeach
      ],
      chart: {
        type: 'donut',
        height: 300,
        background: 'transparent'
      },
      labels: [
        @foreach($complaintsByStatus as $status => $count)
        '{{ ucfirst(str_replace('_', ' ', $status)) }}',
        @endforeach
      ],
      colors: ['#3b82f6', '#f59e0b', '#a855f7', '#22c55e', '#6b7280'],
      legend: {
        position: 'bottom',
        labels: {
          colors: '#e2e8f0'
        }
      },
      dataLabels: {
        enabled: true,
        style: {
          colors: ['#fff']
        }
      }
    };

    var complaintsStatusChart = new ApexCharts(document.querySelector("#complaintsStatusChart"), complaintsStatusOptions);
    complaintsStatusChart.render();

    // Complaints by Type Chart
    var complaintsTypeOptions = {
      series: [
        @foreach($complaintsByType as $type => $count)
        {{ $count }},
        @endforeach
      ],
      chart: {
        type: 'pie',
        height: 300,
        background: 'transparent'
      },
      labels: [
        @foreach($complaintsByType as $type => $count)
        '{{ ucfirst($type) }}',
        @endforeach
      ],
      colors: ['#3b82f6', '#f59e0b', '#a855f7', '#22c55e'],
      legend: {
        position: 'bottom',
        labels: {
          colors: '#e2e8f0'
        }
      },
      dataLabels: {
        enabled: true,
        style: {
          colors: ['#fff']
        }
      }
    };

    var complaintsTypeChart = new ApexCharts(document.querySelector("#complaintsTypeChart"), complaintsTypeOptions);
    complaintsTypeChart.render();

    // Monthly Trends Chart
    var monthlyTrendsOptions = {
      series: [{
        name: 'Complaints',
        data: @json($monthlyTrends['complaints'])
      }, {
        name: 'Resolutions',
        data: @json($monthlyTrends['resolutions'])
      }],
      chart: {
        type: 'line',
        height: 350,
        background: 'transparent'
      },
      colors: ['#3b82f6', '#22c55e'],
      xaxis: {
        categories: @json($monthlyTrends['months']),
        labels: {
          style: {
            colors: '#e2e8f0'
          }
        }
      },
      yaxis: {
        labels: {
          style: {
            colors: '#e2e8f0'
          }
        }
      },
      legend: {
        labels: {
          colors: '#e2e8f0'
        }
      },
      grid: {
        borderColor: '#374151'
      },
      stroke: {
        width: 3
      },
      markers: {
        size: 5
      }
    };

    var monthlyTrendsChart = new ApexCharts(document.querySelector("#monthlyTrendsChart"), monthlyTrendsOptions);
    monthlyTrendsChart.render();

    // Refresh dashboard function
    function refreshDashboard() {
      location.reload();
    }

    // Auto-refresh every 5 minutes
    setInterval(function() {
      fetch('{{ route("admin.dashboard.real-time-updates") }}')
        .then(response => response.json())
        .then(data => {
          // Update real-time data here if needed
          console.log('Dashboard updated:', data);
        })
        .catch(error => console.error('Error updating dashboard:', error));
    }, 300000); // 5 minutes
</script>
</body>
</html>
