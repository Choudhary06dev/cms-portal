@extends('layouts.sidebar')

@section('title', 'Employees Report — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">CMS COMPLAINT MANAGEMENT SYSTEM</h2>
      <p class="text-light">Employee Performance Report</p>
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
<div class="card-glass mb-4" style="display: inline-block; width: fit-content;">
  <div class="card-body">
    <form id="employeesReportFiltersForm" method="GET" class="row g-3">
      <div class="col-md-4">
        <label for="date_from" class="form-label text-white">From Date</label>
        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom }}" required onchange="submitEmployeesReportFilters()">
      </div>
      <div class="col-md-4">
        <label for="date_to" class="form-label text-white">To Date</label>
        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo }}" required onchange="submitEmployeesReportFilters()">
      </div>
      <div class="col-md-4">
        <label for="category" class="form-label text-white">Category</label>
        <select class="form-select" id="category" name="category" onchange="submitEmployeesReportFilters()">
          <option value="">All Categories</option>
          @foreach(\App\Models\Employee::distinct()->whereNotNull('category')->pluck('category') as $cat)
            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
          @endforeach
        </select>
      </div>
    </form>
  </div>
</div>

<!-- SUMMARY STATS -->
<div id="employeesReportSummary">
<div class="row g-4 mb-4">
  <div class="col-md-4">
    <div class="card-glass text-center">
      <div class="card-body">
        <h4 class="text-primary mb-1">{{ $summary['total_employees'] ?? 0 }}</h4>
        <p class="text-muted mb-0">Total Employees</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card-glass text-center">
      <div class="card-body">
        <h4 class="text-success mb-1">{{ number_format($summary['avg_resolution_rate'] ?? 0, 1) }}%</h4>
        <p class="text-muted mb-0">Avg Resolution Rate</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card-glass text-center">
      <div class="card-body">
        <h4 class="text-info mb-1">{{ $summary['top_performer']['employee']->name ?? 'N/A' }}</h4>
        <p class="text-muted mb-0">Top Performer</p>
      </div>
    </div>
  </div>
</div>
</div>

<!-- REPORT TABLE -->
<div class="card-glass" id="employeesReportContent" data-print-area="report-print-area">
  <div class="card-body">
    <div class="text-center mb-3">
      <h4 class="text-white mb-2">Employee Performance Report</h4>
      <p class="text-muted small mb-0">Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</p>
    </div>
    
    <div class="table-responsive">
      <table class="table table-bordered table-dark" style="font-size: 0.875rem;">
        <thead>
          <tr>
            <th>#</th>
            <th>Employee Name</th>
            <th>Category</th>
            <th>Designation</th>
            <th class="text-end">Total Complaints</th>
            <th class="text-end">Resolved</th>
            <th class="text-end">Resolution Rate</th>
          </tr>
        </thead>
        <tbody>
          @forelse($employees as $emp)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $emp['employee']->name ?? 'N/A' }}</td>
            <td>{{ $emp['employee']->category ?? 'N/A' }}</td>
            <td>{{ $emp['employee']->designation ?? '' }}</td>
            <td class="text-end">{{ number_format($emp['total_complaints']) }}</td>
            <td class="text-end">{{ number_format($emp['resolved_complaints']) }}</td>
            <td class="text-end">{{ number_format($emp['resolution_rate'], 1) }}%</td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center py-4">
              <i data-feather="users" class="feather-lg mb-2"></i>
              <div>No employees found</div>
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
    #employeesReportContent, #employeesReportContent * {
      visibility: visible;
    }
    #employeesReportContent {
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
let employeesReportDebounceTimer;

function submitEmployeesReportFilters() {
    clearTimeout(employeesReportDebounceTimer);
    employeesReportDebounceTimer = setTimeout(() => {
        loadEmployeesReport();
    }, 300);
}

function loadEmployeesReport() {
    const form = document.getElementById('employeesReportFiltersForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    
    // Show loading
    const summaryDiv = document.getElementById('employeesReportSummary');
    const content = document.getElementById('employeesReportContent');
    if (summaryDiv) summaryDiv.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></div>';
    if (content) content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    // Update URL without reload
    const url = '{{ route("admin.reports.employees") }}?' + params.toString();
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
        const newSummary = doc.getElementById('employeesReportSummary');
        const newContent = doc.getElementById('employeesReportContent');
        
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
