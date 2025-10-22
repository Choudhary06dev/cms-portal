@extends('layouts.sidebar')

@section('title', 'Complaints Report')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Complaints Report</h5>
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
                  <h5 class="card-title">{{ $summary['total_complaints'] ?? 0 }}</h5>
                  <p class="card-text">Total Complaints</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-success text-white">
                <div class="card-body">
                  <h5 class="card-title">{{ $summary['resolved_complaints'] ?? 0 }}</h5>
                  <p class="card-text">Resolved</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-warning text-white">
                <div class="card-body">
                  <h5 class="card-title">{{ $summary['pending_complaints'] ?? 0 }}</h5>
                  <p class="card-text">Pending</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-info text-white">
                <div class="card-body">
                  <h5 class="card-title">{{ round($summary['avg_resolution_time'] ?? 0, 1) }}h</h5>
                  <p class="card-text">Avg Resolution Time</p>
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
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>{{ ucfirst($groupBy) }}</th>
                      <th>Count</th>
                      <th>Percentage</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($data as $item)
                    <tr>
                      <td>
                        @if($groupBy === 'employee' && isset($item->assignedEmployee))
                          {{ $item->assignedEmployee->user->username ?? 'Unknown' }}
                        @elseif($groupBy === 'client' && isset($item->client))
                          {{ $item->client->client_name }}
                        @else
                          {{ ucfirst($item->{$groupBy} ?? $item->category ?? $item->status ?? $item->priority ?? 'Unknown') }}
                        @endif
                      </td>
                      <td>{{ $item->count }}</td>
                      <td>
                        @php
                          $percentage = $summary['total_complaints'] > 0 ? round(($item->count / $summary['total_complaints']) * 100, 1) : 0;
                        @endphp
                        {{ $percentage }}%
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Chart -->
          <div class="row mt-4">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h6 class="card-title">Distribution Chart</h6>
                </div>
                <div class="card-body">
                  <canvas id="complaintsChart" height="100"></canvas>
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
  const ctx = document.getElementById('complaintsChart').getContext('2d');
  const data = @json($data);
  
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: data.map(item => {
        @if($groupBy === 'employee')
          return item.assignedEmployee?.user?.username || 'Unknown';
        @elseif($groupBy === 'client')
          return item.client?.client_name || 'Unknown';
        @else
          return item.{{ $groupBy }} || item.category || item.status || item.priority || 'Unknown';
        @endif
      }),
      datasets: [{
        data: data.map(item => item.count),
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
