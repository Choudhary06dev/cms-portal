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
      <a class="btn btn-outline-light" href="{{ route('admin.reports.spares.print', request()->query()) }}" target="_blank">
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
      <i data-feather="package" class="me-2"></i>Spare Parts Report
    </h5>
  </div>
  <div class="card-body">
          <!-- Report Summary -->
          <div class="row mb-4">
            <div class="col-md-3">
              <div class="stats-card">
                <div class="stats-card-body">
                  <div class="stats-icon blue">
                    <i data-feather="package"></i>
                  </div>
                  <div class="stats-info">
                    <h3>{{ $summary['total_spares'] ?? 0 }}</h3>
                    <p>Total Spare Parts</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stats-card">
                <div class="stats-card-body">
                  <div class="stats-icon green">
                    <i data-feather="dollar-sign"></i>
                  </div>
                  <div class="stats-info">
                    <h3>₹{{ number_format($summary['total_consumption'] ?? 0, 2) }}</h3>
                    <p>Total Consumption</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stats-card">
                <div class="stats-card-body">
                  <div class="stats-icon yellow">
                    <i data-feather="alert-triangle"></i>
                  </div>
                  <div class="stats-info">
                    <h3>{{ $summary['low_stock_items'] ?? 0 }}</h3>
                    <p>Low Stock Items</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stats-card">
                <div class="stats-card-body">
                  <div class="stats-icon red">
                    <i data-feather="x-circle"></i>
                  </div>
                  <div class="stats-info">
                    <h3>{{ $summary['out_of_stock_items'] ?? 0 }}</h3>
                    <p>Out of Stock</p>
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
            .stats-icon.red { background: rgba(239,68,68,0.12); color:#ef4444; }
            .stats-info h3 { margin:0; color:#fff; font-size:1.4rem; font-weight:700; }
            .stats-info p { margin:0.25rem 0 0; color: rgba(255,255,255,0.7); font-size:0.88rem; }
          </style>

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
                      <th>Current Stock</th>
                      <th>Stock Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($spares as $spareData)
                    <tr>
                      <td>
                        <div>
                          <strong>{{ $spareData['spare']->item_name }}</strong>
                          @if($spareData['spare']->product_code)
                            <br><small class="text-muted">#{{ $spareData['spare']->product_code }}</small>
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
