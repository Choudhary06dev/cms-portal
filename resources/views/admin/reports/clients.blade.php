@extends('layouts.sidebar')

@section('title', 'Clients Report — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Clients Report</h2>
      <p class="text-light">View clients stats and activity</p>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-light" href="{{ route('admin.reports.clients.print', request()->query()) }}" target="_blank">
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
      <i data-feather="briefcase" class="me-2"></i>Clients Report
    </h5>
  </div>
  <div class="card-body">
          <!-- Summary -->
          <div class="row mb-4">
            <div class="col-md-4">
              <div class="stats-card">
                <div class="stats-card-body d-flex align-items-center justify-content-between">
                  <span class="stats-icon blue"><i data-feather="users"></i></span>
                  <div class="text-end">
                    <div class="stats-value">{{ $summary['total_clients'] ?? 0 }}</div>
                    <div class="stats-label">Total Clients</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="stats-card">
                <div class="stats-card-body d-flex align-items-center justify-content-between">
                  <span class="stats-icon green"><i data-feather="check-circle"></i></span>
                  <div class="text-end">
                    <div class="stats-value">{{ $summary['active_clients'] ?? 0 }}</div>
                    <div class="stats-label">Active Clients</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="stats-card">
                <div class="stats-card-body d-flex align-items-center justify-content-between">
                  <span class="stats-icon yellow"><i data-feather="file-text"></i></span>
                  <div class="text-end">
                    <div class="stats-value">{{ $summary['complaints_this_period'] ?? 0 }}</div>
                    <div class="stats-label">Complaints (Period)</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <style>
            .stats-card { background: rgba(255,255,255,0.04); border-radius: 12px; border:1px solid rgba(255,255,255,0.06); margin-bottom:1rem; transition:all 0.25s ease; backdrop-filter: blur(8px); }
            .stats-card:hover { transform: translateY(-6px); box-shadow: 0 12px 24px rgba(0,0,0,0.25); background: rgba(255,255,255,0.06); }
            .stats-card-body { padding:.9rem 1.1rem; }
            .stats-icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; }
            .stats-icon.blue { background: rgba(59,130,246,0.18); color:#3b82f6; }
            .stats-icon.green { background: rgba(34,197,94,0.12); color:#22c55e; }
            .stats-icon.yellow { background: rgba(234,179,8,0.12); color:#eab308; }
            .stats-value { margin:0; color:#fff; font-size:1.1rem; font-weight:700; }
            .stats-label { margin:0.2rem 0 0; color: rgba(255,255,255,0.7); font-size:.82rem; }
          </style>

          <!-- Report Period -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="alert alert-info">
                <strong>Report Period:</strong> {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
                @if($status)
                  <br><strong>Status:</strong> {{ ucfirst($status) }}
                @endif
              </div>
            </div>
          </div>

          <!-- Clients Table -->
          <div class="row">
            <div class="col-12">
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Client</th>
                      <th>Email</th>
                      <th>Status</th>
                      <th>Total Complaints</th>
                      <th>Resolved</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($clients as $row)
                    <tr>
                      <td><strong>{{ $row['client']->client_name }}</strong></td>
                      <td>{{ $row['client']->email ?? 'N/A' }}</td>
                      <td><span class="badge bg-{{ ($row['client']->status ?? '') === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($row['client']->status ?? 'N/A') }}</span></td>
                      <td><span class="badge bg-primary">{{ $row['total_complaints'] }}</span></td>
                      <td><span class="badge bg-success">{{ $row['resolved_complaints'] }}</span></td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="5" class="text-center text-muted py-4">
                        <i data-feather="inbox" class="feather-lg mb-2"></i>
                        <div>No clients found for the selected period.</div>
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

<script></script>
@endsection
