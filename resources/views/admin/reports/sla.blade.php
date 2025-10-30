@extends('layouts.sidebar')

@section('title', 'SLA Report — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">SLA Compliance Report</h2>
      <p class="text-light">View SLA compliance metrics and performance</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
        <i data-feather="arrow-left" class="me-2"></i>Back to Reports
      </a>
      
    </div>
  </div>
</div>

<!-- REPORT CONTENT -->
<div class="card-glass">
  <div class="card-header">
    <h5 class="card-title mb-0 text-white">
      <i data-feather="clock" class="me-2"></i>SLA Compliance Report
    </h5>
  </div>
  <div class="card-body">
          <!-- SLA Summary -->
          <div class="row mb-4">
            <div class="col-md-2">
              <div class="stats-card">
                <div class="stats-card-body">
                  <div class="stats-icon blue">
                    <i data-feather="file-text"></i>
                  </div>
                  <div class="stats-info">
                    <h3>{{ $summary['total_complaints'] ?? 0 }}</h3>
                    <p>Total Complaints</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="stats-card">
                <div class="stats-card-body">
                  <div class="stats-icon green">
                    <i data-feather="check-circle"></i>
                  </div>
                  <div class="stats-info">
                    <h3>{{ $summary['within_sla'] ?? 0 }}</h3>
                    <p>Within SLA</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="stats-card">
                <div class="stats-card-body">
                  <div class="stats-icon red">
                    <i data-feather="alert-circle"></i>
                  </div>
                  <div class="stats-info">
                    <h3>{{ $summary['breached_sla'] ?? 0 }}</h3>
                    <p>SLA Breached</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="stats-card">
                <div class="stats-card-body">
                  <div class="stats-icon yellow">
                    <i data-feather="zap"></i>
                  </div>
                  <div class="stats-info">
                    <h3>{{ $summary['critical_urgent'] ?? 0 }}</h3>
                    <p>Critical</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="stats-card">
                <div class="stats-card-body">
                  <div class="stats-icon cyan">
                    <i data-feather="shield"></i>
                  </div>
                  <div class="stats-info">
                    <h3>{{ $summary['sla_compliance_rate'] ?? 0 }}%</h3>
                    <p>Compliance Rate</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="stats-card">
                <div class="stats-card-body">
                  <div class="stats-icon gray">
                    <i data-feather="clock"></i>
                  </div>
                  <div class="stats-info">
                    <h3>{{ round($summary['average_resolution_time'] ?? 0, 1) }}h</h3>
                    <p>Avg Resolution</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <style>
            .stats-card { background: rgba(255,255,255,0.04); border-radius: 12px; border:1px solid rgba(255,255,255,0.06); margin-bottom:1rem; transition:all 0.25s ease; backdrop-filter: blur(8px); }
            .stats-card:hover { transform: translateY(-6px); box-shadow: 0 12px 24px rgba(0,0,0,0.25); background: rgba(255,255,255,0.06); }
            .stats-card-body { padding:0.8rem 1rem; display:flex; align-items:center; gap:0.9rem; }
            .stats-icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; }
            .stats-icon.blue { background: rgba(59,130,246,0.18); color:#3b82f6; }
            .stats-icon.green { background: rgba(34,197,94,0.12); color:#22c55e; }
            .stats-icon.red { background: rgba(239,68,68,0.12); color:#ef4444; }
            .stats-icon.yellow { background: rgba(234,179,8,0.12); color:#eab308; }
            .stats-icon.cyan { background: rgba(56,189,248,0.12); color:#38bdf8; }
            .stats-icon.gray { background: rgba(148,163,184,0.08); color:#94a3b8; }
            .stats-info h3 { margin:0; color:#fff; font-size:1.1rem; font-weight:700; }
            .stats-info p { margin:0.2rem 0 0; color: rgba(255,255,255,0.7); font-size:0.82rem; }
          </style>

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
                          <th>SLA Limit</th>
                          <th>Time Remaining</th>
                          <th>Urgency</th>
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
                            <span class="badge bg-info">{{ ucfirst($complaintData['complaint']->category) }}</span>
                          </td>
                          <td>
                            <span class="badge bg-{{ $complaintData['complaint']->status === 'resolved' ? 'success' : ($complaintData['complaint']->status === 'closed' ? 'info' : 'warning') }}">
                              {{ ucfirst($complaintData['complaint']->status) }}
                            </span>
                          </td>
                          <td>{{ $complaintData['complaint']->assignedEmployee->name ?? 'Unassigned' }}</td>
                          <td>
                            <span class="badge bg-{{ $complaintData['age_hours'] > $complaintData['max_response_time'] ? 'danger' : 'success' }}">
                              {{ $complaintData['age_hours'] }}h
                            </span>
                          </td>
                          <td>
                            <span class="badge bg-secondary">{{ $complaintData['max_response_time'] }}h</span>
                          </td>
                          <td>
                            @if($complaintData['time_remaining'] > 0)
                              <span class="badge bg-success">{{ $complaintData['time_remaining'] }}h left</span>
                            @else
                              <span class="badge bg-danger">Overdue</span>
                            @endif
                          </td>
                          <td>
                            <span class="badge bg-{{ $complaintData['urgency_level'] === 'critical' ? 'danger' : ($complaintData['urgency_level'] === 'high' ? 'warning' : ($complaintData['urgency_level'] === 'medium' ? 'info' : 'success')) }}">
                              {{ ucfirst($complaintData['urgency_level']) }}
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

          <!-- SLA Rules Summary -->
          <div class="row mt-4">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h6 class="card-title">SLA Rules Performance</h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Complaint Type</th>
                          <th>Max Response Time</th>
                          <th>Total Complaints</th>
                          <th>Within SLA</th>
                          <th>Breached SLA</th>
                          <th>Compliance Rate</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($slaRulesSummary as $ruleSummary)
                        <tr>
                          <td>
                            <span class="badge bg-primary">{{ $ruleSummary['rule']->getComplaintTypeDisplayAttribute() }}</span>
                          </td>
                          <td>
                            <span class="badge bg-info">{{ $ruleSummary['rule']->getMaxResponseTimeDisplayAttribute() }}</span>
                          </td>
                          <td>{{ $ruleSummary['total_complaints'] }}</td>
                          <td>
                            <span class="badge bg-success">{{ $ruleSummary['within_sla'] }}</span>
                          </td>
                          <td>
                            <span class="badge bg-danger">{{ $ruleSummary['breached_sla'] }}</span>
                          </td>
                          <td>
                            <span class="badge bg-{{ $ruleSummary['compliance_rate'] >= 80 ? 'success' : ($ruleSummary['compliance_rate'] >= 60 ? 'warning' : 'danger') }}">
                              {{ $ruleSummary['compliance_rate'] }}%
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
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            color: '#ffffff',
            font: {
              size: 12,
              weight: '500'
            }
          }
        }
      }
    }
  });

  // Performance Chart - Urgency Levels
  const performanceCtx = document.getElementById('performanceChart').getContext('2d');
  const urgencyGroups = {
    'Critical': complaints.filter(c => c.urgency_level === 'critical').length,
    'High': complaints.filter(c => c.urgency_level === 'high').length,
    'Medium': complaints.filter(c => c.urgency_level === 'medium').length,
    'Low': complaints.filter(c => c.urgency_level === 'low').length
  };
  
  new Chart(performanceCtx, {
    type: 'bar',
    data: {
      labels: Object.keys(urgencyGroups),
      datasets: [{
        label: 'Number of Complaints',
        data: Object.values(urgencyGroups),
        backgroundColor: [
          '#dc3545',
          '#ffc107',
          '#17a2b8',
          '#28a745'
        ],
        borderColor: [
          '#bd2130',
          '#e0a800',
          '#138496',
          '#1e7e34'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          labels: {
            color: '#ffffff',
            font: {
              size: 12,
              weight: '500'
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            color: '#ffffff',
            font: {
              size: 11
            }
          },
          grid: {
            color: '#4b5563'
          }
        },
        x: {
          ticks: {
            color: '#ffffff',
            font: {
              size: 11
            }
          },
          grid: {
            color: '#4b5563'
          }
        }
      }
    }
  });
});

function exportReport(format) {
  // Export removed
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
