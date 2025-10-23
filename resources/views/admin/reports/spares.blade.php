@extends('layouts.sidebar')

@section('title', 'Spare Parts Report — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Spare Parts Report</h2>
      <p class="text-light">View spare parts inventory and usage statistics</p>
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
      <i data-feather="package" class="me-2"></i>Spare Parts Report
    </h5>
  </div>
  <div class="card-body">
          <!-- Report Summary -->
          <div class="row mb-4">
            <div class="col-md-3">
              <div class="card bg-primary text-white">
                <div class="card-body">
                  <h5 class="card-title">{{ $summary['total_spares'] ?? 0 }}</h5>
                  <p class="card-text">Total Spare Parts</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-success text-white">
                <div class="card-body">
                  <h5 class="card-title">₹{{ number_format($summary['total_consumption'] ?? 0, 2) }}</h5>
                  <p class="card-text">Total Consumption</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-warning text-white">
                <div class="card-body">
                  <h5 class="card-title">{{ $summary['low_stock_items'] ?? 0 }}</h5>
                  <p class="card-text">Low Stock Items</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-danger text-white">
                <div class="card-body">
                  <h5 class="card-title">{{ $summary['out_of_stock_items'] ?? 0 }}</h5>
                  <p class="card-text">Out of Stock</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Report Period -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="alert alert-info">
                <strong>Report Period:</strong> {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
                @if($category)
                  <br><strong>Category:</strong> {{ ucfirst($category) }}
                @endif
              </div>
            </div>
          </div>

          <!-- Spare Parts Usage Table -->
          <div class="row">
            <div class="col-12">
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Part Name</th>
                      <th>Category</th>
                      <th>Total Used</th>
                      <th>Usage Count</th>
                      <th>Total Cost</th>
                      <th>Current Stock</th>
                      <th>Stock Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($spares as $spareData)
                    <tr>
                      <td>
                        <div>
                          <strong>{{ $spareData['spare']->part_name }}</strong>
                          @if($spareData['spare']->part_number)
                            <br><small class="text-muted">#{{ $spareData['spare']->part_number }}</small>
                          @endif
                        </div>
                      </td>
                      <td>
                        <span class="badge bg-info">{{ ucfirst($spareData['spare']->category) }}</span>
                      </td>
                      <td>
                        <span class="badge bg-primary">{{ $spareData['total_used'] }}</span>
                      </td>
                      <td>
                        <span class="badge bg-secondary">{{ $spareData['usage_count'] }}</span>
                      </td>
                      <td>
                        <span class="badge bg-success">₹{{ number_format($spareData['total_cost'], 2) }}</span>
                      </td>
                      <td>
                        <span class="badge bg-info">{{ $spareData['current_stock'] }}</span>
                      </td>
                      <td>
                        @php
                          $status = $spareData['stock_status'];
                          $badgeClass = $status === 'out_of_stock' ? 'danger' : ($status === 'low_stock' ? 'warning' : 'success');
                          $statusText = $status === 'out_of_stock' ? 'Out of Stock' : ($status === 'low_stock' ? 'Low Stock' : 'In Stock');
                        @endphp
                        <span class="badge bg-{{ $badgeClass }}">{{ $statusText }}</span>
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
  fetch(`/admin/reports/download/spares/${format}?${params.toString()}`)
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
        a.download = `spares_report_${new Date().toISOString().split('T')[0]}.json`;
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
