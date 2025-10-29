@extends('layouts.sidebar')

@section('title', 'Complaints Report — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Complaints Report</h2>
      <p class="text-light">View complaint statistics and trends</p>
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
      <i data-feather="alert-circle" class="me-2"></i>Complaints Report
    </h5>
  </div>
  <div class="card-body">
          <!-- Report Summary -->
          <div class="row mb-4">
            <div class="col-md-3">
              <div class="card bg-primary text-white">
                <div class="card-body text-center">
                  <h4 class="mb-0">{{ $summary['total_complaints'] ?? 0 }}</h4>
                  <small>Total Complaints</small>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-success text-white">
                <div class="card-body text-center">
                  <h4 class="mb-0">{{ $summary['resolved_complaints'] ?? 0 }}</h4>
                  <small>Resolved</small>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-warning text-white">
                <div class="card-body text-center">
                  <h4 class="mb-0">{{ $summary['pending_complaints'] ?? 0 }}</h4>
                  <small>Pending</small>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-info text-white">
                <div class="card-body text-center">
                  <h4 class="mb-0">{{ round($summary['avg_resolution_time'] ?? 0, 1) }}h</h4>
                  <small>Avg Resolution Time</small>
                </div>
              </div>
            </div>
          </div>

          <!-- Report Period -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="alert alert-info">
                <strong>Report Period:</strong> {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
                <br>
                <strong>Grouped By:</strong> {{ ucfirst($groupBy) }}
              </div>
            </div>
          </div>

          <!-- Report Data -->
          <div class="row">
            <div class="col-12">
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead class="table-dark">
                    <tr>
                      <th>{{ ucfirst($groupBy === 'type' ? 'Category' : str_replace('_', ' ', $groupBy)) }}</th>
                      <th class="text-center">Count</th>
                      <th class="text-center">Percentage</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($data as $item)
                    <tr>
                      <td>
                        @if($groupBy === 'employee' && isset($item->assignedEmployee))
                          {{ $item->assignedEmployee->user->username ?? 'Unknown' }}
                        @elseif($groupBy === 'client' && isset($item->client))
                          {{ $item->client->client_name ?? 'Unknown' }}
                        @elseif($groupBy === 'type')
                          {{ ucfirst($item->category ?? 'Unknown') }}
                        @else
                          {{ ucfirst(str_replace('_', ' ', $item->{$groupBy} ?? 'Unknown')) }}
                        @endif
                      </td>
                      <td>{{ $item->count ?? 0 }}</td>
                      <td>
                        @php
                          $total = $summary['total_complaints'] ?? 1;
                          $percentage = $total > 0 ? round(($item->count / $total) * 100, 1) : 0;
                        @endphp
                        {{ $percentage }}%
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="3" class="text-center text-muted py-4">
                        <i data-feather="inbox" class="feather-lg mb-2"></i>
                        <div>No complaints found for the selected period.</div>
                      </td>
                    </tr>
                    @endforelse
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
@endsection

@push('scripts')
<script>
feather.replace();

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
  fetch(`/admin/reports/download/complaints/${format}?${params.toString()}`)
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
        a.download = `complaints_report_${new Date().toISOString().split('T')[0]}.json`;
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
@endpush
