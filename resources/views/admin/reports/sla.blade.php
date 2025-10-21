@extends('layouts.sidebar')

@section('title', 'SLA Report')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">SLA Compliance Report</h5>
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
          <!-- SLA Summary -->
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
                  <h5 class="card-title">{{ $summary['within_sla'] ?? 0 }}</h5>
                  <p class="card-text">Within SLA</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-danger text-white">
                <div class="card-body">
                  <h5 class="card-title">{{ $summary['breached_sla'] ?? 0 }}</h5>
                  <p class="card-text">SLA Breached</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-info text-white">
                <div class="card-body">
                  <h5 class="card-title">{{ $summary['sla_compliance_rate'] ?? 0 }}%</h5>
                  <p class="card-text">Compliance Rate</p>
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

          <!-- SLA Compliance Chart -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h6 class="card-title">SLA Compliance Overview</h6>
                </div>
                <div class="card-body">
                  <canvas id="slaChart" height="100"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Complaints Table -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h6 class="card-title">Complaints SLA Status</h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Ticket</th>
                          <th>Client</th>
                          <th>Type</th>
                          <th>Status</th>
                          <th>Assigned To</th>
                          <th>Age (Hours)</th>
                          <th>SLA Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($complaints as $complaintData)
                        <tr>
                          <td>
                            <a href="{{ route('admin.complaints.show', $complaintData['complaint']) }}" class="text-decoration-none">
                              {{ $complaintData['complaint']->ticket_number }}
                            </a>
                          </td>
                          <td>{{ $complaintData['complaint']->client->client_name ?? 'N/A' }}</td>
                          <td>
                            <span class="badge bg-info">{{ ucfirst($complaintData['complaint']->complaint_type) }}</span>
                          </td>
                          <td>
                            <span class="badge bg-{{ $complaintData['complaint']->status === 'resolved' ? 'success' : ($complaintData['complaint']->status === 'closed' ? 'info' : 'warning') }}">
                              {{ ucfirst($complaintData['complaint']->status) }}
                            </span>
                          </td>
                          <td>{{ $complaintData['complaint']->assignedEmployee->user->username ?? 'Unassigned' }}</td>
                          <td>
                            <span class="badge bg-{{ $complaintData['age_hours'] > 24 ? 'danger' : 'success' }}">
                              {{ $complaintData['age_hours'] }}h
                            </span>
                          </td>
                          <td>
                            <span class="badge bg-{{ $complaintData['sla_status'] === 'breached' ? 'danger' : 'success' }}">
                              {{ ucfirst(str_replace('_', ' ', $complaintData['sla_status'])) }}
                            </span>
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

          <!-- SLA Performance Chart -->
          <div class="row mt-4">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h6 class="card-title">SLA Performance Over Time</h6>
                </div>
                <div class="card-body">
                  <canvas id="performanceChart" height="100"></canvas>
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
  const complaints = @json($complaints);
  
  // SLA Compliance Chart
  const slaCtx = document.getElementById('slaChart').getContext('2d');
  new Chart(slaCtx, {
    type: 'doughnut',
    data: {
      labels: ['Within SLA', 'SLA Breached'],
      datasets: [{
        data: [summary.within_sla, summary.breached_sla],
        backgroundColor: ['#28a745', '#dc3545'],
        borderColor: ['#1e7e34', '#bd2130'],
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false
    }
  });

  // Performance Chart
  const performanceCtx = document.getElementById('performanceChart').getContext('2d');
  const ageGroups = {
    '0-6h': complaints.filter(c => c.age_hours <= 6).length,
    '6-12h': complaints.filter(c => c.age_hours > 6 && c.age_hours <= 12).length,
    '12-24h': complaints.filter(c => c.age_hours > 12 && c.age_hours <= 24).length,
    '24h+': complaints.filter(c => c.age_hours > 24).length
  };
  
  new Chart(performanceCtx, {
    type: 'bar',
    data: {
      labels: Object.keys(ageGroups),
      datasets: [{
        label: 'Number of Complaints',
        data: Object.values(ageGroups),
        backgroundColor: [
          '#28a745',
          '#ffc107',
          '#fd7e14',
          '#dc3545'
        ],
        borderColor: [
          '#1e7e34',
          '#e0a800',
          '#e55a00',
          '#bd2130'
        ],
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
});

function exportReport(format) {
  const url = new URL(window.location);
  url.searchParams.set('format', format);
  window.open(url.toString(), '_blank');
}
</script>
@endsection
