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
      <a class="btn btn-outline-light" href="{{ route('admin.reports.complaints.print', request()->query()) }}" target="_blank">
        <i data-feather="printer" class="me-2"></i>Print Report
      </a>
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
<div id="report-print-area" class="card-glass">
  <div class="card-header">
    <h5 class="card-title mb-0 text-white">
      <i data-feather="alert-circle" class="me-2"></i>Complaints Report
    </h5>
  </div>
  <div class="card-body">
          <!-- Report Summary (same simple cards as Employee page) -->
          <style>
            .metric-card { background: #fff; color: #111827; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); }
            .metric-card .card-body { min-height: 72px; padding: .75rem 1rem; }
            .metric-chip { width: 42px; height: 42px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; flex: 0 0 42px; }
            .chip-blue { background: rgba(59,130,246,0.15); color: #2563eb; }
            .chip-green { background: rgba(34,197,94,0.15); color: #16a34a; }
            .chip-yellow { background: rgba(234,179,8,0.18); color: #a16207; }
            .chip-cyan { background: rgba(56,189,248,0.18); color: #0891b2; }
            .metric-value { margin-bottom: .125rem; font-weight: 700; font-size: 1.05rem; line-height: 1; }
            .metric-label { font-size: .85rem; color: #6b7280; }
          </style>
          <div class="row mb-4">
            <div class="col-md-3">
              <div class="metric-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                  <span class="metric-chip chip-blue">
                    <i data-feather="file-text"></i>
                  </span>
                  <div class="text-end">
                    <div class="metric-value">{{ $summary['total_complaints'] ?? 0 }}</div>
                    <div class="metric-label">Total Complaints</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="metric-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                  <span class="metric-chip chip-green">
                    <i data-feather="check-circle"></i>
                  </span>
                  <div class="text-end">
                    <div class="metric-value">{{ $summary['resolved_complaints'] ?? 0 }}</div>
                    <div class="metric-label">Resolved</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="metric-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                  <span class="metric-chip chip-yellow">
                    <i data-feather="clock"></i>
                  </span>
                  <div class="text-end">
                    <div class="metric-value">{{ $summary['pending_complaints'] ?? 0 }}</div>
                    <div class="metric-label">Pending</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="metric-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                  <span class="metric-chip chip-cyan">
                    <i data-feather="activity"></i>
                  </span>
                  <div class="text-end">
                    <div class="metric-value">{{ round($summary['avg_resolution_time'] ?? 0, 1) }}h</div>
                    <div class="metric-label">Avg Resolution Time</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Report Period -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="alert alert-info">
                <strong>Report Period:</strong> {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
                @if(isset($groupBy))
                <br>
                <strong>Grouped By:</strong> {{ ucfirst($groupBy) }}
                @endif
              </div>
            </div>
          </div>

          <!-- Complaints Table (matches Employee page style) -->
          <div class="row">
            <div class="col-12">
              <div class="table-responsive">
                @if(isset($complaints))
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Ticket</th>
                      <th>Title</th>
                      <th>Client</th>
                      <th>Assigned To</th>
                      <th class="text-center">Priority</th>
                      <th class="text-center">Status</th>
                      <th class="text-center">Created</th>
                      <th class="text-center">Resolution Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($complaints as $complaint)
                    <tr>
                      <td>{{ $complaint->ticket_number ?? 'N/A' }}</td>
                      <td>{{ $complaint->title }}</td>
                      <td>{{ $complaint->client->client_name ?? $complaint->client_name ?? 'Unknown' }}</td>
                      <td>{{ $complaint->assignedEmployee->user->username ?? $complaint->assigned_employee_name ?? 'Unassigned' }}</td>
                      <td class="text-center">
                        <span class="badge bg-{{ ($complaint->priority ?? '') === 'high' ? 'danger' : (($complaint->priority ?? '') === 'medium' ? 'warning' : 'secondary') }}">
                          {{ $complaint->priority_display ?? ucfirst($complaint->priority ?? 'N/A') }}
                        </span>
                      </td>
                      <td class="text-center">
                        <span class="badge bg-{{ in_array(($complaint->status ?? ''), ['resolved','closed']) ? 'success' : (($complaint->status ?? '') === 'in_progress' ? 'info' : 'secondary') }}">
                          {{ $complaint->status_display ?? ucfirst($complaint->status ?? 'N/A') }}
                        </span>
                      </td>
                      <td class="text-center">{{ optional($complaint->created_at)->format('M d, Y') }}</td>
                      <td class="text-center">
                        <span class="badge bg-info">{{ $complaint->resolution_time ? $complaint->resolution_time . 'd' : '-' }}</span>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="8" class="text-center text-muted py-4">
                        <i data-feather="inbox" class="feather-lg mb-2"></i>
                        <div>No complaints found for the selected period.</div>
                      </td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
                @else
                <table class="table table-striped">
                  <thead>
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
                      <td class="text-center">{{ $item->count ?? 0 }}</td>
                      <td class="text-center">
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
                @endif
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
