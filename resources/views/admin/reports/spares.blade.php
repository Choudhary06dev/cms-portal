@extends('layouts.sidebar')

@section('title', 'Spare Parts Report — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">CMS COMPLAINT MANAGEMENT SYSTEM</h2>
      <p class="text-light">Spare Parts Inventory Report</p>
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
    <form action="{{ route('admin.reports.spares') }}" method="GET" class="row g-3">
      <div class="col-md-4">
        <label for="date_from" class="form-label text-white">From Date</label>
        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom }}" required>
      </div>
      <div class="col-md-4">
        <label for="date_to" class="form-label text-white">To Date</label>
        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo }}" required>
      </div>
      <div class="col-md-4">
        <label for="category" class="form-label text-white">Category</label>
        <select class="form-select" id="category" name="category">
          <option value="">All Categories</option>
          @foreach(\App\Models\Spare::distinct()->whereNotNull('category')->pluck('category') as $cat)
            <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-12">
        <button type="submit" class="btn btn-accent">Filter</button>
        <a href="{{ route('admin.reports.spares') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
      </div>
    </form>
  </div>
</div>

<!-- SUMMARY STATS -->
<div class="row g-4 mb-4">
  <div class="col-md-3">
    <div class="card-glass text-center">
      <div class="card-body">
        <h4 class="text-primary mb-1">{{ $summary['total_spares'] ?? 0 }}</h4>
        <p class="text-muted mb-0">Total Items</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card-glass text-center">
      <div class="card-body">
        <h4 class="text-success mb-1">{{ number_format($summary['total_usage_count'] ?? 0, 0) }}</h4>
        <p class="text-muted mb-0">Total Usage Count</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card-glass text-center">
      <div class="card-body">
        <h4 class="text-warning mb-1">{{ $summary['low_stock_items'] ?? 0 }}</h4>
        <p class="text-muted mb-0">Low Stock Items</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card-glass text-center">
      <div class="card-body">
        <h4 class="text-danger mb-1">{{ $summary['out_of_stock_items'] ?? 0 }}</h4>
        <p class="text-muted mb-0">Out of Stock</p>
      </div>
    </div>
  </div>
</div>

<!-- REPORT TABLE -->
<div class="card-glass" id="report-print-area">
  <div class="card-body">
    <div class="text-center mb-3">
      <h4 class="text-white mb-2">Spare Parts Inventory Report</h4>
      <p class="text-muted small mb-0">Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</p>
    </div>
    
    <div class="table-responsive">
      <table class="table table-bordered table-dark" style="font-size: 0.875rem;">
        <thead>
          <tr>
            <th>#</th>
            <th>Item Name</th>
            <th>Category</th>
            <th class="text-end">Total Received</th>
            <th class="text-end">Issued Quantity</th>
            <th class="text-end">Balance Quantity</th>
            <th class="text-end">%age Utilized</th>
            <th class="text-end">Usage Count</th>
            <th>Stock Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($spares as $spare)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $spare['spare']->item_name ?? 'N/A' }}</td>
            <td><span class="badge bg-info">{{ ucfirst($spare['spare']->category ?? 'N/A') }}</span></td>
            <td class="text-end"><span class="text-success">{{ number_format($spare['spare']->total_received_quantity ?? 0, 0) }}</span></td>
            <td class="text-end"><span class="text-danger">{{ number_format($spare['total_used'] ?? 0, 0) }}</span></td>
            <td class="text-end">{{ number_format($spare['current_stock'] ?? 0, 0) }}</td>
            <td class="text-end">
              @php
                $totalReceived = $spare['spare']->total_received_quantity ?? 0;
                $utilized = $totalReceived > 0 ? round((($totalReceived - ($spare['current_stock'] ?? 0)) / $totalReceived) * 100, 1) : 0;
              @endphp
              {{ number_format($utilized, 1) }}%
            </td>
            <td class="text-end">{{ number_format($spare['usage_count'] ?? 0, 0) }}</td>
            <td>
              @if(($spare['current_stock'] ?? 0) <= 0)
                <span class="badge bg-danger">Out of Stock</span>
              @elseif(($spare['current_stock'] ?? 0) <= ($spare['spare']->threshold_level ?? 0))
                <span class="badge bg-warning text-dark">Low Stock</span>
              @else
                <span class="badge bg-success">In Stock</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="text-center py-4">
              <i data-feather="package" class="feather-lg mb-2"></i>
              <div>No spare parts found</div>
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

@endsection
