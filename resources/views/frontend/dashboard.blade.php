@extends('frontend.layouts.app')

@section('title', 'Complaint Dashboard')

@push('styles')
<style>
  /* Dashboard specific styles */
  body.dashboard-page,
  body {
    background: linear-gradient(180deg, #0e1a4b 0%, #08123a 55%) !important;
    color: #fff !important;
    padding: 60px !important;
    min-height: 100vh !important;
  }

  main {
    background: transparent !important;
    padding: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
  }

  .dashboard-container {
    width: 100%;
    max-width: 100%;
    margin: 0;
    margin-top: 0;
    padding: 1.5rem;
  }

  /* Top header */
  .header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 24px;
  }

  .brand {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .brand .logo {
    width: 64px;
    height: 64px;
    background: #fff;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0e1a4b;
    font-weight: 700;
    font-size: 24px;
    position: relative;
  }

  .brand .logo::before {
    content: '';
    position: absolute;
    width: 32px;
    height: 32px;
    border: 3px solid #0e1a4b;
    border-top: none;
    border-radius: 0 0 16px 16px;
    top: 8px;
  }

  .welcome {
    line-height: 1.3;
  }

  .welcome h1 {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 4px;
  }

  .welcome p {
    font-size: 13px;
    opacity: .8;
    color: rgba(255, 255, 255, 0.9);
  }


  /* stats row - only 2 cards */
  .stats-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-top: 18px;
  }

  .stat-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 20px;
    border-radius: 12px;
    color: #fff;
  }

  .stat-card .left {
    display: flex;
    gap: 16px;
    align-items: center;
    flex: 1;
  }

  .stat-card .icon {
    width: 56px;
    height: 56px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    background: rgba(255, 255, 255, 0.18);
  }

  .stat-card .meta {
    line-height: 1.3;
  }

  .stat-card .meta h2 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
    opacity: .95;
  }

  .stat-card .meta .value {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 4px;
    line-height: 1;
  }

  .stat-card .meta p {
    font-size: 12px;
    opacity: .85;
  }

  .stat-card.blue {
    background: linear-gradient(180deg, #2fb6ff, #1f9fdd);
  }

  .stat-card.orange {
    background: linear-gradient(180deg, #ffd47a, #ffb24a);
  }

  /* main charts area */
  .charts-section {
    margin-top: 18px;
  }

  .chart-top-row {
    margin-bottom: 16px;
  }

  .charts-bottom-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
  }

  .chart-large {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 20px;
    color: #fff;
    min-height: 320px;
    display: flex;
    flex-direction: column;
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
    box-sizing: border-box;
  }

  .chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    gap: 12px;
    flex-wrap: nowrap;
  }

  .chart-title {
    font-weight: 700;
    font-size: 16px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex-shrink: 0;
  }

  .chart-filter {
    background: rgba(255, 255, 255, 0.9) !important;
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 6px;
    padding: 6px 12px;
    color: #1a1a1a !important;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
  }

  .chart-filter option {
    background: #1a1a1a !important;
    color: #fff !important;
    padding: 8px;
  }

  .chart-filter:hover {
    background: rgba(255, 255, 255, 1) !important;
    color: #1a1a1a !important;
  }

  .chart-filter:focus {
    background: rgba(255, 255, 255, 1) !important;
    outline: none;
    border-color: rgba(255, 255, 255, 0.8);
    color: #1a1a1a !important;
  }

  .chart-plot {
    background: rgba(255, 255, 255, 0.08);
    height: 220px;
    border-radius: 8px;
    padding: 15px 15px 25px 35px;
    margin-top: 12px;
    position: relative;
    flex: 1;
    width: 100%;
    box-sizing: border-box;
    overflow: hidden;
  }

  .chart-y-axis {
    position: absolute;
    left: 10px;
    top: 15px;
    bottom: 25px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    font-size: 10px;
    opacity: .7;
    width: 20px;
    color: #fff;
  }

  .chart-y-axis span {
    white-space: nowrap;
  }

  .chart-x-axis {
    position: absolute;
    bottom: 5px;
    left: 35px;
    right: 15px;
    display: flex;
    justify-content: space-between;
    font-size: 9px;
    opacity: .7;
    color: #fff;
    white-space: nowrap;
  }

  .chart-x-axis span {
    white-space: nowrap;
    flex: 1;
    text-align: center;
    min-width: 0;
  }

  .chart-line {
    position: absolute;
    top: 15px;
    left: 35px;
    right: 15px;
    bottom: 25px;
    width: calc(100% - 50px);
    height: calc(100% - 40px);
  }

  .chart-line svg {
    width: 100%;
    height: 100%;
    display: block;
  }


  .bar-card {
    background: linear-gradient(180deg, #6e61ff, #5a4bff);
    padding: 18px;
    border-radius: 12px;
    min-height: 280px;
    display: flex;
    flex-direction: column;
  }

  .bar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
  }

  .bar-title {
    font-weight: 700;
    font-size: 14px;
    color: #fff;
  }

  .bar-legend {
    display: flex;
    gap: 16px;
    font-size: 11px;
    opacity: .95;
    margin-bottom: 8px;
    color: #fff;
  }

  .bar-legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .bar-legend-color {
    width: 12px;
    height: 12px;
    border-radius: 3px;
  }

  .bars-container {
    flex: 1;
    position: relative;
    height: 160px;
    padding: 8px 0 30px 30px;
    min-height: 160px;
  }

  .bar-y-axis {
    position: absolute;
    left: 0;
    top: 8px;
    bottom: 30px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    font-size: 10px;
    opacity: .9;
    width: 25px;
    color: #fff;
    font-weight: 500;
  }

  .bar-x-axis {
    position: absolute;
    bottom: 8px;
    left: 30px;
    right: 0;
    display: flex;
    justify-content: space-around;
    align-items: center;
    font-size: 10px;
    opacity: .9;
    color: #fff;
    font-weight: 500;
    gap: 6px;
  }
  
  .bar-x-axis span {
    flex: 1;
    text-align: center;
    min-width: 0;
    white-space: nowrap;
    max-width: 40px;
  }

  .bar-increase-label {
    position: absolute;
    top: -18px;
    font-size: 10px;
    color: #fff;
    font-weight: 600;
    white-space: nowrap;
    z-index: 10;
  }

  .bars-wrapper {
    position: absolute;
    top: 8px;
    left: 30px;
    right: 0;
    bottom: 30px;
    display: flex;
    align-items: flex-end;
    justify-content: space-around;
    gap: 6px;
    height: calc(100% - 38px);
  }

  .bar {
    border-radius: 4px 4px 0 0;
    position: relative;
    min-height: 4px;
  }

  .bar-yellow {
    background: #ffd166 !important;
  }

  .bar-blue {
    background: #6bd1ff !important;
  }

  .bar-label {
    font-size: 10px;
    color: #fff;
    text-align: center;
    margin-top: 4px;
    opacity: .9;
  }

  .bar-group {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    gap: 3px;
    flex: 1;
    max-width: 40px;
    min-width: 0;
    height: 100%;
  }

  .bar-group .bar {
    width: 100%;
    min-width: 18px;
    max-width: 24px;
  }

  .gauge-card {
    background: linear-gradient(180deg, #ff9a5b, #ff7a3b);
    padding: 18px;
    border-radius: 12px;
    min-height: 460px;
    display: flex;
    flex-direction: column;
  }

  .gauge-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
  }

  .gauge-title {
    font-weight: 700;
    font-size: 14px;
    color: #fff;
  }

  .gauge-legend {
    display: flex;
    gap: 16px;
    font-size: 11px;
    opacity: .95;
    margin-bottom: 8px;
    color: #fff;
  }

  .gauge-legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .gauge-legend-color {
    width: 10px;
    height: 10px;
    border-radius: 50%;
  }

  .gauge-center {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex: 1;
    position: relative;
    padding: 10px 0;
  }

  .gauge-value {
    font-size: 48px;
    font-weight: 800;
    color: #fff;
    margin-bottom: 6px;
    line-height: 1;
  }

  .gauge-label {
    font-size: 13px;
    opacity: .95;
    color: #fff;
    font-weight: 500;
  }

  .gauge-svg {
    position: absolute;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 0;
  }


  .small-metrics {
    display: flex;
    gap: 12px;
    margin-top: 12px;
  }

  .small-metrics .m {
    background: rgba(255, 255, 255, 0.12);
    padding: 10px;
    border-radius: 8px;
    flex: 1;
    text-align: center;
  }

  .small-metrics .m .value {
    font-weight: 700;
    font-size: 14px;
    color: #fff;
    margin-bottom: 4px;
  }

  .small-metrics .m .label {
    font-size: 11px;
    opacity: .85;
    color: #fff;
  }

  /* list area */
  .list-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    color: #1a1a1a;
    margin-top: 25px;
  }

  .list-card h3 {
    margin-bottom: 16px;
    font-size: 18px;
    font-weight: 600;
    color: #1a1a1a;
  }

  .list-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 14px 8px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
  }

  .list-item:last-child {
    border-bottom: none;
  }

  .bullet {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #d6cfff;
    flex-shrink: 0;
  }

  .item-meta {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 20px;
  }

  .item-info {
    flex: 1;
  }

  .item-title {
    font-weight: 700;
    font-size: 14px;
    color: #1a1a1a;
    margin-bottom: 4px;
  }

  .item-id {
    font-size: 12px;
    color: #6b6b6b;
  }

  .complainant {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .avatar-small {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #ff9a88;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    flex-shrink: 0;
  }

  .complainant-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .complainant-name {
    font-size: 11px;
    color: #a6a6a6;
  }

  .complainant-label {
    font-weight: 700;
    font-size: 13px;
    color: #1a1a1a;
  }

  .item-phone {
    font-weight: 700;
    font-size: 13px;
    color: #1a1a1a;
    min-width: 120px;
  }

  .item-actions {
    display: flex;
    gap: 8px;
    align-items: center;
  }

  .action-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border-radius: 6px;
    color: #6b6b6b;
    font-size: 16px;
  }

  .action-icon:hover {
    background: rgba(0, 0, 0, 0.05);
  }

  .item-actions {
    position: relative;
  }

  .actions-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 4px;
    background: #fff;
    border: 1px solid #e6e6e6;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    min-width: 160px;
    overflow: hidden;
  }

  .actions-dropdown .dropdown-item {
    display: block;
    padding: 10px 16px;
    color: #333;
    text-decoration: none;
    font-size: 14px;
    transition: background 0.2s;
    border-bottom: 1px solid #f0f0f0;
  }

  .actions-dropdown .dropdown-item:last-child {
    border-bottom: none;
  }

  .actions-dropdown .dropdown-item:hover {
    background: #f5f5f5;
    color: #667eea;
  }

  .pagination-info {
    color: #7b7b7b;
    font-size: 13px;
    margin-top: 12px;
    margin-bottom: 12px;
  }

  .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 12px;
  }

  .pagination-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 1px solid #e6e6e6;
    background: #fff;
    color: #111;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
  }

  .pagination-btn:hover {
    background: #f5f5f5;
  }

  .pagination-btn.active {
    background: #667eea;
    color: #fff;
    border-color: #667eea;
  }

  .pagination-btn.arrow {
    background: transparent;
    color: #777;
    border-color: #e6e6e6;
  }

  .pagination-btn.arrow:hover {
    background: #f5f5f5;
  }

  .pagination-btn.complaints-page-link {
    text-decoration: none;
    color: inherit;
  }

  .pagination-btn.complaints-page-link:hover {
    background: #f5f5f5;
  }

  .dashboard-footer {
    margin-top: 24px;
    text-align: center;
    color: rgba(255, 255, 255, 0.6);
    font-size: 13px;
  }

    /* responsive */
    @media (max-width: 900px) {
      .charts-bottom-row {
        grid-template-columns: 1fr;
      }

      .stats-row {
        grid-template-columns: 1fr;
      }

      .header {
        flex-direction: column;
        align-items: flex-start;
      }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
  <div class="header">
    <div class="brand">
      <div class="logo">G</div>
      <div class="welcome">
        <h1>Welcome, Dashboard</h1>
        <p>{{ now()->format('D d M Y') }}</p>
      </div>
    </div>

  </div>

  <div class="stats-row">
    <div class="stat-card blue">
      <div class="left">
        <div class="icon">👤</div>
        <div class="meta">
          <h2>Total Complaints</h2>
          <div class="value">{{ number_format($stats['total_complaints']) }}</div>
          <p>{{ $totalComplaintsChange >= 0 ? '+' : '' }}{{ $totalComplaintsChange }}% than last month</p>
        </div>
      </div>
    </div>

    <div class="stat-card orange">
      <div class="left">
        <div class="icon">⏳</div>
        <div class="meta">
          <h2>Total In Progress</h2>
          <div class="value">{{ number_format($stats['pending_complaints']) }}</div>
          <p>{{ $inProgressChange >= 0 ? '+' : '' }}{{ $inProgressChange }}% than last month</p>
        </div>
      </div>
    </div>
  </div>

  <div class="charts-section">
    <div class="chart-top-row">
      <div class="chart-large">
        <div class="chart-header">
          <div class="chart-title">Total Complaints Addressed</div>
          <select class="chart-filter">
            <option>Month</option>
            <option>Year</option>
            <option>Week</option>
          </select>
        </div>
        <div class="chart-plot">
          @php
            $complaintsData = $monthlyTrends['complaints'] ?? [];
            $resolutionsData = $monthlyTrends['resolutions'] ?? [];
            $monthsLabels = $monthlyTrends['months'] ?? [];
            $maxValue = max(max($complaintsData ?: [0]), max($resolutionsData ?: [0]), 1);
            $chartHeight = 200;
            $chartWidth = 400;
            $pointCount = max(count($complaintsData), count($resolutionsData), 1);
            $xStep = $pointCount > 1 ? $chartWidth / ($pointCount - 1) : $chartWidth;
            
            // Ensure arrays have same length
            while(count($complaintsData) < $pointCount) {
              $complaintsData[] = 0;
            }
            while(count($resolutionsData) < $pointCount) {
              $resolutionsData[] = 0;
            }
          @endphp
          <div class="chart-y-axis">
            <span>{{ $maxValue }}</span>
            <span>{{ round($maxValue * 0.75) }}</span>
            <span>{{ round($maxValue * 0.5) }}</span>
            <span>{{ round($maxValue * 0.25) }}</span>
            <span>0</span>
          </div>
          <div class="chart-x-axis">
            @foreach($monthsLabels as $month)
            <span>{{ $month }}</span>
            @endforeach
          </div>
          <div class="chart-line">
            <svg width="100%" height="100%" viewBox="0 0 400 200" preserveAspectRatio="none" style="overflow:visible">
              <defs>
                <linearGradient id="lineGradient" x1="0" x2="0" y1="0" y2="1">
                  <stop offset="0%" stop-color="#ffd166" stop-opacity="0.4" />
                  <stop offset="50%" stop-color="#ffd166" stop-opacity="0.2" />
                  <stop offset="100%" stop-color="#ffd166" stop-opacity="0" />
                </linearGradient>
                <linearGradient id="greenLineGradient" x1="0" x2="0" y1="0" y2="1">
                  <stop offset="0%" stop-color="#22c55e" stop-opacity="0.25" />
                  <stop offset="50%" stop-color="#22c55e" stop-opacity="0.15" />
                  <stop offset="100%" stop-color="#22c55e" stop-opacity="0" />
                </linearGradient>
                <filter id="glowWhite">
                  <feGaussianBlur stdDeviation="2" result="coloredBlur"/>
                  <feMerge>
                    <feMergeNode in="coloredBlur"/>
                    <feMergeNode in="SourceGraphic"/>
                  </feMerge>
                </filter>
                <filter id="glowGreen">
                  <feGaussianBlur stdDeviation="1.5" result="coloredBlur"/>
                  <feMerge>
                    <feMergeNode in="coloredBlur"/>
                    <feMergeNode in="SourceGraphic"/>
                  </feMerge>
                </filter>
              </defs>
              
              @php
                // Generate paths for resolutions line (green)
                $resolutionsPath = '';
                $resolutionsAreaPath = '';
                $lastIndex = count($resolutionsData) - 1;
                for($i = 0; $i < count($resolutionsData); $i++) {
                  $x = $i * $xStep;
                  $y = $chartHeight - (($resolutionsData[$i] / $maxValue) * $chartHeight);
                  if($i == 0) {
                    $resolutionsPath .= "M $x,$y ";
                    $resolutionsAreaPath .= "M $x,$y ";
                  } else {
                    $resolutionsPath .= "L $x,$y ";
                    $resolutionsAreaPath .= "L $x,$y ";
                  }
                }
                $resolutionsAreaPath .= "L " . ($lastIndex * $xStep) . ",$chartHeight L 0,$chartHeight Z";
                
                // Generate paths for complaints line (yellow)
                $complaintsPath = '';
                $complaintsAreaPath = '';
                for($i = 0; $i < count($complaintsData); $i++) {
                  $x = $i * $xStep;
                  $y = $chartHeight - (($complaintsData[$i] / $maxValue) * $chartHeight);
                  if($i == 0) {
                    $complaintsPath .= "M $x,$y ";
                    $complaintsAreaPath .= "M $x,$y ";
                  } else {
                    $complaintsPath .= "L $x,$y ";
                    $complaintsAreaPath .= "L $x,$y ";
                  }
                }
                $complaintsAreaPath .= "L " . ($lastIndex * $xStep) . ",$chartHeight L 0,$chartHeight Z";
              @endphp
              
              <!-- Resolutions line (green) -->
              <path d="{{ $resolutionsAreaPath }}" fill="url(#greenLineGradient)" />
              <path d="{{ $resolutionsPath }}" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-opacity="0.5" stroke-linecap="round" stroke-linejoin="round" filter="url(#glowGreen)" />
              
              <!-- Complaints line (yellow) -->
              <path d="{{ $complaintsAreaPath }}" fill="url(#lineGradient)" />
              <path d="{{ $complaintsPath }}" fill="none" stroke="#ffd166" stroke-width="3.5" stroke-opacity="1" stroke-linecap="round" stroke-linejoin="round" filter="url(#glowWhite)" />
              
              <!-- Data points for resolutions -->
              @foreach($resolutionsData as $index => $value)
                @php
                  $x = $index * $xStep;
                  $y = $chartHeight - (($value / $maxValue) * $chartHeight);
                @endphp
                <circle cx="{{ $x }}" cy="{{ $y }}" r="2.5" fill="#22c55e" opacity="0.6" />
              @endforeach
              
              <!-- Data points for complaints -->
              @foreach($complaintsData as $index => $value)
                @php
                  $x = $index * $xStep;
                  $y = $chartHeight - (($value / $maxValue) * $chartHeight);
                  $isLast = $index == count($complaintsData) - 1;
                @endphp
                @if($isLast)
                  <circle cx="{{ $x }}" cy="{{ $y }}" r="5" fill="#ffd166" opacity="0.3" filter="url(#glowWhite)" />
                  <circle cx="{{ $x }}" cy="{{ $y }}" r="4.5" fill="#ffd166" stroke="#fff" stroke-width="2" filter="url(#glowWhite)" />
                  <circle cx="{{ $x }}" cy="{{ $y }}" r="3" fill="#fff" />
                @else
                  <circle cx="{{ $x }}" cy="{{ $y }}" r="3.5" fill="#ffd166" stroke="#fff" stroke-width="1.5" filter="url(#glowWhite)" />
                @endif
              @endforeach 
            </svg>
          </div>
        </div>
      </div>
    </div>

    <div class="charts-bottom-row">
      <div class="bar-card">
        <div class="bar-header">
          <div class="bar-title">Total Complaint Bar</div>
        </div>
        @php
          $thisWeekTotal = array_sum($weeklyData ?? []);
          $lastWeekTotal = array_sum($lastWeekData ?? []);
          $weekDays = $weekDayLabels ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
          // Ensure we have 7 days
          while(count($weekDays) < 7) {
            $weekDays[] = '';
          }
          while(count($weeklyData) < 7) {
            $weeklyData[] = 0;
          }
          while(count($lastWeekData) < 7) {
            $lastWeekData[] = 0;
          }
        @endphp
        <div class="bar-legend">
          <div class="bar-legend-item">
            <div class="bar-legend-color" style="background:#6bd1ff"></div>
            <span>This Week {{ number_format($thisWeekTotal) }}</span>
          </div>
          <div class="bar-legend-item">
            <div class="bar-legend-color" style="background:#ffd166"></div>
            <span>Last Week {{ number_format($lastWeekTotal) }}</span>
          </div>
        </div>
        <div class="bars-container">
          <div class="bar-y-axis">
            <span>{{ $maxWeeklyValue }}</span>
            <span>{{ round($maxWeeklyValue * 0.8) }}</span>
            <span>{{ round($maxWeeklyValue * 0.6) }}</span>
            <span>{{ round($maxWeeklyValue * 0.4) }}</span>
            <span>{{ round($maxWeeklyValue * 0.2) }}</span>
            <span>0</span>
          </div>
          <div class="bars-wrapper">
            @foreach($weeklyData as $index => $thisWeekValue)
              @php
                $lastWeekValue = $lastWeekData[$index] ?? 0;
                $thisWeekHeight = $maxWeeklyValue > 0 ? ($thisWeekValue / $maxWeeklyValue) * 100 : 0;
                $lastWeekHeight = $maxWeeklyValue > 0 ? ($lastWeekValue / $maxWeeklyValue) * 100 : 0;
                $increase = $lastWeekValue > 0 ? round((($thisWeekValue - $lastWeekValue) / $lastWeekValue) * 100) : 0;
              @endphp
              <div class="bar-group" style="position: relative;">
                <div class="bar bar-blue" style="height: {{ $thisWeekHeight }}%; width: 100%;"></div>
                <div class="bar bar-yellow" style="height: {{ $lastWeekHeight }}%; width: 100%;"></div>
                @if($increase > 0)
                <div class="bar-increase-label" style="left: 50%; transform: translateX(-50%); top: -20px;">{{ $increase }}% increase</div>
                @endif
              </div>
            @endforeach
          </div>
          <div class="bar-x-axis">
            @foreach($weekDays as $index => $day)
            <span>{{ $day }}</span>
            @endforeach
          </div>
        </div>
      </div>

      <div class="gauge-card">
        <div class="gauge-header">
          <div class="gauge-title">Total Complaint Chart</div>
          <div style="font-size:11px;opacity:.9">This Week</div>
        </div>
        <div class="gauge-legend">
          <div class="gauge-legend-item">
            <div class="gauge-legend-color" style="background:#ffd166"></div>
            <span>This Week {{ number_format($thisWeekTotal) }}</span>
          </div>
          <div class="gauge-legend-item">
            <div class="gauge-legend-color" style="background:#ff6b4b"></div>
            <span>Last Week {{ number_format($lastWeekTotal) }}</span>
          </div>
        </div>
        <div class="gauge-center">
          @php
            $gaugePercentage = min($averagePercentage, 100);
            $gaugeAngle = ($gaugePercentage / 100) * 180;
            $gaugeX = 80 + 70 * cos(deg2rad(180 - $gaugeAngle));
            $gaugeY = 80 - 70 * sin(deg2rad(180 - $gaugeAngle));
          @endphp
          <svg class="gauge-svg" width="180" height="100" viewBox="0 0 160 90" style="pointer-events: none;">
            <path d="M10 80 A70 70 0 0 1 150 80" fill="none" stroke="#ffd779" stroke-width="16" stroke-linecap="round" opacity="0.6"/>
            <path d="M10 80 A70 70 0 {{ $gaugePercentage > 50 ? 1 : 0 }} 1 {{ $gaugeX }} {{ $gaugeY }}" fill="none" stroke="#ff6b4b" stroke-width="18" stroke-linecap="round"/>
          </svg>
          <div style="position: relative; z-index: 2; text-align: center;">
            <div class="gauge-value">{{ number_format($averagePercentage, 2) }}</div>
            <div class="gauge-label">Average</div>
          </div>
        </div>
        <div class="small-metrics">
          <div class="m">
            <div class="value">{{ $stats['total_complaints'] > 1000 ? number_format($stats['total_complaints'] / 1000, 2) . 'k' : number_format($stats['total_complaints']) }}</div>
            <div class="label">Total Complaints</div>
          </div>
          <div class="m">
            <div class="value">{{ $stats['resolved_complaints'] > 1000 ? number_format($stats['resolved_complaints'] / 1000, 2) . 'k' : number_format($stats['resolved_complaints']) }}</div>
            <div class="label">Solved Complaints</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="list-card">
    <h3>Details on Complaints</h3>
    <div id="complaintsList">
    @forelse($recentComplaints as $complaint)
    @php
      $displayStatus = ($complaint->status === 'new') ? 'assigned' : $complaint->status;
      $statusLabels = [
        'assigned' => 'Assigned',
        'in_progress' => 'In Progress',
        'resolved' => 'Addressed',
        'closed' => 'Closed',
        'work_performa' => 'Work Performa',
        'maint_performa' => 'Maintenance Performa',
        'work_priced_performa' => 'Work Priced',
        'maint_priced_performa' => 'Maintenance Priced',
        'product_na' => 'Product N/A',
        'un_authorized' => 'Un-Authorized',
        'pertains_to_ge_const_isld' => 'GE Const Isld',
      ];
      $statusLabel = $statusLabels[$displayStatus] ?? ucfirst(str_replace('_', ' ', $displayStatus));
      $statusColors = [
        'assigned' => '#64748b',
        'in_progress' => '#dc2626',
        'resolved' => '#16a34a',
        'closed' => '#16a34a',
        'work_performa' => '#60a5fa',
        'maint_performa' => '#eab308',
        'work_priced_performa' => '#9333ea',
        'maint_priced_performa' => '#ea580c',
        'product_na' => '#000000',
        'un_authorized' => '#ec4899',
        'pertains_to_ge_const_isld' => '#06b6d4',
      ];
      $statusColor = $statusColors[$displayStatus] ?? '#64748b';
    @endphp
    <div class="list-item">
      <div class="bullet"></div>
      <div class="item-meta">
        <div class="item-info">
          <div class="item-title">{{ Str::limit($complaint->title, 40) }}</div>
          <div class="item-id">ID: {{ $complaint->id }} | Category: {{ $complaint->category ?? 'N/A' }} | Status: <span style="color: {{ $statusColor }}; font-weight: 600;">{{ $statusLabel }}</span></div>
        </div>
        <div class="complainant">
          <div class="avatar-small">{{ strtoupper(substr($complaint->client->name ?? ($complaint->client->client_name ?? 'U'), 0, 1)) }}</div>
          <div class="complainant-info">
            <div class="complainant-name">Complaint by</div>
            <div class="complainant-label">{{ $complaint->client->name ?? ($complaint->client->client_name ?? 'Unknown') }}</div>
          </div>
        </div>
        <div class="item-phone">{{ $complaint->client->phone ?? ($complaint->client->phone_number ?? 'N/A') }}</div>
        <div class="item-actions">
          <div class="action-icon print-complaint" data-complaint-id="{{ $complaint->id }}" title="Print Complaint">🖨️</div>
          <div class="action-icon more-actions" data-complaint-id="{{ $complaint->id }}" title="More Actions">⋯</div>
          <div class="actions-dropdown" id="dropdown-{{ $complaint->id }}" style="display: none;">
            <a href="/admin/complaints/{{ $complaint->id }}" target="_blank" class="dropdown-item">View Details</a>
            <a href="/admin/complaints/{{ $complaint->id }}/print-slip" target="_blank" class="dropdown-item">Print Slip</a>
          </div>
        </div>
      </div>
    </div>
    @empty
    <div class="list-item">
      <div class="item-meta" style="justify-content: center; padding: 20px;">
        <div style="color: #6b6b6b;">No complaints found</div>
      </div>
    </div>
    @endforelse
    </div>

    @php
      $currentPage = $recentComplaints->currentPage();
      $totalPages = $recentComplaints->lastPage();
      $firstItem = $recentComplaints->firstItem() ?? 0;
      $lastItem = $recentComplaints->lastItem() ?? 0;
      $totalComplaints = $recentComplaints->total();
    @endphp
    <div class="pagination-info">Showing {{ $firstItem }}-{{ $lastItem }} from {{ number_format($totalComplaints) }} complaints</div>
    @if($totalPages > 1)
    <div class="pagination" id="complaintsPagination">
      @if($currentPage > 1)
      <a href="{{ $recentComplaints->previousPageUrl() }}" class="pagination-btn arrow complaints-page-link">←</a>
      @else
      <button class="pagination-btn arrow" disabled>←</button>
      @endif
      
      @php
        $startPage = max(1, $currentPage - 1);
        $endPage = min($totalPages, $currentPage + 1);
        if ($endPage - $startPage < 2) {
          if ($startPage == 1) {
            $endPage = min($totalPages, $startPage + 2);
          } else {
            $startPage = max(1, $endPage - 2);
          }
        }
      @endphp
      
      @if($startPage > 1)
      <a href="{{ $recentComplaints->url(1) }}" class="pagination-btn complaints-page-link">1</a>
      @if($startPage > 2)
      <span class="pagination-btn" style="pointer-events: none;">...</span>
      @endif
      @endif
      
      @for($i = $startPage; $i <= $endPage; $i++)
      @if($i == $currentPage)
      <button class="pagination-btn active">{{ $i }}</button>
      @else
      <a href="{{ $recentComplaints->url($i) }}" class="pagination-btn complaints-page-link">{{ $i }}</a>
      @endif
      @endfor
      
      @if($endPage < $totalPages)
      @if($endPage < $totalPages - 1)
      <span class="pagination-btn" style="pointer-events: none;">...</span>
      @endif
      <a href="{{ $recentComplaints->url($totalPages) }}" class="pagination-btn complaints-page-link">{{ $totalPages }}</a>
      @endif
      
      @if($currentPage < $totalPages)
      <a href="{{ $recentComplaints->nextPageUrl() }}" class="pagination-btn arrow complaints-page-link">→</a>
      @else
      <button class="pagination-btn arrow" disabled>→</button>
      @endif
    </div>
    @endif
  </div>

</div>
@endsection

@push('scripts')
<script>
  // Animate bars on load
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.bar').forEach((b, i) => {
      b.style.transformOrigin = 'bottom';
      b.style.transition = 'transform 700ms ease';
      b.style.transform = 'scaleY(0)';
      setTimeout(() => {
        b.style.transform = 'scaleY(1)';
      }, 80 * i);
    });

    // Handle pagination clicks - scroll to top of complaints section
    document.querySelectorAll('.complaints-page-link').forEach(link => {
      link.addEventListener('click', function(e) {
        // Let the link work normally, but scroll to complaints section after navigation
        const complaintsSection = document.querySelector('.list-card');
        if (complaintsSection) {
          setTimeout(() => {
            complaintsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }, 100);
        }
      });
    });

    // Handle print complaint icon click
    document.querySelectorAll('.print-complaint').forEach(icon => {
      icon.addEventListener('click', function(e) {
        e.stopPropagation();
        const complaintId = this.getAttribute('data-complaint-id');
        if (complaintId) {
          window.open(`/admin/complaints/${complaintId}/print-slip`, '_blank');
        }
      });
    });

    // Handle more actions dropdown
    document.querySelectorAll('.more-actions').forEach(icon => {
      icon.addEventListener('click', function(e) {
        e.stopPropagation();
        const complaintId = this.getAttribute('data-complaint-id');
        const dropdown = document.getElementById(`dropdown-${complaintId}`);
        
        // Close all other dropdowns
        document.querySelectorAll('.actions-dropdown').forEach(dd => {
          if (dd.id !== `dropdown-${complaintId}`) {
            dd.style.display = 'none';
          }
        });

        // Toggle current dropdown
        if (dropdown) {
          dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }
      });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.item-actions')) {
        document.querySelectorAll('.actions-dropdown').forEach(dd => {
          dd.style.display = 'none';
        });
      }
    });
  });
</script>
@endpush
