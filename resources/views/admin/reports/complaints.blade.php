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
<div class="card-glass mb-4">
  <div class="card-body">
    <form action="{{ route('admin.reports.complaints') }}" method="GET" class="row g-3">
      <div class="col-md-5">
        <label for="date_from" class="form-label text-white">From Date</label>
        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom }}" required>
      </div>
      <div class="col-md-5">
        <label for="date_to" class="form-label text-white">To Date</label>
        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo }}" required>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-accent w-100">Filter</button>
      </div>
      <div class="col-md-12">
        <a href="{{ route('admin.reports.complaints') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
      </div>
    </form>
  </div>
</div>

<!-- REPORT TABLE -->
<div class="card-glass" id="report-print-area">
  <div class="card-body">
    <div class="text-center mb-3">
      <h4 class="text-white mb-2">Performance Report</h4>
      <p class="text-muted small mb-0">Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</p>
    </div>
    
    <div class="table-responsive">
      <table class="table table-bordered table-dark" style="font-size: 0.875rem; width: 100%;">
        <thead>
          <tr>
            <th rowspan="2" class="align-middle text-center" style="min-width: 200px;">Description</th>
            @foreach($categories as $catKey => $catName)
              <th colspan="2" class="text-center">{{ $catName }}</th>
            @endforeach
            <th colspan="2" class="text-center">Total</th>
          </tr>
          <tr>
            @foreach($categories as $catKey => $catName)
              <th class="text-center">Qty (No's)</th>
              <th class="text-center">%age</th>
            @endforeach
            <th class="text-center">Qty (No's)</th>
            <th class="text-center">%age</th>
          </tr>
        </thead>
        <tbody>
          @foreach($reportData as $rowKey => $row)
          <tr>
            <td class="fw-bold">{{ $row['name'] }}</td>
            @foreach($categories as $catKey => $catName)
              @php
                $cellData = $row['categories'][$catKey] ?? ['count' => 0, 'percentage' => 0];
              @endphp
              <td class="text-center">{{ number_format($cellData['count']) }}</td>
              <td class="text-center">{{ number_format($cellData['percentage'], 1) }}%</td>
            @endforeach
            @php
              $rowGrandTotal = array_sum(array_column($row['categories'], 'count'));
              $rowGrandPercent = $grandTotal > 0 ? ($rowGrandTotal / $grandTotal * 100) : 0;
            @endphp
            <td class="text-center fw-bold">{{ number_format($rowGrandTotal) }}</td>
            <td class="text-center fw-bold">{{ number_format($rowGrandPercent, 1) }}%</td>
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
    #report-print-area, #report-print-area * {
      visibility: visible;
    }
    #report-print-area {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      background: #fff !important;
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
    .table-bordered {
      border: 2px solid #000 !important;
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
</style>
@endpush

@endsection

