@extends('layouts.sidebar')

@section('title', 'Clients Report — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">CMS COMPLAINT MANAGEMENT SYSTEM</h2>
      <p class="text-light">Clients Report</p>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-success" onclick="window.print()">
        <i data-feather="printer" class="me-2"></i>Print
      </button>
      <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
        <i data-feather="arrow-left" class="me-2"></i>Back to Reports
      </a>
    </div>
  </div>
</div>

<!-- DATE FILTERS -->
<div class="card-glass mb-4">
  <div class="card-body">
    <form id="clientsReportFiltersForm" method="GET" class="row g-3">
      <div class="col-md-4">
        <label for="date_from" class="form-label text-white">From Date</label>
        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom }}" required onchange="submitClientsReportFilters()">
      </div>
      <div class="col-md-4">
        <label for="date_to" class="form-label text-white">To Date</label>
        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo }}" required onchange="submitClientsReportFilters()">
      </div>
      <div class="col-md-4">
        <label for="status" class="form-label text-white">Status</label>
        <select class="form-select" id="status" name="status" onchange="submitClientsReportFilters()">
          <option value="">All Status</option>
          <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>
    </form>
  </div>
</div>

<!-- SUMMARY STATS -->
<div id="clientsReportSummary">
<div class="row g-4 mb-4">
  <div class="col-md-4">
    <div class="card-glass text-center">
      <div class="card-body">
        <h4 class="text-primary mb-1">{{ $summary['total_clients'] ?? 0 }}</h4>
        <p class="text-muted mb-0">Total Clients</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card-glass text-center">
      <div class="card-body">
        <h4 class="text-success mb-1">{{ $summary['active_clients'] ?? 0 }}</h4>
        <p class="text-muted mb-0">Active Clients</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card-glass text-center">
      <div class="card-body">
        <h4 class="text-info mb-1">{{ $summary['complaints_this_period'] ?? 0 }}</h4>
        <p class="text-muted mb-0">Complaints This Period</p>
      </div>
    </div>
  </div>
</div>
</div>

<!-- REPORT TABLE -->
<div class="card-glass" id="clientsReportContent" data-print-area="report-print-area">
  <div class="card-body">
    <div class="text-center mb-3">
      <h4 class="text-white mb-2">Clients Report</h4>
      <p class="text-muted small mb-0">Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</p>
    </div>
    
    <div class="table-responsive">
      <table class="table table-bordered table-dark" style="font-size: 0.875rem;">
        <thead>
          <tr>
            <th>#</th>
            <th>Client Name</th>
            <th>Contact Person</th>
            <th>Email</th>
            <th>Phone</th>
            <th>City</th>
            <th>Address</th>
            <th>Sector</th>
            <th class="text-end">Total Complaints</th>
            <th class="text-end">Resolved</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($clients as $client)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $client['client']->client_name ?? 'N/A' }}</td>
            <td>{{ $client['client']->contact_person ?? 'N/A' }}</td>
            <td>{{ $client['client']->email ?? 'N/A' }}</td>
            <td>{{ $client['client']->phone ?? 'N/A' }}</td>
            <td>{{ $client['client']->city ?? 'N/A' }}</td>
            <td>{{ $client['client']->address ?? 'N/A' }}</td>
            <td>{{ $client['client']->sector ?? 'N/A' }}</td>
            <td class="text-end">{{ number_format($client['total_complaints']) }}</td>
            <td class="text-end">{{ number_format($client['resolved_complaints']) }}</td>
            <td>
              @if(($client['client']->status ?? '') == 'active')
                <span class="badge bg-success">Active</span>
              @else
                <span class="badge bg-secondary">Inactive</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="11" class="text-center py-4">
              <i data-feather="users" class="feather-lg mb-2"></i>
              <div>No clients found</div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@push('styles')
<style>
  @media print {
    body * {
      visibility: hidden;
    }
    #clientsReportContent, #clientsReportContent * {
      visibility: visible;
    }
    #clientsReportContent {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      background: #fff !important;
    }
    .btn, .card-glass .card-body form, .mb-4:first-child {
      display: none !important;
    }
    .table-dark {
      background-color: #fff !important;
      color: #000 !important;
    }
    .table-dark th {
      background-color: #f8f9fa !important;
      color: #000 !important;
      border: 1px solid #000 !important;
    }
    .table-dark td {
      border: 1px solid #000 !important;
      color: #000 !important;
    }
    .text-white {
      color: #000 !important;
    }
    .text-muted {
      color: #666 !important;
    }
  }
</style>
@endpush

@push('scripts')
<script>
let clientsReportDebounceTimer;

function submitClientsReportFilters() {
    clearTimeout(clientsReportDebounceTimer);
    clientsReportDebounceTimer = setTimeout(() => {
        loadClientsReport();
    }, 300);
}

function loadClientsReport() {
    const form = document.getElementById('clientsReportFiltersForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    
    // Show loading
    const summaryDiv = document.getElementById('clientsReportSummary');
    const content = document.getElementById('clientsReportContent');
    if (summaryDiv) summaryDiv.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></div>';
    if (content) content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    // Update URL without reload
    const url = '{{ route("admin.reports.clients") }}?' + params.toString();
    window.history.pushState({}, '', url);
    
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html',
        }
    })
    .then(response => response.text())
    .then(html => {
        // Parse the response
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Extract summary and report content
        const newSummary = doc.getElementById('clientsReportSummary');
        const newContent = doc.getElementById('clientsReportContent') || doc.getElementById('report-print-area');
        
        if (newSummary && summaryDiv) {
            summaryDiv.innerHTML = newSummary.innerHTML;
        }
        if (newContent && content) {
            content.innerHTML = newContent.innerHTML;
        }
        
        // Re-initialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    })
    .catch(error => {
        console.error('Error loading report:', error);
        if (content) {
            content.innerHTML = '<div class="alert alert-danger">Error loading report. Please refresh the page.</div>';
        }
    });
}
</script>
@endpush

@endsection
