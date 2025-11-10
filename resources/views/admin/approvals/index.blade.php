@extends('layouts.sidebar')

@section('title', 'Approvals Management — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Total Complaints</h2>
      <p class="text-light">View and manage complaint records</p>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-secondary" onclick="refreshPage()">
        <i data-feather="refresh-cw" class="me-2"></i>Refresh
      </button>
    </div>
  </div>
</div>

<!-- FILTERS -->
<div class="card-glass mb-4" style="display: inline-block; width: fit-content;">
  <form id="approvalsFiltersForm" method="GET" action="{{ route('admin.approvals.index') }}" onsubmit="event.preventDefault(); submitApprovalsFilters(event); return false;">
  <div class="row g-2 align-items-end">
    <div class="col-auto">
      <label class="form-label small mb-1" style="font-size: 0.8rem; color: #000000 !important; font-weight: 500;">Search</label>
      <input type="text" class="form-control" id="searchInput" name="search" placeholder="Complaint ID or Address..." 
             value="{{ request('search') }}" autocomplete="off" style="font-size: 0.9rem; width: 200px;">
    </div>
    <div class="col-auto">
      <label class="form-label small mb-1" style="font-size: 0.8rem; color: #000000 !important; font-weight: 500;">From Date</label>
      <input type="date" class="form-control" name="complaint_date" 
             value="{{ request('complaint_date') }}" placeholder="Select Date" autocomplete="off" style="font-size: 0.9rem; width: 150px;">
    </div>
    <div class="col-auto">
      <label class="form-label small mb-1" style="font-size: 0.8rem; color: #000000 !important; font-weight: 500;">To Date</label>
      <input type="date" class="form-control" name="date_to" 
             value="{{ request('date_to') }}" placeholder="End Date" autocomplete="off" style="font-size: 0.9rem; width: 150px;">
    </div>
    <div class="col-auto">
      <label class="form-label small mb-1" style="font-size: 0.8rem; color: #000000 !important; font-weight: 500;">Category</label>
      <select class="form-select" name="category" autocomplete="off" style="font-size: 0.9rem; width: 140px;">
        <option value="" {{ request('category') ? '' : 'selected' }}>All</option>
        @if(isset($categories) && $categories->count() > 0)
          @foreach($categories as $cat)
            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
          @endforeach
        @else
          <option value="electric">Electric</option>
          <option value="technical">Technical</option>
          <option value="service">Service</option>
          <option value="billing">Billing</option>
          <option value="water">Water Supply</option>
          <option value="sanitary">Sanitary</option>
          <option value="plumbing">Plumbing</option>
          <option value="kitchen">Kitchen</option>
          <option value="other">Other</option>
        @endif
      </select>
    </div>
    <div class="col-auto">
      <label class="form-label small mb-1" style="font-size: 0.8rem; color: #000000 !important; font-weight: 500;">Status</label>
      <select class="form-select" name="status" autocomplete="off" onchange="submitApprovalsFilters()" style="font-size: 0.9rem; width: 180px;">
        <option value="" {{ request('status') ? '' : 'selected' }}>All</option>
        @if(isset($statuses) && $statuses->count() > 0)
          @foreach($statuses as $statusValue => $statusLabel)
            @if(!empty($statusValue) && !empty($statusLabel))
              <option value="{{ $statusValue }}" {{ request('status') == $statusValue ? 'selected' : '' }}>{{ $statusLabel }}</option>
            @endif
          @endforeach
        @else
          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
          <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        @endif
      </select>
    </div>
    <div class="col-auto">
      <label class="form-label small text-muted mb-1" style="font-size: 0.8rem;">&nbsp;</label>
      <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetApprovalsFilters()" style="font-size: 0.9rem; padding: 0.35rem 0.8rem;">
        <i data-feather="refresh-cw" class="me-1" style="width: 14px; height: 14px;"></i>Reset
      </button>
    </div>
  </div>
  </form>
</div>

<!-- APPROVALS TABLE -->

<div class="card-glass">
  <div class="table-responsive">
        <table class="table table-dark table-sm">
      <thead>
        <tr>
          <th>#</th>
          <th>Registration Date/Time</th>
          <th>Addressed Date/Time</th>
          <th>Complaint ID</th>
          <th>Complainant Name</th>
          <th>Address</th>
          <th>Complaint Nature & Type</th>
          <th>Phone No.</th>
          <th>Performa Required</th>
          <th>Status</th>
          <th>Actions|Feedback</th>
        </tr>
      </thead>
      <tbody id="approvalsTableBody">
        @forelse($approvals as $approval)
        @php
          $complaint = $approval->complaint ?? null;
        @endphp
        @if($complaint)
        @php
          $category = $complaint->category ?? 'N/A';
          $designation = $complaint->assignedEmployee->designation ?? 'N/A';
          // Use original complaint category as-is, don't change it
          // Only format basic categories, keep all other categories as they are
          $categoryDisplay = [
            'electric' => 'Electric',
            'technical' => 'Technical',
            'service' => 'Service',
            'billing' => 'Billing',
            'water' => 'Water Supply',
            'sanitary' => 'Sanitary',
            'plumbing' => 'Plumbing',
            'kitchen' => 'Kitchen',
            'other' => 'Other',
          ];
          // If category exists in mapping, use it; otherwise use original category as-is
          $catDisplay = $categoryDisplay[strtolower($category)] ?? $category;
          $displayText = $catDisplay . ' - ' . $designation;
          
          // Convert 'new' status to 'assigned' for display
          $rawStatus = $complaint->status ?? 'new';
          $complaintStatus = ($rawStatus == 'new') ? 'assigned' : $rawStatus;
          $statusDisplay = $complaintStatus == 'in_progress' ? 'In-Process' : 
                          ($complaintStatus == 'resolved' ? 'Addressed' : 
                          ucfirst(str_replace('_', ' ', $complaintStatus)));
          
          // Status colors mapping
          $statusColors = [
            'in_progress' => ['bg' => '#dc2626', 'text' => '#ffffff', 'border' => '#b91c1c'], // Darker Red
            'resolved' => ['bg' => '#16a34a', 'text' => '#ffffff', 'border' => '#15803d'], // Darker Green
            'work_performa' => ['bg' => '#60a5fa', 'text' => '#ffffff', 'border' => '#3b82f6'], // Light Blue
            'maint_performa' => ['bg' => '#eab308', 'text' => '#ffffff', 'border' => '#ca8a04'], // Dark Yellow
            'work_priced_performa' => ['bg' => '#9333ea', 'text' => '#ffffff', 'border' => '#7e22ce'], // Purple
            'maint_priced_performa' => ['bg' => '#ea580c', 'text' => '#ffffff', 'border' => '#c2410c'], // Dark Orange
            'product_na' => ['bg' => '#6b7280', 'text' => '#ffffff', 'border' => '#4b5563'], // Gray
            'un_authorized' => ['bg' => '#ec4899', 'text' => '#ffffff', 'border' => '#db2777'], // Pink
            'pertains_to_ge_const_isld' => ['bg' => '#06b6d4', 'text' => '#ffffff', 'border' => '#0891b2'], // Aqua/Cyan
            'assigned' => ['bg' => '#64748b', 'text' => '#ffffff', 'border' => '#475569'], // Default Gray
          ];
          
          // Get current status color or default
          $currentStatusColor = $statusColors[$complaintStatus] ?? $statusColors['assigned'];
        @endphp
        <tr style="position: relative;">
          @php
            $waitingRaw = $approval->waiting_for_authority ?? false;
            $showDot = ($waitingRaw === true || $waitingRaw === 1 || $waitingRaw === '1' || $waitingRaw === 'true');
          @endphp
          @if($showDot)
          <td style="position: relative; padding-left: 20px; text-align: center;">
            <span class="blinking-dot" style="position: absolute; left: 8px; top: 50%; transform: translateY(-50%); width: 8px; height: 8px; background-color: #ffffff; border-radius: 50%; animation: blink 1s infinite; z-index: 1;"></span>
            <span style="display: inline-block;">{{ $approval->id }}</span>
          </td>
          @else
          <td style="text-align: center;">{{ $approval->id }}</td>
          @endif
          <td>{{ $complaint->created_at ? $complaint->created_at->timezone('Asia/Karachi')->format('M d, Y H:i:s') : 'N/A' }}</td>
          <td>{{ $complaint->closed_at ? $complaint->closed_at->timezone('Asia/Karachi')->format('M d, Y H:i:s') : '' }}</td>
          <td>
            <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="text-decoration-none" style="color: #3b82f6;">
              {{ str_pad($complaint->complaint_id ?? $complaint->id, 4, '0', STR_PAD_LEFT) }}
            </a>
          </td>
          <td>{{ $complaint->client->client_name ?? 'N/A' }}</td>
          <td>{{ $complaint->client->address ?? 'N/A' }}</td>
          <td>
            <div class="text-white">{{ $displayText }}</div>
          </td>
          <td>{{ $complaint->client->phone ?? 'N/A' }}</td>
          <td style="color: white !important; position: relative;">
            @if($complaintStatus == 'resolved' || $complaintStatus == 'closed')
              <span style="color: white !important;">-</span>
            @else
              @if(isset($approval->performa_type) && $approval->performa_type)
                @php
                  // Handle special case for maint_performa to show "Maintenance Performa" instead of "Maint Performa"
                  if ($approval->performa_type === 'maint_performa') {
                    $performaTypeLabel = 'Maintenance Performa';
                  } else {
                    $performaTypeLabel = ucwords(str_replace('_', ' ', $approval->performa_type));
                  }
                  // Always use performa type color when performa type is selected
                  // Color should change as soon as performa type is selected, regardless of waiting_for_authority
                  $badgeColor = $statusColors[$approval->performa_type]['bg'] ?? $statusColors['work_performa']['bg'];
                @endphp
                <span class="badge rounded-pill performa-badge" style="padding: 6px 10px; font-weight:600; color: white !important; background-color: {{ $badgeColor }} !important;">
                  {{ $performaTypeLabel }}
                </span>
              @else
                <span class="badge rounded-pill performa-badge" style="display:none; padding: 6px 10px; font-weight:600; color: white !important;"></span>
              @endif
            @endif
          </td>
          <td>
            @php
              // Get product and price information
              $complaintSpare = $complaint->spareParts->first();
              $pricePerForma = 'N/A';
              
              if ($complaintSpare && $complaintSpare->spare && $complaintSpare->spare->unit_price) {
                $pricePerForma = 'PKR ' . number_format($complaintSpare->spare->unit_price, 2);
              }
            @endphp
            @if($complaintStatus == 'resolved')
              <div class="status-chip" style="background-color: {{ $statusColors['resolved']['bg'] }}; color: {{ $statusColors['resolved']['text'] }}; border-color: {{ $statusColors['resolved']['border'] }}; width: 140px; height: 28px; justify-content: center;">
                <span style="font-size: 11px; font-weight: 700; color: white !important;">Addressed</span>
              </div>
            @elseif($complaintStatus == 'in_progress')
              <div class="status-chip" style="background-color: {{ $statusColors['in_progress']['bg'] }}; color: {{ $statusColors['in_progress']['text'] }}; border-color: {{ $statusColors['in_progress']['border'] }}; position: relative;">
                @php
                  $waitingRawStatus = $approval->waiting_for_authority ?? false;
                  $showDotStatus = ($waitingRawStatus === true || $waitingRawStatus === 1 || $waitingRawStatus === '1' || $waitingRawStatus === 'true');
                @endphp
                @if($showDotStatus)
                <span class="blinking-dot" style="position: absolute; left: 8px; top: 50%; transform: translateY(-50%); width: 6px; height: 6px; background-color: #ffffff; border-radius: 50%; z-index: 10; animation: blink 1s infinite;"></span>
                @endif
                <span class="status-indicator" style="background-color: {{ $statusColors['in_progress']['bg'] }}; border-color: {{ $statusColors['in_progress']['border'] }};"></span>
              <select class="form-select form-select-sm status-select" 
                      data-complaint-id="{{ $complaint->id }}"
                      data-actual-status="{{ $rawStatus }}"
                      data-status-color="in_progress"
                      style="width: 140px; font-size: 11px; font-weight: 700; height: 28px; text-align: center; text-align-last: center;">
                @if(isset($statuses) && $statuses->count() > 0)
                  @foreach($statuses as $statusValue => $statusLabel)
                    <option value="{{ $statusValue }}" {{ $complaintStatus == $statusValue ? 'selected' : '' }}>{{ $statusLabel }}</option>
                  @endforeach
                @else
                  <option value="assigned" {{ $complaintStatus == 'assigned' ? 'selected' : '' }}>Assigned</option>
                  <option value="in_progress" {{ $complaintStatus == 'in_progress' ? 'selected' : '' }}>In-Process</option>
                  <option value="resolved" {{ $complaintStatus == 'resolved' ? 'selected' : '' }}>Addressed</option>
                  <option value="work_priced_performa" {{ $complaintStatus == 'work_priced_performa' ? 'selected' : '' }}>Work Performa Priced</option>
                  <option value="maint_priced_performa" {{ $complaintStatus == 'maint_priced_performa' ? 'selected' : '' }}>Maintenance Performa Priced</option>
                  <option value="product_na" {{ $complaintStatus == 'product_na' ? 'selected' : '' }}>Product N/A</option>
                  <option value="un_authorized" {{ $complaintStatus == 'un_authorized' ? 'selected' : '' }}>Un-Authorized</option>
                  <option value="pertains_to_ge_const_isld" {{ $complaintStatus == 'pertains_to_ge_const_isld' ? 'selected' : '' }}>Pertains to GE(N) Const Isld</option>
                @endif
              </select>
              <i data-feather="chevron-down" style="width: 14px; height: 14px; color: #ffffff !important; position: absolute; right: 8px; top: 50%; transform: translateY(-50%); pointer-events: none; z-index: 10; stroke: #ffffff;"></i>
              </div>
            @elseif($complaintStatus == 'work_performa' || (isset($performaBadge) && strpos($performaBadge ?? '', 'Work') !== false))
              <div class="status-chip" style="background-color: {{ $statusColors['work_performa']['bg'] }}; color: {{ $statusColors['work_performa']['text'] }}; border-color: {{ $statusColors['work_performa']['border'] }};">
                <span class="status-indicator" style="background-color: {{ $statusColors['work_performa']['bg'] }}; border-color: {{ $statusColors['work_performa']['border'] }};"></span>
              <select class="form-select form-select-sm status-select" 
                      data-complaint-id="{{ $complaint->id }}"
                      data-actual-status="{{ $rawStatus }}"
                      data-status-color="work_performa"
                      style="width: 140px; font-size: 11px; font-weight: 700; height: 28px; text-align: center; text-align-last: center;">
                @if(isset($statuses) && $statuses->count() > 0)
                  @foreach($statuses as $statusValue => $statusLabel)
                    <option value="{{ $statusValue }}" {{ $complaintStatus == $statusValue ? 'selected' : '' }}>{{ $statusLabel }}</option>
                  @endforeach
                @else
                  <option value="assigned" {{ $complaintStatus == 'assigned' ? 'selected' : '' }}>Assigned</option>
                  <option value="in_progress" {{ $complaintStatus == 'in_progress' ? 'selected' : '' }}>In-Process</option>
                  <option value="resolved" {{ $complaintStatus == 'resolved' ? 'selected' : '' }}>Addressed</option>
                  <option value="work_priced_performa" {{ $complaintStatus == 'work_priced_performa' ? 'selected' : '' }}>Work Performa Priced</option>
                  <option value="maint_priced_performa" {{ $complaintStatus == 'maint_priced_performa' ? 'selected' : '' }}>Maintenance Performa Priced</option>
                  <option value="product_na" {{ $complaintStatus == 'product_na' ? 'selected' : '' }}>Product N/A</option>
                  <option value="un_authorized" {{ $complaintStatus == 'un_authorized' ? 'selected' : '' }}>Un-Authorized</option>
                  <option value="pertains_to_ge_const_isld" {{ $complaintStatus == 'pertains_to_ge_const_isld' ? 'selected' : '' }}>Pertains to GE(N) Const Isld</option>
                @endif
              </select>
              </div>
            @elseif($complaintStatus == 'maint_performa' || (isset($performaBadge) && (strpos($performaBadge ?? '', 'Maint') !== false || strpos($performaBadge ?? '', 'Maintenance') !== false)))
              <div class="status-chip" style="background-color: {{ $statusColors['maint_performa']['bg'] }}; color: {{ $statusColors['maint_performa']['text'] }}; border-color: {{ $statusColors['maint_performa']['border'] }};">
                <span class="status-indicator" style="background-color: {{ $statusColors['maint_performa']['bg'] }}; border-color: {{ $statusColors['maint_performa']['border'] }};"></span>
              <select class="form-select form-select-sm status-select" 
                      data-complaint-id="{{ $complaint->id }}"
                      data-actual-status="{{ $rawStatus }}"
                      data-status-color="maint_performa"
                      style="width: 140px; font-size: 11px; font-weight: 700; height: 28px; text-align: center; text-align-last: center;">
                @if(isset($statuses) && $statuses->count() > 0)
                  @foreach($statuses as $statusValue => $statusLabel)
                    <option value="{{ $statusValue }}" {{ $complaintStatus == $statusValue ? 'selected' : '' }}>{{ $statusLabel }}</option>
                  @endforeach
                @else
                  <option value="assigned" {{ $complaintStatus == 'assigned' ? 'selected' : '' }}>Assigned</option>
                  <option value="in_progress" {{ $complaintStatus == 'in_progress' ? 'selected' : '' }}>In-Process</option>
                  <option value="resolved" {{ $complaintStatus == 'resolved' ? 'selected' : '' }}>Addressed</option>
                  <option value="work_priced_performa" {{ $complaintStatus == 'work_priced_performa' ? 'selected' : '' }}>Work Performa Priced</option>
                  <option value="maint_priced_performa" {{ $complaintStatus == 'maint_priced_performa' ? 'selected' : '' }}>Maintenance Performa Priced</option>
                  <option value="product_na" {{ $complaintStatus == 'product_na' ? 'selected' : '' }}>Product N/A</option>
                  <option value="un_authorized" {{ $complaintStatus == 'un_authorized' ? 'selected' : '' }}>Un-Authorized</option>
                  <option value="pertains_to_ge_const_isld" {{ $complaintStatus == 'pertains_to_ge_const_isld' ? 'selected' : '' }}>Pertains to GE(N) Const Isld</option>
                @endif
              </select>
              </div>
            @elseif($complaintStatus == 'un_authorized')
              <div class="status-chip" style="background-color: {{ $statusColors['un_authorized']['bg'] }}; color: {{ $statusColors['un_authorized']['text'] }}; border-color: {{ $statusColors['un_authorized']['border'] }};">
                <span class="status-indicator" style="background-color: {{ $statusColors['un_authorized']['bg'] }}; border-color: {{ $statusColors['un_authorized']['border'] }};"></span>
              <select class="form-select form-select-sm status-select" 
                      data-complaint-id="{{ $complaint->id }}"
                      data-actual-status="{{ $rawStatus }}"
                      data-status-color="un_authorized"
                      style="width: 140px; font-size: 11px; font-weight: 700; height: 28px; text-align: center; text-align-last: center;">
                @if(isset($statuses) && $statuses->count() > 0)
                  @foreach($statuses as $statusValue => $statusLabel)
                    <option value="{{ $statusValue }}" {{ $complaintStatus == $statusValue ? 'selected' : '' }}>{{ $statusLabel }}</option>
                  @endforeach
                @else
                  <option value="assigned" {{ $complaintStatus == 'assigned' ? 'selected' : '' }}>Assigned</option>
                  <option value="in_progress" {{ $complaintStatus == 'in_progress' ? 'selected' : '' }}>In-Process</option>
                  <option value="resolved" {{ $complaintStatus == 'resolved' ? 'selected' : '' }}>Addressed</option>
                  <option value="work_priced_performa" {{ $complaintStatus == 'work_priced_performa' ? 'selected' : '' }}>Work Performa Priced</option>
                  <option value="maint_priced_performa" {{ $complaintStatus == 'maint_priced_performa' ? 'selected' : '' }}>Maintenance Performa Priced</option>
                  <option value="product_na" {{ $complaintStatus == 'product_na' ? 'selected' : '' }}>Product N/A</option>
                  <option value="un_authorized" {{ $complaintStatus == 'un_authorized' ? 'selected' : '' }}>Un-Authorized</option>
                  <option value="pertains_to_ge_const_isld" {{ $complaintStatus == 'pertains_to_ge_const_isld' ? 'selected' : '' }}>Pertains to GE(N) Const Isld</option>
                @endif
              </select>
              </div>
            @elseif($complaintStatus == 'pertains_to_ge_const_isld')
              <div class="status-chip" style="background-color: {{ $statusColors['pertains_to_ge_const_isld']['bg'] }}; color: {{ $statusColors['pertains_to_ge_const_isld']['text'] }}; border-color: {{ $statusColors['pertains_to_ge_const_isld']['border'] }};">
                <span class="status-indicator" style="background-color: {{ $statusColors['pertains_to_ge_const_isld']['bg'] }}; border-color: {{ $statusColors['pertains_to_ge_const_isld']['border'] }};"></span>
              <select class="form-select form-select-sm status-select" 
                      data-complaint-id="{{ $complaint->id }}"
                      data-actual-status="{{ $rawStatus }}"
                      data-status-color="pertains_to_ge_const_isld"
                      style="width: 140px; font-size: 11px; font-weight: 700; height: 28px; text-align: center; text-align-last: center;">
                @if(isset($statuses) && $statuses->count() > 0)
                  @foreach($statuses as $statusValue => $statusLabel)
                    <option value="{{ $statusValue }}" {{ $complaintStatus == $statusValue ? 'selected' : '' }}>{{ $statusLabel }}</option>
                  @endforeach
                @else
                  <option value="assigned" {{ $complaintStatus == 'assigned' ? 'selected' : '' }}>Assigned</option>
                  <option value="in_progress" {{ $complaintStatus == 'in_progress' ? 'selected' : '' }}>In-Process</option>
                  <option value="resolved" {{ $complaintStatus == 'resolved' ? 'selected' : '' }}>Addressed</option>
                  <option value="work_priced_performa" {{ $complaintStatus == 'work_priced_performa' ? 'selected' : '' }}>Work Performa Priced</option>
                  <option value="maint_priced_performa" {{ $complaintStatus == 'maint_priced_performa' ? 'selected' : '' }}>Maintenance Performa Priced</option>
                  <option value="product_na" {{ $complaintStatus == 'product_na' ? 'selected' : '' }}>Product N/A</option>
                  <option value="un_authorized" {{ $complaintStatus == 'un_authorized' ? 'selected' : '' }}>Un-Authorized</option>
                  <option value="pertains_to_ge_const_isld" {{ $complaintStatus == 'pertains_to_ge_const_isld' ? 'selected' : '' }}>Pertains to GE(N) Const Isld</option>
                @endif
              </select>
              </div>
            @else
              <div class="status-chip" style="background-color: {{ $statusColors['assigned']['bg'] }}; color: {{ $statusColors['assigned']['text'] }}; border-color: {{ $statusColors['assigned']['border'] }};">
                <span class="status-indicator" style="background-color: {{ $statusColors['assigned']['bg'] }}; border-color: {{ $statusColors['assigned']['border'] }};"></span>
              <select class="form-select form-select-sm status-select" 
                      data-complaint-id="{{ $complaint->id }}"
                      data-actual-status="{{ $rawStatus }}"
                      data-status-color="assigned"
                      style="width: 140px; font-size: 11px; font-weight: 700; height: 28px; text-align: center; text-align-last: center;">
                @if(isset($statuses) && $statuses->count() > 0)
                  @foreach($statuses as $statusValue => $statusLabel)
                    <option value="{{ $statusValue }}" {{ $complaintStatus == $statusValue ? 'selected' : '' }}>{{ $statusLabel }}</option>
                  @endforeach
                @else
                  <option value="assigned" {{ $complaintStatus == 'assigned' ? 'selected' : '' }}>Assigned</option>
                  <option value="in_progress" {{ $complaintStatus == 'in_progress' ? 'selected' : '' }}>In-Process</option>
                  <option value="resolved" {{ $complaintStatus == 'resolved' ? 'selected' : '' }}>Addressed</option>
                  <option value="work_priced_performa" {{ $complaintStatus == 'work_priced_performa' ? 'selected' : '' }}>Work Performa Priced</option>
                  <option value="maint_priced_performa" {{ $complaintStatus == 'maint_priced_performa' ? 'selected' : '' }}>Maintenance Performa Priced</option>
                  <option value="product_na" {{ $complaintStatus == 'product_na' ? 'selected' : '' }}>Product N/A</option>
                  <option value="un_authorized" {{ $complaintStatus == 'un_authorized' ? 'selected' : '' }}>Un-Authorized</option>
                  <option value="pertains_to_ge_const_isld" {{ $complaintStatus == 'pertains_to_ge_const_isld' ? 'selected' : '' }}>Pertains to GE(N) Const Isld</option>
                @endif
              </select>
              </div>
            @endif
          </td>
          <td>
            <div class="btn-group" role="group">
              <button type="button" class="btn btn-outline-success btn-sm" title="View Details" onclick="viewApproval({{ $approval->id }})" style="padding: 3px 8px;">
                <i data-feather="eye" style="width: 16px; height: 16px;"></i>
              </button>
              @if($complaintStatus == 'resolved' || $complaintStatus == 'closed')
                <button type="button" class="btn btn-outline-secondary btn-sm add-stock-btn" title="Stock cannot be issued for addressed complaints" data-approval-id="{{ $approval->id }}" data-category="{{ $category }}" disabled style="padding: 3px 8px; cursor: not-allowed; opacity: 0.6;">
                  <i data-feather="plus-circle" style="width: 16px; height: 16px;"></i>
                </button>
              @elseif(isset($approval->has_issued_stock) && $approval->has_issued_stock)
                <button type="button" class="btn btn-outline-secondary btn-sm add-stock-btn" title="Stock already issued" data-approval-id="{{ $approval->id }}" data-category="{{ $category }}" disabled style="padding: 3px 8px; cursor: not-allowed; opacity: 0.6;">
                  <i data-feather="plus-circle" style="width: 16px; height: 16px;"></i>
                </button>
              @else
                <button type="button" class="btn btn-outline-primary btn-sm add-stock-btn" title="Issue Stock" data-approval-id="{{ $approval->id }}" data-category="{{ $category }}" onclick="openAddStockModal({{ $approval->id }}, '{{ $category }}')" style="padding: 3px 8px; cursor: pointer;">
                  <i data-feather="plus-circle" style="width: 16px; height: 16px;"></i>
                </button>
              @endif
              @if($complaintStatus == 'resolved' || $complaintStatus == 'closed')
                @php
                  $hasFeedback = false;
                  $feedbackId = null;
                  // Safely check if feedback exists without triggering lazy loading errors
                  try {
                    if ($complaint && $complaint->relationLoaded('feedback')) {
                      $feedback = $complaint->getRelation('feedback');
                      if ($feedback && $feedback->id) {
                        $hasFeedback = true;
                        $feedbackId = $feedback->id;
                      }
                    } else {
                      // Use exists() method to check without loading the relationship
                      $hasFeedback = \App\Models\ComplaintFeedback::where('complaint_id', $complaint->id)->exists();
                      if ($hasFeedback) {
                        $feedback = \App\Models\ComplaintFeedback::where('complaint_id', $complaint->id)->first();
                        if ($feedback) {
                          $feedbackId = $feedback->id;
                        }
                      }
                    }
                  } catch (\Exception $e) {
                    $hasFeedback = false;
                    $feedbackId = null;
                  }
                @endphp
                @if($hasFeedback && $feedbackId)
                  <a href="{{ route('admin.feedback.edit', $feedbackId) }}" class="btn btn-success btn-sm" title="Edit Feedback" style="padding: 3px 8px; background-color: #16a34a !important; border-color: #16a34a !important; color: #ffffff !important;">
                    <i data-feather="check-circle" style="width: 16px; height: 16px; color: #ffffff;"></i>
                  </a>
                @else
                  <a href="{{ route('admin.feedback.create', $complaint->id) }}" class="btn btn-outline-warning btn-sm" title="Add Feedback" style="padding: 3px 8px; border-color: #f59e0b !important; color: #f59e0b !important;">
                    <i data-feather="message-square" style="width: 16px; height: 16px; color: #f59e0b;"></i>
                  </a>
                @endif
              @endif
            </div>
          </td>
        </tr>
        @endif
          @empty
        <tr>
          <td colspan="11" class="text-center py-4">
            <i data-feather="check-circle" class="feather-lg mb-2"></i>
            <div>No complaints found</div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <!-- PAGINATION -->
  <div class="d-flex justify-content-center mt-3" id="approvalsPagination">
    <div>
      {{ $approvals->links() }}
    </div>
  </div>
</div>

<!-- Issue Stock Modal -->
<div class="modal fade" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addStockModalLabel">Issue Stock</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" tabindex="0"></button>
      </div>
      <div class="modal-body" id="addStockModalBody">
        <!-- Stock items will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" tabindex="0">Close</button>
        <button type="button" class="btn btn-success" id="submitAddStockBtn" onclick="if(window.submitIssueStock) { window.submitIssueStock(); } else { alert('Submit function not available. Please refresh the page.'); }" style="display: none;" tabindex="0">
          <i data-feather="check-circle"></i> Issue Stock
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Complaint Modal -->
<div class="modal fade" id="complaintModal" tabindex="-1" aria-labelledby="complaintModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content card-glass" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border: 1px solid rgba(59, 130, 246, 0.3);">
            <div class="modal-header" style="border-bottom: 2px solid rgba(59, 130, 246, 0.2);">
                <h5 class="modal-title text-white" id="complaintModalLabel">
                    <i data-feather="alert-triangle" class="me-2"></i>Complaint Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="closeComplaintModal()" style="background-color: rgba(255, 255, 255, 0.2); border-radius: 4px; padding: 0.5rem !important; opacity: 1 !important; filter: invert(1); background-size: 1.5em;"></button>
            </div>
            <div class="modal-body" id="complaintModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content card-glass" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border: 1px solid rgba(59, 130, 246, 0.3);">
            <div class="modal-header" style="border-bottom: 2px solid rgba(59, 130, 246, 0.2);">
                <h5 class="modal-title text-white" id="approvalModalLabel">
                    <i data-feather="file-check" class="me-2"></i>Approval Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="closeApprovalModal()" style="background-color: rgba(255, 255, 255, 0.2); border-radius: 4px; padding: 0.5rem !important; opacity: 1 !important; filter: invert(1); background-size: 1.5em;"></button>
            </div>
            <div class="modal-body" id="approvalModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
  body.modal-open-blur {
      overflow: hidden;
  }
  body.modal-open-blur::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(5px);
      -webkit-backdrop-filter: blur(5px);
      z-index: 1040;
      pointer-events: none;
  }
  body.modal-open-blur .modal-backdrop,
  #approvalModal.modal.show ~ .modal-backdrop,
  #approvalModal.modal.show + .modal-backdrop,
  .modal-backdrop.show,
  .modal-backdrop {
      display: none !important;
      visibility: hidden !important;
      opacity: 0 !important;
      background-color: transparent !important;
      backdrop-filter: none !important;
      -webkit-backdrop-filter: none !important;
      pointer-events: none !important;
  }
  
  /* Ensure modal content is above blur layer */
  #approvalModal {
      z-index: 1055 !important;
  }
  
  #approvalModal .modal-dialog {
      z-index: 1055 !important;
      position: relative;
  }
  
  #approvalModal .modal-content {
      max-height: 90vh;
      overflow-y: auto;
      z-index: 1055 !important;
      position: relative;
  }
  
  #approvalModal .modal-body {
      padding: 1.5rem;
  }
  
  #approvalModal .btn-close {
      background-color: rgba(255, 255, 255, 0.2);
      border-radius: 4px;
      padding: 0.5rem !important;
      opacity: 1 !important;
  }
  
  #approvalModal .btn-close:hover {
      background-color: rgba(255, 255, 255, 0.3);
  }
  
  /* Toast Notification Animations */
  @keyframes slideInRight {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
  
  @keyframes fadeOut {
    from {
      opacity: 1;
      transform: translateX(0);
    }
    to {
      opacity: 0;
      transform: translateX(100%);
    }
  }
  
  @keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
  }
  
  @keyframes blink {
    0%, 100% { 
      opacity: 1; 
    }
    50% { 
      opacity: 0.3; 
    }
  }
  
  .blinking-dot {
    animation: blink 1s infinite !important;
    -webkit-animation: blink 1s infinite !important;
    -moz-animation: blink 1s infinite !important;
    -o-animation: blink 1s infinite !important;
  }
  
  .type-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  .type-spare { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
  
  .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  .status-pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
  .status-approved { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
  
  /* Performa badge - ensure white text for all badges including Product N/A (only badges, not heading) */
  .table td .performa-badge {
    color: white !important;
  }
  
  /* Table text styling for all themes */
  .table td {
    color: #1e293b !important;
  }
  
  .theme-dark .table td,
  .theme-night .table td {
    color: #f1f5f9 !important;
  }
  
  .table .text-muted {
    color: #64748b !important;
  }
  
  .theme-dark .table .text-muted,
  .theme-night .table .text-muted {
    color: #94a3b8 !important;
  }
  
  /* Performa Required column - white text (only values, not heading) */
  .table td:nth-child(9) {
    color: white !important;
  }
  
  .table td:nth-child(9) .performa-badge {
    color: white !important;
  }
  
  /* Compact status select box */
  .status-select {
    width: 140px !important;
    padding: 2px 6px !important;
    font-size: 11px !important;
    height: 28px !important;
    line-height: 1.4 !important;
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: 4px;
    border: 1px solid rgba(0, 0, 0, 0.2) !important;
    text-align: center !important;
    text-align-last: center !important;
    /* Make native arrow consistent */
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    padding-right: 22px !important; /* room for arrow */
    background-repeat: no-repeat !important;
    background-position: right 6px center !important;
    background-size: 12px 12px !important;
    /* SVG arrow uses white color */
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>") !important;
  }
  
  .status-select:hover {
    opacity: 0.9;
    transform: scale(1.02);
  }
  
  .status-select:focus {
    outline: 2px solid rgba(59, 130, 246, 0.5);
    outline-offset: 2px;
  }
  
  .status-select option {
    padding: 10px 12px;
    font-size: 12px;
    font-weight: 500;
    line-height: 1.6;
    background-color: #ffffff;
    color: #1f2937;
    min-height: 36px;
  }
  
  .status-select option:disabled {
    color: #9ca3af;
    font-style: italic;
  }

  /* Live color indicator next to status select (works across browsers) */
  .status-indicator {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 6px;
    border: 1px solid rgba(0,0,0,0.25);
    vertical-align: middle;
  }

  /* Colored chip that wraps the whole status control */
  .status-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid rgba(0, 0, 0, 0.2) !important;
    height: 28px;
    width: 140px;
    justify-content: center;
    color: white !important;
  }
  .status-chip .status-select {
    background: transparent !important;
    color: white !important;
    border: none !important;
    padding-left: 0 !important;
    /* keep right padding for arrow */
    padding-right: 22px !important;
    height: 20px !important;
    line-height: 20px !important;
  }
  .status-chip span {
    font-size: 11px;
    font-weight: 700;
    color: white !important;
  }
  
  /* Always show number input spinner arrows (not only on hover) */
  input[type=number]::-webkit-inner-spin-button,
  input[type=number]::-webkit-outer-spin-button {
    opacity: 1;
    -webkit-appearance: inner-spin-button;
    appearance: auto;
    margin: 0;
  }

  /* Ensure Firefox shows the spinner controls */
  input[type=number] {
    -moz-appearance: number-input;
    appearance: number-input;
  }

  /* Ensure the spinner is visible on small control too */
  #manualRequestQty::-webkit-inner-spin-button,
  #manualRequestQty::-webkit-outer-spin-button {
    opacity: 1 !important;
  }
  
  /* Ensure Add Stock button is clickable */
  .add-stock-btn {
    pointer-events: auto !important;
    z-index: 10 !important;
    position: relative !important;
    cursor: pointer !important;
    opacity: 1 !important;
  }
  
  .add-stock-btn:hover {
    opacity: 0.8 !important;
  }
  
  .add-stock-btn:active {
    opacity: 0.6 !important;
  }
  
  .btn-group .add-stock-btn {
    margin-left: 4px;
  }
  
  /* Ensure Submit button in Add Stock Modal is clickable */
  #submitAddStockBtn {
    pointer-events: auto !important;
    z-index: 1050 !important;
    position: relative !important;
    cursor: pointer !important;
    opacity: 1 !important;
    display: inline-block !important;
  }
  
  #submitAddStockBtn:hover {
    opacity: 0.9 !important;
  }
  
  #submitAddStockBtn:active {
    opacity: 0.7 !important;
  }
  
  #submitAddStockBtn:disabled {
    opacity: 0.6 !important;
    cursor: not-allowed !important;
  }
  
  /* Ensure modal footer buttons are clickable */
  #addStockModal .modal-footer button {
    pointer-events: auto !important;
    z-index: 1050 !important;
    position: relative !important;
  }
  
  /* Add Stock Modal Table Styling */
  #addStockModal .table {
    margin-bottom: 0;
  }
  
  #addStockModal .table thead th {
    background-color: #0d6efd;
    color: white;
    border: 1px solid #0d6efd;
    padding: 12px 15px;
    text-align: center;
    vertical-align: middle;
  }
  
  #addStockModal .table tbody td {
    padding: 12px 15px;
    border: 1px solid #dee2e6;
    vertical-align: middle;
  }
  
  #addStockModal .table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
  }
  
  #addStockModal .table tbody tr:hover {
    background-color: #e9ecef;
  }
  
  #addStockModal .table-responsive {
    border-radius: 8px;
    overflow: hidden;
  }
  
  #addStockModal .total-quantity-input {
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 6px 10px;
    font-size: 14px;
  }
  
  #addStockModal .total-quantity-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    outline: none;
  }
  
