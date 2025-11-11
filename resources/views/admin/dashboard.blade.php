
@extends('layouts.sidebar')

@section('title', 'Dashboard — CMS Admin')

@section('content')
  <style>
  /* Enhanced & Attractive Light Theme for Dashboard */
  .theme-light {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
    color: #1e293b !important;
  }
  
  .theme-light body {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
    color: #1e293b !important;
  }
  
  .theme-light .content {
    background: transparent !important;
    color: #1e293b !important;
  }
  
  /* Modern Glass Morphism Cards */
  .theme-light .card-glass {
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.9) 100%) !important;
    border: 1px solid rgba(59, 130, 246, 0.1) !important;
    border-radius: 20px !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04) !important;
    backdrop-filter: blur(10px) !important;
    color: #1e293b !important;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
    overflow: hidden !important;
    position: relative !important;
  }
  
  .theme-light .card-glass::before {
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    height: 3px !important;
    background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899) !important;
    opacity: 0 !important;
    transition: opacity 0.4s ease !important;
  }
  
  .theme-light .card-glass:hover::before {
    opacity: 1 !important;
  }
  
  .theme-light .card-glass:hover {
    box-shadow: 0 16px 48px rgba(59, 130, 246, 0.15), 0 8px 16px rgba(59, 130, 246, 0.1) !important;
    transform: translateY(-4px) scale(1.02) !important;
    border-color: rgba(59, 130, 246, 0.3) !important;
  }
  
  /* Gradient Stat Cards - Dark Theme with Original Colors */
  .stat-card {
    border-radius: 16px !important;
    padding: 0.75rem 1rem !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
    position: relative !important;
    overflow: hidden !important;
    min-height: 90px !important;
    height: 100% !important;
    color: #ffffff !important;
    max-width: 100% !important;
    display: flex !important;
    flex-direction: column !important;
  }
  
  /* Reduce height for text-center cards (Users, Employees, etc.) */
  .stat-card.text-center {
    padding: 0.6rem 0.75rem !important;
    min-height: 90px !important;
  }
  
  .stat-card.text-center .stat-icon {
    width: 35px !important;
    height: 35px !important;
    margin-bottom: 0.5rem !important;
  }
  
  .stat-card.text-center .stat-number {
    font-size: 1.3rem !important;
    margin-bottom: 0.25rem !important;
  }
  
  .stat-card.text-center .stat-label {
    font-size: 0.7rem !important;
    margin-top: 0 !important;
  }
  
  /* Ensure all cards have equal height */
  .row .col-md-2,
  .row .col-lg-2 {
    display: flex !important;
  }
  
  .row .col-md-2 > .stat-card,
  .row .col-lg-2 > .stat-card {
    width: 100% !important;
  }
  
  /* Force all text inside stat-card to be white */
  .stat-card * {
    color: #ffffff !important;
  }
  
  /* Override any inline color styles */
  .stat-card *[style*="color"] {
    color: #ffffff !important;
  }
  
  .stat-card .text-primary,
  .stat-card .text-success,
  .stat-card .text-danger,
  .stat-card .text-warning,
  .stat-card .text-info,
  .stat-card .text-muted {
    color: #ffffff !important;
  }
  
  /* Force all numbers and text content to be white */
  .stat-card .flex-grow-1,
  .stat-card .flex-grow-1 * {
    color: #ffffff !important;
  }
  
  .stat-card::after {
    content: '' !important;
    position: absolute !important;
    top: -50% !important;
    right: -50% !important;
    width: 200% !important;
    height: 200% !important;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%) !important;
    opacity: 0 !important;
    transition: opacity 0.6s ease !important;
  }
  
  .stat-card:hover::after {
    opacity: 1 !important;
    animation: shimmer 2s infinite !important;
  }
  
  @keyframes shimmer {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
  }
  
  .stat-card:hover {
    transform: translateY(-4px) scale(1.02) !important;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4) !important;
    border-color: rgba(255, 255, 255, 0.2) !important;
  }
  
  /* Stat Card Icon Containers */
  .stat-icon {
    width: 45px !important;
    height: 45px !important;
    border-radius: 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    backdrop-filter: blur(10px) !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2) !important;
    transition: all 0.4s ease !important;
    background: rgba(255, 255, 255, 0.2) !important;
  }
  
  .stat-icon i,
  .stat-icon svg,
  .stat-card i,
  .stat-card svg {
    color: #ffffff !important;
    stroke: #ffffff !important;
    fill: #ffffff !important;
  }
  
  .stat-icon .feather-lg {
    width: 24px !important;
    height: 24px !important;
  }
  
  /* Ensure feather icons are white */
  .stat-card [data-feather],
  .stat-card i[data-feather],
  .stat-card svg[data-feather] {
    color: #ffffff !important;
    stroke: #ffffff !important;
  }
  
  /* Force SVG stroke to white for all icons in stat-card */
  .stat-card svg {
    stroke: #ffffff !important;
    color: #ffffff !important;
  }
  
  /* Override any inline styles for icons */
  .stat-card i[style*="color"],
  .stat-card svg[style*="color"],
  .stat-card svg[style*="stroke"] {
    color: #ffffff !important;
    stroke: #ffffff !important;
  }
  
  .stat-card:hover .stat-icon {
    transform: rotate(360deg) scale(1.1) !important;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
  }
  
  /* Animated Number Display */
  .stat-number {
    font-size: 1.4rem !important;
    font-weight: 800 !important;
    color: #ffffff !important;
    letter-spacing: -0.02em !important;
    line-height: 1.2 !important;
  }
  
  /* Force all numbers to be white */
  .stat-card .stat-number,
  .stat-card div.stat-number,
  .stat-card span.stat-number {
    color: #ffffff !important;
  }
  
  .stat-label {
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    color: #ffffff !important;
    margin-top: 0.25rem !important;
    letter-spacing: 0.3px !important;
    line-height: 1.2 !important;
  }
  
  /* Force all labels to be white */
  .stat-card .stat-label,
  .stat-card div.stat-label,
  .stat-card span.stat-label {
    color: #ffffff !important;
  }
  
  /* Force all divs, spans, and text nodes to be white */
  .stat-card div,
  .stat-card span,
  .stat-card p,
  .stat-card h1,
  .stat-card h2,
  .stat-card h3,
  .stat-card h4,
  .stat-card h5,
  .stat-card h6 {
    color: #ffffff !important;
  }
  
  /* Typography Enhancements */
  .theme-light h1, .theme-light h2, .theme-light h3, 
  .theme-light h4, .theme-light h5, .theme-light h6 {
    color: #0f172a !important;
    font-weight: 700 !important;
    letter-spacing: -0.02em !important;
  }
  
  .theme-light .dashboard-header h2 {
    color: #0f172a !important;
    font-weight: 800 !important;
    font-size: 2.25rem !important;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
    -webkit-background-clip: text !important;
    background-clip: text !important;
  }
  
  .theme-light .dashboard-header p {
    color: #64748b !important;
    font-size: 1rem !important;
    font-weight: 500 !important;
  }
  
  /* Enhanced Button Styling */
  .theme-light .btn {
    font-weight: 600 !important;
    border-radius: 12px !important;
    padding: 0.625rem 1.5rem !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    letter-spacing: 0.3px !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
  }
  
  .theme-light .btn-primary,
  .theme-light .btn-accent {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
    border: none !important;
    color: #ffffff !important;
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3) !important;
  }
  
  .theme-light .btn-primary:hover,
  .theme-light .btn-accent:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
    box-shadow: 0 8px 24px rgba(59, 130, 246, 0.4) !important;
    transform: translateY(-2px) !important;
  }
  
  .theme-light .btn-outline-secondary {
    color: #64748b !important;
    border: 2px solid #e2e8f0 !important;
    background: transparent !important;
  }
  
  .theme-light .btn-outline-secondary:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: #475569 !important;
  }
  
  /* Modern Table Styling */
  .theme-light .table {
    color: #1e293b !important;
    background: transparent !important;
    border-radius: 12px !important;
    overflow: hidden !important;
  }
  
  .theme-light .table th {
    color: #0f172a !important;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%) !important;
    font-weight: 700 !important;
    border-bottom: 2px solid #cbd5e1 !important;
    padding: 1rem !important;
    text-transform: uppercase !important;
    font-size: 0.75rem !important;
    letter-spacing: 0.5px !important;
  }
  
  .theme-light .table td {
    color: #334155 !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 1rem !important;
    font-weight: 500 !important;
  }
  
  .theme-light .table tbody tr {
    transition: all 0.2s ease !important;
  }
  
  .theme-light .table tbody tr:hover {
    background: linear-gradient(90deg, #f8fafc 0%, #ffffff 100%) !important;
    transform: scale(1.01) !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
  }
  
  /* Enhanced Badge Styling */
  .theme-light .badge,
  .theme-light .status-badge,
  .theme-light .priority-badge {
    font-weight: 700 !important;
    padding: 0.5rem 1rem !important;
    border-radius: 10px !important;
    font-size: 0.75rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
    border: 2px solid transparent !important;
    transition: all 0.3s ease !important;
  }
  
  .theme-light .badge:hover,
  .theme-light .status-badge:hover,
  .theme-light .priority-badge:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
  }
  
  /* Form Controls */
  .theme-light .form-control,
  .theme-light .form-select {
    background: #ffffff !important;
    border: 2px solid #e2e8f0 !important;
    color: #1e293b !important;
    border-radius: 10px !important;
    padding: 0.625rem 1rem !important;
    font-weight: 500 !important;
    transition: all 0.3s ease !important;
  }
  
  .theme-light .form-control:focus,
  .theme-light .form-select:focus {
    background: #ffffff !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
    transform: translateY(-1px) !important;
  }
  
  .theme-light .form-label {
    color: #1e293b !important;
    font-weight: 600 !important;
    font-size: 0.875rem !important;
    letter-spacing: 0.3px !important;
  }
  
  /* Progress Bars with Gradient */
  .theme-light .progress {
    background: #f1f5f9 !important;
    border-radius: 10px !important;
    height: 16px !important;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06) !important;
    overflow: hidden !important;
  }
  
  .theme-light .progress-bar {
    background: linear-gradient(90deg, #3b82f6, #8b5cf6) !important;
    border-radius: 10px !important;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3) !important;
    transition: width 0.6s ease !important;
  }
  
  /* Chart Container Enhancements */
  .chart-container {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%) !important;
    border-radius: 20px !important;
    padding: 2rem !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08) !important;
  }
  
  /* Filter Box Styling */
  .filter-box {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%) !important;
    border-radius: 20px !important;
    padding: 1.5rem 2rem !important;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06) !important;
    border: 1px solid rgba(59, 130, 246, 0.1) !important;
  }
  
  /* Icon Feature Enhancements */
  .feather-lg {
    width: 32px !important;
    height: 32px !important;
    stroke-width: 2.5 !important;
  }
  
  /* Alert Boxes */
  .theme-light .alert {
    background: #ffffff !important;
    border: 2px solid #e5e7eb !important;
    border-radius: 16px !important;
    padding: 1.25rem 1.5rem !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06) !important;
  }
  
  .theme-light .alert-success {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%) !important;
    border-color: #22c55e !important;
    color: #166534 !important;
  }
  
  .theme-light .alert-warning {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%) !important;
    border-color: #f59e0b !important;
    color: #92400e !important;
  }
  
  /* Colored Text Classes */
  .theme-light .text-primary {
    color: #2563eb !important;
    font-weight: 700 !important;
  }
  
  .theme-light .text-success {
    color: #16a34a !important;
    font-weight: 700 !important;
  }
  
  .theme-light .text-warning {
    color: #ea580c !important;
    font-weight: 700 !important;
  }
  
  .theme-light .text-danger {
    color: #dc2626 !important;
    font-weight: 700 !important;
  }
  
  /* Keep all other existing theme-light styles */
  .theme-light p, .theme-light span, .theme-light div {
    color: #1e293b !important;
  }
  
  .theme-light .text-white {
    color: #1e293b !important;
    font-weight: 700 !important;
  }
  
  .theme-light .text-light {
    color: #64748b !important;
  }
  
  .theme-light .text-muted {
    color: #64748b !important;
    font-size: 0.875rem !important;
    font-weight: 500 !important;
  }
  
  /* Chart toolbar and tooltip fixes remain the same */
  .apexcharts-toolbar {
    z-index: 1000 !important;
  }
  
  .theme-light .apexcharts-menu {
    background: #ffffff !important;
    border: 1px solid #d1d5db !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12) !important;
  }
  
  .theme-light .apexcharts-menu-item {
    color: #1e293b !important;
    padding: 0.625rem 1rem !important;
    font-weight: 500 !important;
  }
  
  .theme-light .apexcharts-menu-item:hover {
    background: #f3f4f6 !important;
  }
  
  .theme-light .apexcharts-tooltip {
    background: #ffffff !important;
    color: #1e293b !important;
    border: 2px solid #e2e8f0 !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
  }
  
  .theme-light .apexcharts-tooltip-title {
    background: #f9fafb !important;
    color: #1e293b !important;
    border-bottom: 2px solid #e2e8f0 !important;
    font-weight: 700 !important;
    padding: 0.75rem 1rem !important;
  }
  
  /* GE Progress Cards Enhancement */
  .ge-progress-card {
    border-radius: 24px !important;
    padding: 2rem !important;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
    position: relative !important;
    overflow: hidden !important;
  }
  
  .ge-progress-card::before {
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.2), transparent) !important;
    opacity: 0 !important;
    transition: opacity 0.4s ease !important;
  }
  
  .ge-progress-card:hover::before {
    opacity: 1 !important;
  }
  
  .ge-progress-card:hover {
    transform: translateY(-8px) scale(1.02) !important;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2) !important;
  }
  
  /* Keep all dark/night theme styles unchanged */
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
  
  .theme-dark body {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: #e2e8f0;
  }
  
  .theme-dark .content {
    color: #e2e8f0 !important;
  }
  
  .theme-dark .card-glass {
    background: rgba(30, 41, 59, 0.8) !important;
    border: 1px solid rgba(148, 163, 184, 0.1) !important;
    color: #e2e8f0 !important;
  }
