@extends('frontend.layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
  body {
    background: #0f172a !important;
    color: #ffffff;
    font-family: 'Inter', sans-serif;
  }

  .dashboard-container {
    background: #0f172a;
    min-height: 100vh;
    padding: 1.5rem;
  }

  /* Header Section */
  .dashboard-header {
    padding: 0;
    margin-bottom: 0;
  }

  /* Second Bar - User Feedback */
  .user-feedback-bar {
    background: rgba(15, 23, 42, 0.8);
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }

  .user-feedback-btn {
    background: linear-gradient(135deg, #3b82f6, #1e40af);
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.95rem;
  }

  .user-feedback-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
  }

  /* Welcome Section */
  .welcome-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
  }

  .welcome-text h2 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
  }

  .welcome-date {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
  }

  /* Metric Cards */
  .metric-cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
  }

  .metric-card {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(30, 64, 175, 0.2));
    border: 1px solid rgba(59, 130, 246, 0.3);
    border-radius: 12px;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
  }

  .metric-card.yellow {
    background: linear-gradient(135deg, rgba(234, 179, 8, 0.2), rgba(202, 138, 4, 0.2));
    border-color: rgba(234, 179, 8, 0.3);
  }

  .metric-card-icon {
    width: 50px;
    height: 50px;
    background: rgba(59, 130, 246, 0.3);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    font-size: 1.5rem;
  }

  .metric-card.yellow .metric-card-icon {
    background: rgba(234, 179, 8, 0.3);
  }

  .metric-title {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 0.5rem;
  }

  .metric-value {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }

  .metric-trend {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.7);
  }

  /* Charts Section */
  .charts-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
  }

  .chart-card {
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1.5rem;
  }

  .chart-card.green {
    background: rgba(34, 197, 94, 0.1);
    border-color: rgba(34, 197, 94, 0.3);
  }

  .chart-card.blue {
    background: rgba(59, 130, 246, 0.1);
    border-color: rgba(59, 130, 246, 0.3);
  }

  .chart-card.orange {
    background: rgba(249, 115, 22, 0.1);
    border-color: rgba(249, 115, 22, 0.3);
  }

  .chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
  }

  .chart-title {
    font-size: 1.1rem;
    font-weight: 600;
  }

  .chart-filter {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    padding: 0.4rem 0.8rem;
    color: white;
    font-size: 0.85rem;
    cursor: pointer;
  }

  .chart-container {
    height: 300px;
    position: relative;
  }

  .gauge-chart {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 250px;
  }

  .gauge-value {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
  }

  .gauge-summary {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    width: 100%;
    margin-top: 1rem;
  }

  .gauge-summary-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
  }

  .gauge-summary-value {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
  }

  .gauge-summary-label {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.7);
  }

  /* Complaints List */
  .complaints-list-section {
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1.5rem;
  }

  .section-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
  }

  .complaint-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    margin-bottom: 0.75rem;
    transition: all 0.3s ease;
  }

  .complaint-item:hover {
    background: rgba(255, 255, 255, 0.1);
  }

  .complaint-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #a855f7, #7c3aed);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    flex-shrink: 0;
  }

  .complaint-info {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .complaint-details {
    flex: 1;
  }

  .complaint-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
  }

  .complaint-id {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.7);
  }

  .complaint-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.7);
  }

  .complaint-icon {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border-radius: 6px;
    transition: all 0.3s ease;
  }

  .complaint-icon:hover {
    background: rgba(255, 255, 255, 0.1);
  }

  .pagination-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
  }

  .pagination-info {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.7);
  }

  .pagination-controls {
    display: flex;
    gap: 0.5rem;
    align-items: center;
  }

  .pagination-btn {
    width: 35px;
    height: 35px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .pagination-btn:hover {
    background: rgba(255, 255, 255, 0.2);
  }

  .pagination-btn.active {
    background: linear-gradient(135deg, #3b82f6, #1e40af);
    border-color: #3b82f6;
  }

  /* Footer */
  .dashboard-footer {
    text-align: center;
    padding: 2rem 0;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.85rem;
    margin-top: 2rem;
  }

  @media (max-width: 991px) {
    .charts-section {
      grid-template-columns: 1fr;
    }
    
    .metric-cards {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="dashboard-container">
  <!-- User Feedback Bar -->
  <div class="user-feedback-bar">
    <button class="user-feedback-btn">USER FEEDBACK'S</button>
  </div>

  <!-- Welcome Section -->
  <div class="welcome-section">
    <div class="welcome-text">
      <h2>Welcome, Dashboard</h2>
      <div class="welcome-date">{{ now()->format('D d M Y') }}</div>
    </div>
  </div>

  <!-- Metric Cards -->
  <div class="metric-cards">
    <div class="metric-card">
      <div class="metric-card-icon">
        <i data-feather="user"></i>
      </div>
      <div class="metric-title">Total Complaints</div>
      <div class="metric-value">{{ $stats['total_complaints'] ?? 932 }}</div>
      <div class="metric-trend">+10% than last month</div>
    </div>
    <div class="metric-card yellow">
      <div class="metric-card-icon">
        <i data-feather="refresh-cw"></i>
      </div>
      <div class="metric-title">Total in Progress</div>
      <div class="metric-value">{{ $stats['pending_complaints'] ?? 357 }}</div>
      <div class="metric-trend">+23% than last month</div>
    </div>
  </div>

  <!-- Charts Section -->
  <div class="charts-section">
    <!-- Total Complaints Addressed Chart -->
    <div class="chart-card green">
      <div class="chart-header">
        <div class="chart-title">Total Complaints Addressed</div>
        <select class="chart-filter">
          <option>Month</option>
        </select>
      </div>
      <div class="chart-container">
        <canvas id="addressedChart"></canvas>
      </div>
    </div>

    <!-- Total Complaint Bar Chart -->
    <div class="chart-card blue">
      <div class="chart-header">
        <div class="chart-title">Total Complaint Bar</div>
      </div>
      <div class="chart-container">
        <canvas id="barChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Total Complaint Chart (Gauge) -->
  <div class="chart-card orange" style="margin-bottom: 2rem;">
    <div class="chart-header">
      <div class="chart-title">Total Complaint Chart</div>
    </div>
    <div class="gauge-chart">
      <div class="gauge-value">Average 46.78</div>
      <div style="width: 200px; height: 200px; position: relative;">
        <canvas id="gaugeChart"></canvas>
      </div>
      <div class="gauge-summary">
        <div class="gauge-summary-card">
          <div class="gauge-summary-value">146.06k</div>
          <div class="gauge-summary-label">Total Complaints</div>
        </div>
        <div class="gauge-summary-card">
          <div class="gauge-summary-value">40.36k</div>
          <div class="gauge-summary-label">Solved Complaints</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Details on Complaints -->
  <div class="complaints-list-section">
    <div class="section-title">Details on Complaints</div>
    
    @forelse(($recentComplaints ?? [])->take(5) as $complaint)
    <div class="complaint-item">
      <div class="complaint-avatar">P</div>
      <div class="complaint-info">
        <div class="complaint-details">
          <div class="complaint-name">Problem {{ $loop->iteration }}</div>
          <div class="complaint-id">ID {{ $complaint->id ?? '123456789' }}</div>
        </div>
        <div class="complaint-meta">
          <i data-feather="user" style="width: 18px; height: 18px;"></i>
          <span>Name Complaint by</span>
          <span>+92 12345 6789</span>
        </div>
      </div>
      <div class="complaint-icon">
        <i data-feather="file-text"></i>
      </div>
      <div class="complaint-icon">
        <i data-feather="more-vertical"></i>
      </div>
    </div>
    @empty
    @for($i = 1; $i <= 5; $i++)
    <div class="complaint-item">
      <div class="complaint-avatar">P</div>
      <div class="complaint-info">
        <div class="complaint-details">
          <div class="complaint-name">Problem {{ $i }}</div>
          <div class="complaint-id">ID 123456789</div>
        </div>
        <div class="complaint-meta">
          <i data-feather="user" style="width: 18px; height: 18px;"></i>
          <span>Name Complaint by</span>
          <span>+92 12345 6789</span>
        </div>
      </div>
      <div class="complaint-icon">
        <i data-feather="file-text"></i>
      </div>
      <div class="complaint-icon">
        <i data-feather="more-vertical"></i>
      </div>
    </div>
    @endfor
    @endforelse

    <div class="pagination-section">
      <div class="pagination-info">Showing 1-5 from 100 data</div>
      <div class="pagination-controls">
        <div class="pagination-btn">
          <i data-feather="chevron-left"></i>
        </div>
        <div class="pagination-btn active">1</div>
        <div class="pagination-btn">2</div>
        <div class="pagination-btn">3</div>
        <div class="pagination-btn">
          <i data-feather="chevron-right"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <div class="dashboard-footer">
    © 2021 - 2025 All rights reserved.
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Initialize feather icons
  if (typeof feather !== 'undefined') {
    feather.replace();
  }

  // Total Complaints Addressed Line Chart
  const addressedCtx = document.getElementById('addressedChart');
  if (addressedCtx) {
    new Chart(addressedCtx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
          label: 'O increase',
          data: [20, 35, 45, 55, 65, 75, 85, 80, 70, 60, 50, 40],
          borderColor: 'rgba(34, 197, 94, 0.6)',
          backgroundColor: 'rgba(34, 197, 94, 0.1)',
          tension: 0.4
        }, {
          label: 'O solved',
          data: [15, 30, 40, 50, 60, 70, 80, 75, 65, 55, 45, 35],
          borderColor: 'rgba(34, 197, 94, 1)',
          backgroundColor: 'rgba(34, 197, 94, 0.2)',
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: 'bottom',
            labels: {
              color: 'rgba(255, 255, 255, 0.8)'
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            max: 100,
            ticks: {
              stepSize: 25,
              color: 'rgba(255, 255, 255, 0.7)'
            },
            grid: {
              color: 'rgba(255, 255, 255, 0.1)'
            }
          },
          x: {
            ticks: {
              color: 'rgba(255, 255, 255, 0.7)'
            },
            grid: {
              color: 'rgba(255, 255, 255, 0.1)'
            }
          }
        }
      }
    });
  }

  // Total Complaint Bar Chart
  const barCtx = document.getElementById('barChart');
  if (barCtx) {
    new Chart(barCtx, {
      type: 'bar',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'This Week',
          data: [45, 60, 55, 70, 65, 50, 40],
          backgroundColor: 'rgba(234, 179, 8, 0.8)',
          borderColor: 'rgba(234, 179, 8, 1)',
          borderWidth: 1
        }, {
          label: 'Last Week',
          data: [35, 50, 45, 60, 55, 40, 30],
          backgroundColor: 'rgba(59, 130, 246, 0.6)',
          borderColor: 'rgba(59, 130, 246, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: 'bottom',
            labels: {
              color: 'rgba(255, 255, 255, 0.8)'
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            max: 100,
            ticks: {
              stepSize: 20,
              color: 'rgba(255, 255, 255, 0.7)'
            },
            grid: {
              color: 'rgba(255, 255, 255, 0.1)'
            }
          },
          x: {
            ticks: {
              color: 'rgba(255, 255, 255, 0.7)'
            },
            grid: {
              color: 'rgba(255, 255, 255, 0.1)'
            }
          }
        }
      }
    });
  }

  // Gauge Chart
  const gaugeCtx = document.getElementById('gaugeChart');
  if (gaugeCtx) {
    new Chart(gaugeCtx, {
      type: 'doughnut',
      data: {
        datasets: [{
          data: [46.78, 53.22],
          backgroundColor: [
            'rgba(234, 179, 8, 0.8)',
            'rgba(239, 68, 68, 0.3)'
          ],
          borderWidth: 0,
          cutout: '75%'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            enabled: false
          }
        },
        rotation: -90,
        circumference: 180
      }
    });
  }
</script>

@endsection
