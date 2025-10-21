@extends('layouts.sidebar')

@section('title', 'Financial Report')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Financial Report</h5>
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
          <!-- Financial Summary -->
          <div class="row mb-4">
            <div class="col-md-3">
              <div class="card bg-primary text-white">
                <div class="card-body">
                  <h5 class="card-title">₹{{ number_format($summary['total_spare_costs'] ?? 0, 2) }}</h5>
                  <p class="card-text">Total Spare Costs</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-success text-white">
                <div class="card-body">
                  <h5 class="card-title">{{ $summary['total_approvals'] ?? 0 }}</h5>
                  <p class="card-text">Total Approvals</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-warning text-white">
                <div class="card-body">
                  <h5 class="card-title">{{ $summary['approved_approvals'] ?? 0 }}</h5>
                  <p class="card-text">Approved</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-info text-white">
                <div class="card-body">
                  <h5 class="card-title">{{ round((($summary['approved_approvals'] ?? 0) / max($summary['total_approvals'] ?? 1, 1)) * 100, 1) }}%</h5>
                  <p class="card-text">Approval Rate</p>
                </div>
              </div>
            </div>
          </div>

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
              <div class="card">
                <div class="card-header">
                  <h6 class="card-title">Spare Parts Costs by Category</h6>
                </div>
                <div class="card-body">
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

          <!-- Monthly Approvals Chart -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h6 class="card-title">Monthly Approval Costs</h6>
                </div>
                <div class="card-body">
                  <canvas id="monthlyChart" height="100"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Category Costs Chart -->
          <div class="row mt-4">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h6 class="card-title">Category Cost Distribution</h6>
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
  const summary = @json($summary);
  
  // Monthly Chart
  const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
  const monthlyData = summary.monthly_approvals;
  
  new Chart(monthlyCtx, {
    type: 'line',
    data: {
      labels: Object.keys(monthlyData),
      datasets: [{
        label: 'Approval Costs',
        data: Object.values(monthlyData),
        borderColor: '#36A2EB',
        backgroundColor: 'rgba(54, 162, 235, 0.1)',
        borderWidth: 2,
        fill: true
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
  const categoryData = summary.category_breakdown;
  
  new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
      labels: categoryData.map(cat => cat.category.charAt(0).toUpperCase() + cat.category.slice(1)),
      datasets: [{
        data: categoryData.map(cat => cat.total_cost),
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
