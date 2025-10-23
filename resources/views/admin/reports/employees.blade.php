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
      <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
        <i data-feather="arrow-left" class="me-2"></i>Back to Reports
      </a>
      <button class="btn btn-accent" onclick="exportReport('pdf')">
        <i data-feather="download" class="me-2"></i>Export PDF
      </button>
      <button class="btn btn-outline-info" onclick="exportReport('excel')">
        <i data-feather="file-text" class="me-2"></i>Export Excel
      </button>
    </div>
  </div>
</div>

<!-- REPORT CONTENT -->
<div class="card-glass">
  <div class="card-header">
    <h5 class="card-title mb-0 text-white">
      <i data-feather="users" class="me-2"></i>Employee Performance Report
    </h5>
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

        </div>
      </div>
    </div>
  </div>
</div>


<script>
function exportReport(format) {
  // Show loading state
  const buttons = document.querySelectorAll('button[onclick*="exportReport"]');
  buttons.forEach(btn => {
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i data-feather="loader" class="me-2"></i>Exporting...';
    btn.disabled = true;
  });
  feather.replace();

  // Get current URL parameters
  const url = new URL(window.location);
  const params = new URLSearchParams(url.search);
  params.set('format', format);
  
  // Make export request
  fetch(`/admin/reports/download/employees/${format}?${params.toString()}`)
    .then(response => {
      if (response.ok) {
        if (format === 'json') {
          return response.json();
        } else {
          return response.json();
        }
      }
      throw new Error('Export failed');
    })
    .then(data => {
      if (format === 'json') {
        // Download JSON file
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        const downloadUrl = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = downloadUrl;
        a.download = `employees_report_${new Date().toISOString().split('T')[0]}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(downloadUrl);
      } else {
        // Show message for PDF/Excel
        alert(data.message || 'Export completed');
      }
      
      // Show success notification
      showNotification(`${format.toUpperCase()} export completed successfully!`, 'success');
    })
    .catch(error => {
      console.error('Export error:', error);
      showNotification('Export failed: ' + error.message, 'error');
    })
    .finally(() => {
      // Reset buttons
      buttons.forEach(btn => {
        const originalText = btn.innerHTML.includes('PDF') ? 
          '<i data-feather="download" class="me-2"></i>Export PDF' :
          '<i data-feather="file-text" class="me-2"></i>Export Excel';
        btn.innerHTML = originalText;
        btn.disabled = false;
      });
      feather.replace();
    });
}

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
