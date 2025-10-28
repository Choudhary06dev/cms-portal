@extends('layouts.sidebar')

@section('title', 'Dashboard — CMS Admin')

@section('content')
  <style>
  /* Simple and effective light theme for dashboard */
  .theme-light {
    background: #ffffff !important;
    color: #000000 !important;
  }
  
  .theme-light body {
    background: #ffffff !important;
    color: #000000 !important;
  }
  
  .theme-light .content {
    background: #ffffff !important;
    color: #000000 !important;
  }
  
  .theme-light .card-glass {
    background: #ffffff !important;
    border: 1px solid #e5e7eb !important;
    color: #000000 !important;
  }
  
  .theme-light h1, .theme-light h2, .theme-light h3, 
  .theme-light h4, .theme-light h5, .theme-light h6 {
    color: #000000 !important;
  }
  
  .theme-light p, .theme-light span, .theme-light div {
    color: #000000 !important;
  }
  
  .theme-light .text-white {
    color: #000000 !important;
  }
  
  .theme-light .text-light {
    color: #000000 !important;
  }
  
  .theme-light .text-muted {
    color: #6b7280 !important;
  }
  
  .theme-light .text-secondary {
    color: #6b7280 !important;
  }
  
  .theme-light .table {
    color: #000000 !important;
  }
  
  .theme-light .table th {
    color: #000000 !important;
    background: #f9fafb !important;
  }
  
  .theme-light .table td {
    color: #000000 !important;
  }
  
  .theme-light .btn {
    color: #000000 !important;
  }
  
  .theme-light .btn-primary {
    background: #3b82f6 !important;
    border-color: #3b82f6 !important;
    color: #ffffff !important;
  }
  
  .theme-light .btn-outline-primary {
    color: #3b82f6 !important;
    border-color: #3b82f6 !important;
  }
  
  .theme-light .btn-outline-primary:hover {
    background: #3b82f6 !important;
    color: #ffffff !important;
  }
  
  .theme-light .text-primary {
    color: #3b82f6 !important;
  }
  
  .theme-light .text-success {
    color: #22c55e !important;
  }
  
  .theme-light .text-warning {
    color: #f59e0b !important;
  }
  
  .theme-light .text-danger {
    color: #ef4444 !important;
  }
  
  .theme-light .text-info {
    color: #06b6d4 !important;
  }
  
  .theme-light .badge {
    color: #ffffff !important;
  }
  
  .theme-light .form-control {
    background: #ffffff !important;
    border: 1px solid #d1d5db !important;
    color: #000000 !important;
  }
  
  .theme-light .form-control:focus {
    background: #ffffff !important;
    border-color: #3b82f6 !important;
    color: #000000 !important;
  }
  
  .theme-light .form-label {
    color: #000000 !important;
  }
  
  .theme-light .alert {
    background: #ffffff !important;
    border: 1px solid #e5e7eb !important;
    color: #000000 !important;
  }
  
  .theme-light .alert-success {
    background: #f0fdf4 !important;
    border-color: #22c55e !important;
    color: #166534 !important;
  }
  
  .theme-light .alert-warning {
    background: #fffbeb !important;
    border-color: #f59e0b !important;
    color: #92400e !important;
  }
  
  .theme-light .alert-danger {
    background: #fef2f2 !important;
    border-color: #ef4444 !important;
    color: #991b1b !important;
  }
  
  .theme-light .alert-info {
    background: #f0f9ff !important;
    border-color: #06b6d4 !important;
    color: #155e75 !important;
  }
  
  .theme-light .progress {
    background: #f3f4f6 !important;
  }
  
  .theme-light .progress-bar {
    background: #3b82f6 !important;
  }
  
  .theme-light .list-group-item {
    background: #ffffff !important;
    border: 1px solid #e5e7eb !important;
    color: #000000 !important;
  }
  
  .theme-light .list-group-item:hover {
    background: #f9fafb !important;
  }
  
  /* Table hover effects removed - no hover styling */
  
  /* Chart toolbar dropdown visibility */
  .apexcharts-toolbar {
    z-index: 1000 !important;
  }
  
  .apexcharts-menu {
    background: #1f2937 !important;
    border: 1px solid #374151 !important;
    border-radius: 6px !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
  }
  
  .apexcharts-menu-item {
    color: #ffffff !important;
    background: transparent !important;
  }
  
  .apexcharts-menu-item:hover {
    background: #374151 !important;
    color: #ffffff !important;
  }
  
  /* Light theme chart toolbar */
  .theme-light .apexcharts-menu {
    background: #ffffff !important;
    border: 1px solid #d1d5db !important;
    color: #000000 !important;
  }
  
  .theme-light .apexcharts-menu-item {
    color: #000000 !important;
  }
  
  .theme-light .apexcharts-menu-item:hover {
    background: #f3f4f6 !important;
    color: #000000 !important;
  }
  
  /* Chart tooltip styling for light theme */
  .theme-light .apexcharts-tooltip {
    background: #ffffff !important;
    color: #000000 !important;
    border: 1px solid #d1d5db !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
  }
  
  .theme-light .apexcharts-tooltip-title {
    background: #f9fafb !important;
    color: #000000 !important;
    border-bottom: 1px solid #d1d5db !important;
  }
  
  .theme-light .apexcharts-tooltip-series-group {
    background: #ffffff !important;
    color: #000000 !important;
  }
  
  .theme-light .apexcharts-tooltip-y-group {
    color: #000000 !important;
  }
  
  .theme-light .apexcharts-tooltip-marker {
    background: #ffffff !important;
  }
  
  /* Chart tooltip content styling for light theme */
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-title {
    background: #f9fafb !important;
    color: #000000 !important;
    border-bottom: 1px solid #d1d5db !important;
    font-weight: 600 !important;
  }
  
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-series-group {
    background: #ffffff !important;
    color: #000000 !important;
  }
  
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-series-group .apexcharts-tooltip-y-group {
    color: #000000 !important;
  }
  
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-series-group .apexcharts-tooltip-y-group .apexcharts-tooltip-y-label {
    color: #6b7280 !important;
  }
  
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-series-group .apexcharts-tooltip-y-group .apexcharts-tooltip-y-value {
    color: #000000 !important;
    font-weight: 600 !important;
  }
  
  /* Force all tooltip text to be black in light theme */
  .theme-light .apexcharts-tooltip * {
    color: #000000 !important;
  }
  
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-title {
    color: #000000 !important;
  }
  
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-series-group * {
    color: #000000 !important;
  }
  
  /* Specific styling for month/date in tooltip */
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-title {
    background: #f9fafb !important;
    color: #000000 !important;
    border-bottom: 1px solid #d1d5db !important;
  }
  
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-title * {
    color: #000000 !important;
    background: transparent !important;
  }
  
  /* Force tooltip title background to be light */
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-title {
    background: #f9fafb !important;
    background-color: #f9fafb !important;
  }
  
  /* Override all inline styles */
  .theme-light [style*="color: #ffffff"] {
    color: #000000 !important;
  }
  
  /* Force tooltip title to have light background and black text */
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-title[style*="background"] {
    background: #f9fafb !important;
    background-color: #f9fafb !important;
    color: #000000 !important;
  }
  
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-title[style*="color"] {
    color: #000000 !important;
  }
  
  /* Override any dark background styles */
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-title[style*="background-color: #000000"],
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-title[style*="background: #000000"] {
    background: #f9fafb !important;
    background-color: #f9fafb !important;
    color: #000000 !important;
  }
  
  /* Force all ApexCharts tooltips to be light theme */
  .apexcharts-tooltip {
    background: #ffffff !important;
    color: #000000 !important;
    border: 1px solid #d1d5db !important;
  }
  
  .apexcharts-tooltip .apexcharts-tooltip-title {
    background: #f9fafb !important;
    color: #000000 !important;
    border-bottom: 1px solid #d1d5db !important;
  }
  
  .apexcharts-tooltip .apexcharts-tooltip-series-group {
    background: #ffffff !important;
    color: #000000 !important;
  }
  
  .apexcharts-tooltip .apexcharts-tooltip-series-group * {
    color: #000000 !important;
  }
  
  .theme-light [style*="color: #cbd5e1"] {
    color: #000000 !important;
  }
  
  .theme-light [style*="color: #94a3b8"] {
    color: #6b7280 !important;
  }
  
  .theme-light [style*="color: #1e293b"] {
    color: #000000 !important;
  }
  
  .theme-light [style*="color: #f1f5f9"] {
    color: #000000 !important;
  }
  
  .theme-light [style*="color: #64748b"] {
    color: #6b7280 !important;
  }
  
  /* Force ALL headings to be black in light theme */
  .theme-light h1, .theme-light h2, .theme-light h3, 
  .theme-light h4, .theme-light h5, .theme-light h6 {
    color: #000000 !important;
  }
  
  .theme-light .dashboard-header h2 {
    color: #000000 !important;
  }
  
  .theme-light .dashboard-header p {
    color: #000000 !important;
  }
  
  .theme-light .mb-4 h2 {
    color: #000000 !important;
  }
  
  .theme-light .mb-4 p {
    color: #000000 !important;
  }
  
  .theme-light .card-glass h1,
  .theme-light .card-glass h2,
  .theme-light .card-glass h3,
  .theme-light .card-glass h4,
  .theme-light .card-glass h5,
  .theme-light .card-glass h6 {
    color: #000000 !important;
  }
  
  .theme-light .content h1,
  .theme-light .content h2,
  .theme-light .content h3,
  .theme-light .content h4,
  .theme-light .content h5,
  .theme-light .content h6 {
    color: #000000 !important;
  }
  
  /* Override any remaining white text */
  .theme-light * {
    color: #000000 !important;
  }
  
  .theme-light .text-muted {
    color: #6b7280 !important;
  }
  
  .theme-light .text-secondary {
    color: #6b7280 !important;
  }
  
  /* Force all text in light theme to be black */
  .theme-light .text-white {
    color: #000000 !important;
  }
  
  .theme-light .text-light {
    color: #000000 !important;
  }
  
  .theme-light .text-dark {
    color: #000000 !important;
  }
  
  .theme-light .text-body {
    color: #000000 !important;
  }
  
  .theme-light .text-reset {
    color: #000000 !important;
  }
  
  /* Force override of ALL inline styles in light theme */
  .theme-light [style*="color: #ffffff"] {
    color: #000000 !important;
  }
  
  .theme-light [style*="color: #cbd5e1"] {
    color: #000000 !important;
  }
  
  .theme-light [style*="color: #94a3b8"] {
    color: #6b7280 !important;
  }
  
  .theme-light [style*="color: #1e293b"] {
    color: #000000 !important;
  }
  
  .theme-light [style*="color: #f1f5f9"] {
    color: #000000 !important;
  }
  
  .theme-light [style*="color: #64748b"] {
    color: #6b7280 !important;
  }
  
  /* Force all dashboard content to be black */
  .theme-light .dashboard-header * {
    color: #000000 !important;
  }
  
  .theme-light .card-glass * {
    color: #000000 !important;
  }
  
  .theme-light .content * {
    color: #000000 !important;
  }
  
  .theme-light .mb-4 * {
    color: #000000 !important;
  }
  
  .theme-light .row * {
    color: #000000 !important;
  }
  
  .theme-light .col-md-3 * {
    color: #000000 !important;
  }
  
  .theme-light .col-md-6 * {
    color: #000000 !important;
  }
  
  .theme-light .col-md-4 * {
    color: #000000 !important;
  }
  
  .theme-light .col-md-8 * {
    color: #000000 !important;
  }
  
  .theme-light .col-md-12 * {
    color: #000000 !important;
  }
  
  /* Override specific dashboard elements */
  .theme-light .dashboard-header h2[style*="color: #ffffff"] {
    color: #000000 !important;
  }
  
  .theme-light .dashboard-header p[style*="color: #cbd5e1"] {
    color: #000000 !important;
  }
  
  .theme-light .card-glass div[style*="color: #ffffff"] {
    color: #000000 !important;
  }
  
  .theme-light .card-glass div[style*="color: #94a3b8"] {
    color: #000000 !important;
  }
  
  .theme-light .card-glass div[style*="color: #cbd5e1"] {
    color: #000000 !important;
  }
  
  .theme-light .card-glass .h4[style*="color: #ffffff"] {
    color: #000000 !important;
  }
  
  .theme-light .card-glass .h4[style*="color: #f59e0b"] {
    color: #f59e0b !important;
  }
  
  .theme-light .card-glass .h4[style*="color: #22c55e"] {
    color: #22c55e !important;
  }
  
  .theme-light .card-glass .h4[style*="color: #ef4444"] {
    color: #ef4444 !important;
  }
  
  .theme-light .card-glass .h4[style*="color: #3b82f6"] {
    color: #3b82f6 !important;
  }
  
  .theme-light .card-glass .h4[style*="color: #06b6d4"] {
    color: #06b6d4 !important;
  }
  
  /* Force all text in light theme to be black except colored numbers */
  .theme-light .card-glass div[style*="color: #94a3b8"] {
    color: #000000 !important;
  }
  
  .theme-light .card-glass div[style*="color: #cbd5e1"] {
    color: #000000 !important;
  }
  
  .theme-light .card-glass div[style*="color: #ffffff"] {
    color: #000000 !important;
  }
  
  /* Night theme styles */
  .theme-night body {
    background: linear-gradient(135deg, #000000 0%, #111111 100%);
    color: #e5e5e5;
  }
  
  .theme-night .content {
    color: #e5e5e5 !important;
  }
  
  .theme-night .card-glass {
    background: rgba(0, 0, 0, 0.8) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    color: #e5e5e5 !important;
  }
  
  .theme-night h1, .theme-night h2, .theme-night h3, 
  .theme-night h4, .theme-night h5, .theme-night h6 {
    color: #e5e5e5 !important;
  }
  
  .theme-night p, .theme-night span, .theme-night div {
    color: #e5e5e5 !important;
  }
  
  .theme-night .text-white {
    color: #e5e5e5 !important;
  }
  
  .theme-night .text-muted {
    color: #9ca3af !important;
  }
  
  .theme-night .text-secondary {
    color: #9ca3af !important;
  }
  
  .theme-night .table {
    color: #e5e5e5 !important;
  }
  
  .theme-night .table th {
    color: #e5e5e5 !important;
    background: rgba(0, 0, 0, 0.5) !important;
  }
  
  .theme-night .table td {
    color: #e5e5e5 !important;
  }
  
  .theme-night .btn {
    color: #e5e5e5 !important;
  }
  
  .theme-night .btn-primary {
    background: #1d4ed8 !important;
    border-color: #1d4ed8 !important;
    color: #ffffff !important;
  }
  
  .theme-night .btn-outline-primary {
    color: #60a5fa !important;
    border-color: #60a5fa !important;
  }
  
  .theme-night .btn-outline-primary:hover {
    background: #60a5fa !important;
    color: #000000 !important;
  }
  
  .theme-night .text-primary {
    color: #60a5fa !important;
  }
  
  .theme-night .text-success {
    color: #34d399 !important;
  }
  
  .theme-night .text-warning {
    color: #fbbf24 !important;
  }
  
  .theme-night .text-danger {
    color: #f87171 !important;
  }
  
  .theme-night .text-info {
    color: #22d3ee !important;
  }
  
  .theme-night .badge {
    color: #ffffff !important;
  }
  
  /* NUCLEAR OPTION - Force ALL text to be black in light theme */
  .theme-light * {
    color: #000000 !important;
  }
  
  .theme-light .text-muted {
    color: #6b7280 !important;
  }
  
  .theme-light .text-secondary {
    color: #6b7280 !important;
  }
  
  /* Override ALL inline styles with maximum specificity */
  .theme-light h1[style*="color"],
  .theme-light h2[style*="color"],
  .theme-light h3[style*="color"],
  .theme-light h4[style*="color"],
  .theme-light h5[style*="color"],
  .theme-light h6[style*="color"],
  .theme-light p[style*="color"],
  .theme-light span[style*="color"],
  .theme-light div[style*="color"] {
    color: #000000 !important;
  }
  
  /* Force dashboard header to be black */
  .theme-light .dashboard-header h2[style*="color: #ffffff"] {
    color: #000000 !important;
  }
  
  .theme-light .dashboard-header p[style*="color: #cbd5e1"] {
    color: #000000 !important;
  }
  
  /* Force all card content to be black */
  .theme-light .card-glass h1[style*="color"],
  .theme-light .card-glass h2[style*="color"],
  .theme-light .card-glass h3[style*="color"],
  .theme-light .card-glass h4[style*="color"],
  .theme-light .card-glass h5[style*="color"],
  .theme-light .card-glass h6[style*="color"],
  .theme-light .card-glass p[style*="color"],
  .theme-light .card-glass span[style*="color"],
  .theme-light .card-glass div[style*="color"] {
    color: #000000 !important;
  }
  
  /* Override Bootstrap classes with maximum specificity */
  .theme-light .text-white {
    color: #000000 !important;
  }
  
  .theme-light .text-light {
    color: #000000 !important;
  }
  
  .theme-light .text-dark {
    color: #000000 !important;
  }
  
  .theme-light .text-body {
    color: #000000 !important;
  }
  
  .theme-light .text-reset {
    color: #000000 !important;
  }
  
  /* Force all headings to be black */
  .theme-light h1,
  .theme-light h2,
  .theme-light h3,
  .theme-light h4,
  .theme-light h5,
  .theme-light h6 {
    color: #000000 !important;
  }
  
  /* Force all paragraphs to be black */
  .theme-light p {
    color: #000000 !important;
  }
  
  /* Force all spans to be black */
  .theme-light span {
    color: #000000 !important;
  }
  
  /* Force all divs to be black */
  .theme-light div {
    color: #000000 !important;
  }
</style>
<!-- DASHBOARD HEADER -->
<div class="mb-4 dashboard-header">
  <h2 class="text-white mb-2">Dashboard Overview</h2>
  <p class="text-light">Real-time complaint management system</p>
    </div>

    <!-- STATISTICS CARDS -->
    <div class="row mb-4">
      <div class="col-md-3 mb-3">
    <div class="card-glass">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <div class="h4 mb-1 text-white" style="font-size: 2rem; font-weight: bold;">{{ $stats['total_complaints'] ?? 0 }}</div>
          <div class="text-muted" style="font-size: 0.9rem;">Total Complaints</div>
        </div>
        <div class="text-primary">
          <i data-feather="alert-circle" class="feather-lg"></i>
        </div>
      </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
    <div class="card-glass">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <div class="h4 mb-1 text-warning" style="font-size: 2rem; font-weight: bold;">{{ $stats['pending_complaints'] ?? 0 }}</div>
          <div class="text-muted" style="font-size: 0.9rem;">Pending</div>
        </div>
        <div class="text-warning">
          <i data-feather="clock" class="feather-lg"></i>
        </div>
      </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
    <div class="card-glass">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <div class="h4 mb-1 text-success" style="font-size: 2rem; font-weight: bold;">{{ $stats['resolved_complaints'] ?? 0 }}</div>
          <div class="text-muted" style="font-size: 0.9rem;">Resolved</div>
        </div>
        <div class="text-success">
          <i data-feather="check-circle" class="feather-lg"></i>
        </div>
      </div>
        </div>
            </div>
      <div class="col-md-3 mb-3">
    <div class="card-glass">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <div class="h4 mb-1 text-danger" style="font-size: 2rem; font-weight: bold;">{{ $stats['overdue_complaints'] ?? 0 }}</div>
          <div class="text-muted" style="font-size: 0.9rem;">Overdue</div>
        </div>
        <div class="text-danger">
          <i data-feather="alert-triangle" class="feather-lg"></i>
        </div>
      </div>
        </div>
      </div>
    </div>

    <!-- APPROVALS STATISTICS -->
    <div class="row mb-4">
      <div class="col-md-4 mb-3">
        <div class="card-glass">
          <div class="d-flex align-items-center">
            <div class="flex-grow-1">
              <div class="h4 mb-1 text-warning" style="font-size: 2rem; font-weight: bold;">{{ $stats['pending_approvals'] ?? 0 }}</div>
              <div class="text-muted" style="font-size: 0.9rem;">Pending Approvals</div>
            </div>
            <div class="text-warning">
              <i data-feather="clock" class="feather-lg"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card-glass">
          <div class="d-flex align-items-center">
            <div class="flex-grow-1">
              <div class="h4 mb-1 text-success" style="font-size: 2rem; font-weight: bold;">{{ $stats['approved_this_month'] ?? 0 }}</div>
              <div class="text-muted" style="font-size: 0.9rem;">Approved This Month</div>
            </div>
            <div class="text-success">
              <i data-feather="check-circle" class="feather-lg"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card-glass">
          <div class="d-flex align-items-center">
            <div class="flex-grow-1">
              <div class="h4 mb-1 text-info" style="font-size: 2rem; font-weight: bold;">{{ $stats['total_approvals'] ?? 0 }}</div>
              <div class="text-muted" style="font-size: 0.9rem;">Total Approvals</div>
            </div>
            <div class="text-info">
              <i data-feather="file-text" class="feather-lg"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ADDITIONAL STATS -->
    <div class="row mb-4 justify-content-center">
      <div class="col-md-2 mb-3">
    <div class="card-glass text-center">
      <div class="h5 mb-1 text-white" style="font-size: 1.5rem; font-weight: bold;">{{ $stats['total_users'] ?? 0 }}</div>
      <div class="text-muted" style="font-size: 0.8rem;">Users</div>
        </div>
      </div>
      <div class="col-md-2 mb-3">
    <div class="card-glass text-center">
      <div class="h5 mb-1 text-white" style="font-size: 1.5rem; font-weight: bold;">{{ $stats['total_employees'] ?? 0 }}</div>
      <div class="text-muted" style="font-size: 0.8rem;">Employees</div>
        </div>
      </div>
      <div class="col-md-2 mb-3">
    <div class="card-glass text-center">
      <div class="h5 mb-1 text-white" style="font-size: 1.5rem; font-weight: bold;">{{ $stats['total_clients'] ?? 0 }}</div>
      <div class="text-muted" style="font-size: 0.8rem;">Clients</div>
        </div>
      </div>
      <div class="col-md-2 mb-3">
    <div class="card-glass text-center">
      <div class="h5 mb-1 text-warning" style="font-size: 1.5rem; font-weight: bold;">{{ $stats['low_stock_items'] ?? 0 }}</div>
      <div class="text-muted" style="font-size: 0.8rem;">Low Stock</div>
        </div>
      </div>
      <div class="col-md-2 mb-3">
    <div class="card-glass text-center">
      <div class="h5 mb-1 text-success" style="font-size: 1.5rem; font-weight: bold;">{{ $slaPerformance['sla_percentage'] ?? 0 }}%</div>
      <div class="text-muted" style="font-size: 0.8rem;">SLA Performance</div>
        </div>
      </div>
    </div>

    <!-- CHARTS ROW -->
    <div class="row mb-4">
      <div class="col-md-6">
    <div class="card-glass">
      <h5 class="mb-3 text-white" style="font-weight: bold;">Complaints by Status</h5>
      <div id="complaintsStatusChart" style="height: 300px;"></div>
        </div>
      </div>
      <div class="col-md-6">
    <div class="card-glass">
      <h5 class="mb-3 text-white" style="font-weight: bold;">Complaints by Type</h5>
      <div id="complaintsTypeChart" style="height: 300px;"></div>
        </div>
      </div>
        </div>

    <!-- MONTHLY TRENDS CHART -->
    <div class="row mb-4">
      <div class="col-12">
    <div class="card-glass">
      <h5 class="mb-3 text-white" style="font-weight: bold;">Monthly Trends</h5>
      <div id="monthlyTrendsChart" style="height: 350px;"></div>
        </div>
      </div>
            </div>

    <!-- TABLES ROW -->
    <div class="row">
      <div class="col-md-6">
        <div class="card-glass">
          <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 text-white" style="font-weight: bold;">Recent Complaints</h5>
        <a href="{{ route('admin.complaints.index') }}" class="btn btn-accent btn-sm">View All</a>
          </div>
          <div class="table-responsive">
            <table class="table table-dark ">
                <thead>
                <tr>
                  <th>Ticket</th>
                  <th>Client</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Priority</th>
                </tr>
                </thead>
              <tbody>
            @forelse($recentComplaints ?? [] as $complaint)
                <tr>
                  <td>{{ $complaint->getTicketNumberAttribute() }}</td>
                  <td>{{ $complaint->client->client_name }}</td>
                  <td>{{ $complaint->getCategoryDisplayAttribute() }}</td>
                  <td><span class="status-badge status-{{ $complaint->status }}">{{ $complaint->getStatusDisplayAttribute() }}</span></td>
                  <td><span class="priority-badge priority-{{ $complaint->priority }}">{{ $complaint->getPriorityDisplayAttribute() }}</span></td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center py-3">No recent complaints</td>
                </tr>
                @endforelse
                </tbody>
              </table>
        </div>
      </div>
    </div>

    <!-- PENDING APPROVALS SECTION -->
    @if(isset($pendingApprovals) && $pendingApprovals->count() > 0)
    <div class="row mt-4">
      <div class="col-12">
        <div class="card-glass">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 text-warning">
              <i data-feather="clock" class="me-2"></i>Pending Approvals
            </h5>
            <a href="{{ route('admin.approvals.index') }}" class="btn btn-outline-warning btn-sm">View All</a>
          </div>
          <div class="table-responsive">
            <table class="table table-dark">
              <thead>
                <tr>
                  <th>Approval ID</th>
                  <th>Complaint</th>
                  <th>Client</th>
                  <th>Requested By</th>
                  <th>Items</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($pendingApprovals as $approval)
                <tr>
                  <td>#{{ $approval->id }}</td>
                  <td>{{ $approval->complaint ? $approval->complaint->getTicketNumberAttribute() : 'N/A' }}</td>
                  <td>{{ $approval->complaint && $approval->complaint->client ? $approval->complaint->client->client_name : 'N/A' }}</td>
                  <td>{{ $approval->requestedBy && $approval->requestedBy->user ? $approval->requestedBy->user->username : 'N/A' }}</td>
                  <td>{{ $approval->items ? $approval->items->count() : 0 }} items</td>
                  <td>{{ $approval->created_at->format('M d, Y H:i') }}</td>
                  <td>
                    <a href="{{ route('admin.approvals.show', $approval->id) }}" class="btn btn-sm btn-outline-primary">
                      <i data-feather="eye" class="me-1"></i>View
                    </a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    @endif

    <!-- LOW STOCK ALERTS -->
@if(isset($lowStockItems) && $lowStockItems->count() > 0)
    <div class="row mt-4">
      <div class="col-12">
        <div class="card-glass">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 text-warning">
              <i data-feather="alert-triangle" class="me-2"></i>Low Stock Alerts
            </h5>
            <a href="{{ route('admin.spares.index') }}" class="btn btn-outline-warning btn-sm">Manage Stock</a>
        </div>
        <div class="table-responsive">
            <table class="table table-dark ">
            <thead>
                <tr>
                  <th>Item</th>
                  <th>Category</th>
                  <th>Current Stock</th>
                  <th>Threshold</th>
                  <th>Status</th>
                </tr>
            </thead>
              <tbody>
                @foreach($lowStockItems as $item)
                <tr>
                  <td>{{ $item->item_name }}</td>
                  <td>{{ ucfirst($item->category) }}</td>
                  <td>{{ $item->stock_quantity }}</td>
                  <td>{{ $item->threshold_level }}</td>
                  <td>
                    @if($item->stock_quantity <= 0)
                      <span class="badge bg-danger">Out of Stock</span>
                    @else
                      <span class="badge bg-warning">Low Stock</span>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
          </table>
          </div>
        </div>
      </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
  feather.replace();

  // Define chart data
  @php
    $defaultComplaints = [0,0,0,0,0,0,0,0,0,0,0,0];
    $defaultResolutions = [0,0,0,0,0,0,0,0,0,0,0,0];
    $defaultMonths = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  @endphp
  var complaintsData = @json($monthlyTrends['complaints'] ?? $defaultComplaints);
  var resolutionsData = @json($monthlyTrends['resolutions'] ?? $defaultResolutions);
  var monthsData = @json($monthlyTrends['months'] ?? $defaultMonths);

    // Complaints by Status Chart
  @php
    $statusData = isset($complaintsByStatus) ? array_values($complaintsByStatus) : [0, 0, 0, 0, 0];
    $statusLabels = isset($complaintsByStatus) ? array_map(function($status) { return ucfirst(str_replace('_', ' ', $status)); }, array_keys($complaintsByStatus)) : ['New', 'Assigned', 'In Progress', 'Resolved', 'Closed'];
  @endphp
    var complaintsStatusOptions = {
    series: @json($statusData),
      chart: {
        type: 'donut',
        height: 300,
        background: 'transparent'
      },
    labels: @json($statusLabels),
      colors: ['#3b82f6', '#f59e0b', '#a855f7', '#22c55e', '#6b7280'],
      legend: {
        position: 'bottom',
        labels: {
          colors: '#e2e8f0'
        }
      },
      dataLabels: {
        enabled: true,
        style: {
          colors: ['#fff']
        }
      },
      tooltip: {
        theme: document.body.classList.contains('theme-light') ? 'light' : 'dark',
        style: {
          fontSize: '12px',
          fontFamily: 'inherit'
        }
      }
    };

    var complaintsStatusChart = new ApexCharts(document.querySelector("#complaintsStatusChart"), complaintsStatusOptions);
    complaintsStatusChart.render();

    // Complaints by Type Chart
  @php
    $typeData = isset($complaintsByType) ? array_values($complaintsByType) : [0, 0, 0, 0];
    $typeLabels = isset($complaintsByType) ? array_map(function($type) { return ucfirst($type); }, array_keys($complaintsByType)) : ['Electric', 'Sanitary', 'Kitchen', 'General'];
  @endphp
    var complaintsTypeOptions = {
    series: @json($typeData),
      chart: {
        type: 'pie',
        height: 300,
        background: 'transparent'
      },
    labels: @json($typeLabels),
      colors: ['#3b82f6', '#f59e0b', '#a855f7', '#22c55e'],
      legend: {
        position: 'bottom',
        labels: {
          colors: '#e2e8f0'
        }
      },
      dataLabels: {
        enabled: true,
        style: {
          colors: ['#fff']
        }
      },
      tooltip: {
        theme: document.body.classList.contains('theme-light') ? 'light' : 'dark',
        style: {
          fontSize: '12px',
          fontFamily: 'inherit'
        }
      }
    };

    var complaintsTypeChart = new ApexCharts(document.querySelector("#complaintsTypeChart"), complaintsTypeOptions);
    complaintsTypeChart.render();

    // Monthly Trends Chart
    var monthlyTrendsOptions = {
      series: [{
        name: 'Complaints',
      data: complaintsData
      }, {
        name: 'Resolutions',
      data: resolutionsData
      }],
      chart: {
        type: 'line',
        height: 350,
        background: 'transparent',
        toolbar: {
          show: true,
          tools: {
            download: true,
            selection: true,
            zoom: true,
            zoomin: true,
            zoomout: true,
            pan: true,
            reset: true
          }
        }
      },
      colors: ['#3b82f6', '#22c55e'],
      xaxis: {
      categories: monthsData,
        labels: {
          style: {
            colors: document.body.classList.contains('theme-light') ? '#000000' : '#e2e8f0'
          }
        }
      },
      yaxis: {
        labels: {
          style: {
            colors: document.body.classList.contains('theme-light') ? '#000000' : '#e2e8f0'
          }
        }
      },
      legend: {
        labels: {
          colors: document.body.classList.contains('theme-light') ? '#000000' : '#e2e8f0'
        }
      },
      grid: {
        borderColor: document.body.classList.contains('theme-light') ? '#d1d5db' : '#374151'
      },
      stroke: {
        width: 3
      },
      markers: {
        size: 5
      },
      tooltip: {
        theme: 'light',
        style: {
          fontSize: '12px',
          fontFamily: 'inherit'
        }
      }
    };

    var monthlyTrendsChart = new ApexCharts(document.querySelector("#monthlyTrendsChart"), monthlyTrendsOptions);
    monthlyTrendsChart.render();

    // Refresh dashboard function
    function refreshDashboard() {
      location.reload();
    }

    // Auto-refresh every 5 minutes
    setInterval(function() {
      fetch('{{ route("admin.dashboard.real-time-updates") }}')
        .then(response => response.json())
        .then(data => {
          // Update real-time data here if needed
          console.log('Dashboard updated:', data);
        })
        .catch(error => console.error('Error updating dashboard:', error));
    }, 300000); // 5 minutes
</script>
@endpush