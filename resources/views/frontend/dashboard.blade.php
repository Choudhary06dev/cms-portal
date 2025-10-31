@extends('frontend.layouts.app')

@section('title', 'Dashboard')

@section('content')
  <style>
  /* Copied styling adapted for frontend layout */
  .theme-light {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
    color: #1e293b !important;
  }
  .card-glass {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%) !important;
    border: 1px solid rgba(59, 130, 246, 0.15) !important;
    border-radius: 12px !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    color: #1e293b !important;
    transition: all 0.3s ease !important;
    padding: 16px;
  }
  .dashboard-header h2 { font-weight: 700; font-size: 1.875rem; }
  .table-dark { background: transparent !important; }
  </style>

  <!-- DASHBOARD HEADER -->
  <div class="mb-4 dashboard-header">
    <h2 class="mb-2">Dashboard Overview</h2>
    <p class="text-secondary">Real-time complaint management system</p>
  </div>

  <!-- APPROVALS STATISTICS -->
  <div class="row mb-4">
    <div class="col-md-4 mb-3">
      <div class="card-glass">
        <div class="d-flex align-items-center">
          <div class="flex-grow-1">
            <div class="h4 mb-1 text-warning" style="font-size: 2rem; font-weight: bold;">{{ $stats['pending_approvals'] ?? 0 }}</div>
            <div class="text-muted" style="font-size: 0.9rem;">Pending Approvals</div>
          </div>
          <div class="text-warning">
            <i data-feather="clock" class="feather-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card-glass">
        <div class="d-flex align-items-center">
          <div class="flex-grow-1">
            <div class="h4 mb-1 text-success" style="font-size: 2rem; font-weight: bold;">{{ $stats['approved_this_month'] ?? 0 }}</div>
            <div class="text-muted" style="font-size: 0.9rem;">Approved This Month</div>
          </div>
          <div class="text-success">
            <i data-feather="check-circle" class="feather-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card-glass">
        <div class="d-flex align-items-center">
          <div class="flex-grow-1">
            <div class="h4 mb-1 text-info" style="font-size: 2rem; font-weight: bold;">{{ $stats['total_approvals'] ?? 0 }}</div>
            <div class="text-muted" style="font-size: 0.9rem;">Total Approvals</div>
          </div>
          <div class="text-info">
            <i data-feather="file-text" class="feather-lg"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- STATISTICS CARDS -->
  <div class="row mb-4">
    <div class="col-md-3 mb-3">
      <div class="card-glass">
        <div class="d-flex align-items-center">
          <div class="flex-grow-1">
            <div class="h4 mb-1" style="font-size: 2rem; font-weight: bold;">{{ $stats['total_complaints'] ?? 0 }}</div>
            <div class="text-muted" style="font-size: 0.9rem;">Total Complaints</div>
          </div>
          <div class="text-primary">
            <i data-feather="alert-circle" class="feather-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card-glass">
        <div class="d-flex align-items-center">
          <div class="flex-grow-1">
            <div class="h4 mb-1 text-warning" style="font-size: 2rem; font-weight: bold;">{{ $stats['pending_complaints'] ?? 0 }}</div>
            <div class="text-muted" style="font-size: 0.9rem;">Pending</div>
          </div>
          <div class="text-warning">
            <i data-feather="clock" class="feather-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card-glass">
        <div class="d-flex align-items-center">
          <div class="flex-grow-1">
            <div class="h4 mb-1 text-success" style="font-size: 2rem; font-weight: bold;">{{ $stats['resolved_complaints'] ?? 0 }}</div>
            <div class="text-muted" style="font-size: 0.9rem;">Resolved</div>
          </div>
          <div class="text-success">
            <i data-feather="check-circle" class="feather-lg"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card-glass">
        <div class="d-flex align-items-center">
          <div class="flex-grow-1">
            <div class="h4 mb-1 text-danger" style="font-size: 2rem; font-weight: bold;">{{ $stats['overdue_complaints'] ?? 0 }}</div>
            <div class="text-muted" style="font-size: 0.9rem;">Overdue</div>
          </div>
          <div class="text-danger">
            <i data-feather="alert-triangle" class="feather-lg"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ADDITIONAL STATS -->
  <div class="row mb-4 justify-content-center">
    <div class="col-md-2 mb-3">
      <div class="card-glass text-center">
        <div class="h5 mb-1" style="font-size: 1.5rem; font-weight: bold;">{{ $stats['total_users'] ?? 0 }}</div>
        <div class="text-muted" style="font-size: 0.8rem;">Users</div>
      </div>
    </div>
    <div class="col-md-2 mb-3">
      <div class="card-glass text-center">
        <div class="h5 mb-1" style="font-size: 1.5rem; font-weight: bold;">{{ $stats['total_employees'] ?? 0 }}</div>
        <div class="text-muted" style="font-size: 0.8rem;">Employees</div>
      </div>
    </div>
    <div class="col-md-2 mb-3">
      <div class="card-glass text-center">
        <div class="h5 mb-1" style="font-size: 1.5rem; font-weight: bold;">{{ $stats['total_clients'] ?? 0 }}</div>
        <div class="text-muted" style="font-size: 0.8rem;">Clients</div>
      </div>
    </div>
    <div class="col-md-2 mb-3">
      <div class="card-glass text-center">
        <div class="h5 mb-1 text-warning" style="font-size: 1.5rem; font-weight: bold;">{{ $stats['low_stock_items'] ?? 0 }}</div>
        <div class="text-muted" style="font-size: 0.8rem;">Low Stock</div>
      </div>
    </div>
    <div class="col-md-2 mb-3">
      <div class="card-glass text-center">
        <div class="h5 mb-1 text-success" style="font-size: 1.5rem; font-weight: bold;">{{ $slaPerformance['sla_percentage'] ?? 0 }}%</div>
        <div class="text-muted" style="font-size: 0.8rem;">SLA Performance</div>
      </div>
    </div>
  </div>

  <!-- CHARTS ROW (containers only; scripts from admin view can be reused if loaded globally) -->
  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card-glass">
        <h5 class="mb-3">Complaints by Status</h5>
        <div id="complaintsStatusChart" style="height: 300px;"></div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card-glass">
        <h5 class="mb-3">Complaints by Type</h5>
        <div id="complaintsTypeChart" style="height: 300px;"></div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-12">
      <div class="card-glass">
        <h5 class="mb-3">Monthly Trends</h5>
        <div id="monthlyTrendsChart" style="height: 350px;"></div>
      </div>
    </div>
  </div>

  <!-- TABLES ROW -->
  <div class="row">
    <div class="col-12">
      <div class="card-glass">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Recent Complaints</h5>
        </div>
        <div class="table-responsive">
          <table class="table">
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
              @forelse($recentComplaints ?? [] as $complaint)
                <tr>
                  <td>{{ $complaint->getTicketNumberAttribute() ?? ('#'.$complaint->id) }}</td>
                  <td>{{ $complaint->client->client_name ?? '-' }}</td>
                  <td>{{ method_exists($complaint, 'getCategoryDisplayAttribute') ? $complaint->getCategoryDisplayAttribute() : ($complaint->category ?? '-') }}</td>
                  <td><span class="badge bg-secondary">{{ method_exists($complaint, 'getStatusDisplayAttribute') ? $complaint->getStatusDisplayAttribute() : ($complaint->status ?? '-') }}</span></td>
                  <td><span class="badge bg-secondary">{{ method_exists($complaint, 'getPriorityDisplayAttribute') ? $complaint->getPriorityDisplayAttribute() : ($complaint->priority ?? '-') }}</span></td>
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
  </div>

  @if(isset($pendingApprovals) && $pendingApprovals->count() > 0)
  <div class="row mt-4">
    <div class="col-12">
      <div class="card-glass">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Pending Approvals</h5>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Approval ID</th>
                <th>Complaint</th>
                <th>Client</th>
                <th>Requested By</th>
                <th>Items</th>
                <th>Created</th>
              </tr>
            </thead>
            <tbody>
              @foreach($pendingApprovals as $approval)
              <tr>
                <td>#{{ $approval->id }}</td>
                <td>{{ optional($approval->complaint)->getTicketNumberAttribute() ?? (optional($approval->complaint)->id ? '#'.optional($approval->complaint)->id : 'N/A') }}</td>
                <td>{{ optional(optional($approval->complaint)->client)->client_name ?? 'N/A' }}</td>
                <td>{{ optional($approval->requestedBy)->name ?? 'N/A' }}</td>
                <td>{{ $approval->items ? $approval->items->count() : 0 }} items</td>
                <td>{{ $approval->created_at?->format('Y-m-d H:i') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  @endif

  @if(isset($lowStockItems) && $lowStockItems->count() > 0)
  <div class="row mt-4">
    <div class="col-12">
      <div class="card-glass">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Low Stock Alerts</h5>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Item</th>
                <th>Category</th>
                <th>Current Stock</th>
                <th>Threshold</th>
                <th>Status</h>
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
                  @if(($item->stock_quantity ?? 0) <= 0)
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

  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script>
    (function() {
      var statusCounts = @json(array_values($complaintsByStatus ?? []));
      var statusLabels = @json(isset($complaintsByStatus) ? array_keys($complaintsByStatus) : []);
      statusLabels = (statusLabels || []).map(function(s){ return (s || '').replace(/_/g,' ').replace(/^./, function(m){ return m.toUpperCase(); }); });
      if (!statusCounts || statusCounts.length === 0) { statusCounts = [0,0,0,0,0]; }
      if (!statusLabels || statusLabels.length === 0) { statusLabels = ['New','Assigned','In progress','Resolved','Closed']; }

      var typeCounts = @json(array_values($complaintsByType ?? []));
      var typeLabels = @json(isset($complaintsByType) ? array_keys($complaintsByType) : []);
      typeLabels = (typeLabels || []).map(function(s){ return (s || '').replace(/^./, function(m){ return m.toUpperCase(); }); });
      if (!typeCounts || typeCounts.length === 0) { typeCounts = [0,0,0,0]; }
      if (!typeLabels || typeLabels.length === 0) { typeLabels = ['Electric','Sanitary','Kitchen','General']; }

      var months = @json(($monthlyTrends['months'] ?? []));
      var complaintsTrend = @json(($monthlyTrends['complaints'] ?? []));
      var resolutionsTrend = @json(($monthlyTrends['resolutions'] ?? []));
      if (!months || months.length === 0) { months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; }
      if (!complaintsTrend || complaintsTrend.length === 0) { complaintsTrend = new Array(months.length).fill(0); }
      if (!resolutionsTrend || resolutionsTrend.length === 0) { resolutionsTrend = new Array(months.length).fill(0); }

      var statusEl = document.querySelector('#complaintsStatusChart');
      if (statusEl && window.ApexCharts) {
        new ApexCharts(statusEl, {
          series: statusCounts,
          chart: { type: 'donut', height: 300, background: 'transparent' },
          labels: statusLabels,
          colors: ['#3b82f6', '#f59e0b', '#a855f7', '#22c55e', '#6b7280'],
          legend: { position: 'bottom' },
          dataLabels: { enabled: true }
        }).render();
      }

      var typeEl = document.querySelector('#complaintsTypeChart');
      if (typeEl && window.ApexCharts) {
        new ApexCharts(typeEl, {
          series: typeCounts,
          chart: { type: 'pie', height: 300, background: 'transparent' },
          labels: typeLabels,
          colors: ['#3b82f6', '#f59e0b', '#a855f7', '#22c55e'],
          legend: { position: 'bottom' },
          dataLabels: { enabled: true }
        }).render();
      }

      var trendsEl = document.querySelector('#monthlyTrendsChart');
      if (trendsEl && window.ApexCharts) {
        new ApexCharts(trendsEl, {
          series: [
            { name: 'Complaints', data: complaintsTrend },
            { name: 'Resolutions', data: resolutionsTrend }
          ],
          chart: { type: 'line', height: 350, background: 'transparent', toolbar: { show: true } },
          xaxis: { categories: months },
          colors: ['#3b82f6', '#22c55e'],
          stroke: { width: 3 },
          markers: { size: 4 }
        }).render();
      }
    })();
  </script>

@endsection