</style>

<!-- DASHBOARD HEADER -->
<div class="mb-5 dashboard-header">
  <h2 class="text-white mb-2">Dashboard Overview</h2>
  <p class="text-light">Real-time complaint management system</p>
</div>

<!-- FILTERS SECTION -->
@php
  $user = Auth::user();
  $filterCityId = request('city_id');
  if (!$filterCityId && isset($cityId)) {
    $filterCityId = $cityId;
  }
  
  $userHasNoCity = $user && (is_null($user->city_id) || $user->city_id == 0 || $user->city_id === '');
  $showCityFilter = $userHasNoCity;
  $showSectorFilter = $user && (!$user->sector_id || $user->sector_id == null || $user->sector_id == 0 || $user->sector_id == '');
@endphp

@if($showCityFilter || $showSectorFilter || $categories->count() > 0 || (isset($complaintStatuses) && count($complaintStatuses) > 0) || true)
<div class="mb-5 d-flex justify-content-center">
  <div class="filter-box" style="display: inline-block; width: fit-content;">
    <form id="dashboardFiltersForm" method="GET" action="{{ route('admin.dashboard') }}">
      <div class="row g-3 align-items-end">
        @if($showCityFilter)
        <div class="col-auto" id="cityFilterContainer">
          <label class="form-label small mb-1" style="font-size: 0.8rem; color: #1e293b !important; font-weight: 600;">GE</label>
          <select class="form-select" id="cityFilter" name="city_id" style="font-size: 0.9rem; width: 180px;">
            <option value="">Select GE</option>
            @if($cities && $cities->count() > 0)
              @foreach($cities as $city)
                @php
                  $geUser = $city->users->where('role_id', $geRole->id ?? null)->where('status', 'active')->first();
                  $displayName = $city->name;
                  if ($geUser) {
                    if ($geUser->name) {
                      $displayName = $geUser->name . ' - ' . $city->name;
                    } elseif ($geUser->username) {
                      $displayName = $geUser->username . ' - ' . $city->name;
                    }
                  }
                @endphp
                <option value="{{ $city->id }}" {{ (request('city_id') == $city->id || $cityId == $city->id) ? 'selected' : '' }}>{{ $displayName }}</option>
              @endforeach
            @endif
          </select>
        </div>
        @endif
        
        @if($showSectorFilter)
        <div class="col-auto">
          <label class="form-label small mb-1" style="font-size: 0.8rem; color: #1e293b !important; font-weight: 600;">Sector</label>
          <select class="form-select" id="sectorFilter" name="sector_id" style="font-size: 0.9rem; width: 180px;">
            <option value="">All Sectors</option>
            @if($sectors && $sectors->count() > 0)
              @foreach($sectors as $sector)
                <option value="{{ $sector->id }}" {{ (request('sector_id') == $sector->id || $sectorId == $sector->id) ? 'selected' : '' }}>{{ $sector->name }}</option>
              @endforeach
            @endif
          </select>
        </div>
        @endif
        
        <div class="col-auto">
          <label class="form-label small mb-1" style="font-size: 0.8rem; color: #1e293b !important; font-weight: 600;">Complaint Category</label>
          <select class="form-select" id="categoryFilter" name="category" style="font-size: 0.9rem; width: 180px;">
            <option value="">All Categories</option>
            @if($categories && $categories->count() > 0)
              @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ (request('category') == $cat || $category == $cat) ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
              @endforeach
            @endif
          </select>
        </div>
        
        @if(isset($complaintStatuses) && count($complaintStatuses) > 0)
        <div class="col-auto">
          <label class="form-label small mb-1" style="font-size: 0.8rem; color: #1e293b !important; font-weight: 600;">Complaints Status</label>
          <select class="form-select" id="complaintStatusFilter" name="complaint_status" style="font-size: 0.9rem; width: 180px;">
            <option value="">All Status</option>
            @foreach($complaintStatuses as $statusKey => $statusLabel)
              <option value="{{ $statusKey }}" {{ (request('complaint_status') == $statusKey || $complaintStatus == $statusKey) ? 'selected' : '' }}>{{ $statusLabel }}</option>
            @endforeach
          </select>
        </div>
        @endif
        
        <div class="col-auto">
          <label class="form-label small mb-1" style="font-size: 0.8rem; color: #1e293b !important; font-weight: 600;">Date Range</label>
          <select class="form-select" id="dateRangeFilter" name="date_range" style="font-size: 0.9rem; width: 180px;">
            <option value="">All Time</option>
            <option value="yesterday" {{ (request('date_range') == 'yesterday' || $dateRange == 'yesterday') ? 'selected' : '' }}>Yesterday</option>
            <option value="today" {{ (request('date_range') == 'today' || $dateRange == 'today') ? 'selected' : '' }}>Today</option>
            <option value="this_week" {{ (request('date_range') == 'this_week' || $dateRange == 'this_week') ? 'selected' : '' }}>This Week</option>
            <option value="last_week" {{ (request('date_range') == 'last_week' || $dateRange == 'last_week') ? 'selected' : '' }}>Last Week</option>
            <option value="this_month" {{ (request('date_range') == 'this_month' || $dateRange == 'this_month') ? 'selected' : '' }}>This Month</option>
            <option value="last_month" {{ (request('date_range') == 'last_month' || $dateRange == 'last_month') ? 'selected' : '' }}>Last Month</option>
            <option value="last_6_months" {{ (request('date_range') == 'last_6_months' || $dateRange == 'last_6_months') ? 'selected' : '' }}>Last 6 Months</option>
          </select>
        </div>
        
        <div class="col-auto">
          <label class="form-label small text-muted mb-1" style="font-size: 0.8rem;">&nbsp;</label>
          <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetDashboardFilters()" style="font-size: 0.9rem; padding: 0.5rem 1.25rem;">
            <i data-feather="refresh-cw" class="me-1" style="width: 16px; height: 16px;"></i>Reset
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
@endif

