<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Reports & Analytics — CMS Admin</title>
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
    .kpi { display:flex; gap:14px; }
    .kpi .item { 
      flex:1; 
      padding:18px; 
      border-radius:12px; 
      background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.05));
      border: 1px solid rgba(59, 130, 246, 0.2);
      transition: transform 0.3s ease;
    }
    .kpi .item:hover { transform: translateY(-2px); }
    .kpi .value { font-size:22px; font-weight:700; color:#fff; }
    .muted { color: var(--muted); font-size:13px; }
    .chart-container { height: 300px; }
    .report-card { 
      background: var(--glass-bg); 
      border:1px solid rgba(59, 130, 246, 0.1); 
      border-radius:14px; 
      padding:18px; 
      box-shadow: 0 8px 30px rgba(15, 23, 42, 0.4);
      backdrop-filter: blur(10px);
      margin-bottom: 20px;
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
        <h4 class="mb-0 text-white">Reports & Analytics</h4>
        <small class="text-blue-200">Comprehensive system reports and analytics</small>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-accent btn-sm" onclick="exportReport()">
          <i data-feather="download" class="me-1"></i> Export Report
        </button>
        <button class="btn btn-outline-light btn-sm" onclick="refreshData()">
          <i data-feather="refresh-cw" class="me-1"></i> Refresh
        </button>
      </div>
    </div>

    <!-- KPI CARDS -->
    <div class="kpi mb-4">
      <div class="item">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="muted">Total Complaints</div>
            <div class="value">{{ $totalComplaints ?? 0 }}</div>
          </div>
          <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i data-feather="alert-circle" class="text-white" style="width: 24px; height: 24px;"></i>
          </div>
        </div>
      </div>
      <div class="item">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="muted">Resolved Complaints</div>
            <div class="value">{{ $resolvedComplaints ?? 0 }}</div>
          </div>
          <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i data-feather="check-circle" class="text-white" style="width: 24px; height: 24px;"></i>
          </div>
        </div>
      </div>
      <div class="item">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="muted">Active Clients</div>
            <div class="value">{{ $activeClients ?? 0 }}</div>
          </div>
          <div class="bg-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i data-feather="briefcase" class="text-white" style="width: 24px; height: 24px;"></i>
          </div>
        </div>
      </div>
      <div class="item">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="muted">Resolution Rate</div>
            <div class="value">{{ $resolutionRate ?? 0 }}%</div>
          </div>
          <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i data-feather="trending-up" class="text-white" style="width: 24px; height: 24px;"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- CHARTS ROW -->
    <div class="row mb-4">
      <div class="col-md-6">
        <div class="report-card">
          <h5 class="mb-3">Complaints by Status</h5>
          <div id="complaintsStatusChart" class="chart-container"></div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="report-card">
          <h5 class="mb-3">Complaints by Priority</h5>
          <div id="complaintsPriorityChart" class="chart-container"></div>
        </div>
      </div>
    </div>

    <!-- REPORTS SUMMARY TABLE -->
    <div class="card-glass">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Reports Summary</h5>
        <div class="d-flex gap-2">
          <select class="form-select form-select-sm" style="width: 120px;">
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
            <option value="year">This Year</option>
          </select>
          <button class="btn btn-accent btn-sm">
            <i data-feather="filter" class="me-1"></i> Filter
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-dark table-hover">
          <thead>
            <tr>
              <th>Report Date</th>
              <th>Total Complaints</th>
              <th>Resolved</th>
              <th>Pending</th>
              <th>Avg Resolution Time</th>
              <th>Employee Performance</th>
              <th>Client Satisfaction</th>
              <th>SLA Compliance</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentReports as $report)
            <tr>
              <td>{{ $report->generated_at->format('M d, Y') }}</td>
              <td><span class="badge bg-primary">{{ $report->getData('total_complaints', 0) }}</span></td>
              <td><span class="badge bg-success">{{ $report->getData('resolved_complaints', 0) }}</span></td>
              <td><span class="badge bg-warning">{{ $report->getData('pending_complaints', 0) }}</span></td>
              <td>{{ $report->getData('avg_resolution_time', 'N/A') }}</td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="progress me-2" style="width: 60px; height: 8px;">
                    <div class="progress-bar" style="width: {{ $report->getData('employee_performance', 0) }}%"></div>
                  </div>
                  <small>{{ $report->getData('employee_performance', 0) }}%</small>
                </div>
              </td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="progress me-2" style="width: 60px; height: 8px;">
                    <div class="progress-bar bg-success" style="width: {{ $report->client_satisfaction }}%"></div>
                  </div>
                  <small>{{ $report->client_satisfaction }}%</small>
                </div>
              </td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="progress me-2" style="width: 60px; height: 8px;">
                    <div class="progress-bar bg-info" style="width: {{ $report->getData('sla_compliance', 0) }}%"></div>
                  </div>
                  <small>{{ $report->getData('sla_compliance', 0) }}%</small>
                </div>
              </td>
              <td>
                <div class="d-flex gap-1">
                  <button class="btn btn-outline-info btn-sm" onclick="viewReport({{ $report->id }})">
                    <i data-feather="eye"></i>
                  </button>
                  <button class="btn btn-outline-success btn-sm" onclick="downloadReport({{ $report->id }})">
                    <i data-feather="download"></i>
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="9" class="text-center py-4">
                <div class="text-muted">
                  <i data-feather="bar-chart-2" class="mb-2" style="width: 48px; height: 48px; opacity: 0.5;"></i>
                  <br>No reports available
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    feather.replace();
    
    // Initialize charts
    function initCharts() {
      // Complaints by Status Chart
      var statusOptions = {
        series: [{{ $complaintsByStatus['open'] ?? 0 }}, {{ $complaintsByStatus['in_progress'] ?? 0 }}, {{ $complaintsByStatus['resolved'] ?? 0 }}, {{ $complaintsByStatus['closed'] ?? 0 }}],
        chart: {
          type: 'donut',
          height: 300,
          background: 'transparent'
        },
        labels: ['Open', 'In Progress', 'Resolved', 'Closed'],
        colors: ['#3b82f6', '#f59e0b', '#22c55e', '#6b7280'],
        legend: {
          position: 'bottom',
          labels: {
            colors: '#f1f5f9'
          }
        }
      };
      var statusChart = new ApexCharts(document.querySelector("#complaintsStatusChart"), statusOptions);
      statusChart.render();

      // Complaints by Priority Chart
      var priorityOptions = {
        series: [{{ $complaintsByPriority['low'] ?? 0 }}, {{ $complaintsByPriority['medium'] ?? 0 }}, {{ $complaintsByPriority['high'] ?? 0 }}, {{ $complaintsByPriority['urgent'] ?? 0 }}],
        chart: {
          type: 'bar',
          height: 300,
          background: 'transparent'
        },
        xaxis: {
          categories: ['Low', 'Medium', 'High', 'Urgent'],
          labels: {
            style: {
              colors: '#f1f5f9'
            }
          }
        },
        yaxis: {
          labels: {
            style: {
              colors: '#f1f5f9'
            }
          }
        },
        colors: ['#22c55e', '#f59e0b', '#ef4444', '#9333ea'],
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '55%',
          }
        }
      };
      var priorityChart = new ApexCharts(document.querySelector("#complaintsPriorityChart"), priorityOptions);
      priorityChart.render();
    }

    // Initialize charts when page loads
    document.addEventListener('DOMContentLoaded', function() {
      initCharts();
    });

    // Export report function
    function exportReport() {
      // Implementation for exporting reports
      alert('Export functionality will be implemented');
    }

    // Refresh data function
    function refreshData() {
      location.reload();
    }

    // View report function
    function viewReport(reportId) {
      // Implementation for viewing specific report
      alert('View report ' + reportId);
    }

    // Download report function
    function downloadReport(reportId) {
      // Implementation for downloading specific report
      alert('Download report ' + reportId);
    }
  </script>
</body>
</html>
