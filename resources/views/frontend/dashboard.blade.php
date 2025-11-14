@extends('frontend.layouts.app')

@section('title', 'Complaint Dashboard')

@push('styles')
<style>
  /* Dashboard specific styles */
  body.dashboard-page,
  body {
    background: linear-gradient(180deg, #0e1a4b 0%, #08123a 55%) !important;
    color: #fff !important;
    padding: 30px !important;
    min-height: 100vh !important;
  }

  main {
    background: transparent !important;
    padding: 0 !important;
  }

  .dashboard-container {
    max-width: 900px;
    margin: 0 auto;
    margin-top: 30px;
    padding-top: 20px;
    padding-left: 20px;
    padding-right: 20px;
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
    background: linear-gradient(180deg, #12b05f, #0ea04a);
    border-radius: 12px;
    padding: 20px;
    color: #fff;
    min-height: 320px;
    display: flex;
    flex-direction: column;
  }

  .chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
  }

  .chart-title {
    font-weight: 700;
    font-size: 16px;
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
  }

  .chart-line {
    position: absolute;
    top: 15px;
    left: 35px;
    right: 15px;
    bottom: 25px;
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
    font-size: 10px;
    opacity: .9;
    color: #fff;
    font-weight: 500;
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
          <div class="value">932</div>
          <p>+10% than last month</p>
        </div>
      </div>
    </div>

    <div class="stat-card orange">
      <div class="left">
        <div class="icon">⏳</div>
        <div class="meta">
          <h2>Total In Progress</h2>
          <div class="value">357</div>
          <p>+23% than last month</p>
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
          <div class="chart-y-axis">
            <span>100</span>
            <span>75</span>
            <span>50</span>
            <span>25</span>
            <span>0</span>
          </div>
          <div class="chart-x-axis">
            <span>Jan</span>
            <span>Feb</span>
            <span>Mar</span>
            <span>Apr</span>
            <span>May</span>
            <span>Jun</span>
            <span>Jul</span>
            <span>Aug</span>
            <span>Sep</span>
            <span>Oct</span>
            <span>Nov</span>
            <span>Dec</span>
          </div>
          <div class="chart-line">
            <svg width="100%" height="100%" viewBox="0 0 400 200" preserveAspectRatio="none" style="overflow:visible">
              <defs>
                <linearGradient id="lineGradient" x1="0" x2="0" y1="0" y2="1">
                  <stop offset="0%" stop-color="#ffffff" stop-opacity="0.3" />
                  <stop offset="100%" stop-color="#ffffff" stop-opacity="0" />
                </linearGradient>
              </defs>
              <!-- Area fill with smooth curve -->
              <path d="M0,180 C16,160 25,150 33,140 C50,125 58,115 66,150 C75,130 87,115 100,100 C115,85 124,75 133,80 C142,75 154,70 166,60 C178,55 189,52 200,50 C211,48 222,46 233,45 C244,46 255,50 266,55 C277,60 288,65 300,70 C311,75 322,82 333,90 C344,98 355,104 366,110 C377,115 388,118 400,120 L400,200 L0,200 Z" fill="url(#lineGradient)" />
              <!-- Smooth curved line -->
              <path d="M0,180 C16,160 25,150 33,140 C50,125 58,115 66,150 C75,130 87,115 100,100 C115,85 124,75 133,80 C142,75 154,70 166,60 C178,55 189,52 200,50 C211,48 222,46 233,45 C244,46 255,50 266,55 C277,60 288,65 300,70 C311,75 322,82 333,90 C344,98 355,104 366,110 C377,115 388,118 400,120" fill="none" stroke="#fff" stroke-width="2.5" stroke-opacity="0.95" stroke-linecap="round" stroke-linejoin="round" />
              <!-- Data points -->
              <circle cx="33" cy="140" r="3.5" fill="#fff" />
              <circle cx="66" cy="150" r="3.5" fill="#fff" />
              <circle cx="100" cy="100" r="3.5" fill="#fff" />
              <circle cx="133" cy="80" r="3.5" fill="#fff" />
              <circle cx="166" cy="60" r="3.5" fill="#fff" />
              <circle cx="200" cy="50" r="3.5" fill="#fff" />
              <circle cx="233" cy="45" r="3.5" fill="#fff" />
              <circle cx="266" cy="55" r="3.5" fill="#fff" />
              <circle cx="300" cy="70" r="3.5" fill="#fff" />
              <circle cx="333" cy="90" r="3.5" fill="#fff" />
              <circle cx="366" cy="110" r="3.5" fill="#fff" />
              <circle cx="400" cy="120" r="3.5" fill="#fff" />
              <!-- Tooltip for July -->
              <g transform="translate(233, 45)">
                <rect x="-35" y="-28" width="70" height="22" fill="rgba(0,0,0,0.8)" rx="4" />
                <text x="0" y="-8" fill="#fff" font-size="10" text-anchor="middle" font-weight="500">1,105 July 2020</text>
              </g>
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
        <div class="bar-legend">
          <div class="bar-legend-item">
            <div class="bar-legend-color" style="background:#6bd1ff"></div>
            <span>This Week 1.245</span>
          </div>
          <div class="bar-legend-item">
            <div class="bar-legend-color" style="background:#ffd166"></div>
            <span>Last Week 1.356</span>
          </div>
        </div>
        <div class="bars-container">
          <div class="bar-y-axis">
            <span>100</span>
            <span>80</span>
            <span>60</span>
            <span>40</span>
            <span>20</span>
            <span>0</span>
          </div>
          <div class="bar-x-axis">
            <span>Mon</span>
            <span>Tue</span>
            <span>Wed</span>
            <span>Thu</span>
            <span>Fri</span>
            <span>Sat</span>
            <span>Sun</span>
          </div>
          <div class="bars-wrapper">
            <div class="bar-group">
              <div class="bar bar-blue" style="height:65%; width: 100%;"></div>
              <div class="bar bar-yellow" style="height:50%; width: 100%;"></div>
            </div>
            <div class="bar-group">
              <div class="bar bar-blue" style="height:55%; width: 100%;"></div>
              <div class="bar bar-yellow" style="height:45%; width: 100%;"></div>
            </div>
            <div class="bar-group">
              <div class="bar bar-blue" style="height:45%; width: 100%;"></div>
              <div class="bar bar-yellow" style="height:40%; width: 100%;"></div>
            </div>
            <div class="bar-group" style="position: relative;">
              <div class="bar bar-blue" style="height:60%; width: 100%;"></div>
              <div class="bar bar-yellow" style="height:55%; width: 100%;"></div>
              <div class="bar-increase-label" style="left: 50%; transform: translateX(-50%); top: -20px;">42% increases</div>
            </div>
            <div class="bar-group">
              <div class="bar bar-blue" style="height:70%; width: 100%;"></div>
              <div class="bar bar-yellow" style="height:65%; width: 100%;"></div>
            </div>
            <div class="bar-group">
              <div class="bar bar-blue" style="height:80%; width: 100%;"></div>
              <div class="bar bar-yellow" style="height:75%; width: 100%;"></div>
            </div>
            <div class="bar-group">
              <div class="bar bar-blue" style="height:58%; width: 100%;"></div>
              <div class="bar bar-yellow" style="height:52%; width: 100%;"></div>
            </div>
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
            <span>This Week 1.245</span>
          </div>
          <div class="gauge-legend-item">
            <div class="gauge-legend-color" style="background:#ff6b4b"></div>
            <span>Last Week 1.356</span>
          </div>
        </div>
        <div class="gauge-center">
          <svg class="gauge-svg" width="180" height="100" viewBox="0 0 160 90" style="pointer-events: none;">
            <path d="M10 80 A70 70 0 0 1 150 80" fill="none" stroke="#ffd779" stroke-width="16" stroke-linecap="round" opacity="0.6"/>
            <path d="M35 80 A45 45 0 0 1 125 80" fill="none" stroke="#ff6b4b" stroke-width="18" stroke-linecap="round"/>
          </svg>
          <div style="position: relative; z-index: 2; text-align: center;">
            <div class="gauge-value">46.78</div>
            <div class="gauge-label">Average</div>
          </div>
        </div>
        <div class="small-metrics">
          <div class="m">
            <div class="value">146.06k</div>
            <div class="label">Total Complaints</div>
          </div>
          <div class="m">
            <div class="value">40.36k</div>
            <div class="label">Solved Complaints</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="list-card">
    <h3>Details on Complaints</h3>

    <div class="list-item">
      <div class="bullet"></div>
      <div class="item-meta">
        <div class="item-info">
          <div class="item-title">Problem 1</div>
          <div class="item-id">ID 123456789</div>
        </div>
        <div class="complainant">
          <div class="avatar-small">A</div>
          <div class="complainant-info">
            <div class="complainant-name">Name</div>
            <div class="complainant-label">Complaint by</div>
          </div>
        </div>
        <div class="item-phone">+92 12345 6789</div>
        <div class="item-actions">
          <div class="action-icon">🖨️</div>
          <div class="action-icon">⋯</div>
        </div>
      </div>
    </div>

    <div class="list-item">
      <div class="bullet"></div>
      <div class="item-meta">
        <div class="item-info">
          <div class="item-title">Problem 1</div>
          <div class="item-id">ID 123456789</div>
        </div>
        <div class="complainant">
          <div class="avatar-small">B</div>
          <div class="complainant-info">
            <div class="complainant-name">Name</div>
            <div class="complainant-label">Complaint by</div>
          </div>
        </div>
        <div class="item-phone">+92 12345 6789</div>
        <div class="item-actions">
          <div class="action-icon">🖨️</div>
          <div class="action-icon">⋯</div>
        </div>
      </div>
    </div>

    <div class="list-item">
      <div class="bullet"></div>
      <div class="item-meta">
        <div class="item-info">
          <div class="item-title">Problem 1</div>
          <div class="item-id">ID 123456789</div>
        </div>
        <div class="complainant">
          <div class="avatar-small">C</div>
          <div class="complainant-info">
            <div class="complainant-name">Name</div>
            <div class="complainant-label">Complaint by</div>
          </div>
        </div>
        <div class="item-phone">+92 12345 6789</div>
        <div class="item-actions">
          <div class="action-icon">🖨️</div>
          <div class="action-icon">⋯</div>
        </div>
      </div>
    </div>

    <div class="list-item">
      <div class="bullet"></div>
      <div class="item-meta">
        <div class="item-info">
          <div class="item-title">Problem 1</div>
          <div class="item-id">ID 123456789</div>
        </div>
        <div class="complainant">
          <div class="avatar-small">D</div>
          <div class="complainant-info">
            <div class="complainant-name">Name</div>
            <div class="complainant-label">Complaint by</div>
          </div>
        </div>
        <div class="item-phone">+92 12345 6789</div>
        <div class="item-actions">
          <div class="action-icon">🖨️</div>
          <div class="action-icon">⋯</div>
        </div>
      </div>
    </div>

    <div class="list-item">
      <div class="bullet"></div>
      <div class="item-meta">
        <div class="item-info">
          <div class="item-title">Problem 1</div>
          <div class="item-id">ID 123456789</div>
        </div>
        <div class="complainant">
          <div class="avatar-small">E</div>
          <div class="complainant-info">
            <div class="complainant-name">Name</div>
            <div class="complainant-label">Complaint by</div>
          </div>
        </div>
        <div class="item-phone">+92 12345 6789</div>
        <div class="item-actions">
          <div class="action-icon">🖨️</div>
          <div class="action-icon">⋯</div>
        </div>
      </div>
    </div>

    <div class="pagination-info">Showing 1-5 from 100 data</div>
    <div class="pagination">
      <button class="pagination-btn arrow">←</button>
      <button class="pagination-btn active">1</button>
      <button class="pagination-btn">2</button>
      <button class="pagination-btn">3</button>
      <button class="pagination-btn arrow">→</button>
    </div>
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
  });
</script>
@endpush