<!-- STATISTICS CARDS -->
<div class="row mb-5 g-3 justify-content-center">
  <div class="col-md-2 col-lg-2">
    <div class="stat-card" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;">
      <div class="d-flex align-items-center justify-content-between">
        <div class="flex-grow-1">
          <div class="stat-number">{{ $stats['total_complaints'] ?? 0 }}</div>
          <div class="stat-label">Total Complaints</div>
        </div>
        <div class="stat-icon">
          <i data-feather="alert-circle" class="feather-lg"></i>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-2 col-lg-2">
    <div class="stat-card" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;">
      <div class="d-flex align-items-center justify-content-between">
        <div class="flex-grow-1">
          <div class="stat-number">{{ $stats['pending_complaints'] ?? 0 }}</div>
          <div class="stat-label">Pending</div>
        </div>
        <div class="stat-icon">
          <i data-feather="clock" class="feather-lg"></i>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-2 col-lg-2">
    <div class="stat-card" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%) !important;">
      <div class="d-flex align-items-center justify-content-between">
        <div class="flex-grow-1">
          <div class="stat-number">{{ $stats['addressed_complaints'] ?? 0 }}</div>
          <div class="stat-label">Addressed</div>
        </div>
        <div class="stat-icon">
          <i data-feather="check-circle" class="feather-lg"></i>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-2 col-lg-2">
    <div class="stat-card" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;">
      <div class="d-flex align-items-center justify-content-between">
        <div class="flex-grow-1">
          <div class="stat-number">{{ $stats['overdue_complaints'] ?? 0 }}</div>
          <div class="stat-label">Overdue</div>
        </div>
        <div class="stat-icon">
          <i data-feather="alert-triangle" class="feather-lg"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- COMPLAINT STATUS CARDS -->
