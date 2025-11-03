@extends('layouts.sidebar')

@section('title', 'Reports — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2" >Reports & Analytics</h2>
      <p class="text-light" >Generate comprehensive reports and analytics</p>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-accent">
        <i data-feather="refresh-cw" class="me-2"></i>Refresh Data
      </button>
    </div>
  </div>
</div>

<!-- REPORT CARDS -->
<div class="row g-4 mb-4 justify-content-center">
  <div class="col-md-3">
    <div class="card-glass text-center">
      <div class="mb-3">
        <i data-feather="alert-circle" class="feather-lg text-primary"></i>
      </div>
      <h4 class="text-white mb-1">Complaints Report</h4>
      <p class="text-muted mb-3">Performance</p>
      <a href="{{ route('admin.reports.complaints') }}" class="btn btn-outline-primary btn-sm">
        View Report
      </a>
    </div>
  </div>
  
  <div class="col-md-3">
    <div class="card-glass text-center">
      <div class="mb-3">
        <i data-feather="users" class="feather-lg text-success"></i>
      </div>
      <h4 class="text-white mb-1">Employee Report</h4>
      <p class="text-muted mb-3">Employee performance and attendance</p>
      <a href="{{ route('admin.reports.employees') }}" class="btn btn-outline-success btn-sm">View Report</a>
    </div>
  </div>
  
  <div class="col-md-3">
    <div class="card-glass text-center">
      <div class="mb-3">
        <i data-feather="package" class="feather-lg text-warning"></i>
      </div>
      <h4 class="text-white mb-1">Spare Parts Report</h4>
      <p class="text-muted mb-3">Inventory and usage statistics</p>
      <a href="{{ route('admin.reports.spares') }}" class="btn btn-outline-warning btn-sm">View Report</a>
    </div>
  </div>
  
  
</div>

<!-- QUICK STATS -->
<div class="row g-4 mb-4">
  <div class="col-md-6">
    <div class="card-glass">
      <h5 class="text-white mb-3">Quick Statistics</h5>
      <div class="row g-3">
        <!-- Row 1: Complaints & SLA -->
        <div class="col-6">
          <div class="text-center">
            <div class="h4 text-primary mb-1">{{ $stats['active_complaints'] ?? 0 }}</div>
            <div class="text-muted small">Active Complaints</div>
          </div>
        </div>
        <div class="col-6">
          <div class="text-center">
            <div class="h4 text-success mb-1">{{ $stats['resolved_this_month'] ?? 0 }}</div>
            <div class="text-muted small">Resolved This Month</div>
          </div>
        </div>
        
        <!-- Row 2: SLA & Employees -->
        <div class="col-6">
          <div class="text-center">
            <div class="h4 text-warning mb-1">{{ $stats['sla_compliance'] ?? 0 }}%</div>
            <div class="text-muted small">SLA Compliance</div>
          </div>
        </div>
        <div class="col-6">
          <div class="text-center">
            <div class="h4 text-info mb-1">{{ $stats['active_employees'] ?? 0 }}</div>
            <div class="text-muted small">Active Employees</div>
          </div>
        </div>
        
        <!-- Row 3: Spare Parts & Stock -->
        <div class="col-6">
          <div class="text-center">
            <div class="h4 text-secondary mb-1">{{ $stats['total_spares'] ?? 0 }}</div>
            <div class="text-muted small">Total Spare Parts</div>
          </div>
        </div>
        <div class="col-6">
          <div class="text-center">
            <div class="h4 text-danger mb-1">{{ $stats['low_stock_items'] ?? 0 }}</div>
            <div class="text-muted small">Low Stock Items</div>
          </div>
        </div>
        
        <!-- Row 4: Approvals & Clients -->
        <div class="col-6">
          <div class="text-center">
            <div class="h4 text-warning mb-1">{{ $stats['pending_approvals'] ?? 0 }}</div>
            <div class="text-muted small">Pending Approvals</div>
          </div>
        </div>
        <div class="col-6">
          <div class="text-center">
            <div class="h4 text-success mb-1">{{ $stats['active_clients'] ?? 0 }}</div>
            <div class="text-muted small">Active Clients</div>
          </div>
        </div>
        
        <!-- Row 5: Performance & Resolution Time -->
        <div class="col-6">
          <div class="text-center">
            <div class="h4 text-info mb-1">{{ $stats['employee_performance'] ?? 0 }}%</div>
            <div class="text-muted small">Avg Performance</div>
          </div>
        </div>
        <div class="col-6">
          <div class="text-center">
            <div class="h4 text-primary mb-1">{{ $stats['avg_resolution_time'] ?? 0 }}h</div>
            <div class="text-muted small">Avg Resolution Time</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="card-glass">
      <h5 class="text-white mb-3">Recent Activity</h5>
      <div class="list-group list-group-flush">
        @forelse($recentActivity as $activity)
        <div class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border: none; color: #cbd5e1;">
          <div>
            <div class="fw-bold">{{ $activity['title'] }}</div>
            <small class="text-muted">{{ $activity['description'] }}</small>
            <br><small class="text-muted">{{ $activity['time'] }}</small>
          </div>
          <span class="badge bg-{{ $activity['badge_class'] }}">{{ $activity['badge'] }}</span>
        </div>
        @empty
        <div class="list-group-item text-center" style="background: transparent; border: none; color: #cbd5e1;">
          <div class="text-muted">No recent activity</div>
        </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

<!-- REPORT FILTERS -->
<div class="card-glass">
  <h5 class="text-white mb-3">Generate Custom Report</h5>
  <form id="customReportForm">
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label text-white">Report Type</label>
        <select name="report_type" class="form-select" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;" required>
          <option value="">Select Report Type</option>
          <option value="complaints">Complaints Report</option>
          <option value="employees">Employee Report</option>
          <option value="spares">Spare Parts Report</option>
          
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label text-white">Date From</label>
        <input type="date" name="date_from" class="form-control" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;" required>
      </div>
      <div class="col-md-3">
        <label class="form-label text-white">Date To</label>
        <input type="date" name="date_to" class="form-control" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;" required>
      </div>
      <div class="col-md-3">
        <label class="form-label text-white">&nbsp;</label>
        <div class="d-grid">
          <button type="submit" class="btn btn-accent">
            <i data-feather="file-text" class="me-2"></i>Generate Report
          </button>
        </div>
      </div>
    </div>
  </form>
</div>

@endsection

@push('scripts')
<script>
  feather.replace();


  document.addEventListener('DOMContentLoaded', function() {
    // Refresh Data functionality
    const refreshBtn = document.querySelector('.btn-accent');
    if (refreshBtn && refreshBtn.textContent.includes('Refresh Data')) {
      refreshBtn.addEventListener('click', function() {
        refreshReportData();
      });
    }

    // Custom Report Form
    const customReportForm = document.getElementById('customReportForm');
    if (customReportForm) {
      customReportForm.addEventListener('submit', function(e) {
        e.preventDefault();
        generateCustomReport();
      });
    }
  });

  function refreshReportData() {
    // Show loading state
    const btn = document.querySelector('.btn-accent');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i data-feather="loader" class="me-2"></i>Refreshing...';
    btn.disabled = true;
    feather.replace();

    // Refresh the page data
    setTimeout(() => {
      location.reload();
    }, 1500);
  }

  function generateCustomReport() {
    const form = document.getElementById('customReportForm');
    const formData = new FormData(form);
    
    const reportType = formData.get('report_type');
    const dateFrom = formData.get('date_from');
    const dateTo = formData.get('date_to');

    if (!reportType || !dateFrom || !dateTo) {
      showNotification('Please fill in all fields', 'error');
      return;
    }

    // Show loading
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i data-feather="loader" class="me-2"></i>Generating...';
    submitBtn.disabled = true;
    feather.replace();

    // Generate report based on type
    setTimeout(() => {
      const reportUrl = getReportUrl(reportType, dateFrom, dateTo);
      window.open(reportUrl, '_blank');
      
      // Reset button
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
      feather.replace();
      
      showNotification('Custom report generated successfully!', 'success');
    }, 2000);
  }

  function getReportUrl(type, from, to) {
    const baseUrl = window.location.origin + '/admin/reports/';
    const params = new URLSearchParams({
      date_from: from,
      date_to: to
    });
    
    switch(type) {
      case 'complaints':
        return `${baseUrl}complaints?${params.toString()}`;
      case 'employees':
        return `${baseUrl}employees?${params.toString()}`;
      case 'spares':
        return `${baseUrl}spares?${params.toString()}`;
      
      default:
        return baseUrl;
    }
  }

  

  function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification);
      }
    }, 5000);
  }
</script>
@endpush