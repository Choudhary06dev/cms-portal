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
      <button class="btn btn-outline-light">
        <i data-feather="download" class="me-2"></i>Export All
      </button>
      <button class="btn btn-accent">
        <i data-feather="refresh-cw" class="me-2"></i>Refresh Data
      </button>
    </div>
  </div>
</div>

<!-- REPORT CARDS -->
<div class="row g-4 mb-4">
  <div class="col-md-3">
    <div class="card-glass text-center">
      <div class="mb-3">
        <i data-feather="alert-circle" class="feather-lg text-primary"></i>
      </div>
      <h4 class="text-white mb-1">Complaints Report</h4>
      <p class="text-muted mb-3">View complaint statistics and trends</p>
      <a href="{{ route('admin.reports.complaints') }}" class="btn btn-outline-primary btn-sm">View Report</a>
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
  
  <div class="col-md-3">
    <div class="card-glass text-center">
      <div class="mb-3">
        <i data-feather="dollar-sign" class="feather-lg text-info"></i>
      </div>
      <h4 class="text-white mb-1">Financial Report</h4>
      <p class="text-muted mb-3">Cost analysis and budgeting</p>
      <a href="{{ route('admin.reports.financial') }}" class="btn btn-outline-info btn-sm">View Report</a>
    </div>
  </div>
</div>

<!-- QUICK STATS -->
<div class="row g-4 mb-4">
  <div class="col-md-6">
    <div class="card-glass">
      <h5 class="text-white mb-3">Quick Statistics</h5>
      <div class="row g-3">
        <div class="col-6">
          <div class="text-center">
            <div class="h4 text-primary mb-1">24</div>
            <div class="text-muted small">Active Complaints</div>
          </div>
        </div>
        <div class="col-6">
          <div class="text-center">
            <div class="h4 text-success mb-1">156</div>
            <div class="text-muted small">Resolved This Month</div>
          </div>
        </div>
        <div class="col-6">
          <div class="text-center">
            <div class="h4 text-warning mb-1">89%</div>
            <div class="text-muted small">SLA Compliance</div>
          </div>
        </div>
        <div class="col-6">
          <div class="text-center">
            <div class="h4 text-info mb-1">45</div>
            <div class="text-muted small">Active Employees</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="card-glass">
      <h5 class="text-white mb-3">Recent Activity</h5>
      <div class="list-group list-group-flush">
        <div class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border: none; color: #cbd5e1;">
          <div>
            <div class="fw-bold">New complaint submitted</div>
            <small class="text-muted">2 minutes ago</small>
          </div>
          <span class="badge bg-primary">New</span>
        </div>
        <div class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border: none; color: #cbd5e1;">
          <div>
            <div class="fw-bold">Spare part approved</div>
            <small class="text-muted">15 minutes ago</small>
          </div>
          <span class="badge bg-success">Approved</span>
        </div>
        <div class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border: none; color: #cbd5e1;">
          <div>
            <div class="fw-bold">Employee leave request</div>
            <small class="text-muted">1 hour ago</small>
          </div>
          <span class="badge bg-warning">Pending</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- REPORT FILTERS -->
<div class="card-glass">
  <h5 class="text-white mb-3">Generate Custom Report</h5>
  <form>
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label text-white">Report Type</label>
        <select class="form-select" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;">
          <option value="">Select Report Type</option>
          <option value="complaints">Complaints Report</option>
          <option value="employees">Employee Report</option>
          <option value="spares">Spare Parts Report</option>
          <option value="financial">Financial Report</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label text-white">Date From</label>
        <input type="date" class="form-control" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;">
      </div>
      <div class="col-md-3">
        <label class="form-label text-white">Date To</label>
        <input type="date" class="form-control" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #fff;">
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
</script>
@endpush