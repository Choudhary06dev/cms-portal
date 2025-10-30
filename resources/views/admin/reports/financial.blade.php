@extends('layouts.sidebar')

@section('title', 'Financial Report — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Financial Report</h2>
      <p class="text-light">View financial metrics and cost analysis</p>
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
      <i data-feather="dollar-sign" class="me-2"></i>Financial Report
    </h5>
  </div>
  <div class="card-body">
          <!-- Financial Summary -->
          <div class="row mb-4">
            <div class="col-md-3">
              <div class="stats-card">
                <div class="stats-card-body">
                  <div class="stats-icon blue">
                    <i data-feather="dollar-sign"></i>
                  </div>
                  <div class="stats-info">
                    <h3>₹{{ number_format($summary['total_spare_costs'] ?? 0, 2) }}</h3>
                    <p>Total Spare Costs</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stats-card">
                <div class="stats-card-body">
                  <div class="stats-icon green">
                    <i data-feather="layers"></i>
                  </div>
                  <div class="stats-info">
                    <h3>{{ $summary['total_approvals'] ?? 0 }}</h3>
                    <p>Total Approvals</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stats-card">
                <div class="stats-card-body">
                  <div class="stats-icon yellow">
                    <i data-feather="check-square"></i>
                  </div>
                  <div class="stats-info">
                    <h3>{{ $summary['approved_approvals'] ?? 0 }}</h3>
                    <p>Approved</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stats-card">
                <div class="stats-card-body">
                  <div class="stats-icon green">
                    <i data-feather="percent"></i>
                  </div>
                  <div class="stats-info">
                    <h3>{{ round((($summary['approved_approvals'] ?? 0) / max($summary['total_approvals'] ?? 1, 1)) * 100, 1) }}%</h3>
                    <p>Approval Rate</p>
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
            .stats-info h3 { margin:0; color:#fff; font-size:1.4rem; font-weight:700; }
            .stats-info p { margin:0.25rem 0 0; color: rgba(255,255,255,0.7); font-size:0.88rem; }
          </style>

          <!-- Report Period -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="alert alert-info">
                <strong>Report Period:</strong> {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
              </div>
            </div>
          </div>

          <!-- Category Breakdown -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="card-glass">
                <div class="card-header" style="background: transparent; border: none;">
                  <h6 class="card-title text-white">Spare Parts Costs by Category</h6>
                </div>
                <div class="card-body" style="background: transparent;">
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Category</th>
                          <th>Total Cost</th>
                          <th>Percentage</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($summary['category_breakdown'] as $category)
                        <tr>
                          <td>
                            <span class="badge bg-info">{{ ucfirst($category->category) }}</span>
                          </td>
                          <td>
                            <span class="fw-bold">₹{{ number_format($category->total_cost, 2) }}</span>
                          </td>
                          <td>
                            @php
                              $percentage = $summary['total_spare_costs'] > 0 ? round(($category->total_cost / $summary['total_spare_costs']) * 100, 1) : 0;
                            @endphp
                            <div class="d-flex align-items-center">
                              <div class="progress me-2" style="width: 100px; height: 20px;">
                                <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                              </div>
                              <span class="fw-bold">{{ $percentage }}%</span>
                            </div>
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
  fetch(`/admin/reports/download/financial/${format}?${params.toString()}`)
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
        a.download = `financial_report_${new Date().toISOString().split('T')[0]}.json`;
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
