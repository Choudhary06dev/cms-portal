@extends('layouts.sidebar')

@section('title', 'Employee Performance Report — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Employee Performance Report</h2>
      <p class="text-light">View employee performance metrics and statistics</p>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-light" href="{{ route('admin.reports.employees.print', request()->query()) }}" target="_blank">
        <i data-feather="printer" class="me-2"></i>Print Report
      </a>
      <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
        <i data-feather="arrow-left" class="me-2"></i>Back to Reports
      </a>
    </div>
  </div>
</div>

<!-- REPORT CONTENT -->
<div id="report-print-area" class="card-glass">
  <div class="card-header">
    <h5 class="card-title mb-0 text-white">
      <i data-feather="users" class="me-2"></i>Employee Performance Report
    </h5>
  </div>
  <div class="card-body">
          <!-- Report Summary -->
          <div class="row mb-4">
            <div class="col-md-3">
              <div class="stats-card">
                <div class="stats-card-body">
                  <div class="stats-icon blue">
                    <i data-feather="users"></i>
                  </div>
                  <div class="stats-info">
                    <h3>{{ $summary['total_employees'] ?? 0 }}</h3>
                    <p>Total Employees</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stats-card">
                <div class="stats-card-body">
                  <div class="stats-icon green">
                    <i data-feather="trending-up"></i>
                  </div>
                  <div class="stats-info">
                    <h3>{{ round($summary['avg_resolution_rate'] ?? 0, 1) }}%</h3>
                    <p>Avg Resolution Rate</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stats-card">
                <div class="stats-card-body">
                  <div class="stats-icon yellow">
                    <i data-feather="star"></i>
                  </div>
                  <div class="stats-info">
                    <h3>{{ $summary['top_performer']['employee']['user']['username'] ?? 'N/A' }}</h3>
                    <p>Top Performer</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stats-card">
                <div class="stats-card-body">
                  <div class="stats-icon cyan">
                    <i data-feather="award"></i>
                  </div>
                  <div class="stats-info">
                    <h3>{{ $summary['top_performer']['resolution_rate'] ?? 0 }}%</h3>
                    <p>Top Performance</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <style>
            .stats-card { background: rgba(255,255,255,0.04); border-radius: 12px; border:1px solid rgba(255,255,255,0.06); margin-bottom:1rem; transition:all 0.25s ease; backdrop-filter: blur(8px); }
            .stats-card:hover { transform: translateY(-6px); box-shadow: 0 12px 24px rgba(0,0,0,0.25); background: rgba(255,255,255,0.06); }
            .stats-card-body { padding:1rem 1.25rem; display:flex; align-items:center; gap:1rem; }
            .stats-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; }
            .stats-icon.blue { background: rgba(59,130,246,0.18); color:#3b82f6; }
            .stats-icon.green { background: rgba(34,197,94,0.12); color:#22c55e; }
            .stats-icon.yellow { background: rgba(234,179,8,0.12); color:#eab308; }
            .stats-icon.cyan { background: rgba(56,189,248,0.12); color:#38bdf8; }
            .stats-info h3 { margin:0; color:#fff; font-size:1.4rem; font-weight:700; }
            .stats-info p { margin:0.25rem 0 0; color: rgba(255,255,255,0.7); font-size:0.88rem; }
          </style>

          <!-- Report Period -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="alert alert-info">
                <strong>Report Period:</strong> {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
                @if($department)
                  <br><strong>Department:</strong> {{ ucfirst($department) }}
                @endif
              </div>
            </div>
          </div>

          <!-- Employee Performance Table -->
          <div class="row">
            <div class="col-12">
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Employee</th>
                      <th>Department</th>
                      <th>Total Complaints</th>
                      <th>Resolved</th>
                      <th>Resolution Rate</th>
                      <th>Avg Resolution Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($employees as $employeeData)
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                            <span class="text-white fw-bold">{{ substr($employeeData['employee']->name, 0, 1) }}</span>
                          </div>
                          <div>
                            <strong>{{ $employeeData['employee']->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $employeeData['employee']->email }}</small>
                          </div>
                        </div>
                      </td>
                      <td>
                        <span class="badge bg-info">{{ ucfirst($employeeData['employee']->department) }}</span>
                      </td>
                      <td>
                        <span class="badge bg-primary">{{ $employeeData['total_complaints'] }}</span>
                      </td>
                      <td>
                        <span class="badge bg-success">{{ $employeeData['resolved_complaints'] }}</span>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="progress me-2" style="width: 100px; height: 20px;">
                            <div class="progress-bar bg-{{ $employeeData['resolution_rate'] >= 80 ? 'success' : ($employeeData['resolution_rate'] >= 60 ? 'warning' : 'danger') }}" 
                                 style="width: {{ $employeeData['resolution_rate'] }}%"></div>
                          </div>
                          <span class="fw-bold">{{ $employeeData['resolution_rate'] }}%</span>
                        </div>
                      </td>
                      <td>
                        <span class="badge bg-info">{{ round($employeeData['avg_resolution_time'], 1) }}h</span>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>


@push('styles')
<style>
  @media print {
    @page { size: A5 portrait; margin: 10mm; }
    body * { visibility: hidden !important; }
    #report-print-area, #report-print-area * { visibility: visible !important; }
    #report-print-area { position: absolute; left: 0; top: 0; width: 100%; max-width: 700px; }
    .topbar, .sidebar { display: none !important; }
    .card-glass .btn, .btn { display: none !important; }
    html, body { font-size: 11px; }
    .table { font-size: 11px; }
  }
</style>
@endpush

<script>
function printReport(){ window.print(); }
function showNotification(message, type = 'info') {
  // Create notification element
  const notification = document.createElement('div');
  notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
  notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
  notification.innerHTML = `
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  `;
  
  document.body.appendChild(notification);
  
  // Auto remove after 5 seconds
  setTimeout(() => {
    if (notification.parentNode) {
      notification.parentNode.removeChild(notification);
    }
  }, 5000);
}
</script>
@endsection
