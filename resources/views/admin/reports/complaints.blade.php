@extends('layouts.sidebar')

@section('title', 'SUB-DIVISION WISE PERFORMANCE Report — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">(CMS) COMPLAINT MANAGEMENT SYSTEM</h2>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-success" onclick="window.print()">
        <i data-feather="printer" class="me-2"></i>Print Pdf
      </button>
      <a href="{{ route('admin.reports.complaints', array_merge(request()->query(), ['format' => 'excel'])) }}" class="btn btn-success">
        <i data-feather="download" class="me-2"></i>Excel
      </a>
    </div>
  </div>
</div>

<!-- DATE FILTERS -->
<div class="card-glass mb-4" style="display: inline-block; width: fit-content;">
  <div class="card-body">
    <form id="complaintsReportFiltersForm" method="GET" class="row g-3">
      <div class="col-md-6">
        <label for="date_from" class="form-label text-white">From Date</label>
        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom }}" required onchange="submitComplaintsReportFilters()">
      </div>
      <div class="col-md-6">
        <label for="date_to" class="form-label text-white">To Date</label>
        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo }}" required onchange="submitComplaintsReportFilters()">
      </div>
    </form>
  </div>
</div>

<!-- REPORT TABLE -->
<div class="card-glass" id="complaintsReportContent" data-print-area="report-print-area">
  <div class="card-body">
    <div class="text-center mb-3">
      <h4 class="text-white mb-2">Performance Report</h4>
      <p class="text-muted small mb-0">Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</p>
    </div>
    
    <div class="table-responsive">
      <table class="table table-bordered table-dark" style="font-size: 0.875rem; width: 100%;">
        <thead>
          <tr class="table-header-row">
            <th rowspan="2" class="align-middle text-left" style="min-width: 200px; color: #000000 !important; font-weight: 700 !important; text-align: left !important;">Description</th>
            @foreach($categories as $catKey => $catName)
              <th colspan="2" class="text-center" style="color: #000000 !important; font-weight: 700 !important;">{{ $catName }}</th>
            @endforeach
            <th colspan="2" class="text-center" style="color: #000000 !important; font-weight: 700 !important;">Total</th>
          </tr>
          <tr class="table-header-row">
            @foreach($categories as $catKey => $catName)
              <th class="text-center" style="color: #000000 !important; font-weight: 700 !important;">Qty (No's)</th>
              <th class="text-center" style="color: #000000 !important; font-weight: 700 !important;">%age</th>
            @endforeach
            <th class="text-center" style="color: #000000 !important; font-weight: 700 !important;">Qty (No's)</th>
            <th class="text-center" style="color: #000000 !important; font-weight: 700 !important;">%age</th>
          </tr>
        </thead>
        <tbody>
          @php
            // Status display names mapping
            $statusDisplayNames = [
              'assigned' => 'Assigned',
              'in_progress' => 'In-Process',
              'resolved' => 'Addressed',
              'work' => 'Work',
              'maintenance' => 'Maintenance',
              'work_priced_performa' => 'Work Performa Priced',
              'maint_priced_performa' => 'Maintenance Performa Priced',
              'product_na' => 'Product N/A',
              'un_authorized' => 'Un-Authorized',
              'pertains_to_ge_const_isld' => 'Pertains to GE(N) Const Isld',
            ];
          @endphp
          @foreach($reportData as $rowKey => $row)
          <tr class="{{ $rowKey === 'total' ? 'table-total-row' : '' }}">
            <td class="fw-bold text-left" style="text-align: left !important; {{ $rowKey === 'total' ? 'color: #000000 !important; font-weight: 700 !important;' : '' }}">
              {{ $statusDisplayNames[$rowKey] ?? $row['name'] }}
            </td>
            @foreach($categories as $catKey => $catName)
              @php
                $cellData = $row['categories'][$catKey] ?? ['count' => 0, 'percentage' => 0];
              @endphp
              <td class="text-center" style="{{ $rowKey === 'total' ? 'color: #000000 !important; font-weight: 700 !important;' : '' }}">{{ number_format($cellData['count']) }}</td>
              <td class="text-center" style="{{ $rowKey === 'total' ? 'color: #000000 !important; font-weight: 700 !important;' : '' }}">{{ number_format($cellData['percentage'], 1) }}%</td>
            @endforeach
            @php
              // Calculate grand total: sum of all primary columns
              // Individual E&M NRC columns (Electric, Gas, Water Supply) should be EXCLUDED from Total
              // E&M NRC (Total) should be INCLUDED in Total
              $rowGrandTotal = 0;
              $emNrcTotalKeyLocal = $emNrcTotalKey ?? 'em_nrc_total';
              $hasEmNrcTotal = isset($row['categories'][$emNrcTotalKeyLocal]);
              
              foreach ($row['categories'] as $catKey => $catData) {
                // Always include E&M NRC Total if it exists
                if ($catKey === $emNrcTotalKeyLocal) {
                  $rowGrandTotal += $catData['count'] ?? 0;
                } 
                // For other categories, check if it's an individual E&M NRC column
                elseif (isset($categories[$catKey])) {
                  $catName = $categories[$catKey];
                  
                  // Skip individual E&M NRC columns (Electric, Gas, Water Supply) if E&M NRC Total exists
                  $isIndividualEmNrc = false;
                  if ($hasEmNrcTotal) {
                    // Check if this is one of the 3 individual E&M NRC columns
                    if (stripos($catName, 'E&M NRC') !== false && stripos($catName, 'Total') === false) {
                      $isIndividualEmNrc = true;
                    }
                  }
                  
                  // Include all other columns (non-individual E&M NRC columns)
                  if (!$isIndividualEmNrc) {
                    $rowGrandTotal += $catData['count'] ?? 0;
                  }
                } else {
                  // Include if category key not found in categories array (fallback)
                  $rowGrandTotal += $catData['count'] ?? 0;
                }
              }
              $rowGrandPercent = $grandTotal > 0 ? ($rowGrandTotal / $grandTotal * 100) : 0;
            @endphp
            <td class="text-center fw-bold" style="color: #000000 !important; font-weight: 700 !important;">{{ number_format($rowGrandTotal) }}</td>
            <td class="text-center fw-bold" style="color: #000000 !important; font-weight: 700 !important;">{{ number_format($rowGrandPercent, 1) }}%</td>
          </tr>
          @endforeach
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
    #complaintsReportContent, #complaintsReportContent * {
      visibility: visible;
    }
    #complaintsReportContent {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      background: #fff !important;
      padding: 0 !important;
    }
    /* Remove any boxes/shadows/wrappers in print */
    .card-glass, .card-glass .card-body {
      box-shadow: none !important;
      background: transparent !important;
      border: none !important;
      padding: 0 !important;
      margin: 0 !important;
    }
    .btn, .card-glass .card-body form, .card-header, .mb-4:first-child {
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
    /* Remove outer table border box; keep only cell borders */
    .table-bordered {
      border: none !important;
      border-radius: 0 !important;
    }
    .text-white {
      color: #000 !important;
    }
    .text-muted {
      color: #666 !important;
    }
  }
  .table th, .table td {
    padding: 0.5rem;
    vertical-align: middle;
    text-align: center;
  }
  .table-dark {
    background-color: #1e293b;
    color: #fff;
  }
  .table-dark th {
    background-color: #0f172a;
    border-color: #334155;
    font-weight: bold;
  }
  .table-dark td {
    border-color: #334155;
  }
  .table-responsive {
    overflow-x: auto;
  }
  
  /* Header styling - sirf text black, background original */
  .table-header-black {
    color: #000000 !important;
    font-weight: 700 !important;
  }
  
  /* Total row styling - sirf text black, background original */
  .table-total-row {
    font-weight: 700 !important;
  }
  
  .table-total-row td {
    color: #000000 !important;
    font-weight: 700 !important;
  }
  
  /* Better table styling */
  .table-dark {
    border: 2px solid #000000;
  }
  
  .table-dark th,
  .table-dark td {
    border: 1px solid #334155;
  }
  
  .table-dark tbody tr:hover:not(.table-total-row) {
    background-color: #334155;
  }
  
  /* Print styling */
  @media print {
    .table-header-black {
      color: #000000 !important;
    }
    
    .table-total-row td {
      color: #000000 !important;
    }
  }
</style>
@endpush

@push('scripts')
<script>
let complaintsReportDebounceTimer;

function submitComplaintsReportFilters() {
    clearTimeout(complaintsReportDebounceTimer);
    complaintsReportDebounceTimer = setTimeout(() => {
        loadComplaintsReport();
    }, 300);
}

function loadComplaintsReport() {
    const form = document.getElementById('complaintsReportFiltersForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    
    // Show loading
    const content = document.getElementById('complaintsReportContent');
    if (content) {
        content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    }
    
    // Update URL without reload
    const url = '{{ route("admin.reports.complaints") }}?' + params.toString();
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
        
        // Extract report content - use report-print-area for print compatibility
        const newContent = doc.getElementById('complaintsReportContent') || doc.getElementById('report-print-area') || doc.querySelector('.card-glass');
        
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