</style>
@endpush

@push('scripts')
<script>
  feather.replace();

  // Global variables
  let currentApprovalId = null;
  let isProcessing = false;
  let currentComplaintId = null;

  // Complaint Functions
  function viewComplaint(complaintId) {
    if (!complaintId) {
      alert('Invalid complaint ID');
      return;
    }
    
    currentComplaintId = complaintId;
    
    const modalElement = document.getElementById('complaintModal');
    const modalBody = document.getElementById('complaintModalBody');
    
    // Show loading state
    modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    // Add blur effect to background first
    document.body.classList.add('modal-open-blur');
    
    // Show modal WITHOUT backdrop so we can see the blurred background
    const modal = new bootstrap.Modal(modalElement, {
      backdrop: false, // Disable Bootstrap backdrop completely
      keyboard: true,
      focus: true
    });
    modal.show();
    
    // Ensure any backdrop that might be created is removed
    const removeBackdrop = () => {
      const backdrops = document.querySelectorAll('.modal-backdrop');
      backdrops.forEach(backdrop => {
        backdrop.remove(); // Remove from DOM
      });
    };
    
    // Use MutationObserver to catch and remove any backdrop creation
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
          if (node.nodeType === 1 && node.classList && node.classList.contains('modal-backdrop')) {
            node.remove(); // Remove immediately if created
          }
        });
      });
      removeBackdrop();
    });
    
    observer.observe(document.body, {
      childList: true,
      subtree: true
    });
    
    // Remove any existing backdrops
    removeBackdrop();
    setTimeout(removeBackdrop, 10);
    setTimeout(removeBackdrop, 50);
    setTimeout(removeBackdrop, 100);
    
    // Clean up observer when modal is hidden
    modalElement.addEventListener('hidden.bs.modal', function() {
      observer.disconnect();
      removeBackdrop();
    }, { once: true });
    
    // Load complaint details via AJAX - force HTML response
    fetch(`/admin/complaints/${complaintId}?format=html`, {
      method: 'GET',
      headers: {
        'Accept': 'text/html',
      },
      credentials: 'same-origin'
    })
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      // Check if response is JSON
      const contentType = response.headers.get('content-type');
      if (contentType && contentType.includes('application/json')) {
        return response.json().then(data => {
          throw new Error('Received JSON instead of HTML. Please check the route.');
        });
      }
      return response.text();
    })
    .then(html => {
      // Check if response is actually JSON (starts with {)
      if (html.trim().startsWith('{')) {
        console.error('Received JSON instead of HTML');
        modalBody.innerHTML = '<div class="text-center py-5 text-danger">Error: Server returned JSON instead of HTML. Please check the route configuration.</div>';
        return;
      }
      
      // Extract the content from the show page
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      
      // Remove all share-modal.js scripts BEFORE processing content
      const allScripts = doc.querySelectorAll('script');
      allScripts.forEach(script => {
        if (script.src && script.src.includes('share-modal')) {
          script.remove();
        }
        if (script.textContent && script.textContent.includes('share-modal')) {
          script.remove();
        }
      });
      
      // Get the content section - try multiple selectors
      let contentSection = doc.querySelector('section.content');
      if (!contentSection) {
        contentSection = doc.querySelector('.content');
      }
      if (!contentSection) {
        // Try to find the main content area
        const mainContent = doc.querySelector('main') || doc.querySelector('[role="main"]');
        if (mainContent) {
          contentSection = mainContent;
        } else {
          contentSection = doc.body;
        }
      }
      
      // Extract the complaint details sections
      let complaintContent = '';
      
      // Get all rows that contain complaint information (skip page header)
      const allRows = contentSection.querySelectorAll('.row');
      const seenRows = new Set();
      
      allRows.forEach(row => {
        // Skip rows that are in page headers
        const isInHeader = row.closest('.mb-4') && row.closest('.mb-4').querySelector('h2');
        
        // Check if this row contains card-glass elements
        const hasCardGlass = row.querySelector('.card-glass');
        
        if (!isInHeader && hasCardGlass) {
          const rowHTML = row.outerHTML;
          // Use a simple hash to avoid duplicates
          const rowId = rowHTML.substring(0, 200);
          if (!seenRows.has(rowId)) {
            seenRows.add(rowId);
            complaintContent += rowHTML;
          }
        }
      });
      
      // If no rows found, fallback to extracting individual cards
      if (!complaintContent) {
        const allCards = contentSection.querySelectorAll('.card-glass');
        const seenCards = new Set();
        
        allCards.forEach(card => {
          // Skip cards that are in page headers
          const parentRow = card.closest('.row');
          const isInHeader = parentRow && parentRow.closest('.mb-4') && parentRow.closest('.mb-4').querySelector('h2');
          
          // Skip duplicate "Complainant Comments" sections
          const cardText = card.textContent || '';
          const isCommentsSection = cardText.includes('Complainant Comments') && !card.closest('.card-body');
          
          if (!isInHeader && !isCommentsSection) {
            const cardHTML = card.outerHTML;
            const cardId = cardHTML.substring(0, 300);
            if (!seenCards.has(cardId)) {
              seenCards.add(cardId);
              complaintContent += '<div class="mb-3">' + cardHTML + '</div>';
            }
          }
        });
      }
      
      // Remove duplicate "Complainant Comments" sections
      if (complaintContent) {
        const tempDivForComments = document.createElement('div');
        tempDivForComments.innerHTML = complaintContent;
        const commentSections = tempDivForComments.querySelectorAll('h6, h5, h4');
        let foundCommentsSection = false;
        commentSections.forEach(heading => {
          if (heading.textContent && heading.textContent.includes('Complainant Comments')) {
            if (foundCommentsSection) {
              // Remove duplicate - find the parent row and remove it
              const parentRow = heading.closest('.row');
              if (parentRow) {
                parentRow.remove();
              }
            } else {
              foundCommentsSection = true;
            }
          }
        });
        complaintContent = tempDivForComments.innerHTML;
      }
      
      if (complaintContent) {
        // Remove any share-modal.js scripts from the content before inserting
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = complaintContent;
        const scriptsInContent = tempDiv.querySelectorAll('script');
        scriptsInContent.forEach(script => {
          if (script.src && script.src.includes('share-modal')) {
            script.remove();
          }
          if (script.textContent && script.textContent.includes('share-modal')) {
            script.remove();
          }
        });
        complaintContent = tempDiv.innerHTML;
        
        modalBody.innerHTML = complaintContent;
        // Replace feather icons after content is loaded
        setTimeout(() => {
          feather.replace();
          // Double-check and remove any share-modal.js scripts that might have been added
          const shareModalScripts = document.querySelectorAll('script[src*="share-modal"]');
          shareModalScripts.forEach(script => {
            try {
              script.remove();
            } catch(e) {
              // Ignore errors
            }
          });
        }, 100);
      } else {
        console.error('Could not find complaint content in response');
        console.log('Content section:', contentSection);
        console.log('Found cards:', contentSection.querySelectorAll('.card-glass').length);
        modalBody.innerHTML = '<div class="text-center py-5 text-danger">Error: Could not load complaint details. Please refresh and try again.</div>';
      }
    })
    .catch(error => {
      console.error('Error loading complaint:', error);
      modalBody.innerHTML = '<div class="text-center py-5 text-danger">Error loading complaint details: ' + error.message + '. Please try again.</div>';
    });
    
    // Replace feather icons when modal is shown
    modalElement.addEventListener('shown.bs.modal', function() {
      feather.replace();
    });
    
    // Remove blur when modal is hidden
    modalElement.addEventListener('hidden.bs.modal', function() {
      document.body.classList.remove('modal-open-blur');
      feather.replace();
    }, { once: true });
  }
  
  // Function to close complaint modal and remove blur
  function closeComplaintModal() {
    const modalElement = document.getElementById('complaintModal');
    if (modalElement) {
      const modal = bootstrap.Modal.getInstance(modalElement);
      if (modal) {
        modal.hide();
      }
    }
    document.body.classList.remove('modal-open-blur');
  }

  // Approval Functions
  function viewApproval(approvalId) {
    if (!approvalId) {
      alert('Invalid approval ID');
      return;
    }
    
    currentApprovalId = approvalId;
    
    const modalElement = document.getElementById('approvalModal');
    const modalBody = document.getElementById('approvalModalBody');
    
    // Show loading state
    modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    // Add blur effect to background first
    document.body.classList.add('modal-open-blur');
    
    // Show modal WITHOUT backdrop so we can see the blurred background
    const modal = new bootstrap.Modal(modalElement, {
      backdrop: false, // Disable Bootstrap backdrop completely
      keyboard: true,
      focus: true
    });
    modal.show();
    
    // Ensure any backdrop that might be created is removed
    const removeBackdrop = () => {
      const backdrops = document.querySelectorAll('.modal-backdrop');
      backdrops.forEach(backdrop => {
        backdrop.remove(); // Remove from DOM
      });
    };
    
    // Use MutationObserver to catch and remove any backdrop creation
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
          if (node.nodeType === 1 && node.classList && node.classList.contains('modal-backdrop')) {
            node.remove(); // Remove immediately if created
          }
        });
      });
      removeBackdrop();
    });
    
    observer.observe(document.body, {
      childList: true,
      subtree: true
    });
    
    // Remove any existing backdrops
    removeBackdrop();
    setTimeout(removeBackdrop, 10);
    setTimeout(removeBackdrop, 50);
    setTimeout(removeBackdrop, 100);
    
    // Clean up observer when modal is hidden
    modalElement.addEventListener('hidden.bs.modal', function() {
      observer.disconnect();
      removeBackdrop();
    }, { once: true });
    
    // Load approval details via AJAX - force HTML response
    fetch(`/admin/approvals/${approvalId}?format=html`, {
      method: 'GET',
      headers: {
        'Accept': 'text/html',
      },
      credentials: 'same-origin'
    })
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      // Check if response is JSON
      const contentType = response.headers.get('content-type');
      if (contentType && contentType.includes('application/json')) {
        return response.json().then(data => {
          throw new Error('Received JSON instead of HTML. Please check the route.');
        });
      }
      return response.text();
    })
    .then(html => {
      // Check if response is actually JSON (starts with {)
      if (html.trim().startsWith('{')) {
        console.error('Received JSON instead of HTML');
        modalBody.innerHTML = '<div class="text-center py-5 text-danger">Error: Server returned JSON instead of HTML. Please check the route configuration.</div>';
        return;
      }
      
      // Extract the content from the show page
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      
      // Get the content section - try multiple selectors
      let contentSection = doc.querySelector('section.content');
      if (!contentSection) {
        contentSection = doc.querySelector('.content');
      }
      if (!contentSection) {
        // Try to find the main content area
        const mainContent = doc.querySelector('main') || doc.querySelector('[role="main"]');
        if (mainContent) {
          contentSection = mainContent;
        } else {
          contentSection = doc.body;
        }
      }
      
      // Extract the approval details sections
      let approvalContent = '';
      
      // Get all rows that contain approval information (skip page header)
      const allRows = contentSection.querySelectorAll('.row');
      const seenRows = new Set();
      
      allRows.forEach(row => {
        // Skip rows that are in page headers
        const isInHeader = row.closest('.mb-4') && row.closest('.mb-4').querySelector('h2');
        
        // Check if this row contains card-glass elements
        const hasCardGlass = row.querySelector('.card-glass');
        
        if (!isInHeader && hasCardGlass) {
          const rowHTML = row.outerHTML;
          // Use a simple hash to avoid duplicates
          const rowId = rowHTML.substring(0, 200);
          if (!seenRows.has(rowId)) {
            seenRows.add(rowId);
            approvalContent += rowHTML;
          }
        }
      });
      
      // Also extract standalone card-glass elements (like Requested Items section)
      const allCards = contentSection.querySelectorAll('.card-glass');
      const seenCards = new Set();
      
      allCards.forEach(card => {
        // Skip cards that are in page headers
        const parentRow = card.closest('.row');
        const isInHeader = parentRow && parentRow.closest('.mb-4') && parentRow.closest('.mb-4').querySelector('h2');
        
        // Skip if already added from rows
        const cardHTML = card.outerHTML;
        const cardId = cardHTML.substring(0, 300);
        
        if (!isInHeader && !seenCards.has(cardId) && !approvalContent.includes(cardHTML.substring(0, 100))) {
          seenCards.add(cardId);
          // Check if it's already in a row that was added
          const isInAddedRow = parentRow && approvalContent.includes(parentRow.outerHTML.substring(0, 100));
          if (!isInAddedRow) {
            approvalContent += '<div class="mb-3">' + cardHTML + '</div>';
          }
        }
      });
      
      if (approvalContent) {
        modalBody.innerHTML = approvalContent;
        // Replace feather icons after content is loaded
        setTimeout(() => {
          feather.replace();
        }, 100);
      } else {
        console.error('Could not find approval content in response');
        console.log('Content section:', contentSection);
        console.log('Found cards:', contentSection.querySelectorAll('.card-glass').length);
        modalBody.innerHTML = '<div class="text-center py-5 text-danger">Error: Could not load approval details. Please refresh and try again.</div>';
      }
    })
    .catch(error => {
      console.error('Error loading approval:', error);
      modalBody.innerHTML = '<div class="text-center py-5 text-danger">Error loading approval details: ' + error.message + '. Please try again.</div>';
    });
    
    // Replace feather icons when modal is shown
    modalElement.addEventListener('shown.bs.modal', function() {
      feather.replace();
    });
    
    // Remove blur when modal is hidden
    modalElement.addEventListener('hidden.bs.modal', function() {
      document.body.classList.remove('modal-open-blur');
      feather.replace();
    }, { once: true });
  }
  
  function closeApprovalModal() {
    const modalElement = document.getElementById('approvalModal');
    if (modalElement) {
      const modal = bootstrap.Modal.getInstance(modalElement);
      if (modal) {
        modal.hide();
      }
    }
    document.body.classList.remove('modal-open-blur');
  }
  // approveRequest and rejectRequest functions removed as per user request

  // Utility Functions
  function refreshPage() {
    console.log('Refreshing page...');
    location.reload();
  }

  // Debounced search input handler - auto filter on typing (instant response)
  let approvalsSearchTimeout = null;
  function handleApprovalsSearchInput(e) {
    if (e) e.preventDefault();
    if (e) e.stopPropagation();
    
    // Clear existing timeout
    if (approvalsSearchTimeout) clearTimeout(approvalsSearchTimeout);
    
    // Set new timeout - auto search after 200ms of no typing (faster response)
    approvalsSearchTimeout = setTimeout(() => {
      console.log('Auto-search triggered');
      loadApprovals();
    }, 200);
  }

  // Reset filters function
  function resetApprovalsFilters() {
    const form = document.getElementById('approvalsFiltersForm');
    if (!form) return;
    
    // Clear all form inputs
    form.querySelectorAll('input[type="text"], input[type="date"], select').forEach(input => {
      if (input.type === 'select-one') {
        input.selectedIndex = 0;
      } else {
        input.value = '';
      }
    });
    
    // Reset URL to base route
    window.location.href = '{{ route('admin.approvals.index') }}';
  }

  // Auto-submit for select filters - immediate filter on change
  function submitApprovalsFilters(e) {
    if (e) e.preventDefault();
    if (e) e.stopPropagation();
    
    const form = document.getElementById('approvalsFiltersForm');
    if (!form) {
      console.error('Filter form not found');
      return;
    }
    
    // Cancel any pending search timeout
    if (approvalsSearchTimeout) {
      clearTimeout(approvalsSearchTimeout);
      approvalsSearchTimeout = null;
    }
    
    // Immediately load approvals with current filter values (no delay)
    console.log('Filter change triggered');
    loadApprovals();
  }
  
  // Ensure functions are globally available
  window.handleApprovalsSearchInput = handleApprovalsSearchInput;
  window.submitApprovalsFilters = submitApprovalsFilters;
  window.loadApprovals = loadApprovals;
  
  // Helper function to escape HTML
  function escapeHtml(text) {
    if (!text) return '';
    const map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
  }

  // Helper function to render item row
  function renderItemRow(item) {
    const canDelete = !item.isExisting;
    return `
      <tr data-item-id="${item.itemId}" data-spare-id="${item.spareId}" data-is-existing="${item.isExisting}">
        <td style="vertical-align: middle; font-weight: 500; padding: 12px;">${escapeHtml(item.productName)}</td>
        <td style="vertical-align: middle; text-align: center; font-weight: 500; padding: 12px;">${escapeHtml(item.category)}</td>
        <td style="vertical-align: middle; text-align: center; font-weight: 500; padding: 12px;">${item.requestedQty}</td>
        <td style="vertical-align: middle; text-align: center; font-weight: 500; padding: 12px;">
          <span class="badge ${item.availableStock > 0 ? 'bg-success' : 'bg-danger'}" style="font-size: 12px;">${item.availableStock}</span>
        </td>
        <td style="vertical-align: middle; text-align: center; padding: 12px;">
          <input type="number" 
                 class="form-control form-control-sm issue-quantity-input" 
                 name="items[${item.itemId}][issue_quantity]" 
                 value="${item.issueQty}" 
                 min="0" 
                 max="${item.availableStock}"
                 data-spare-id="${item.spareId}"
                 data-item-id="${item.itemId}"
                 data-product-name="${escapeHtml(item.productName)}"
                 data-available-stock="${item.availableStock}"
                 style="width: 120px; text-align: center; margin: 0 auto; display: block;">
        </td>
        <td style="vertical-align: middle; text-align: center; padding: 12px;">
          ${canDelete ? `<button type="button" class="btn btn-danger btn-sm remove-item-btn" data-item-id="${item.itemId}" style="padding: 3px 8px;" title="Remove">
            <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
          </button>` : '<span class="text-muted">-</span>'}
        </td>
      </tr>
    `;
  }

  // Load categories for modal dropdown
  function loadCategoriesForModal(selectedCategory = null) {
    console.log('Loading categories for modal...', selectedCategory);
    const categorySelect = document.getElementById('manualCategory');
    
    if (!categorySelect) {
      console.error('Category select element not found!');
      // Retry after a short delay
      setTimeout(() => {
        loadCategoriesForModal(selectedCategory);
      }, 200);
      return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    fetch('/admin/spares/get-categories', {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      credentials: 'same-origin'
    })
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then(data => {
      console.log('Categories response:', data);
      if (data.success && data.categories && Array.isArray(data.categories)) {
        categorySelect.innerHTML = '<option value="">All Products</option>';
        data.categories.forEach(cat => {
          const option = document.createElement('option');
          option.value = cat;
          option.textContent = cat;
          if (selectedCategory && cat.toLowerCase() === selectedCategory.toLowerCase()) {
            option.selected = true;
          }
          categorySelect.appendChild(option);
        });
        
        // Auto-select complaint category if provided
        if (selectedCategory) {
          categorySelect.value = selectedCategory;
          categorySelect.disabled = true; // Disable category dropdown
        }
        
        console.log(`✅ Loaded ${data.categories.length} categories`);
      } else {
        console.warn('⚠️ No categories found or invalid response:', data);
        categorySelect.innerHTML = '<option value="">All Products</option>';
      }
    })
    .catch(error => {
      console.error('❌ Error loading categories:', error);
      categorySelect.innerHTML = '<option value="">Error loading categories</option>';
    });
  }

  // Load products by category (or all products if category is empty)
  function loadProductsByCategory(category, sector = null, city = null) {
    const productSelect = document.getElementById('manualProduct');
    const availableStockInput = document.getElementById('manualAvailableStock');
    
    if (!productSelect) return;
    
    // If no category, show all products
    if (!category) {
      category = ''; // Empty category will fetch all products
    }

    productSelect.innerHTML = '<option value="">Loading...</option>';
    productSelect.disabled = true;
    availableStockInput.value = '';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Build URL with category, sector, and city parameters
    const params = new URLSearchParams();
    if (category) {
      params.append('category', category);
    }
    if (sector) {
      params.append('sector', sector);
    }
    if (city) {
      params.append('city', city);
    }
    
    const url = `/admin/spares/get-products-by-category?${params.toString()}`;
    
    fetch(url, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
      if (data.success && data.products && data.products.length > 0) {
        productSelect.innerHTML = '<option value="">Select Product</option>';
        data.products.forEach(product => {
          const option = document.createElement('option');
          option.value = product.id;
          option.textContent = product.item_name;
          option.setAttribute('data-stock', product.stock_quantity || 0);
          option.setAttribute('data-category', product.category || '');
          productSelect.appendChild(option);
        });
        productSelect.disabled = false;
      } else {
        productSelect.innerHTML = '<option value="">No products found</option>';
        productSelect.disabled = false; // Enable even if no products so user can try again
      }
    })
    .catch(error => {
      console.error('Error loading products:', error);
      productSelect.innerHTML = '<option value="">Error loading products</option>';
      productSelect.disabled = false;
    });
  }

  // Add item to table
  function addManualItemToTable() {
    const categorySelect = document.getElementById('manualCategory');
    const productSelect = document.getElementById('manualProduct');
    const availableStockInput = document.getElementById('manualAvailableStock');
    const requestQtyInput = document.getElementById('manualRequestQty');
    
    // Category select is optional, but other fields are required
    if (!productSelect || !availableStockInput || !requestQtyInput) return;
    
    const category = categorySelect ? categorySelect.value || '' : '';
    const productId = productSelect.value;
    const productName = productSelect.options[productSelect.selectedIndex]?.textContent || 'N/A';
    const productCategory = productSelect.options[productSelect.selectedIndex]?.getAttribute('data-category') || category || '';
    const availableStock = parseInt(availableStockInput.value) || 0;
    const requestQty = parseInt(requestQtyInput.value) || 0;

    if (!productId) {
      alert('Please select a product');
      return;
    }

    if (requestQty <= 0) {
      alert('Please enter a valid request quantity');
      requestQtyInput.focus();
      return;
    }

    if (requestQty > availableStock) {
      alert(`Request quantity (${requestQty}) cannot exceed available stock (${availableStock})`);
      requestQtyInput.focus();
      return;
    }

    // Check if product already exists in manual items
    const existingItem = window.manualItems?.find(item => item.spare_id == productId);
    if (existingItem) {
      alert('This product is already added. Please remove it first or update the quantity.');
      return;
    }

    // Add to manual items array
    if (!window.manualItems) {
      window.manualItems = [];
    }

    const tempId = `manual_${Date.now()}`;
    const newItem = {
      tempId: tempId,
      spare_id: parseInt(productId),
      product_name: productName,
      category: productCategory, // Use product's actual category from data attribute
      requested_qty: requestQty,
      available_stock: availableStock,
      isExisting: false
    };

    window.manualItems.push(newItem);

    // Reset form
    if (categorySelect) {
      categorySelect.value = '';
    }
    // Don't reset product select - keep it enabled so user can add more items
    // productSelect.innerHTML = '<option value="">Select Category First</option>';
    // productSelect.disabled = true;
    availableStockInput.value = '';
    requestQtyInput.value = '';

    // Show success message
    console.log('Item added:', newItem);
    console.log('Total items:', window.manualItems.length);

    // Show submit button
    const submitBtn = document.getElementById('submitAddStockBtn');
    if (submitBtn) {
      submitBtn.style.display = 'inline-block';
    }
  }

  // Setup manual form event listeners
  function setupManualFormListeners() {
    const categorySelect = document.getElementById('manualCategory');
    const productSelect = document.getElementById('manualProduct');
    const availableStockInput = document.getElementById('manualAvailableStock');
    const requestQtyInput = document.getElementById('manualRequestQty');
    const authorityYes = document.getElementById('authorityYes');
    const authorityNo = document.getElementById('authorityNo');
    const performaTypeCol = document.getElementById('performaTypeCol');
    const authorityNoCol = document.getElementById('authorityNoCol');
    const performaType = document.getElementById('performaType');
    const authorityNumber = document.getElementById('authorityNumber');
    
    // Do not require categorySelect since it may be removed; only require the fields we use
    if (!productSelect || !availableStockInput || !requestQtyInput) return;

    // Category change (only if category is not disabled)
    if (categorySelect && !categorySelect.disabled) {
      categorySelect.addEventListener('change', function() {
        const category = this.value;
        loadProductsByCategory(category);
        availableStockInput.value = '';
        requestQtyInput.value = '';
      });
    }

    // Product change
    productSelect.addEventListener('change', function() {
      const selectedOption = this.options[this.selectedIndex];
      const stock = parseInt(selectedOption?.getAttribute('data-stock')) || 0;
      availableStockInput.value = stock;
      
      // Check if authority is required first
      const isAuthorityRequired = authorityYes && authorityYes.checked;
      const hasAuthNo = isAuthorityRequired && authorityNumber && authorityNumber.value && authorityNumber.value.trim().length > 0;
      
      // If authority required and no authority number, lock the field
      if (isAuthorityRequired && !hasAuthNo) {
        requestQtyInput.disabled = true;
        requestQtyInput.readOnly = true;
        requestQtyInput.value = '';
        requestQtyInput.placeholder = 'Enter Authority No.';
        updateSubmitButtonState();
        return; // Don't set default value or enable field
      }
      
      // If not locked, set default value and enable field
      requestQtyInput.disabled = false;
      requestQtyInput.readOnly = false;
      requestQtyInput.placeholder = 'Enter quantity';
      
      // Default request quantity to 1 when a product is selected and stock is available
      if (stock > 0) {
        if (!requestQtyInput.value || parseInt(requestQtyInput.value) < 1) {
          requestQtyInput.value = 1;
        }
        requestQtyInput.min = 1;
        requestQtyInput.max = stock;
      } else {
        requestQtyInput.value = '';
      }
      
      updateSubmitButtonState();
    });

    // Request quantity input - enable submit button when valid
    requestQtyInput.addEventListener('input', function() {
      updateSubmitButtonState();
    });
    
    requestQtyInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        if (!requestQtyInput.disabled && productSelect.value && requestQtyInput.value) {
          const submitBtn = document.getElementById('submitAddStockBtn');
          if (submitBtn && !submitBtn.disabled) {
            window.submitIssueStock();
          }
        }
      }
    });
    
    // Function to update submit button state
    function updateSubmitButtonState() {
      const submitBtn = document.getElementById('submitAddStockBtn');
      if (!submitBtn) return;
      
      const hasProduct = productSelect && productSelect.value;
      const hasQty = requestQtyInput && !requestQtyInput.disabled && parseInt(requestQtyInput.value) > 0;
      const stock = parseInt(availableStockInput.value) || 0;
      const qty = parseInt(requestQtyInput.value) || 0;
      const isValid = hasProduct && hasQty && qty <= stock && qty > 0;
      
      submitBtn.disabled = !isValid;
    }

    // Performa Required toggle handlers (show tabs and authority no.)
    if (authorityYes && authorityNo && performaTypeCol && authorityNoCol) {
      const updateAuthorityVisibility = () => {
        const required = authorityYes.checked;
        performaTypeCol.classList.toggle('d-none', !required);
        authorityNoCol.classList.toggle('d-none', !required);
        
        // Make fields required only when authority is required
        if (performaType) performaType.required = required;
        if (authorityNumber) authorityNumber.required = required;
        
        // Lock/unlock request quantity based on performa requirement
        if (requestQtyInput) {
          if (required) {
            const hasAuthNo = authorityNumber && authorityNumber.value && authorityNumber.value.trim().length > 0;
            requestQtyInput.disabled = !hasAuthNo;
            requestQtyInput.readOnly = !hasAuthNo;
            requestQtyInput.placeholder = hasAuthNo ? 'Enter quantity' : 'Enter Authority No.';
            if (!hasAuthNo) {
              requestQtyInput.value = '';
            }
          } else {
            requestQtyInput.disabled = false;
            requestQtyInput.readOnly = false;
            requestQtyInput.placeholder = 'Enter quantity';
            // Set default 1 when enabling and valid stock exists
            const stock = parseInt(availableStockInput.value) || 0;
            if (stock > 0 && (!requestQtyInput.value || parseInt(requestQtyInput.value) < 1)) {
              requestQtyInput.value = 1;
              requestQtyInput.min = 1;
              requestQtyInput.max = stock;
            }
          }
        }
        
        if (!required) {
          if (performaType) performaType.value = '';
          if (authorityNumber) authorityNumber.value = '';
        } else {
          // Default to Work Performa when enabling
          if (performaType && (!performaType.value || performaType.value === '')) {
            performaType.value = 'work_performa';
          }
        }
        
        // Update submit button state
        updateSubmitButtonState();
      };
      authorityYes.addEventListener('change', updateAuthorityVisibility);
      authorityNo.addEventListener('change', updateAuthorityVisibility);
      // Initialize
      updateAuthorityVisibility();
      
      // Helper function to update badge in the table
      const updatePerformaBadge = () => {
        const approvalId = window.currentApprovalId;
        if (!approvalId) return;
        
        // Find the row for this approval by looking for buttons with data-approval-id
        const addStockBtns = document.querySelectorAll(`button.add-stock-btn[data-approval-id="${approvalId}"]`);
        let approvalRow = null;
        
        for (let btn of addStockBtns) {
          approvalRow = btn.closest('tr');
          if (approvalRow) break;
        }
        
        // Fallback: try finding by view links
        if (!approvalRow) {
          const viewLinks = document.querySelectorAll(`a[href*="/approvals/${approvalId}"]`);
          for (let link of viewLinks) {
            approvalRow = link.closest('tr');
            if (approvalRow) break;
          }
        }
        
        if (approvalRow) {
          const badge = approvalRow.querySelector('.performa-badge');
          if (badge) {
            // If performa is not required (No is selected), hide the badge
            if (authorityNo && authorityNo.checked) {
              badge.style.display = 'none';
              badge.textContent = '';
            } else if (authorityYes && authorityYes.checked && performaType && performaType.value) {
              const perfVal = performaType.value;
              // Update badge text and color immediately
              let typeLabel = '';
              if (perfVal === 'work_performa') {
                typeLabel = 'Work Performa';
              } else if (perfVal === 'maint_performa') {
                typeLabel = 'Maintenance Performa';
              } else {
                // Convert snake_case to Title Case
                typeLabel = perfVal.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
              }
              
              badge.textContent = typeLabel;
              
              // Always use performa type color when performa type is selected
              const color = perfVal === 'work_performa' ? '#60a5fa' : 
                           perfVal === 'maint_performa' ? '#eab308' : '#60a5fa';
              badge.style.backgroundColor = color;
              badge.style.display = 'inline-block';
              badge.style.color = '#ffffff';
              badge.style.setProperty('color', '#ffffff', 'important');
              badge.style.setProperty('background-color', color, 'important');
            }
          }
        }
      };
      
      // Update badge and status when performa type changes
      if (performaType) {
        performaType.addEventListener('change', function() {
          updatePerformaBadge();
          
          // If performa is selected and authority is required, update status to in_progress
          if (authorityYes && authorityYes.checked && performaType.value) {
            const approvalId = window.currentApprovalId;
            if (approvalId) {
              // Find the row for this approval
              const addStockBtns = document.querySelectorAll(`button.add-stock-btn[data-approval-id="${approvalId}"]`);
              let approvalRow = null;
              
              for (let btn of addStockBtns) {
                approvalRow = btn.closest('tr');
                if (approvalRow) break;
              }
              
              // Fallback: try finding by view links
              if (!approvalRow) {
                const viewLinks = document.querySelectorAll(`a[href*="/approvals/${approvalId}"]`);
                for (let link of viewLinks) {
                  approvalRow = link.closest('tr');
                  if (approvalRow) break;
                }
              }
              
              if (approvalRow) {
                // Find the status select dropdown
                const statusSelect = approvalRow.querySelector('select.status-select[data-complaint-id]');
                if (statusSelect) {
                  const complaintId = statusSelect.getAttribute('data-complaint-id');
                  const currentStatus = statusSelect.value;
                  
                  // Only update if status is not already in_progress
                  if (currentStatus !== 'in_progress') {
                    // Update the select value
                    statusSelect.value = 'in_progress';
                    
                    // Update the color - check if function exists in global scope
                    if (typeof updateStatusSelectColor === 'function') {
                      updateStatusSelectColor(statusSelect, 'in_progress');
                    } else if (window.updateStatusSelectColor) {
                      window.updateStatusSelectColor(statusSelect, 'in_progress');
                    }
                    
                    // Update status via API
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (complaintId && csrfToken) {
                      fetch(`/admin/approvals/complaints/${complaintId}/update-status`, {
                        method: 'POST',
                        headers: {
                          'Content-Type': 'application/json',
                          'X-CSRF-TOKEN': csrfToken,
                          'X-Requested-With': 'XMLHttpRequest',
                          'Accept': 'application/json'
                        },
                        body: JSON.stringify({ 
                          status: 'in_progress', 
                          notes: `Status updated to In-Process when performa type selected` 
                        })
                      })
                      .then(async (response) => {
                        const contentType = response.headers.get('content-type') || '';
                        const isJson = contentType.includes('application/json');
                        const data = isJson ? await response.json() : null;
                        if (!response.ok) {
                          console.error('Failed to update status:', data?.message || 'Unknown error');
                          // Revert the select value on error
                          statusSelect.value = currentStatus;
                          if (typeof updateStatusSelectColor === 'function') {
                            updateStatusSelectColor(statusSelect, currentStatus);
                          } else if (window.updateStatusSelectColor) {
                            window.updateStatusSelectColor(statusSelect, currentStatus);
                          }
                        } else {
                          console.log('Status updated to in_progress successfully');
                          if (statusSelect.isConnected) {
                            statusSelect.dataset.oldStatus = 'in_progress';
                          }
                        }
                      })
                      .catch(error => {
                        console.error('Error updating status:', error);
                        // Revert the select value on error
                        statusSelect.value = currentStatus;
                        if (typeof updateStatusSelectColor === 'function') {
                          updateStatusSelectColor(statusSelect, currentStatus);
                        } else if (window.updateStatusSelectColor) {
                          window.updateStatusSelectColor(statusSelect, currentStatus);
                        }
                      });
                    }
                  }
                }
              }
            }
          }
        });
      }
      
      // Update badge when performa required radio changes
      if (authorityYes) {
        authorityYes.addEventListener('change', updatePerformaBadge);
      }
      if (authorityNo) {
        authorityNo.addEventListener('change', updatePerformaBadge);
      }
      
      // As user types Authority No., enable quantity when non-empty
      if (authorityNumber) {
        authorityNumber.addEventListener('input', () => {
          const hasAuthNo = authorityNumber.value && authorityNumber.value.trim().length > 0;
          if (requestQtyInput && authorityYes.checked) {
            requestQtyInput.disabled = !hasAuthNo;
            requestQtyInput.readOnly = !hasAuthNo;
            requestQtyInput.placeholder = hasAuthNo ? 'Enter quantity' : 'Enter Authority No.';
            if (!hasAuthNo) {
              requestQtyInput.value = '';
            } else {
              // Set default 1 when enabling and valid stock exists
              const stock = parseInt(availableStockInput.value) || 0;
              if (stock > 0 && (!requestQtyInput.value || parseInt(requestQtyInput.value) < 1)) {
                requestQtyInput.value = 1;
                requestQtyInput.min = 1;
                requestQtyInput.max = stock;
              }
            }
            updateSubmitButtonState();
          }
        });
      }
    }

  }

  // Forward declaration for Add Stock Modal function (defined later)
  window.openAddStockModal = function(approvalId, category = null) {
    console.log('openAddStockModal called with ID:', approvalId, 'Category:', category);
    
      // Store approvalId globally for submitIssueStock
    window.currentApprovalId = approvalId;
    console.log('Modal opened with approval_id:', approvalId);
    
    // Reset manual items array when modal opens (not needed anymore but keeping for compatibility)
    window.manualItems = [];
    
    // Find modal element
    const modalElement = document.getElementById('addStockModal');
    if (!modalElement) {
      console.error('Modal element not found');
      alert('Modal not found. Please refresh the page.');
      return;
    }

    const modalBody = document.getElementById('addStockModalBody');
    if (!modalBody) {
      console.error('Modal body not found');
      alert('Modal body not found. Please refresh the page.');
      return;
    }

    // Show loading immediately
    modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading approval items...</p></div>';

    // Show Submit button and hide it initially
    const submitBtn = document.getElementById('submitAddStockBtn');
    if (submitBtn) {
      submitBtn.style.display = 'none';
      submitBtn.disabled = false;
    }

    // Show modal immediately (before data loads)
    let modalInstance = null;
    try {
      // Check if Bootstrap is available
      if (typeof bootstrap === 'undefined' || typeof bootstrap.Modal === 'undefined') {
        console.error('Bootstrap Modal not available');
        alert('Bootstrap Modal library not loaded. Please refresh the page.');
        return;
      }

      modalInstance = bootstrap.Modal.getInstance(modalElement);
      if (!modalInstance) {
        modalInstance = new bootstrap.Modal(modalElement, {
          backdrop: true,
          keyboard: true,
          focus: true
        });
      }
      
      // Fix accessibility: Set up event listeners before showing modal
      const handleShown = function() {
        // Bootstrap automatically removes aria-hidden on shown event
        // But we ensure it's properly set
        if (modalElement.hasAttribute('aria-hidden')) {
          modalElement.removeAttribute('aria-hidden');
        }
        modalElement.setAttribute('aria-modal', 'true');
        console.log('✅ Modal accessibility fixed');
      };
      
      const handleHidden = function() {
        modalElement.setAttribute('aria-hidden', 'true');
        modalElement.removeAttribute('aria-modal');
        
        // Check if Performa Req = Yes and Performa Type is selected, then save approval
        const authorityYes = document.getElementById('authorityYes');
        const authorityNo = document.getElementById('authorityNo');
        const performaType = document.getElementById('performaType');
        const authorityNumber = document.getElementById('authorityNumber');
        
        // Update badge immediately without page reload (using same logic as in modal)
        const approvalId = window.currentApprovalId;
        if (approvalId) {
          // Find the row for this approval by looking for buttons with data-approval-id
          const addStockBtns = document.querySelectorAll(`button.add-stock-btn[data-approval-id="${approvalId}"]`);
          let approvalRow = null;
          
          for (let btn of addStockBtns) {
            approvalRow = btn.closest('tr');
            if (approvalRow) break;
          }
          
          // Fallback: try finding by view links
          if (!approvalRow) {
            const viewLinks = document.querySelectorAll(`a[href*="/approvals/${approvalId}"]`);
            for (let link of viewLinks) {
              approvalRow = link.closest('tr');
              if (approvalRow) break;
            }
          }
          
          if (approvalRow) {
            const badge = approvalRow.querySelector('.performa-badge');
            if (badge) {
              // If performa is not required (No is selected), hide the badge
              if (authorityNo && authorityNo.checked) {
                badge.style.display = 'none';
                badge.textContent = '';
              } else if (authorityYes && authorityYes.checked && performaType && performaType.value) {
                const perfVal = performaType.value;
                // Update badge text and color immediately
                let typeLabel = '';
                if (perfVal === 'work_performa') {
                  typeLabel = 'Work Performa';
                } else if (perfVal === 'maint_performa') {
                  typeLabel = 'Maintenance Performa';
                } else {
                  // Convert snake_case to Title Case
                  typeLabel = perfVal.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                }
                
                badge.textContent = typeLabel;
                
                // Always use performa type color when performa type is selected
                const color = perfVal === 'work_performa' ? '#60a5fa' : 
                             perfVal === 'maint_performa' ? '#eab308' : '#60a5fa';
                badge.style.backgroundColor = color;
                badge.style.display = 'inline-block';
                badge.style.color = '#ffffff';
                badge.style.setProperty('color', '#ffffff', 'important');
                badge.style.setProperty('background-color', color, 'important');
              }
            }
          }
        }
        
        if (authorityYes && authorityYes.checked && performaType && performaType.value) {
          const perfVal = performaType.value;
          const authNo = authorityNumber && authorityNumber.value ? authorityNumber.value.trim() : '';
          
          // Only save if performa type is selected but authority no. is not entered
          if (perfVal && !authNo && window.currentApprovalId) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            // Save approval with performa info
            fetch(`/admin/approvals/${window.currentApprovalId}/save-performa`, {
              method: 'POST',
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
              },
              body: JSON.stringify({
                performa_type: perfVal,
                remarks: `Performa type selected: ${perfVal}, waiting for authority number`
              }),
              credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                console.log('Approval saved with performa type on modal close');
              } else {
                console.error('Failed to save approval:', data.message);
              }
            })
            .catch(error => {
              console.error('Error saving approval on modal close:', error);
            });
          }
        }
      };
      
      // Remove any existing listeners first
      modalElement.removeEventListener('shown.bs.modal', handleShown);
      modalElement.removeEventListener('hidden.bs.modal', handleHidden);
      
      // Add new listeners
      modalElement.addEventListener('shown.bs.modal', handleShown, { once: true });
      modalElement.addEventListener('hidden.bs.modal', handleHidden);
      
      // Show the modal
      modalInstance.show();
      console.log('Modal shown successfully');
      
      // Also fix immediately after a short delay (in case shown event fires before our listener)
      setTimeout(() => {
        if (modalElement.classList.contains('show') && modalElement.hasAttribute('aria-hidden')) {
          modalElement.removeAttribute('aria-hidden');
          modalElement.setAttribute('aria-modal', 'true');
          console.log('✅ Modal accessibility fixed (delayed check)');
        }
      }, 200);
      
    } catch (error) {
      console.error('Error showing modal:', error);
      alert('Error opening modal: ' + error.message);
      return;
    }

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Fetch approval details
    fetch(`/admin/approvals/${approvalId}`, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      credentials: 'same-origin'
    })
    .then(response => {
      console.log('Fetch response status:', response.status);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then(data => {
      console.log('Fetch response data:', data);
      if (data.success && data.approval && data.approval.items) {
        const items = data.approval.items || [];
        const issuedStock = data.approval.issued_stock || [];
        
        console.log('✅ Creating table with 5 columns:');
        console.log('   1. Product Name (25% width)');
        console.log('   2. Category (20% width)');
        console.log('   3. Request Quantity (15% width)');
        console.log('   4. Available Stock (15% width)');
        console.log('   5. Issue Quantity (25% width)');
        console.log('Items to display:', items.length);
        console.log('Items data:', items);
        console.log('Issued stock:', issuedStock);
        
        if (items.length === 0) {
          console.warn('⚠️ No items found in approval ID:', approvalId);
          console.info('ℹ️ Table structure with 5 columns (Product Name, Category, Request Quantity, Available Stock, Issue Quantity) will still be displayed');
          console.info('ℹ️ Empty state message will be shown in the table');
        } else {
          console.log('✅ Items found:', items.length);
          console.log('✅ Displaying items in table with 5 columns');
        }

        // Initialize manual items array
        if (!window.manualItems) {
          window.manualItems = [];
        }

        // Store items globally for submission (existing + manual)
        window.currentApprovalItems = items;
        window.lastIssuedStock = issuedStock.length > 0 ? issuedStock[0] : null; // Store last issued stock
        console.log('Approval items loaded:', items.length, 'items');
        console.log('Current approval_id:', window.currentApprovalId);
        console.log('Last issued stock:', window.lastIssuedStock);

        // Build form with manual add section
        let itemsHtml = '<form id="addStockForm">';
        
        // Check if stock has already been issued - if yes, disable the form
        const hasIssuedStock = issuedStock.length > 0;
        
        // Manual Add Form Section
        itemsHtml += `
          <div class="card mb-3" style="border: 1px solid #dee2e6; border-radius: 8px;">
            <div class="card-header bg-primary text-white" style="padding: 12px 16px; font-weight: 600; font-size: 14px;">
            </div>
            <div class="card-body" style="padding: 16px;">
              <!-- Performa Required Row - Moved to top -->
              <div class="row g-3 mb-3 align-items-end" id="authorityRow">
                <div class="col-md-3">
                  <label class="form-label small mb-1" style="font-size: 0.85rem; font-weight: 600; color: #000000 !important;">Performa Req</label>
                  <div class="d-flex align-items-center" style="gap: 10px;">
                    <div class="form-check form-check-inline" style="margin: 0;">
                      <input class="form-check-input" type="radio" name="authorityRequired" id="authorityNo" value="no" checked>
                      <label class="form-check-label" for="authorityNo" style="font-size: 0.85rem;">No</label>
                    </div>
                    <div class="form-check form-check-inline" style="margin: 0;">
                      <input class="form-check-input" type="radio" name="authorityRequired" id="authorityYes" value="yes">
                      <label class="form-check-label" for="authorityYes" style="font-size: 0.85rem;">Yes</label>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 d-none" id="performaTypeCol">
                  <label class="form-label small mb-1" style="font-size: 0.85rem; font-weight: 600; color: #000000 !important;">Performa Type</label>
                  <select class="form-select form-select-sm" id="performaType" style="font-size: 0.9rem;">
                    <option value="">Select</option>
                    <option value="work_performa">Work Performa</option>
                    <option value="maint_performa">Maintenance Performa</option>
                  </select>
                </div>
                <div class="col-md-3 d-none" id="authorityNoCol">
                  <label class="form-label small mb-1" style="font-size: 0.85rem; font-weight: 600; color: #000000 !important;">Authority No.</label>
                  <input type="text" class="form-control form-control-sm" id="authorityNumber" placeholder="Enter Authority No." style="font-size: 0.9rem;">
                </div>
              </div>

              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label small mb-1" style="font-size: 0.85rem; font-weight: 600; color: #000000 !important;">Product</label>
                  <select class="form-select form-select-sm" id="manualProduct" style="font-size: 0.9rem;" disabled>
                    <option value="">Loading Products...</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <label class="form-label small mb-1" style="font-size: 0.85rem; font-weight: 600; color: #000000 !important;">Available Stock</label>
                  <input type="text" class="form-control form-control-sm" id="manualAvailableStock" readonly style="font-size: 0.9rem; background-color: #f8f9fa; font-weight: 600; text-align: center;">
                </div>
                <div class="col-md-3">
                  <label class="form-label small mb-1" style="font-size: 0.85rem; font-weight: 600; color: #000000 !important;">Request Quantity</label>
                  <input type="number" class="form-control form-control-sm" id="manualRequestQty" min="1" style="font-size: 0.9rem; text-align: center;" placeholder="Enter quantity">
                </div>
              </div>
            </div>
          </div>
        `;

        itemsHtml += '</form>';
        modalBody.innerHTML = itemsHtml;

        // Get complaint category and location from stored global variable or approval response
        const complaintCategory = window.currentComplaintCategory || data.approval?.complaint?.category || null;
        const complaintSector = data.approval?.complaint?.sector || null;
        const complaintCity = data.approval?.complaint?.city || null;
        console.log('Complaint category:', complaintCategory, 'From stored:', window.currentComplaintCategory, 'From response:', data.approval?.complaint?.category);
        console.log('Complaint sector:', complaintSector, 'Complaint city:', complaintCity);
        
        // Wait for DOM to be ready before initializing
        setTimeout(() => {
          // Load products for complaint category and sector only (no category dropdown needed)
          if (complaintCategory && complaintCategory.trim() !== '' && complaintCategory !== 'N/A') {
            console.log('Loading products for category:', complaintCategory, 'sector:', complaintSector);
            // Ensure category is passed correctly, along with sector for location filtering
            loadProductsByCategory(complaintCategory.trim(), complaintSector, complaintCity);
          } else {
            console.warn('No complaint category found');
            // Don't load all products if category is missing - show empty
            const productSelect = document.getElementById('manualProduct');
            if (productSelect) {
              productSelect.innerHTML = '<option value="">No category found - Please select a product manually</option>';
              productSelect.disabled = false;
            }
          }
          
          // Setup event listeners for manual form
          setupManualFormListeners();
          
          // Restore performa selections if approval has performa_type
          if (data.approval && data.approval.performa_type) {
            const authorityYes = document.getElementById('authorityYes');
            const authorityNo = document.getElementById('authorityNo');
            const performaType = document.getElementById('performaType');
            const performaTypeCol = document.getElementById('performaTypeCol');
            const authorityNoCol = document.getElementById('authorityNoCol');
            
            if (authorityYes && performaType) {
              // Select "Yes" radio button
              authorityYes.checked = true;
              authorityNo.checked = false;
              
              // Show performa type and authority no. columns
              if (performaTypeCol) performaTypeCol.classList.remove('d-none');
              if (authorityNoCol) authorityNoCol.classList.remove('d-none');
              
              // Set performa type value
              performaType.value = data.approval.performa_type;
              
              // Trigger change event to update form state
              if (authorityYes) {
                authorityYes.dispatchEvent(new Event('change'));
              }
              
              console.log('Restored performa type:', data.approval.performa_type);
            }
          }
          
          // Don't populate form if stock has already been issued
          if (hasIssuedStock) {
            // Form is already disabled above, no need to populate
            return;
          }
        }, 100);
        
        // Show Submit button always (will be enabled when product and quantity are selected)
        if (submitBtn) {
          submitBtn.style.display = 'inline-block';
          // Disable button if stock has already been issued
          if (hasIssuedStock) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Stock Already Issued';
            submitBtn.classList.add('btn-secondary');
            submitBtn.classList.remove('btn-success');
            // Disable all form fields
            const productSelect = document.getElementById('manualProduct');
            const requestQtyInput = document.getElementById('manualRequestQty');
            if (productSelect) productSelect.disabled = true;
            if (requestQtyInput) {
              requestQtyInput.disabled = true;
              requestQtyInput.readOnly = true;
            }
          } else {
            submitBtn.disabled = true; // Disabled by default, will be enabled when form is valid
          }
        }
        
        // Replace feather icons (for empty state icon)
        if (typeof feather !== 'undefined') {
          feather.replace();
        }
      } else {
        console.error('Invalid response data:', data);
        modalBody.innerHTML = '<div class="alert alert-danger">Error loading approval items. Invalid response.</div>';
      }
    })
    .catch(error => {
      console.error('Error fetching approval details:', error);
      modalBody.innerHTML = '<div class="alert alert-danger">Error loading approval details: ' + (error.message || 'Unknown error') + '</div>';
    });
  };

  // Forward declaration for Submit Add Stock function (defined later)
  // Issue Stock Function (early definition)
  window.submitIssueStock = function() {
    console.log('submitIssueStock called');
    
    // Get form values directly
    const productSelect = document.getElementById('manualProduct');
    const availableStockInput = document.getElementById('manualAvailableStock');
    const requestQtyInput = document.getElementById('manualRequestQty');
    const authorityYes = document.getElementById('authorityYes');
    const performaType = document.getElementById('performaType');
    const authorityNumber = document.getElementById('authorityNumber');
    
    if (!productSelect || !availableStockInput || !requestQtyInput) {
      alert('Form fields not found. Please refresh the page.');
      return;
    }
    
    const productId = productSelect.value;
    const productName = productSelect.options[productSelect.selectedIndex]?.textContent || 'N/A';
    const availableStock = parseInt(availableStockInput.value) || 0;
    const issueQty = parseInt(requestQtyInput.value) || 0;
    
    // Validate form
    if (!productId) {
      alert('Please select a product');
      productSelect.focus();
      return;
    }
    
    if (issueQty <= 0) {
      alert('Please enter a valid request quantity');
      requestQtyInput.focus();
      return;
    }
    
    if (issueQty > availableStock) {
      alert(`Request quantity (${issueQty}) cannot exceed available stock (${availableStock})`);
      requestQtyInput.focus();
      return;
    }
    
    // Check if Performa Req is Yes and Performa Type is selected but Authority No. is missing
    const isPerformaRequired = authorityYes && authorityYes.checked;
    const perfVal = (performaType && performaType.value) ? performaType.value : '';
    const authNo = authorityNumber && authorityNumber.value ? authorityNumber.value.trim() : '';
    
    // If performa is required and type is selected but authority no. is missing, save approval instead of issuing stock
    if (isPerformaRequired && perfVal && !authNo) {
      // Save approval with performa info and wait for authority
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const submitBtn = document.getElementById('submitAddStockBtn');
      
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
      }
      
      fetch(`/admin/approvals/${window.currentApprovalId}/save-performa`, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          performa_type: perfVal,
          remarks: `Waiting for authority number - Product: ${productName}, Quantity: ${issueQty}`
        }),
        credentials: 'same-origin'
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Approval saved. Waiting for authority number. You can close the form and add authority number later.');
          bootstrap.Modal.getInstance(document.getElementById('addStockModal')).hide();
          window.location.reload();
        } else {
          alert('Failed to save approval: ' + (data.message || 'Unknown error'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error saving approval: ' + error.message);
      })
      .finally(() => {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = '<i data-feather="check-circle"></i> Issue Stock';
          feather.replace();
        }
      });
      
      return; // Exit early, don't proceed to stock issue
    }
    
    // Prepare stock data
    const stockData = [{
      spare_id: parseInt(productId),
      item_id: null,
      issue_quantity: issueQty,
      product_name: productName,
      available_stock: availableStock
    }];

    // Confirm before submitting
    const confirmMessage = `Are you sure you want to ISSUE stock for the following items?\n\n` +
      stockData.map(item => `${item.product_name}: ${item.issue_quantity} units (Available: ${item.available_stock})`).join('\n');
    
    if (!confirm(confirmMessage)) {
      return;
    }

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Disable submit button
    const submitBtn = document.getElementById('submitAddStockBtn');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Issuing Stock...';
    }

    // Send requests for each item to ISSUE stock (decrease inventory)
    const promises = stockData.map(item => {
      // Build optional authority info string (appended in reason)
      let authorityInfo = '';
      if (isPerformaRequired && perfVal && authNo) {
        const typeLabel = (performaType && performaType.value)
          ? (performaType.value === 'work_performa' ? 'Work Performa' : (performaType.value === 'maint_performa' ? 'Maintenance Performa' : ''))
          : '';
        authorityInfo = ` | Performa Req: ${typeLabel || 'Yes'}, Authority No: ${authNo}`;
      }
      return fetch(`/admin/spares/${item.spare_id}/issue-stock`, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          quantity: item.issue_quantity,
          item_id: item.item_id,
          approval_id: window.currentApprovalId || null,
          reason: `Stock issued from approval - Product: ${item.product_name}${authorityInfo}`
        }),
        credentials: 'same-origin'
      });
    });

    // Process all requests
    Promise.all(promises)
      .then(responses => Promise.all(responses.map(r => r.json())))
      .then(results => {
        const successCount = results.filter(r => r.success).length;
        const failedCount = results.length - successCount;

        if (failedCount === 0) {
          // If stock is issued with authority, remove waiting flag and update badge
          if (isPerformaRequired && perfVal && authNo && window.currentApprovalId) {
            // Update approval to remove waiting flag
            fetch(`/admin/approvals/${window.currentApprovalId}/save-performa`, {
              method: 'POST',
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
              },
              body: JSON.stringify({
                performa_type: perfVal,
                waiting_for_authority: false,
                remarks: `Stock issued with authority number: ${authNo}`
              }),
              credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                // Update badge color without page reload
                const approvalId = window.currentApprovalId;
                if (approvalId) {
                  // Find the row for this approval
                  const viewLinks = document.querySelectorAll(`a[href*="/approvals/${approvalId}"]`);
                  let approvalRow = null;
                  
                  for (let link of viewLinks) {
                    approvalRow = link.closest('tr');
                    if (approvalRow) break;
                  }
                  
                  if (approvalRow) {
                    const badge = approvalRow.querySelector('.performa-badge');
                    if (badge) {
                      const performaType = data.approval?.performa_type || perfVal;
                      const isWaiting = data.approval?.waiting_for_authority ?? false;
                      
                      // Update badge text and color
                      let typeLabel = '';
                      if (performaType === 'work_performa') {
                        typeLabel = 'Work Performa';
                      } else if (performaType === 'maint_performa') {
                        typeLabel = 'Maint Performa';
                      } else {
                        typeLabel = performaType.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                      }
                      
                      badge.textContent = typeLabel;
                      
                      // Set color based on waiting status
                      if (isWaiting) {
                        badge.style.backgroundColor = '#dc2626'; // Red
                      } else {
                        // Use performa type color
                        const color = performaType === 'work_performa' ? '#60a5fa' : 
                                     performaType === 'maint_performa' ? '#eab308' : '#60a5fa';
                        badge.style.backgroundColor = color;
                      }
                      badge.style.display = 'inline-block';
                      badge.style.color = '#ffffff';
                      badge.style.setProperty('color', '#ffffff', 'important');
                    }
                  }
                }
              }
            })
            .catch(err => console.error('Error updating approval:', err));
          }
          
          alert(`Successfully issued stock for ${productName} (${issueQty} units)!`);
          bootstrap.Modal.getInstance(document.getElementById('addStockModal')).hide();
          // Reload the page to refresh stock quantities
          window.location.reload();
        } else {
          alert(`Failed to issue stock. Please try again.`);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error issuing stock: ' + error.message);
      })
      .finally(() => {
        // Re-enable submit button
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = '<i data-feather="check-circle"></i> Issue Stock';
          feather.replace();
        }
      });
  };

  // Initialize event listeners on page load
  document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, filters initialized');
    
    // Verify form exists
    const form = document.getElementById('approvalsFiltersForm');
    if (form) {
      console.log('Filter form found');
    } else {
      console.error('Filter form NOT found!');
      return;
    }
    
    // Attach event listener to search input (instant response)
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
      console.log('Search input found, attaching event listener');
      searchInput.addEventListener('input', handleApprovalsSearchInput);
      searchInput.addEventListener('keydown', function(e) {
        // Prevent Enter key from submitting form
        if (e.key === 'Enter') {
          e.preventDefault();
          e.stopPropagation();
          // Cancel timeout and search immediately
          if (approvalsSearchTimeout) {
            clearTimeout(approvalsSearchTimeout);
          }
          loadApprovals();
        }
      });
    } else {
      console.error('Search input NOT found!');
    }
    
    // Attach event listener to date input
    const dateInput = form.querySelector('input[name="complaint_date"]');
    if (dateInput) {
      dateInput.addEventListener('change', submitApprovalsFilters);
    }
    
    // Attach event listener to end date input
    const endDateInput = form.querySelector('input[name="date_to"]');
    if (endDateInput) {
      endDateInput.addEventListener('change', submitApprovalsFilters);
    }
    
    // Attach event listener to category select
    const categorySelect = form.querySelector('select[name="category"]');
    if (categorySelect) {
      categorySelect.addEventListener('change', submitApprovalsFilters);
    }
  });

  // Load Approvals via AJAX
  function loadApprovals(url = null) {
    const form = document.getElementById('approvalsFiltersForm');
    if (!form) {
      console.error('Filter form not found');
      return;
    }
    
    const params = new URLSearchParams();
    
    if (url) {
      // If URL is provided, extract params from it
      const urlObj = new URL(url, window.location.origin);
      urlObj.searchParams.forEach((value, key) => {
        params.append(key, value);
      });
    } else {
      // Get all form inputs and build params
      const inputs = form.querySelectorAll('input[name], select[name], textarea[name]');
      inputs.forEach(input => {
        const name = input.name;
        if (!name) return;
        
        if (input.type === 'checkbox' || input.type === 'radio') {
          if (input.checked) {
            params.append(name, input.value);
          }
        } else {
          // Only append non-empty values to preserve other active filters
          if (input.value && input.value.trim() !== '') {
            params.append(name, input.value.trim());
          }
        }
      });
    }

    const tbody = document.getElementById('approvalsTableBody');
    const paginationContainer = document.getElementById('approvalsPagination');
    
    if (tbody) {
      tbody.innerHTML = '<tr><td colspan="11" class="text-center py-4"><div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
    }

    const fetchUrl = `{{ route('admin.approvals.index') }}?${params.toString()}`;
    console.log('Fetching URL:', fetchUrl);
    console.log('Params:', params.toString());
    
    // Show loading state
    if (tbody) {
      tbody.innerHTML = '<tr><td colspan="11" class="text-center py-4"><div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
    }
    
    fetch(fetchUrl, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'text/html',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      credentials: 'same-origin'
    })
    .then(response => {
      console.log('Response status:', response.status);
      
      // Check if response is JSON (AJAX optimized)
      const contentType = response.headers.get('content-type');
      if (contentType && contentType.includes('application/json')) {
        return response.json().then(data => {
          console.log('Received JSON response');
          // Check if there's an error
          if (!response.ok || !data.success) {
            throw new Error(data.error || data.message || 'Error loading approvals');
          }
          return data.html || data;
        });
      }
      
      if (!response.ok) {
        return response.text().then(text => {
          throw new Error(`HTTP error! status: ${response.status}`);
        });
      }
      
      return response.text();
    })
    .then(html => {
      console.log('Received HTML length:', html.length);
      
      // Try to parse the HTML
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      
      // Check for errors in parsing
      const parserError = doc.querySelector('parsererror');
      if (parserError) {
        console.error('Parser error:', parserError.textContent);
        throw new Error('Failed to parse response HTML');
      }
      
      const newTbody = doc.querySelector('#approvalsTableBody');
      const newPagination = doc.querySelector('#approvalsPagination');
      
      console.log('Found newTbody:', !!newTbody);
      console.log('Found newPagination:', !!newPagination);
      
      if (newTbody && tbody) {
        tbody.innerHTML = newTbody.innerHTML;
        feather.replace();
        // Re-initialize performa badges and status old values after table refresh
        if (typeof initPerformaBadges === 'function') {
          initPerformaBadges();
        }
        // Run initStatusSelects after initPerformaBadges to ensure correct values
        setTimeout(function() {
          if (typeof initStatusSelects === 'function') {
            initStatusSelects();
          }
        }, 50);
        console.log('Table updated successfully');
      } else {
        console.error('Table body not found in response');
        // Try fallback - check if entire page was returned
        if (html.includes('approvalsTableBody')) {
          console.log('Found table body in HTML, trying direct extraction');
          const tempDiv = document.createElement('div');
          tempDiv.innerHTML = html;
          const extractedTbody = tempDiv.querySelector('#approvalsTableBody');
          if (extractedTbody && tbody) {
            tbody.innerHTML = extractedTbody.innerHTML;
            feather.replace();
            if (typeof initPerformaBadges === 'function') {
              initPerformaBadges();
            }
            // Run initStatusSelects after initPerformaBadges to ensure correct values
            setTimeout(function() {
              if (typeof initStatusSelects === 'function') {
                initStatusSelects();
              }
            }, 50);
            console.log('Table updated via direct extraction');
          } else {
            throw new Error('Could not find table body in response');
          }
        } else {
          throw new Error('Response does not contain expected table structure');
        }
      }
      
      if (newPagination && paginationContainer) {
        paginationContainer.innerHTML = newPagination.innerHTML;
        // Re-initialize feather icons after pagination update
        feather.replace();
      } else if (paginationContainer) {
        const extractedPagination = doc.querySelector('#approvalsPagination') || 
          (html.includes('approvalsPagination') ? (() => {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            return tempDiv.querySelector('#approvalsPagination');
          })() : null);
        
        if (extractedPagination) {
          paginationContainer.innerHTML = extractedPagination.innerHTML;
          // Re-initialize feather icons after pagination update
          feather.replace();
        }
      }

      // Update URL without reloading page
      window.history.pushState({path: fetchUrl}, '', fetchUrl);
    })
    .catch(error => {
      console.error('Error loading approvals:', error);
      console.error('Error details:', error.message);
      
      // Show error message to user
      if (tbody) {
        tbody.innerHTML = '<tr><td colspan="11" class="text-center py-4 text-danger">' +
          '<div class="alert alert-danger mb-0">' +
          '<strong>Error:</strong> ' + (error.message || 'Failed to load approvals. Please try again.') +
          '<br><small>If this persists, please refresh the page.</small>' +
          '</div>' +
          '</td></tr>';
      }
      
      // Show error notification
      if (typeof showError === 'function') {
        showError(error.message || 'Failed to load approvals');
      } else {
        alert('Error: ' + (error.message || 'Failed to load approvals'));
      }
      
      // Optionally fallback to regular form submission after a delay
      setTimeout(() => {
        const form = document.getElementById('approvalsFiltersForm');
        if (form && confirm('Would you like to reload the page to see results?')) {
          form.submit();
        }
      }, 3000);
    });

    // Performa Required toggle handlers
    if (authorityYes && authorityNo && performaTypeCol && authorityNoCol) {
      const updateAuthorityVisibility = () => {
        const required = authorityYes.checked;
        performaTypeCol.classList.toggle('d-none', !required);
        authorityNoCol.classList.toggle('d-none', !required);
        // Make fields required only when authority is required
        if (performaType) performaType.required = required;
        if (authorityNumber) authorityNumber.required = required;
        // Disable request quantity until Authority No. provided when required
        if (requestQtyInput) {
          if (required) {
            const hasAuthNo = authorityNumber && authorityNumber.value && authorityNumber.value.trim().length > 0;
            requestQtyInput.disabled = !hasAuthNo;
            requestQtyInput.readOnly = !hasAuthNo;
            requestQtyInput.placeholder = hasAuthNo ? 'Enter quantity' : 'Enter Authority No.';
            if (!hasAuthNo) {
              requestQtyInput.value = '';
            }
          } else {
            requestQtyInput.disabled = false;
            requestQtyInput.readOnly = false;
            requestQtyInput.placeholder = 'Enter quantity';
          }
        }
        if (!required) {
          performaType.value = '';
          authorityNumber.value = '';
        }
      };
      authorityYes.addEventListener('change', updateAuthorityVisibility);
      authorityNo.addEventListener('change', updateAuthorityVisibility);
      // Initialize visibility on load
      updateAuthorityVisibility();
      
      // As user types Authority No., enable quantity when non-empty
      if (authorityNumber) {
        authorityNumber.addEventListener('input', () => {
          const hasAuthNo = authorityNumber.value && authorityNumber.value.trim().length > 0;
          if (requestQtyInput) {
            requestQtyInput.disabled = authorityYes.checked ? !hasAuthNo : false;
            requestQtyInput.readOnly = authorityYes.checked ? !hasAuthNo : false;
            requestQtyInput.placeholder = authorityYes.checked && !hasAuthNo ? 'Enter Authority No.' : 'Enter quantity';
            if (!requestQtyInput.disabled) {
              // Set default 1 when enabling and valid stock exists
              const stock = parseInt(availableStockInput.value) || 0;
              if (stock > 0 && (!requestQtyInput.value || parseInt(requestQtyInput.value) < 1)) {
                requestQtyInput.value = 1;
                requestQtyInput.min = 1;
                requestQtyInput.max = stock;
              }
            }
          }
        });
      }
    }

    // Block wheel and arrow key increments when locked
    const blockWhenLocked = (e) => {
      if (!requestQtyInput) return;
      if (requestQtyInput.disabled || requestQtyInput.readOnly) {
        e.preventDefault();
      }
    };
    requestQtyInput.addEventListener('wheel', blockWhenLocked, { passive: false });
    requestQtyInput.addEventListener('keydown', (e) => {
      const blockedKeys = ['ArrowUp', 'ArrowDown', 'PageUp', 'PageDown'];
      if (requestQtyInput.disabled || requestQtyInput.readOnly) {
        if (blockedKeys.includes(e.key)) {
          e.preventDefault();
        }
      }
    });

  }

  // Handle pagination clicks
  document.addEventListener('click', function(e) {
    const paginationLink = e.target.closest('#approvalsPagination a');
    if (paginationLink && paginationLink.href && !paginationLink.href.includes('javascript:')) {
      e.preventDefault();
      loadApprovals(paginationLink.href);
    }
  });

  // Handle browser back/forward buttons
  window.addEventListener('popstate', function(e) {
    if (e.state && e.state.path) {
      loadApprovals(e.state.path);
    } else {
      loadApprovals();
    }
  });

  // displayApprovalDetails and submitApprovedQuantities functions removed - using show.blade.php page instead

  // Duplicate approveRequest and rejectRequest functions removed - already defined above

  // Utility Functions
  function showSuccess(message) {
    // Remove any existing alerts first
    const existingAlerts = document.querySelectorAll('.custom-alert-toast');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create and show success alert
    const alertDiv = document.createElement('div');
    alertDiv.className = 'custom-alert-toast alert-success-toast';
    alertDiv.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 10000;
      min-width: 320px;
      max-width: 450px;
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: white;
      padding: 16px 20px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3), 0 4px 10px rgba(0, 0, 0, 0.2);
      display: flex;
      align-items: center;
      gap: 12px;
      animation: slideInRight 0.3s ease-out;
      font-size: 14px;
      font-weight: 500;
    `;
    alertDiv.innerHTML = `
      <div style="flex-shrink: 0; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
          <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
      </div>
      <div style="flex: 1; line-height: 1.5;">
        <strong style="display: block; margin-bottom: 2px; font-size: 15px;">Success!</strong>
        <span style="opacity: 0.95;">${message}</span>
      </div>
      <button type="button" onclick="this.parentElement.remove()" style="
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        flex-shrink: 0;
        transition: background 0.2s;
      " onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    `;
    document.body.appendChild(alertDiv);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
      if (alertDiv.parentNode) {
        alertDiv.style.animation = 'fadeOut 0.3s ease-in forwards';
        setTimeout(() => {
          if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
          }
        }, 300);
      }
    }, 3000);
  }

  function showError(message) {
    // Remove any existing alerts first
    const existingAlerts = document.querySelectorAll('.custom-alert-toast');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create and show error alert
    const alertDiv = document.createElement('div');
    alertDiv.className = 'custom-alert-toast alert-error-toast';
    alertDiv.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 10000;
      min-width: 320px;
      max-width: 450px;
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      color: white;
      padding: 16px 20px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3), 0 4px 10px rgba(0, 0, 0, 0.2);
      display: flex;
      align-items: center;
      gap: 12px;
      animation: slideInRight 0.3s ease-out, shake 0.5s ease-in-out 0.3s;
      font-size: 14px;
      font-weight: 500;
    `;
    alertDiv.innerHTML = `
      <div style="flex-shrink: 0; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
      </div>
      <div style="flex: 1; line-height: 1.5;">
        <strong style="display: block; margin-bottom: 2px; font-size: 15px;">Error!</strong>
        <span style="opacity: 0.95;">${message}</span>
      </div>
      <button type="button" onclick="this.parentElement.remove()" style="
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        flex-shrink: 0;
        transition: background 0.2s;
      " onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    `;
    document.body.appendChild(alertDiv);
    
    // Auto remove after 6 seconds (slightly longer for errors)
    setTimeout(() => {
      if (alertDiv.parentNode) {
        alertDiv.style.animation = 'fadeOut 0.3s ease-in forwards';
        setTimeout(() => {
          if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
          }
        }, 300);
      }
    }, 6000);
  }

  // Removed duplicate status change handler (handled by comprehensive handler below)
  // Event delegation for approval/reject buttons removed as per user request

  // Status colors mapping for JavaScript
  const statusColors = {
    'in_progress': { bg: '#dc2626', text: '#ffffff', border: '#b91c1c' }, // Darker Red
    'resolved': { bg: '#16a34a', text: '#ffffff', border: '#15803d' }, // Darker Green
    'work_performa': { bg: '#60a5fa', text: '#ffffff', border: '#3b82f6' }, // Light Blue
    'maint_performa': { bg: '#eab308', text: '#ffffff', border: '#ca8a04' }, // Dark Yellow
    'work_priced_performa': { bg: '#9333ea', text: '#ffffff', border: '#7e22ce' }, // Purple
    'maint_priced_performa': { bg: '#ea580c', text: '#ffffff', border: '#c2410c' }, // Dark Orange
    'product_na': { bg: '#6b7280', text: '#ffffff', border: '#4b5563' }, // Gray
    'un_authorized': { bg: '#ec4899', text: '#ffffff', border: '#db2777' }, // Pink
    'pertains_to_ge_const_isld': { bg: '#06b6d4', text: '#ffffff', border: '#0891b2' }, // Aqua/Cyan
    'assigned': { bg: '#64748b', text: '#ffffff', border: '#475569' }, // Default Gray
  };

  // Function to update status select box colors
  function updateStatusSelectColor(select, status) {
    const normalizedStatus = status === 'in-process' || status === 'in process' ? 'in_progress' : status;
    const color = statusColors[normalizedStatus] || statusColors['assigned'];
    select.style.backgroundColor = color.bg;
    select.style.color = '#ffffff';
    select.style.setProperty('color', '#ffffff', 'important');
    select.style.borderColor = color.border;
    select.setAttribute('data-status-color', normalizedStatus);
    // Update the small status indicator dot next to the select
    const td = select.closest('td');
    if (td) {
      const dot = td.querySelector('.status-indicator');
      const chip = td.querySelector('.status-chip');
      if (dot) {
        dot.style.backgroundColor = color.bg;
        dot.style.borderColor = color.border;
      }
      if (chip) {
        chip.style.backgroundColor = color.bg;
        chip.style.color = '#ffffff';
        chip.style.setProperty('color', '#ffffff', 'important');
        chip.style.borderColor = color.border;
      }
    }
  }

  // Handle Performa Required dropdown changes
  document.addEventListener('change', function(e) {
    if (e.target.classList.contains('status-select')) {
      const select = e.target;
      let newStatus = (select.value || '').toString().trim();
      const row = select.closest('tr');
      const performaBadge = row ? row.querySelector('.performa-badge') : null;
      const complaintId = select.getAttribute('data-complaint-id');
      let skipConfirm = false;

      // Normalize common variants to backend values
      const normalize = (val) => {
        const v = (val || '').toString().trim().toLowerCase();
        if (v === 'in-process' || v === 'in process' || v === 'inprocess') return 'in_progress';
        if (v === 'addressed' || v === 'done' || v === 'completed') return 'resolved';
        if (v === 'assign' || v === 'assignment') return 'assigned';
        return v;
      };
      newStatus = normalize(newStatus);

      // Handle pseudo-statuses locally without backend call
      if (newStatus === 'work_performa' || newStatus === 'maint_performa') {
        if (performaBadge) {
          if (newStatus === 'work_performa') {
            performaBadge.textContent = 'Work Performa Required';
            performaBadge.style.backgroundColor = '#60a5fa';
            performaBadge.style.color = '#ffffff';
            performaBadge.style.setProperty('color', '#ffffff', 'important');
            // Update select box color to light blue
            updateStatusSelectColor(select, 'work_performa');
          } else {
            performaBadge.textContent = 'Maintenance Performa Required';
            performaBadge.style.backgroundColor = '#eab308';
            performaBadge.style.color = '#ffffff';
            performaBadge.style.setProperty('color', '#ffffff', 'important');
            // Update select box color to dark yellow
            updateStatusSelectColor(select, 'maint_performa');
          }
          performaBadge.style.display = 'inline-block';
        }
        // Persist selection locally so it survives reloads
        if (complaintId) {
          const key = `performaRequired:${complaintId}`;
          const val = newStatus === 'work_performa' ? 'work' : 'maint';
          try { localStorage.setItem(key, val); } catch (err) {}
        }
        // Auto-set status to In-Process and continue to update backend
        const performaType = newStatus; // Store original performa type
        newStatus = 'in_progress';
        // Keep dropdown value as in_progress and apply red color
        select.value = 'in_progress';
        updateStatusSelectColor(select, 'in_progress'); // Apply red color for in_progress
        skipConfirm = true;
        showSuccess(performaBadge?.textContent || 'Performa marked');
      } else if (newStatus === 'work_priced_performa' || newStatus === 'maint_priced_performa' || newStatus === 'product_na') {
        // Store original status before changing it for localStorage
        const originalStatus = newStatus;
        
        // Handle Work Performa Priced, Maintenance Performa Priced and Product N/A options
        if (performaBadge) {
          if (newStatus === 'work_priced_performa') {
            performaBadge.textContent = 'Work Performa Priced';
            performaBadge.style.backgroundColor = '#9333ea';
            performaBadge.style.color = '#ffffff';
            performaBadge.style.setProperty('color', '#ffffff', 'important');
            performaBadge.style.display = 'inline-block';
            // Keep status as in_progress and apply red color
            select.value = 'in_progress';
            updateStatusSelectColor(select, 'in_progress'); // Apply red color for in_progress
            newStatus = 'in_progress';
          } else if (newStatus === 'maint_priced_performa') {
            performaBadge.textContent = 'Maintenance Performa Priced';
            performaBadge.style.backgroundColor = '#ea580c';
            performaBadge.style.color = '#ffffff';
            performaBadge.style.setProperty('color', '#ffffff', 'important');
            performaBadge.style.display = 'inline-block';
            // Keep status as in_progress and apply red color
            select.value = 'in_progress';
            updateStatusSelectColor(select, 'in_progress'); // Apply red color for in_progress
            newStatus = 'in_progress';
          } else if (newStatus === 'product_na') {
            performaBadge.textContent = 'Product N/A';
            performaBadge.style.backgroundColor = '#6b7280';
            performaBadge.style.color = '#ffffff';
            performaBadge.style.setProperty('color', '#ffffff', 'important');
            performaBadge.style.display = 'inline-block';
            // Keep status as in_progress and apply red color
            select.value = 'in_progress';
            updateStatusSelectColor(select, 'in_progress'); // Apply red color for in_progress
            newStatus = 'in_progress';
          }
        }
        // Persist selection locally - use originalStatus before it was changed
        if (complaintId) {
          const key = `performaRequired:${complaintId}`;
          const val = originalStatus === 'work_priced_performa' ? 'work_priced' : (originalStatus === 'maint_priced_performa' ? 'maint_priced' : 'product_na');
          try { localStorage.setItem(key, val); } catch (err) {}
        }
        skipConfirm = true;
        showSuccess(performaBadge?.textContent || 'Option marked');
      } else {
        // Update color for regular status changes
        updateStatusSelectColor(select, newStatus);
      }

      // Real statuses only
      const allowed = ['new','assigned','in_progress','resolved','closed','un_authorized','pertains_to_ge_const_isld'];
      if (!allowed.includes(newStatus)) {
        console.warn('Blocked unsupported status:', newStatus);
        // Revert to old on invalid
        const oldStatusLocal = select.dataset.oldStatus || 'assigned';
        select.value = oldStatusLocal;
        showError('Unsupported status selected.');
        return;
      }

      // Clear persisted performa flag when switching to real statuses (but not for product_na, priced_performa, work_performa, or maint_performa)
      // Check localStorage to determine if this is a special option (since dropdown value is now 'in_progress')
      let savedOptionForClear = null;
      if (complaintId) {
        try { savedOptionForClear = localStorage.getItem(`performaRequired:${complaintId}`); } catch (err) { savedOptionForClear = null; }
      }
      const isSpecialOption = savedOptionForClear === 'work' || savedOptionForClear === 'maint' || savedOptionForClear === 'work_priced' || savedOptionForClear === 'maint_priced' || savedOptionForClear === 'product_na' ||
                             newStatus === 'work_performa' || newStatus === 'maint_performa' || newStatus === 'work_priced_performa' || newStatus === 'maint_priced_performa' || newStatus === 'product_na';
      
      // Clear Performa Required badge only if no persisted flag exists and not a special option
      if (performaBadge && complaintId && !isSpecialOption) {
        let savedFlag = null;
        try { savedFlag = localStorage.getItem(`performaRequired:${complaintId}`); } catch (err) { savedFlag = null; }
        if (!savedFlag) {
          performaBadge.style.display = 'none';
          performaBadge.textContent = '';
        } else {
          // Ensure correct styling if persisted
          if (savedFlag === 'work') {
            performaBadge.textContent = 'Work Performa Required';
            performaBadge.style.backgroundColor = '#60a5fa';
            performaBadge.style.color = '#ffffff';
            performaBadge.style.setProperty('color', '#ffffff', 'important');
          } else if (savedFlag === 'maint') {
            performaBadge.textContent = 'Maintenance Performa Required';
            performaBadge.style.backgroundColor = '#eab308';
            performaBadge.style.color = '#ffffff';
            performaBadge.style.setProperty('color', '#ffffff', 'important');
          } else if (savedFlag === 'work_priced') {
            performaBadge.textContent = 'Work Performa Priced';
            performaBadge.style.backgroundColor = '#9333ea';
            performaBadge.style.color = '#ffffff';
            performaBadge.style.setProperty('color', '#ffffff', 'important');
            performaBadge.style.display = 'inline-block';
          } else if (savedFlag === 'maint_priced') {
            performaBadge.textContent = 'Maintenance Performa Priced';
            performaBadge.style.backgroundColor = '#ea580c';
            performaBadge.style.color = '#ffffff';
            performaBadge.style.setProperty('color', '#ffffff', 'important');
            performaBadge.style.display = 'inline-block';
          } else if (savedFlag === 'product_na') {
            performaBadge.textContent = 'Product N/A';
            performaBadge.style.backgroundColor = '#6b7280';
            performaBadge.style.color = '#ffffff';
            performaBadge.style.setProperty('color', '#ffffff', 'important');
          }
          performaBadge.style.display = 'inline-block';
        }
      }
      
      if (complaintId && !isSpecialOption) {
        try { localStorage.removeItem(`performaRequired:${complaintId}`); } catch (err) {}
      }
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      if (!complaintId || !csrfToken) {
        console.error('Missing complaintId or CSRF token');
        return;
      }

      const oldStatus = select.dataset.oldStatus || select.value;
      const labelMap = { in_progress: 'In-Process', resolved: 'Addressed', assigned: 'Assigned', new: 'New', closed: 'Closed' };
      const confirmMsg = `Are you sure you want to change status to "${labelMap[newStatus] || newStatus}"?`;
      if (!skipConfirm) {
        if (!confirm(confirmMsg)) {
          select.value = oldStatus;
          return;
        }
      }

      // Preserve color if special options are selected (check localStorage to determine which special option)
      let savedOption = null;
      if (complaintId) {
        try { savedOption = localStorage.getItem(`performaRequired:${complaintId}`); } catch (err) { savedOption = null; }
      }
      // Determine special option type based on localStorage or previous selection
      const preserveColor = savedOption === 'work' || savedOption === 'maint' || savedOption === 'work_priced' || savedOption === 'maint_priced' || savedOption === 'product_na' ||
                           newStatus === 'work_performa' || newStatus === 'maint_performa' || newStatus === 'work_priced_performa' || newStatus === 'maint_priced_performa' || newStatus === 'product_na';
      // Store the special option type to restore color after fetch
      let specialOptionType = null;
      if (newStatus === 'work_performa' || savedOption === 'work') {
        specialOptionType = 'work_performa';
      } else if (newStatus === 'maint_performa' || savedOption === 'maint') {
        specialOptionType = 'maint_performa';
      } else if (newStatus === 'work_priced_performa' || savedOption === 'work_priced') {
        specialOptionType = 'work_priced_performa';
      } else if (newStatus === 'maint_priced_performa' || savedOption === 'maint_priced') {
        specialOptionType = 'maint_priced_performa';
      } else if (newStatus === 'product_na' || savedOption === 'product_na') {
        specialOptionType = 'product_na';
      }
      
      select.style.opacity = '0.6';
      select.disabled = true;

      fetch(`/admin/approvals/complaints/${complaintId}/update-status`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus, notes: `Status updated from approvals view` })
      })
      .then(async (response) => {
        const contentType = response.headers.get('content-type') || '';
        const isJson = contentType.includes('application/json');
        const data = isJson ? await response.json() : null;
        if (!response.ok) {
          const message = (data && (data.message || (data.errors && Object.values(data.errors)[0]?.[0]))) || `HTTP ${response.status}`;
          throw new Error(message);
        }
        return data;
      })
      .then(data => {
        const updated = data && data.complaint ? data.complaint : null;
        if (updated && updated.closed_at && newStatus === 'resolved') {
          const addressedDateCell = row?.querySelector('td:nth-child(3)');
          if (addressedDateCell) addressedDateCell.textContent = updated.closed_at;
          const statusCell = select.closest('td');
          // Remove arrow and circle if they exist
          const arrow = statusCell?.querySelector('i[data-feather="chevron-down"]');
          const circle = statusCell?.querySelector('.status-indicator');
          if (arrow) arrow.remove();
          if (circle) circle.remove();
          // Create simple Addressed badge without dropdown
          const badge = document.createElement('span');
          badge.className = 'badge';
          const resolvedColor = statusColors['resolved'];
          badge.style.cssText = `background-color: ${resolvedColor.bg}; color: #ffffff !important; padding: 4px 10px; font-size: 11px; font-weight: 600; border-radius: 4px; border: 1px solid ${resolvedColor.border}; width: 140px; height: 28px; display: inline-flex; align-items: center; justify-content: center;`;
          badge.style.setProperty('color', '#ffffff', 'important');
          badge.textContent = 'Addressed';
          // Clear performa badge
          const performaBadgeInRow = row?.querySelector('.performa-badge');
          if (performaBadgeInRow) {
            performaBadgeInRow.style.display = 'none';
            performaBadgeInRow.textContent = '';
          }
          // Replace select with badge
          const statusChip = select.closest('.status-chip');
          if (statusChip) {
            statusChip.innerHTML = '';
            statusChip.appendChild(badge);
            statusChip.style.cssText = `background-color: ${resolvedColor.bg}; color: ${resolvedColor.text}; border-color: ${resolvedColor.border}; width: 140px; height: 28px; justify-content: center;`;
          } else {
            select.replaceWith(badge);
          }
          
          // Show feedback button automatically when status becomes resolved
          const actionsCell = row?.querySelector('td:last-child');
          if (actionsCell) {
            const btnGroup = actionsCell.querySelector('.btn-group');
            if (btnGroup) {
              // Check if feedback button already exists
              const existingFeedbackBtn = btnGroup.querySelector('a[href*="/feedback/"]');
              if (!existingFeedbackBtn) {
                // Get complaint ID from the row - it's in the 4th column (Complaint ID)
                const complaintIdLink = row?.querySelector('td:nth-child(4) a');
                let complaintId = null;
                if (complaintIdLink) {
                  const href = complaintIdLink.getAttribute('href');
                  const match = href?.match(/\/(\d+)$/);
                  complaintId = match ? match[1] : null;
                }
                // Alternative: get from data attribute if available
                if (!complaintId && row) {
                  complaintId = row.getAttribute('data-complaint-id');
                }
                if (complaintId) {
                  // Create "Add Feedback" button
                  const feedbackBtn = document.createElement('a');
                  feedbackBtn.href = `/admin/complaints/${complaintId}/feedback/create`;
                  feedbackBtn.className = 'btn btn-outline-warning btn-sm';
                  feedbackBtn.title = 'Add Feedback';
                  feedbackBtn.style.cssText = 'padding: 3px 8px; border-color: #f59e0b !important; color: #f59e0b !important;';
                  feedbackBtn.innerHTML = '<i data-feather="message-square" style="width: 16px; height: 16px; color: #f59e0b;"></i>';
                  btnGroup.appendChild(feedbackBtn);
                  // Reinitialize feather icons
                  if (typeof feather !== 'undefined') {
                    feather.replace();
                  }
                }
              }
            }
          }
        } else {
          // Update color for other status changes
          // Restore dropdown value to in_progress and apply red color
          if (specialOptionType) {
            select.value = 'in_progress';
            updateStatusSelectColor(select, 'in_progress');
          } else {
            // Check if current select value is a special option to preserve their colors
            const currentSelectValue = select.value;
            if (currentSelectValue === 'product_na') {
              updateStatusSelectColor(select, 'product_na');
            } else if (currentSelectValue === 'work_priced_performa') {
              updateStatusSelectColor(select, 'work_priced_performa');
            } else if (currentSelectValue === 'maint_priced_performa') {
              updateStatusSelectColor(select, 'maint_priced_performa');
            } else if (currentSelectValue === 'work_performa') {
              updateStatusSelectColor(select, 'work_performa');
            } else if (currentSelectValue === 'maint_performa') {
              updateStatusSelectColor(select, 'maint_performa');
            } else if (currentSelectValue === 'un_authorized' || newStatus === 'un_authorized') {
              // Keep un_authorized value and color
              select.value = 'un_authorized';
              updateStatusSelectColor(select, 'un_authorized');
            } else if (currentSelectValue === 'pertains_to_ge_const_isld' || newStatus === 'pertains_to_ge_const_isld') {
              // Keep pertains_to_ge_const_isld value and color
              select.value = 'pertains_to_ge_const_isld';
              updateStatusSelectColor(select, 'pertains_to_ge_const_isld');
            } else {
              updateStatusSelectColor(select, newStatus);
            }
          }
        }
        showSuccess('Complaint status updated successfully!');
        if (select.isConnected) select.dataset.oldStatus = newStatus;
      })
      .catch(error => {
        console.error('Error updating status:', error);
        select.value = oldStatus;
        showError(error.message || 'Failed to update complaint status.');
      })
      .finally(() => {
        if (select.isConnected && select.value !== 'resolved') {
          select.style.opacity = '1';
          select.disabled = false;
          // Restore dropdown value to in_progress and apply red color (only for special performa options)
          if (specialOptionType) {
            select.value = 'in_progress';
            updateStatusSelectColor(select, 'in_progress');
          } else if (preserveColor) {
            const finalSelectValue = select.value;
            if (finalSelectValue === 'product_na') {
              updateStatusSelectColor(select, 'product_na');
            } else if (finalSelectValue === 'work_priced_performa') {
              updateStatusSelectColor(select, 'work_priced_performa');
            } else if (finalSelectValue === 'maint_priced_performa') {
              updateStatusSelectColor(select, 'maint_priced_performa');
            } else if (finalSelectValue === 'work_performa') {
              updateStatusSelectColor(select, 'work_performa');
            } else if (finalSelectValue === 'maint_performa') {
              updateStatusSelectColor(select, 'maint_performa');
            }
          } else if (newStatus === 'un_authorized') {
            // Keep un_authorized value and color
            select.value = 'un_authorized';
            updateStatusSelectColor(select, 'un_authorized');
          } else if (newStatus === 'pertains_to_ge_const_isld') {
            // Keep pertains_to_ge_const_isld value and color
            select.value = 'pertains_to_ge_const_isld';
            updateStatusSelectColor(select, 'pertains_to_ge_const_isld');
          }
        }
      });
    }
  });

  // Helpers to initialize UI after load/refresh
  function initPerformaBadges() {
    document.querySelectorAll('.performa-badge').forEach(function(b){
      // Only hide badges that are truly empty (no textContent and display:none)
      // Don't hide badges that are already rendered from server with content
      if (!b.textContent && b.style.display !== 'none') {
        b.style.display = 'none';
      }
    });
    // Restore persisted Performa Required selections per complaint
    // But only if badge doesn't already have content from server
    document.querySelectorAll('select.status-select[data-complaint-id]').forEach(function(sel){
      const complaintId = sel.getAttribute('data-complaint-id');
      if (!complaintId) return;
      
      const row = sel.closest('tr');
      const badge = row ? row.querySelector('.performa-badge') : null;
      if (!badge) return;
      
      // If badge already has content from server, don't override it
      if (badge.textContent && badge.textContent.trim() !== '') {
        return; // Server-rendered badge, keep it as is
      }
      
      let saved;
      try { saved = localStorage.getItem(`performaRequired:${complaintId}`); } catch (err) { saved = null; }
      if (!saved) return;
      
      // Restore based on saved value - check in order to prevent conflicts
      if (saved === 'priced') {
        badge.textContent = 'Maint/Work Priced';
        badge.style.backgroundColor = '#f59e0b';
        badge.style.color = '#ffffff';
        badge.style.setProperty('color', '#ffffff', 'important');
        badge.style.display = 'inline-block';
        sel.value = 'in_progress';
        updateStatusSelectColor(sel, 'in_progress');
      } else if (saved === 'product_na') {
        badge.textContent = 'Product N/A';
        badge.style.backgroundColor = '#6b7280';
        badge.style.color = '#ffffff';
        badge.style.setProperty('color', '#ffffff', 'important');
        badge.style.display = 'inline-block';
        sel.value = 'in_progress';
        updateStatusSelectColor(sel, 'in_progress');
      } else if (saved === 'work') {
        badge.textContent = 'Work Performa Required';
        badge.style.backgroundColor = '#60a5fa';
        badge.style.color = '#ffffff';
        badge.style.setProperty('color', '#ffffff', 'important');
        badge.style.display = 'inline-block';
        sel.value = 'in_progress';
        updateStatusSelectColor(sel, 'in_progress');
      } else if (saved === 'maint') {
        badge.textContent = 'Maintenance Performa Required';
        badge.style.backgroundColor = '#eab308';
        badge.style.color = '#ffffff';
        badge.style.setProperty('color', '#ffffff', 'important');
        badge.style.display = 'inline-block';
        sel.value = 'in_progress';
        updateStatusSelectColor(sel, 'in_progress');
      } else if (saved === 'work_priced') {
        badge.textContent = 'Work Performa Priced';
        badge.style.backgroundColor = '#9333ea';
        badge.style.color = '#ffffff';
        badge.style.setProperty('color', '#ffffff', 'important');
        badge.style.display = 'inline-block';
        sel.value = 'in_progress';
        updateStatusSelectColor(sel, 'in_progress');
      } else if (saved === 'maint_priced') {
        badge.textContent = 'Maintenance Performa Priced';
        badge.style.backgroundColor = '#ea580c';
        badge.style.color = '#ffffff';
        badge.style.setProperty('color', '#ffffff', 'important');
        badge.style.display = 'inline-block';
        sel.value = 'in_progress';
        updateStatusSelectColor(sel, 'in_progress');
      }
    });
  }

  function initStatusSelects() {
    document.querySelectorAll('.status-select').forEach(function(sel){
      if (!sel.dataset.oldStatus) sel.dataset.oldStatus = sel.value;
      // Check if there's a localStorage value - set dropdown value to in_progress but keep color red
      const complaintId = sel.getAttribute('data-complaint-id');
      if (complaintId) {
        let saved;
        try { saved = localStorage.getItem(`performaRequired:${complaintId}`); } catch (err) { saved = null; }
        // Set dropdown value to in_progress if special option exists
        if (saved === 'work' || saved === 'maint' || saved === 'priced' || saved === 'product_na') {
          sel.value = 'in_progress';
        }
      }
      // Initialize colors based on current status (always red for in_progress)
      const statusColor = sel.getAttribute('data-status-color');
      const currentValue = sel.value;
      if (statusColor) {
        updateStatusSelectColor(sel, statusColor);
      } else if (currentValue) {
        updateStatusSelectColor(sel, currentValue);
      }
    });
  }

  // Open Add Stock Modal
  function openAddStockModal(approvalId, category = null) {
    const modalBody = document.getElementById('addStockModalBody');
    if (!modalBody) {
      alert('Modal not found');
      return;
    }

    // Show loading
    modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Store category globally for use in fetch response
    window.currentComplaintCategory = category;

    // Fetch approval details
    fetch(`/admin/approvals/${approvalId}`, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
      if (data.success && data.approval && data.approval.items) {
        const items = data.approval.items;
        
        if (items.length === 0) {
          modalBody.innerHTML = '<div class="alert alert-info">No items found in this approval.</div>';
          return;
        }

        let itemsHtml = '<form id="addStockForm"><div class="table-responsive"><table class="table table-striped"><thead><tr><th>Category</th><th>Product</th><th>Total Stock</th><th>Request Stock</th></tr></thead><tbody>';

        // Store items globally for submission
        window.currentApprovalItems = items;

        items.forEach((item, index) => {
          const productName = item.spare_name || 'N/A';
          const category = item.category || 'N/A';
          const availableStock = item.available_stock || 0;
          const requestedQty = item.quantity_requested || 0;
          const spareId = item.spare_id || 0;
          
          // Format category display
          const categoryDisplay = {
            'electric': 'Electric',
            'technical': 'Technical',
            'service': 'Service',
            'billing': 'Billing',
            'water': 'Water Supply',
            'sanitary': 'Sanitary',
            'plumbing': 'Plumbing',
            'kitchen': 'Kitchen',
            'other': 'Other',
          };
          const catDisplay = categoryDisplay[category.toLowerCase()] || category.charAt(0).toUpperCase() + category.slice(1);
          
          itemsHtml += `
            <tr>
              <td>${catDisplay}</td>
              <td>${productName}</td>
              <td>
                <span class="badge ${availableStock > 0 ? 'bg-success' : 'bg-danger'}" style="font-size: 12px;">
                  ${availableStock}
                </span>
              </td>
              <td>
                <span class="badge bg-info" style="font-size: 12px;">
                  ${requestedQty}
                </span>
              </td>
            </tr>
          `;
        });

        itemsHtml += '</tbody></table></div></form>';
        modalBody.innerHTML = itemsHtml;
        
        // Show Submit button
        const submitBtn = document.getElementById('submitAddStockBtn');
        if (submitBtn) {
          submitBtn.style.display = 'inline-block';
        }
        
        // Replace feather icons
        feather.replace();
        
        // Show modal
        new bootstrap.Modal(document.getElementById('addStockModal')).show();
      } else {
        modalBody.innerHTML = '<div class="alert alert-danger">Error loading approval items.</div>';
      }
    })
    .catch(error => {
      console.error('Error:', error);
      modalBody.innerHTML = '<div class="alert alert-danger">Error loading approval details: ' + error.message + '</div>';
    });
  }

  // Submit Issue Stock Form
  function submitIssueStock() {
    // Use approval items from window.currentApprovalItems (loaded in modal)
    if (!window.currentApprovalItems || window.currentApprovalItems.length === 0) {
      alert('No items found. Please refresh and try again.');
      return;
    }

    console.log('submitIssueStock - currentApprovalId:', window.currentApprovalId);
    console.log('submitIssueStock - currentApprovalItems:', window.currentApprovalItems);

    // Validate and collect data from approval items
    const stockData = [];
    let hasError = false;
    let errorMessage = '';

    window.currentApprovalItems.forEach(item => {
      const spareId = item.spare_id || 0;
      const productName = item.spare_name || 'N/A';
      const availableStock = item.available_stock || 0;
      const requestedQty = item.quantity_requested || 0;
      const itemId = item.id || null;

      if (spareId === 0) {
        hasError = true;
        errorMessage = `Invalid product: ${productName}`;
        return;
      }

      if (requestedQty <= 0) {
        // Skip items with 0 or negative quantity
        return;
      }

      if (requestedQty > availableStock) {
        hasError = true;
        errorMessage = `Requested quantity (${requestedQty}) cannot exceed available stock (${availableStock}) for ${productName}`;
        return;
      }
      
      stockData.push({
        spare_id: spareId,
        item_id: itemId,
        approval_id: window.currentApprovalId || null,
        issue_quantity: requestedQty,
        product_name: productName,
        available_stock: availableStock
      });
    });

    if (hasError) {
      alert(errorMessage);
      return;
    }

    if (stockData.length === 0) {
      alert('Please add items with valid quantity using the form above.');
      return;
    }

    // Confirm before submitting
    const confirmMessage = `Are you sure you want to ISSUE stock for the following items?\n\n` +
      stockData.map(item => `${item.product_name}: ${item.issue_quantity} units (Available: ${item.available_stock})`).join('\n');
    
    if (!confirm(confirmMessage)) {
      return;
    }

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Disable submit button
    const submitBtn = document.getElementById('submitAddStockBtn');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Issuing Stock...';
    }

    // Log approval_id before sending
    console.log('Issuing stock with approval_id:', window.currentApprovalId);
    console.log('Stock data to send:', stockData);

    // Send requests for each item to ISSUE stock (decrease inventory)
    const promises = stockData.map(item => {
      const requestBody = {
        quantity: item.issue_quantity,
        item_id: item.item_id,
        approval_id: window.currentApprovalId || null,
        reason: `Stock issued from approval - Product: ${item.product_name}${authorityInfo}`
      };
      
      console.log('Sending request for item:', item.product_name, 'with approval_id:', requestBody.approval_id);
      
      return fetch(`/admin/spares/${item.spare_id}/issue-stock`, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(requestBody),
        credentials: 'same-origin'
      });
    });

    // Process all requests
    Promise.all(promises)
      .then(responses => Promise.all(responses.map(r => r.json())))
      .then(results => {
        const successCount = results.filter(r => r.success).length;
        const failedCount = results.length - successCount;

        if (failedCount === 0) {
          alert(`Successfully issued stock for all ${successCount} item(s)!`);
          bootstrap.Modal.getInstance(document.getElementById('addStockModal')).hide();
          // Optionally reload the page to refresh stock quantities
          // window.location.reload();
        } else {
          alert(`Issued stock for ${successCount} item(s), but ${failedCount} item(s) failed.`);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error issuing stock: ' + error.message);
      })
      .finally(() => {
        // Re-enable submit button
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = '<i data-feather="check-circle"></i> Issue Stock';
          feather.replace();
        }
      });
  }

  // Make functions globally accessible
  window.openAddStockModal = openAddStockModal;
  window.submitIssueStock = submitIssueStock;

  // Event delegation for Add Stock buttons (works for dynamically loaded buttons too)
  document.addEventListener('click', function(e) {
    const addStockBtn = e.target.closest('.add-stock-btn');
    if (addStockBtn) {
      e.preventDefault();
      e.stopPropagation();
      const approvalId = addStockBtn.getAttribute('data-approval-id') || addStockBtn.getAttribute('onclick')?.match(/\d+/)?.[0];
      const category = addStockBtn.getAttribute('data-category') || null;
      if (approvalId && window.openAddStockModal) {
        window.openAddStockModal(parseInt(approvalId), category);
      } else {
        console.error('Add Stock button clicked but approval ID or function not found', {
          approvalId: approvalId,
          hasFunction: !!window.openAddStockModal
        });
      }
    }
    
    // Event delegation for Submit button in Add Stock Modal
    const submitBtn = e.target.closest('#submitAddStockBtn');
    if (submitBtn && !submitBtn.disabled) {
      e.preventDefault();
      e.stopPropagation();
      console.log('Submit button clicked in Add Stock Modal');
      if (window.submitAddStock) {
        window.submitAddStock();
      } else {
        console.error('submitAddStock function not found');
        alert('Error: Submit function not available. Please refresh the page.');
      }
    }
  });

  // Initialize rows on page load
  document.addEventListener('DOMContentLoaded', function() {
    initPerformaBadges();
    // Run initStatusSelects after initPerformaBadges to ensure colors are set correctly
    setTimeout(function() {
      initStatusSelects();
    }, 100);
  });

  // Store initial status on page load
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.status-select').forEach(select => {
      select.dataset.oldStatus = select.value;
    });
    
    // Replace feather icons
    feather.replace();
    
    // Check for view_complaint query parameter and open complaint modal automatically
    const urlParams = new URLSearchParams(window.location.search);
    const viewComplaintId = urlParams.get('view_complaint');
    if (viewComplaintId) {
      // Remove the parameter from URL to clean it up
      urlParams.delete('view_complaint');
      const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
      window.history.replaceState({}, '', newUrl);
      
      // Open complaint modal after a short delay to ensure page is fully loaded
      setTimeout(() => {
        if (typeof viewComplaint === 'function') {
          viewComplaint(viewComplaintId);
        }
      }, 300);
    }
  });

</script>
@endpush