<div class="row mb-5 g-3 justify-content-center">
  <div class="col-md-2 col-lg-2">
    <div class="stat-card" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;">
      <div class="d-flex align-items-center justify-content-between">
        <div class="flex-grow-1">
          <div class="stat-number">{{ $stats['work_performa'] ?? 0 }}</div>
          <div class="stat-label">Work Performa</div>
        </div>
        <div class="stat-icon">
          <i data-feather="file-text" class="feather-lg"></i>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-2 col-lg-2">
    <div class="stat-card" style="background: linear-gradient(135deg, #eab308 0%, #ca8a04 100%) !important;">
      <div class="d-flex align-items-center justify-content-between">
        <div class="flex-grow-1">
          <div class="stat-number">{{ $stats['maint_performa'] ?? 0 }}</div>
          <div class="stat-label">Maintenance Performa</div>
        </div>
        <div class="stat-icon">
          <i data-feather="tool" class="feather-lg"></i>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-2 col-lg-2">
    <div class="stat-card" style="background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%) !important;">
      <div class="d-flex align-items-center justify-content-between">
        <div class="flex-grow-1">
          <div class="stat-number">{{ $stats['work_priced_performa'] ?? 0 }}</div>
          <div class="stat-label">Work Performa Priced</div>
        </div>
        <div class="stat-icon">
          <i data-feather="dollar-sign" class="feather-lg"></i>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-2 col-lg-2">
    <div class="stat-card" style="background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%) !important;">
      <div class="d-flex align-items-center justify-content-between">
        <div class="flex-grow-1">
          <div class="stat-number">{{ $stats['maint_priced_performa'] ?? 0 }}</div>
          <div class="stat-label">Maintenance Performa Priced</div>
        </div>
        <div class="stat-icon">
          <i data-feather="dollar-sign" class="feather-lg"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mb-5 g-3 justify-content-center">
  <div class="col-md-2 col-lg-2">
    <div class="stat-card" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%) !important;">
      <div class="d-flex align-items-center justify-content-between">
        <div class="flex-grow-1">
          <div class="stat-number">{{ $stats['un_authorized'] ?? 0 }}</div>
          <div class="stat-label">Un Authorized</div>
        </div>
        <div class="stat-icon">
          <i data-feather="x-octagon" class="feather-lg"></i>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-2 col-lg-2">
    <div class="stat-card" style="background: linear-gradient(135deg, #475569 0%, #334155 100%) !important;">
      <div class="d-flex align-items-center justify-content-between">
        <div class="flex-grow-1">
          <div class="stat-number">{{ $stats['product_na'] ?? 0 }}</div>
          <div class="stat-label">Product N/A</div>
        </div>
        <div class="stat-icon" style="display: flex !important; visibility: visible !important;">
          <i data-feather="box" class="feather-lg" style="display: block !important; visibility: visible !important; width: 24px !important; height: 24px !important;"></i>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-2 col-lg-2">
    <div class="stat-card" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%) !important;">
      <div class="d-flex align-items-center justify-content-between">
        <div class="flex-grow-1">
          <div class="stat-number">{{ $stats['pertains_to_ge_const_isld'] ?? 0 }}</div>
          <div class="stat-label">Pertains to GE/Const/Isld</div>
        </div>
        <div class="stat-icon">
          <i data-feather="map-pin" class="feather-lg"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ADDITIONAL STATS -->
<div class="row mb-5 justify-content-center g-3">
  <div class="col-md-2 col-lg-2">
    <div class="stat-card text-center" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;">
      <div class="stat-icon mx-auto mb-3">
        <i data-feather="users" class="feather-lg"></i>
      </div>
      <div class="stat-number">{{ $stats['total_users'] ?? 0 }}</div>
      <div class="stat-label">Users</div>
    </div>
  </div>
  
  <div class="col-md-2 col-lg-2">
    <div class="stat-card text-center" style="background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%) !important;">
      <div class="stat-icon mx-auto mb-3">
        <i data-feather="user-check" class="feather-lg"></i>
      </div>
      <div class="stat-number">{{ $stats['total_employees'] ?? 0 }}</div>
      <div class="stat-label">Employees</div>
    </div>
  </div>
  
  <div class="col-md-2 col-lg-2">
    <div class="stat-card text-center" style="background: linear-gradient(135deg, #fb923c 0%, #f97316 100%) !important;">
      <div class="stat-icon mx-auto mb-3">
        <i data-feather="alert-triangle" class="feather-lg"></i>
      </div>
      <div class="stat-number">{{ $stats['low_stock_items'] ?? 0 }}</div>
      <div class="stat-label">Low Stock</div>
    </div>
  </div>
  
  <div class="col-md-2 col-lg-2">
    <div class="stat-card text-center" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;">
      <div class="stat-icon mx-auto mb-3">
        <i data-feather="trending-up" class="feather-lg"></i>
      </div>
      <div class="stat-number">{{ $slaPerformance['sla_percentage'] ?? 0 }}%</div>
      <div class="stat-label">SLA Performance</div>
    </div>
  </div>
