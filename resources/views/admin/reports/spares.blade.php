@extends('layouts.sidebar')

@section('title', 'Spare Parts Report')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Spare Parts Report</h5>
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

          <!-- Usage Chart -->
          <div class="row mt-4">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h6 class="card-title">Spare Parts Usage Chart</h6>
                </div>
                <div class="card-body">
                  <canvas id="sparesChart" height="100"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Category Breakdown -->
          <div class="row mt-4">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h6 class="card-title">Category Breakdown</h6>
                </div>
                <div class="card-body">
                  <canvas id="categoryChart" height="100"></canvas>
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
  const spares = @json($spares);
  
  // Usage Chart
  const usageCtx = document.getElementById('sparesChart').getContext('2d');
  new Chart(usageCtx, {
    type: 'bar',
    data: {
      labels: spares.map(spare => spare.spare.part_name),
      datasets: [{
        label: 'Total Used',
        data: spares.map(spare => spare.total_used),
        backgroundColor: '#36A2EB',
        borderColor: '#1E88E5',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  // Category Chart
  const categoryCtx = document.getElementById('categoryChart').getContext('2d');
  const categories = {};
  spares.forEach(spare => {
    const category = spare.spare.category;
    if (!categories[category]) {
      categories[category] = 0;
    }
    categories[category] += spare.total_cost;
  });

  new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
      labels: Object.keys(categories),
      datasets: [{
        data: Object.values(categories),
        backgroundColor: [
          '#FF6384',
          '#36A2EB',
          '#FFCE56',
          '#4BC0C0',
          '#9966FF',
          '#FF9F40'
        ]
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false
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
