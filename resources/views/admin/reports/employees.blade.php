@extends('layouts.admin')

@section('title', 'Employee Performance Report')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Employee Performance Report</h5>
          <div class="btn-group">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary btn-sm">
              <i data-feather="arrow-left"></i> Back to Reports
            </a>
            <button class="btn btn-success btn-sm" onclick="exportReport('pdf')">
              <i data-feather="download"></i> Export PDF
            </button>
            <button class="btn btn-info btn-sm" onclick="exportReport('excel')">
              <i data-feather="file-text"></i> Export Excel
            </button>
          </div>
        </div>
        <div class="card-body">
          <!-- Report Summary -->
          <div class="row mb-4">
            <div class="col-md-3">
              <div class="card bg-primary text-white">
                <div class="card-body">
                  <h5 class="card-title">{{ $summary['total_employees'] ?? 0 }}</h5>
                  <p class="card-text">Total Employees</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-success text-white">
                <div class="card-body">
                  <h5 class="card-title">{{ round($summary['avg_resolution_rate'] ?? 0, 1) }}%</h5>
                  <p class="card-text">Avg Resolution Rate</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-warning text-white">
                <div class="card-body">
                  <h5 class="card-title">{{ $summary['top_performer']['employee']['user']['username'] ?? 'N/A' }}</h5>
                  <p class="card-text">Top Performer</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-info text-white">
                <div class="card-body">
                  <h5 class="card-title">{{ $summary['top_performer']['resolution_rate'] ?? 0 }}%</h5>
                  <p class="card-text">Top Performance</p>
                </div>
              </div>
            </div>
          </div>

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
                            <span class="text-white fw-bold">{{ substr($employeeData['employee']->user->username, 0, 1) }}</span>
                          </div>
                          <div>
                            <strong>{{ $employeeData['employee']->user->username }}</strong>
                            <br>
                            <small class="text-muted">{{ $employeeData['employee']->user->email }}</small>
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

          <!-- Performance Chart -->
          <div class="row mt-4">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h6 class="card-title">Employee Performance Chart</h6>
                </div>
                <div class="card-body">
                  <canvas id="employeesChart" height="100"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const ctx = document.getElementById('employeesChart').getContext('2d');
  const employees = @json($employees);
  
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: employees.map(emp => emp.employee.user.username),
      datasets: [{
        label: 'Resolution Rate (%)',
        data: employees.map(emp => emp.resolution_rate),
        backgroundColor: employees.map(emp => 
          emp.resolution_rate >= 80 ? '#28a745' : 
          emp.resolution_rate >= 60 ? '#ffc107' : '#dc3545'
        ),
        borderColor: employees.map(emp => 
          emp.resolution_rate >= 80 ? '#1e7e34' : 
          emp.resolution_rate >= 60 ? '#e0a800' : '#bd2130'
        ),
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          max: 100
        }
      }
    }
  });
});

function exportReport(format) {
  const url = new URL(window.location);
  url.searchParams.set('format', format);
  window.open(url.toString(), '_blank');
}
</script>
@endsection