</div>

<!-- CHARTS ROW -->
<div class="row mb-5 g-4">
  <div class="col-md-6">
    <div class="card-glass chart-container">
      <h5 class="mb-4 text-white" style="font-weight: 700; font-size: 1.25rem;">
        <i data-feather="pie-chart" class="me-2" style="width: 24px; height: 24px;"></i>
        Complaints by Status
      </h5>
      <div id="complaintsStatusChart" style="height: 300px;"></div>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="card-glass chart-container">
      <h5 class="mb-4 text-white" style="font-weight: 700; font-size: 1.25rem;">
        <i data-feather="bar-chart-2" class="me-2" style="width: 24px; height: 24px;"></i>
        Complaints by Type
      </h5>
      <div id="complaintsTypeChart" style="height: 300px;"></div>
    </div>
  </div>
</div>

<!-- GE PROGRESS SECTION (For Director Only) -->
@if(isset($geProgress) && count($geProgress) > 0)
<div class="row mt-5 mb-5">
  <div class="col-12">
    <div class="card-glass" style="padding: 2.5rem;">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0 text-white" style="font-weight: 700; font-size: 1.5rem;">
          <i data-feather="users" class="me-2" style="width: 28px; height: 28px;"></i>GE Feedback Overview
        </h5>
      </div>
      <div class="row g-4">
        @php
          $colorSchemes = [
            ['bg' => 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)', 'icon' => '#60a5fa', 'progress' => 'linear-gradient(90deg, #3b82f6, #60a5fa)'],
            ['bg' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)', 'icon' => '#34d399', 'progress' => 'linear-gradient(90deg, #10b981, #34d399)'],
            ['bg' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', 'icon' => '#fbbf24', 'progress' => 'linear-gradient(90deg, #f59e0b, #fbbf24)'],
            ['bg' => 'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)', 'icon' => '#a78bfa', 'progress' => 'linear-gradient(90deg, #8b5cf6, #a78bfa)'],
            ['bg' => 'linear-gradient(135deg, #ec4899 0%, #db2777 100%)', 'icon' => '#f472b6', 'progress' => 'linear-gradient(90deg, #ec4899, #f472b6)'],
          ];
          $totalCards = count($geProgress);
        @endphp
        @foreach($geProgress as $index => $geData)
        @php
          $colorScheme = $colorSchemes[$index % count($colorSchemes)];
          $progressColor = $geData['progress_percentage'] >= 80 ? 'linear-gradient(90deg, #ffffff, #f0f9ff)' : 
                          ($geData['progress_percentage'] >= 50 ? 'linear-gradient(90deg, #ffffff, #f0f9ff)' : 
                          ($geData['progress_percentage'] >= 30 ? 'linear-gradient(90deg, #fff7ed, #ffffff)' : 'linear-gradient(90deg, #fef2f2, #ffffff)'));
          
          $colClasses = 'col-md-6 col-lg-4 mb-3';
          $offsetClasses = '';
          
          if ($totalCards == 1) {
            $colClasses = 'col-md-6 col-lg-4 mb-3';
            $offsetClasses = 'offset-md-3 offset-lg-4';
          }
          elseif ($totalCards % 3 == 2 && $index >= $totalCards - 2) {
            if ($index == $totalCards - 2) {
              $offsetClasses = 'offset-lg-2';
            }
          }
          elseif ($totalCards % 3 == 1 && $index == $totalCards - 1) {
            $offsetClasses = 'offset-lg-4';
          }
        @endphp
        <div class="{{ $colClasses }} {{ $offsetClasses }}">
          <div class="ge-progress-card" style="padding: 2rem; background: {{ $colorScheme['bg'] }} !important; border: none !important; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2) !important; border-radius: 24px !important;">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div>
                <h6 class="mb-1 text-white" style="font-weight: 700; font-size: 1.25rem; color: #ffffff !important;">{{ $geData['ge']->name ?? $geData['ge']->username }}</h6>
                <p class="mb-0 text-white" style="font-size: 0.9rem; opacity: 0.95; color: #ffffff !important;">
                  <i data-feather="map-pin" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; color: #ffffff;"></i>
                  <span style="color: #ffffff !important; margin-left: 0.25rem;">{{ $geData['city'] }}</span>
                </p>
              </div>
              <div style="width: 60px; height: 60px; background: rgba(255, 255, 255, 0.25); border-radius: 16px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);">
                <i data-feather="user-check" style="width: 28px; height: 28px; color: #ffffff;"></i>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-white" style="font-size: 0.95rem; font-weight: 600; opacity: 0.95; color: #ffffff !important;">Progress</span>
                <span class="text-white" style="font-weight: 800; font-size: 1.75rem; color: #ffffff !important;">{{ $geData['progress_percentage'] }}%</span>
              </div>
              <div class="progress" style="height: 18px; background-color: rgba(0, 0, 0, 0.25); border-radius: 10px; overflow: hidden; box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.15);">
                <div class="progress-bar" role="progressbar" 
                     style="width: {{ $geData['progress_percentage'] }}%; background: linear-gradient(90deg, #ffffff 0%, rgba(255, 255, 255, 0.8) 100%); border-radius: 10px; box-shadow: 0 2px 12px rgba(255, 255, 255, 0.5), inset 0 1px 2px rgba(255, 255, 255, 0.8); transition: width 0.6s ease; border: 1px solid rgba(255, 255, 255, 0.4);" 
                     aria-valuenow="{{ $geData['progress_percentage'] }}" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-3" style="border-top: 1px solid rgba(255, 255, 255, 0.25);">
              <span class="text-white" style="font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 8px; color: #ffffff !important;">
                <i data-feather="check-circle" style="width: 18px; height: 18px; color: #ffffff;"></i>
                <span style="font-weight: 700; color: #ffffff !important;">{{ $geData['resolved_complaints'] }}</span> <span style="color: #ffffff !important; opacity: 0.9;">Resolved</span>
              </span>
              <span class="text-white" style="font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 8px; color: #ffffff !important;">
                <i data-feather="file-text" style="width: 18px; height: 18px; color: #ffffff;"></i>
                <span style="font-weight: 700; color: #ffffff !important;">{{ $geData['total_complaints'] }}</span> <span style="color: #ffffff !important; opacity: 0.9;">Total</span>
              </span>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endif

<!-- MONTHLY TRENDS CHART -->
<div class="row mb-5">
  <div class="col-12">
    <div class="card-glass chart-container">
      <h5 class="mb-4 text-white" style="font-weight: 700; font-size: 1.25rem;">
        <i data-feather="trending-up" class="me-2" style="width: 24px; height: 24px;"></i>
        Monthly Trends
      </h5>
      <div id="monthlyTrendsChart" style="height: 420px;"></div>
    </div>
  </div>
</div>

<!-- TABLES ROW -->
<div class="row mb-5">
  <div class="col-12">
    <div class="card-glass" style="padding: 2rem;">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0 text-white" style="font-weight: 700; font-size: 1.25rem;">
          <i data-feather="list" class="me-2" style="width: 24px; height: 24px;"></i>
          Recent Complaints
        </h5>
        <a href="{{ route('admin.complaints.index') }}" class="btn btn-accent btn-sm">
          <i data-feather="arrow-right" class="me-1" style="width: 16px; height: 16px;"></i>
          View All
        </a>
      </div>
      <div class="table-responsive">
        <table class="table table-dark">
          <thead>
            <tr>
              <th>Complaint ID</th>
              <th>Complainant</th>
              <th>Type</th>
              <th>Status</th>
              <th>Priority</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentComplaints ?? [] as $complaint)
            <tr>
              <td><strong>#{{ str_pad($complaint->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
              <td>{{ $complaint->client->client_name }}</td>
              <td>{{ $complaint->getCategoryDisplayAttribute() }}</td>
              <td>
                @if($complaint->status === 'resolved')
                  <span class="status-badge status-{{ $complaint->status }}" style="background-color: #15803d !important; color: #ffffff !important; border-color: #166534 !important;">{{ $complaint->getStatusDisplayAttribute() }}</span>
                @elseif($complaint->status === 'in_progress')
                  <span class="status-badge status-{{ $complaint->status }}" style="background-color: #b91c1c !important; color: #ffffff !important; border-color: #991b1b !important;">{{ $complaint->getStatusDisplayAttribute() }}</span>
                @elseif($complaint->status === 'assigned')
                  <span class="status-badge status-{{ $complaint->status }}" style="background-color: #64748b !important; color: #ffffff !important; border-color: #475569 !important;">{{ $complaint->getStatusDisplayAttribute() }}</span>
                @elseif($complaint->status === 'un_authorized')
                  <span class="status-badge status-{{ $complaint->status }}" style="background-color: #ec4899 !important; color: #ffffff !important; border-color: #db2777 !important;">{{ $complaint->getStatusDisplayAttribute() }}</span>
                @elseif($complaint->status === 'pertains_to_ge_const_isld')
                  <span class="status-badge status-{{ $complaint->status }}" style="background-color: #06b6d4 !important; color: #ffffff !important; border-color: #0891b2 !important;">{{ $complaint->getStatusDisplayAttribute() }}</span>
                @elseif($complaint->status === 'product_na')
                  <span class="status-badge status-{{ $complaint->status }}" style="background-color: #000000 !important; color: #ffffff !important; border-color: #1a1a1a !important;">{{ $complaint->getStatusDisplayAttribute() }}</span>
                @else
                  <span class="status-badge status-{{ $complaint->status }}" style="color: #ffffff !important;">{{ $complaint->getStatusDisplayAttribute() }}</span>
                @endif
              </td>
              <td><span class="priority-badge priority-{{ $complaint->priority }}">{{ $complaint->getPriorityDisplayAttribute() }}</span></td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center py-4" style="color: #64748b; font-style: italic;">No recent complaints</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

    <!-- APPROVALS SECTION -->
    @if(isset($pendingApprovals) && $pendingApprovals->count() > 0)
    <div class="row mt-4">
      <div class="col-12">
        <div class="card-glass">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 text-warning">
              <i data-feather="clock" class="me-2"></i>
              @if($approvalStatus)
                {{ ucfirst($approvalStatus) }} Approvals
              @else
                In-progress Complaints
              @endif
            </h5>
            <a href="{{ route('admin.approvals.index') }}" class="btn btn-outline-warning btn-sm">View All</a>
          </div>
          <div class="table-responsive">
            <table class="table table-dark">
              <thead>
                <tr>
                  <th>Complaint ID</th>
                  <th>Complainant</th>
                  <th>Employee Assigned</th>
                  <th>Status</th>
                  <th>Items</th>
                  <th>Registration Date/Time</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($pendingApprovals as $approval)
                <tr>
                  <td>{{ $approval->complaint ? str_pad($approval->complaint->id, 4, '0', STR_PAD_LEFT) : 'N/A' }}</td>
                  <td>{{ $approval->complaint && $approval->complaint->client ? $approval->complaint->client->client_name : 'N/A' }}</td>
                  <td>{{ $approval->requestedBy->name ?? 'N/A' }}</td>
                  <td>
                    @php
                      $statusColors = [
                        'pending' => ['bg' => '#dc2626', 'text' => '#ffffff', 'border' => '#991b1b'],
                        'approved' => ['bg' => '#22c55e', 'text' => '#ffffff', 'border' => '#16a34a'],
                        'rejected' => ['bg' => '#ef4444', 'text' => '#ffffff', 'border' => '#dc2626'],
                      ];
                      $statusColor = $statusColors[$approval->status] ?? ['bg' => '#6b7280', 'text' => '#ffffff', 'border' => '#4b5563'];
                    @endphp
                    <span class="badge" style="background-color: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }} !important; border: 1px solid {{ $statusColor['border'] }}; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 600;">
                      {{ $approval->getStatusDisplayAttribute() }}
                    </span>
                  </td>
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
                      <span class="badge bg-danger" style="color: #ffffff !important;">Out of Stock</span>
                    @else
                      <span class="badge bg-warning" style="color: #ffffff !important;">Low Stock</span>
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
    // Status colors mapping (same as in approvals view)
    $statusColorMap = [
      'assigned' => '#3b82f6', // Blue
      'in_progress' => '#dc2626', // Red
      'resolved' => '#16a34a', // Green
      'work_performa' => '#60a5fa', // Light Blue
      'maint_performa' => '#eab308', // Yellow
      'work_priced_performa' => '#9333ea', // Purple
      'maint_priced_performa' => '#ea580c', // Orange Red
      'product_na' => '#000000', // Black
      'un_authorized' => '#ec4899', // Pink (same as approvals view)
      'pertains_to_ge_const_isld' => '#06b6d4', // Aqua/Cyan (same as approvals view)
    ];
    
    // All possible statuses from approvals page (in order)
    $allPossibleStatuses = [
      'assigned',
      'in_progress',
      'resolved',
      'work_performa',
      'maint_performa',
      'work_priced_performa',
      'maint_priced_performa',
      'product_na',
      'un_authorized',
      'pertains_to_ge_const_isld'
    ];
    
    // Ensure we preserve the order of statuses and include all possible statuses
    $statusKeys = isset($complaintsByStatus) ? array_keys($complaintsByStatus) : [];
    $statusData = isset($complaintsByStatus) ? array_values($complaintsByStatus) : [0, 0, 0, 0, 0];
    
    // Merge with all possible statuses to ensure all are included (even with 0 count)
    $mergedStatusData = [];
    $mergedStatusKeys = [];
    foreach ($allPossibleStatuses as $status) {
      $mergedStatusKeys[] = $status;
      $mergedStatusData[] = isset($complaintsByStatus[$status]) ? $complaintsByStatus[$status] : 0;
    }
    
    // Use merged data if we have complaintsByStatus, otherwise use original
    if (isset($complaintsByStatus) && !empty($complaintsByStatus)) {
      $statusKeys = $mergedStatusKeys;
      $statusData = $mergedStatusData;
    }
    
    $statusLabels = isset($complaintsByStatus) ? array_map(function($status) { 
      $label = ucfirst(str_replace('_', ' ', $status));
      // Handle special cases
      if ($label === 'Resolved') {
        return 'Addressed';
      } elseif ($status === 'work_performa') {
        return 'Work Performa';
      } elseif ($status === 'maint_performa') {
        return 'Maintenance Performa';
      } elseif ($status === 'work_priced_performa') {
        return 'Work Performa Priced';
      } elseif ($status === 'maint_priced_performa') {
        return 'Maintenance Performa Priced';
      } elseif ($status === 'product_na') {
        return 'Product N/A';
      } elseif ($status === 'un_authorized') {
        return 'Un-Authorized';
      } elseif ($status === 'pertains_to_ge_const_isld') {
        return 'Pertains to GE(N) Const Isld';
      } elseif ($status === 'in_progress') {
        return 'In-Process';
      }
      return $label;
    }, $statusKeys) : ['New', 'Assigned', 'In Progress', 'Addressed'];
    
    // Map colors based on status keys - ensure same order as data
    $statusColors = isset($complaintsByStatus) ? array_map(function($status) use ($statusColorMap) {
      return $statusColorMap[$status] ?? '#64748b'; // Default gray if status not found
    }, $statusKeys) : ['#3b82f6', '#f59e0b', '#a855f7', '#22c55e', '#6b7280'];
  @endphp
    var statusDataArray = @json($statusData);
    var statusLabelsArray = @json($statusLabels);
    var statusColorsArray = @json($statusColors);
    
    var complaintsStatusOptions = {
    series: statusDataArray,
      chart: {
        type: 'donut',
        height: 300,
        background: 'transparent'
      },
    labels: statusLabelsArray,
      colors: statusColorsArray,
      plotOptions: {
        pie: {
          donut: {
            labels: {
              show: true
            }
          }
        }
      },
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

    // Monthly Trends Chart - Modern Combination Chart (Column + Line)
    const isLightTheme = document.documentElement.classList.contains('theme-light');
    var monthlyTrendsOptions = {
      series: [{
        name: 'Complaints',
        type: 'column',
        data: complaintsData
      }, {
        name: 'Resolutions',
        type: 'line',
        data: resolutionsData
      }],
      chart: {
        height: 420,
        type: 'line',
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
        },
        animations: {
          enabled: true,
          easing: 'easeinout',
          speed: 1000,
          animateGradually: {
            enabled: true,
            delay: 200
          },
          dynamicAnimation: {
            enabled: true,
            speed: 400
          }
        },
        dropShadow: {
          enabled: true,
          color: '#000',
          top: 18,
          left: 7,
          blur: 10,
          opacity: 0.2
        }
      },
      colors: ['#3b82f6', '#22c55e'],
      plotOptions: {
        bar: {
          borderRadius: 8,
          columnWidth: '60%',
          dataLabels: {
            position: 'top'
          }
        }
      },
      dataLabels: {
        enabled: true,
        enabledOnSeries: [0],
        formatter: function (val) {
          return Math.floor(val);
        },
        offsetY: -20,
        style: {
          fontSize: '11px',
          fontWeight: 700,
          colors: isLightTheme ? ['#1e293b'] : ['#ffffff']
        },
        background: {
          enabled: true,
          foreColor: isLightTheme ? '#ffffff' : '#1e293b',
          padding: 6,
          borderRadius: 6,
          borderWidth: 2,
          borderColor: isLightTheme ? '#3b82f6' : '#60a5fa',
          opacity: 0.95,
          dropShadow: {
            enabled: true,
            top: 1,
            left: 1,
            blur: 3,
            opacity: 0.5
          }
        }
      },
      stroke: {
        width: [0, 4],
        curve: 'smooth',
        dashArray: [0, 0]
      },
      fill: {
        type: 'gradient',
        gradient: {
          shade: 'light',
          type: 'vertical',
          shadeIntensity: 0.5,
          gradientToColors: ['#60a5fa', '#34d399'],
          inverseColors: false,
          opacityFrom: 0.9,
          opacityTo: 0.6,
          stops: [0, 50, 100]
        }
      },
      markers: {
        size: [0, 7],
        strokeWidth: 3,
        strokeColors: ['#ffffff', '#ffffff'],
        fillColors: ['#22c55e', '#22c55e'],
        hover: {
          size: [0, 9]
        },
        shape: 'circle'
      },
      xaxis: {
        categories: monthsData,
        labels: {
          style: {
            colors: isLightTheme ? '#1e293b' : '#e2e8f0',
            fontSize: '13px',
            fontWeight: 600,
            fontFamily: 'inherit'
          }
        },
        axisBorder: {
          show: true,
          color: isLightTheme ? '#d1d5db' : '#374151',
          height: 2,
          width: '100%'
        },
        axisTicks: {
          show: true,
          color: isLightTheme ? '#d1d5db' : '#374151',
          height: 6
        }
      },
      yaxis: [{
        title: {
          text: 'Complaints',
          style: {
            color: '#3b82f6',
            fontSize: '13px',
            fontWeight: 700
          }
        },
        labels: {
          style: {
            colors: isLightTheme ? '#1e293b' : '#e2e8f0',
            fontSize: '12px',
            fontWeight: 600
          },
          formatter: function (val) {
            return Math.floor(val);
          }
        }
      }, {
        opposite: true,
        title: {
          text: 'Resolutions',
          style: {
            color: '#22c55e',
            fontSize: '13px',
            fontWeight: 700
          }
        },
        labels: {
          style: {
            colors: isLightTheme ? '#1e293b' : '#e2e8f0',
            fontSize: '12px',
            fontWeight: 600
          },
          formatter: function (val) {
            return Math.floor(val);
          }
        }
      }],
      legend: {
        position: 'top',
        horizontalAlign: 'center',
        floating: false,
        fontSize: '14px',
        fontWeight: 700,
        labels: {
          colors: isLightTheme ? '#1e293b' : '#e2e8f0',
          useSeriesColors: false
        },
        markers: {
          width: 14,
          height: 14,
          radius: 7,
          offsetX: -5,
          offsetY: 2
        },
        itemMargin: {
          horizontal: 20,
          vertical: 8
        }
      },
      grid: {
        borderColor: isLightTheme ? '#e2e8f0' : '#374151',
        strokeDashArray: 5,
        xaxis: {
          lines: {
            show: false
          }
        },
        yaxis: {
          lines: {
            show: true
          }
        },
        padding: {
          top: 20,
          right: 10,
          bottom: 0,
          left: 10
        }
      },
      tooltip: {
        theme: isLightTheme ? 'light' : 'dark',
        style: {
          fontSize: '13px',
          fontFamily: 'inherit'
        },
        y: [{
          formatter: function (val) {
            return val + ' complaints';
          }
        }, {
          formatter: function (val) {
            return val + ' resolutions';
          }
        }],
        marker: {
          show: true
        },
        shared: true,
        intersect: false
      },
      responsive: [{
        breakpoint: 768,
        options: {
          chart: {
            height: 350
          },
          legend: {
            position: 'bottom'
          },
          dataLabels: {
            enabled: false
          },
          plotOptions: {
            bar: {
              columnWidth: '70%'
            }
          }
        }
      }]
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

    // Dashboard Filters Functions
    function applyDashboardFilters() {
      const form = document.getElementById('dashboardFiltersForm');
      const formData = new FormData(form);
      const params = new URLSearchParams();
      
      // Add filter values to params
      if (formData.get('city_id')) {
        params.append('city_id', formData.get('city_id'));
      }
      if (formData.get('sector_id')) {
        params.append('sector_id', formData.get('sector_id'));
      }
      if (formData.get('category')) {
        params.append('category', formData.get('category'));
      }
      if (formData.get('complaint_status')) {
        params.append('complaint_status', formData.get('complaint_status'));
      }
      if (formData.get('date_range')) {
        params.append('date_range', formData.get('date_range'));
      }
      
      // Reload dashboard with filters
      window.location.href = '{{ route("admin.dashboard") }}?' + params.toString();
    }

    function resetDashboardFilters() {
      window.location.href = '{{ route("admin.dashboard") }}';
    }

    // Dynamic sector loading for Director when city changes and auto-apply filters
    const cityFilter = document.getElementById('cityFilter');
    const sectorFilter = document.getElementById('sectorFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    
    // Keep GE filter visible at all times - don't hide it
    // @if($user && !$user->city_id)
    // const cityFilterContainer = document.getElementById('cityFilterContainer');
    // if (cityFilterContainer && cityFilter) {
    //   const selectedCityId = cityFilter.value;
    //   if (selectedCityId && selectedCityId !== '') {
    //     cityFilterContainer.style.display = 'none';
    //   }
    // }
    // @endif
    
    // Auto-apply filters on change (like other modules)
    if (cityFilter) {
      cityFilter.addEventListener('change', function() {
        @if($user && !$user->city_id)
        // User can see all cities: Load sectors dynamically when city changes
        const cityId = this.value;
        
        // Keep GE filter visible at all times - don't hide it
        // const cityFilterContainer = document.getElementById('cityFilterContainer');
        // if (cityFilterContainer) {
        //   if (cityId && cityId !== '') {
        //     cityFilterContainer.style.display = 'none';
        //   } else {
        //     cityFilterContainer.style.display = 'block';
        //   }
        // }
        
        if (sectorFilter) {
          sectorFilter.innerHTML = '<option value="">Loading sectors...</option>';
          sectorFilter.disabled = true;
          
          if (cityId) {
            // Fetch sectors for selected city
            fetch(`{{ route('admin.sectors.by-city') }}?city_id=${cityId}`, {
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
              },
              credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
              sectorFilter.innerHTML = '<option value="">All Sectors</option>';
              const sectors = Array.isArray(data) ? data : (data.sectors || []);
              if (sectors && sectors.length > 0) {
                sectors.forEach(function(sector) {
                  const option = document.createElement('option');
                  option.value = sector.id;
                  option.textContent = sector.name;
                  sectorFilter.appendChild(option);
                });
              }
              sectorFilter.disabled = false;
              // Auto-apply filters after loading sectors
              applyDashboardFilters();
            })
            .catch(error => {
              console.error('Error loading sectors:', error);
              sectorFilter.innerHTML = '<option value="">All Sectors</option>';
              sectorFilter.disabled = false;
              // Auto-apply filters even on error
              applyDashboardFilters();
            });
          } else {
            // Show all sectors if no city selected (Director) - reload page to get all sectors
            sectorFilter.innerHTML = '<option value="">All Sectors</option>';
            sectorFilter.disabled = false;
            // Auto-apply filters
            applyDashboardFilters();
          }
        } else {
          // Auto-apply filters when city changes
          applyDashboardFilters();
        }
        @else
        // For GE: Auto-apply filters when city changes
        applyDashboardFilters();
        @endif
      });
    }
    
    // Auto-apply filters when sector changes
    if (sectorFilter) {
      sectorFilter.addEventListener('change', function() {
        applyDashboardFilters();
      });
    }
    
    // Auto-apply filters when category changes
    if (categoryFilter) {
      categoryFilter.addEventListener('change', function() {
        applyDashboardFilters();
      });
    }
    
    // Auto-apply filters when complaint status changes
    const complaintStatusFilter = document.getElementById('complaintStatusFilter');
    if (complaintStatusFilter) {
      complaintStatusFilter.addEventListener('change', function() {
        applyDashboardFilters();
      });
    }
    
    // Auto-apply filters when date range changes
    const dateRangeFilter = document.getElementById('dateRangeFilter');
    if (dateRangeFilter) {
      dateRangeFilter.addEventListener('change', function() {
        applyDashboardFilters();
      });
    }
    
    // Override inline styles for filter labels in dark/night theme
    function updateFilterLabelsColor() {
      const body = document.body;
      const isDarkTheme = body.classList.contains('theme-dark');
      const isNightTheme = body.classList.contains('theme-night');
      
      if (isDarkTheme || isNightTheme) {
        const filterLabels = document.querySelectorAll('.card-glass.mb-4 label.form-label');
        filterLabels.forEach(function(label) {
          if (label.style.color && label.style.color.includes('#1e293b')) {
            label.style.setProperty('color', '#ffffff', 'important');
          }
        });
      }
    }
    
    // Run on page load
    updateFilterLabelsColor();
    
    // Watch for theme changes
    const observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
          updateFilterLabelsColor();
        }
      });
    });
    
    if (document.body) {
      observer.observe(document.body, {
        attributes: true,
        attributeFilter: ['class']
      });
    }
</script>
@endpush
