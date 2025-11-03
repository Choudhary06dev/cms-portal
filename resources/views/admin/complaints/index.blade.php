@extends('layouts.sidebar')

@section('title', 'Complaints Management — CMS Admin')

@section('content')
    <!-- PAGE HEADER -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white mb-2">Complaints Management</h2>
                <p class="text-light">Track and manage customer complaints</p>
            </div>
            <a href="{{ route('admin.complaints.create') }}" class="btn btn-accent">
                <i data-feather="plus" class="me-2"></i>Add Complaint
            </a>
        </div>
    </div>

    <!-- FILTERS -->
    <div class="card-glass mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0 text-white">
                <i data-feather="filter" class="me-2"></i>Filters
            </h5>
        </div>
        <div class="card-body">
            <form id="complaintsFiltersForm" method="GET" action="{{ route('admin.complaints.index') }}">
                <div class="row g-2 align-items-end">
                <div class="col-12 col-md-2">
                    <input type="text" class="form-control" id="searchInput" name="search" placeholder="Search complaints..." 
                           value="{{ request('search') }}" oninput="handleComplaintsSearchInput()">
                </div>
                <div class="col-6 col-md-2">
                    <select class="form-select" name="priority" onchange="submitComplaintsFilters()">
                        <option value="" {{ request('priority') ? '' : 'selected' }}>All Priority</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select class="form-select" name="category" onchange="submitComplaintsFilters()">
                        <option value="" {{ request('category') ? '' : 'selected' }}>All Categories</option>
                        @if(isset($categories) && $categories->count() > 0)
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select class="form-select" name="client_id" onchange="submitComplaintsFilters()">
                        <option value="" {{ request('client_id') ? '' : 'selected' }}>All Clients</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->client_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select class="form-select" name="assigned_employee_id" onchange="submitComplaintsFilters()">
                        <option value="" {{ request('assigned_employee_id') ? '' : 'selected' }}>All Employees</option>
                        @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('assigned_employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="row g-2 align-items-end mt-2">
                <div class="col-6 col-md-2">
                    <input type="date" class="form-control" name="date_from" 
                           value="{{ request('date_from') }}" placeholder="From Date" onchange="submitComplaintsFilters()">
                </div>
                <div class="col-6 col-md-2">
                    <input type="date" class="form-control" name="date_to" 
                           value="{{ request('date_to') }}" placeholder="To Date" onchange="submitComplaintsFilters()">
                </div>
            </div>
            </form>
        </div>
    </div>

    <!-- COMPLAINTS TABLE -->
    <div class="card-glass" style="padding: 0 !important;">
        <div class="card-header" style="padding: 12px 18px;">
            <h5 class="card-title mb-0 text-white">
                <i data-feather="list" class="me-2"></i>Complaints List
            </h5>
        </div>
        <div class="card-body" style="padding: 0 !important; margin: 0 !important;">
            <div class="table-responsive-xl" style="margin: 0 !important; padding: 0 !important;">
            <table class="table table-dark table-sm table-compact" style="margin: 0 !important; border-left: none !important;">
                <thead>
                    <tr>
                        <th style="width: 40px; padding-left: 8px !important;">#</th>
                        <th style="width: 130px;">Apply Date/Time</th>
                        <th style="width: 130px;">Completion Time</th>
                        <th style="width: 100px;">Complaint ID</th>
                        <th style="width: 120px;">Complainant Name</th>
                        <th style="width: 150px;">Address</th>
                        <th style="width: 250px;">Complaint Nature & Type</th>
                        <th style="width: 100px;">Mobile No.</th>
                        <th style="width: 80px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="complaintsTableBody">
                    @forelse($complaints as $complaint)
                        <tr>
                            <td>{{ ($complaints->currentPage() - 1) * $complaints->perPage() + $loop->iteration }}</td>
                            <td style="white-space: nowrap;">{{ $complaint->created_at ? $complaint->created_at->format('d-m-Y H:i:s') : '' }}</td>
                            <td style="white-space: nowrap;">{{ $complaint->closed_at ? $complaint->closed_at->format('d-m-Y H:i:s') : '' }}</td>
                            <td style="white-space: nowrap;">
                                <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="text-decoration-none" style="color: #3b82f6;">
                                    {{ $complaint->complaint_id ?? $complaint->id }}
                                </a>
                            </td>
                            <td style="white-space: nowrap;">{{ $complaint->client->client_name ?? 'N/A' }}</td>
                            <td>{{ $complaint->client->address ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $category = $complaint->category ?? 'N/A';
                                    $department = $complaint->department ?? '';
                                    
                                    // Get product name from spare parts
                                    $productName = '';
                                    if ($complaint->spareParts && $complaint->spareParts->count() > 0) {
                                        $firstSpare = $complaint->spareParts->first();
                                        if ($firstSpare && $firstSpare->spare) {
                                            $productName = $firstSpare->spare->item_name ?? '';
                                            // If multiple products, show count
                                            if ($complaint->spareParts->count() > 1) {
                                                $productName .= ' (+' . ($complaint->spareParts->count() - 1) . ')';
                                            }
                                        }
                                    }
                                    
                                    // Category to REQ type mapping
                                    $reqTypeMap = [
                                        'electric' => 'ELECTRECION REQ',
                                        'technical' => 'TECHNICAL REQ',
                                        'service' => 'SERVICE REQ',
                                        'billing' => 'BILLING REQ',
                                        'water' => 'PIPE FITTER REQ',
                                        'sanitary' => 'SANITARY REQ',
                                        'plumbing' => 'PLUMBING REQ',
                                        'kitchen' => 'KITCHEN REQ',
                                        'other' => 'OTHER REQ',
                                    ];
                                    
                                    $reqType = $reqTypeMap[strtolower($category)] ?? strtoupper($category) . ' REQ';
                                    
                                    // Format display text with category and product name
                                    if ($department) {
                                        // If department exists, use format: "B&R - I - Product Name - MASSON REQ"
                                        // Check for department patterns
                                        if (strpos(strtoupper($department), 'B&R') !== false) {
                                            if ($productName) {
                                                $displayText = $department . ' - ' . $productName . ' - MASSON REQ';
                                            } else {
                                                $displayText = $department . ' - MASSON REQ';
                                            }
                                        } else {
                                            if ($productName) {
                                                $displayText = $department . ' - ' . $productName . ' - ' . $reqType;
                                            } else {
                                                $displayText = $department . ' - ' . $reqType;
                                            }
                                        }
                                    } else {
                                        // Use category-based format: "Electric - Product Name - ELECTRECION REQ"
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
                                        $catDisplay = $categoryDisplay[strtolower($category)] ?? ucfirst($category);
                                        
                                        if ($productName) {
                                            $displayText = $catDisplay . ' - ' . $productName . ' - ' . $reqType;
                                        } else {
                                            $displayText = $catDisplay . ' - ' . $reqType;
                                        }
                                    }
                                @endphp
                                <div class="text-white">{{ $displayText }}</div>
                            </td>
                            <td>{{ $complaint->client->phone ?? 'N/A' }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="btn btn-outline-success btn-sm" title="View Details" style="padding: 3px 8px;">
                                        <i data-feather="eye" style="width: 16px; height: 16px;"></i>
                                    </a>
                                    <a href="{{ route('admin.complaints.edit', $complaint->id) }}" class="btn btn-outline-primary btn-sm" title="Edit" style="padding: 3px 8px;">
                                        <i data-feather="edit" style="width: 16px; height: 16px;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i data-feather="alert-circle" class="feather-lg mb-2"></i>
                                <div>No complaints found</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>

            <!-- PAGINATION -->
            <div class="d-flex justify-content-center mt-3 px-3" id="complaintsPagination">
                <div>
                    {{ $complaints->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Table styles - Compact */
        .table-compact {
            margin: 0 !important;
            border-collapse: collapse;
            width: 100% !important;
        }
        .table-compact th {
            white-space: nowrap;
            padding: 4px 6px;
            font-size: 13px;
            font-weight: 600;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        }
        .table-compact th:first-child {
            padding-left: 8px !important;
        }
        .table-compact td {
            padding: 4px 6px;
            font-size: 13px;
            vertical-align: middle;
        }
        .table-compact td:first-child {
            padding-left: 8px !important;
        }
        /* Make complaint ID and name columns compact */
        .table-compact th:nth-child(4),
        .table-compact td:nth-child(4),
        .table-compact th:nth-child(5),
        .table-compact td:nth-child(5) {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        /* Ensure table fits properly */
        .table-compact {
            margin-bottom: 0;
            margin-left: 0;
            width: 100%;
        }
        /* Remove left spacing from table container */
        .card-body > .table-responsive-xl {
            margin-left: 0 !important;
            padding-left: 0 !important;
            margin-right: 0 !important;
        }
        /* Remove all padding from card-glass and card-body for table */
        .card-glass[style*="padding: 0"] {
            padding: 0 !important;
            margin: 0 !important;
        }
        .card-glass[style*="padding: 0"] > .card-body {
            padding: 0 !important;
            margin: 0 !important;
        }
        /* Ensure table container has no spacing */
        .card-glass[style*="padding: 0"] > .card-body > .table-responsive-xl {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        /* Keep padding for pagination only */
        #complaintsPagination {
            padding-left: 1rem;
            padding-right: 1rem;
            padding-top: 1rem;
        }
        /* Table should fill full width */
        .card-glass[style*="padding: 0"] .table-compact {
            margin: 0 !important;
            width: 100% !important;
            border-left: none !important;
            border-right: none !important;
        }
        /* Address column with ellipsis for long text */
        .table-compact td:nth-child(6) {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        /* Complaint Nature column - allow wrap but limit height */
        .table-compact td:nth-child(7) {
            max-width: 250px;
            word-wrap: break-word;
            line-height: 1.3;
        }
        .category-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .category-technical {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .category-service {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .category-billing {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        .category-other {
            background: rgba(107, 114, 128, 0.2);
            color: #6b7280;
        }

        .priority-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .priority-low {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .priority-medium {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        .priority-high {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .priority-urgent {
            background: rgba(139, 92, 246, 0.2);
            color: #8b5cf6;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-new {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .status-assigned {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        .status-in-progress {
            background: rgba(168, 85, 247, 0.2);
            color: #a855f7;
        }

        .status-resolved {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .status-closed {
            background: rgba(107, 114, 128, 0.2);
            color: #6b7280;
        }

        /* Pagination styles are now centralized in components/pagination.blade.php */
        
        /* Table column borders - vertical lines between columns */
        .table-dark th,
        .table-dark td {
            border-right: 1px solid rgba(255, 255, 255, 0.15);
            border-left: none;
        }
        
        .table-dark th:first-child,
        .table-dark td:first-child {
            border-left: none;
        }
        
        .table-dark th:last-child,
        .table-dark td:last-child {
            border-right: none;
        }
    </style>
@endpush

<!-- Complaint Modal -->
<div class="modal fade" id="complaintModal" tabindex="-1" aria-labelledby="complaintModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="complaintModalLabel">Complaint Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="complaintModalBody">
                <!-- Complaint details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn btn-info" id="printSlipBtn" target="_blank" style="display: none;">
                    <i data-feather="printer"></i> Print Slip
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this complaint? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Complaint</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        feather.replace();

        // Debounced search input handler
        let complaintsSearchTimeout = null;
        function handleComplaintsSearchInput() {
            if (complaintsSearchTimeout) clearTimeout(complaintsSearchTimeout);
            complaintsSearchTimeout = setTimeout(() => {
                loadComplaints();
            }, 500);
        }

        // Auto-submit for select filters
        function submitComplaintsFilters() {
            loadComplaints();
        }

        // Load Complaints via AJAX
        function loadComplaints(url = null) {
            const form = document.getElementById('complaintsFiltersForm');
            if (!form) return;
            
            const formData = new FormData(form);
            const params = new URLSearchParams();
            
            if (url) {
                const urlObj = new URL(url, window.location.origin);
                urlObj.searchParams.forEach((value, key) => {
                    params.append(key, value);
                });
            } else {
                for (const [key, value] of formData.entries()) {
                    if (value) {
                        params.append(key, value);
                    }
                }
            }

            const tbody = document.getElementById('complaintsTableBody');
            const paginationContainer = document.getElementById('complaintsPagination');
            
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4"><div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
            }

            fetch(`{{ route('admin.complaints.index') }}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
                credentials: 'same-origin'
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const newTbody = doc.querySelector('#complaintsTableBody');
                const newPagination = doc.querySelector('#complaintsPagination');
                
                if (newTbody && tbody) {
                    tbody.innerHTML = newTbody.innerHTML;
                    feather.replace();
                }
                
                if (newPagination && paginationContainer) {
                    paginationContainer.innerHTML = newPagination.innerHTML;
                }

                const newUrl = `{{ route('admin.complaints.index') }}?${params.toString()}`;
                window.history.pushState({path: newUrl}, '', newUrl);
            })
            .catch(error => {
                console.error('Error loading complaints:', error);
                if (tbody) {
                    tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-danger">Error loading data. Please refresh the page.</td></tr>';
                }
            });
        }

        // Handle pagination clicks
        document.addEventListener('click', function(e) {
            const paginationLink = e.target.closest('#complaintsPagination a');
            if (paginationLink && paginationLink.href && !paginationLink.href.includes('javascript:')) {
                e.preventDefault();
                loadComplaints(paginationLink.href);
            }
        });

        // Handle browser back/forward buttons
        window.addEventListener('popstate', function(e) {
            if (e.state && e.state.path) {
                loadComplaints(e.state.path);
            } else {
                loadComplaints();
            }
        });

        // Complaint Functions
        function viewComplaint(complaintId) {
            currentComplaintId = complaintId;

            // Modal buttons removed - only show and print functionality

            // Show loading state
            const modalBody = document.getElementById('complaintModalBody');
            modalBody.innerHTML =
                '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Fetch complaint data
            fetch(`/admin/complaints/${complaintId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        const complaint = data.complaint;

                        // Set print slip button
                        const printSlipBtn = document.getElementById('printSlipBtn');
                        if (printSlipBtn) {
                            printSlipBtn.href = `/admin/complaints/${complaintId}/print-slip`;
                            printSlipBtn.style.display = 'inline-block';
                        }

                        modalBody.innerHTML = `
                    <div class="row" style="color: var(--text-primary);">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3" style="color: var(--text-primary);">Complaint Information</h6>
                            <div class="mb-3" style="color: var(--text-primary);">
                                <span style="color: var(--text-muted);">Title:</span>
                                <span style="color: var(--text-secondary);" class="ms-2">${complaint.title || 'N/A'}</span>
                            </div>
                            <div class="mb-3" style="color: var(--text-primary);">
                                <span style="color: var(--text-muted);">Category:</span>
                                <span class="ms-2">
                                    <span class="category-badge category-${complaint.category ? complaint.category.toLowerCase() : 'other'}">
                                        ${complaint.category ? complaint.category.charAt(0).toUpperCase() + complaint.category.slice(1) : 'N/A'}
                                    </span>
                                </span>
                            </div>
                            <div class="mb-3" style="color: var(--text-primary);">
                                <span style="color: var(--text-muted);">Priority:</span>
                                <span class="ms-2">
                                    <span class="priority-badge priority-${complaint.priority ? complaint.priority.toLowerCase() : 'low'}">
                                        ${complaint.priority ? complaint.priority.charAt(0).toUpperCase() + complaint.priority.slice(1) : 'N/A'}
                                    </span>
                                </span>
                            </div>
                            <div class="mb-3" style="color: var(--text-primary);">
                                <span style="color: var(--text-muted);">Status:</span>
                                <span class="ms-2">
                                    <span class="badge bg-${complaint.status === 'new' ? 'primary' : complaint.status === 'assigned' ? 'warning' : complaint.status === 'in_progress' ? 'info' : complaint.status === 'resolved' ? 'success' : 'secondary'}">
                                        ${complaint.status ? complaint.status.charAt(0).toUpperCase() + complaint.status.slice(1) : 'N/A'}
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3" style="color: var(--text-primary);">Client & Assignment</h6>
                            <div class="mb-3" style="color: var(--text-primary);">
                                <span style="color: var(--text-muted);">Client:</span>
                                <span style="color: var(--text-secondary);" class="ms-2">${complaint.client ? complaint.client.client_name : 'N/A'}</span>
                            </div>
                            <div class="mb-3" style="color: var(--text-primary);">
                                <span style="color: var(--text-muted);">Assigned To:</span>
                                <span style="color: var(--text-secondary);" class="ms-2">${complaint.assigned_employee ? complaint.assigned_employee.name : 'Unassigned'}</span>
                            </div>
                            <div class="mb-3" style="color: var(--text-primary);">
                                <span style="color: var(--text-muted);">Created:</span>
                                <span style="color: var(--text-secondary);" class="ms-2">${complaint.created_at ? new Date(complaint.created_at).toLocaleDateString() : 'N/A'}</span>
                            </div>
                            <div class="mb-3" style="color: var(--text-primary);">
                                <span style="color: var(--text-muted);">Last Updated:</span>
                                <span style="color: var(--text-secondary);" class="ms-2">${complaint.updated_at ? new Date(complaint.updated_at).toLocaleDateString() : 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="fw-bold mb-3" style="color: var(--text-primary);">Description</h6>
                            <div class="card" style="background-color: var(--bg-secondary); border: 1px solid var(--border-primary);">
                                <div class="card-body">
                                    <p style="color: var(--text-primary);">${complaint.description || 'No description provided'}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                        document.getElementById('complaintModalLabel').textContent = 'Complaint Details';
                        new bootstrap.Modal(document.getElementById('complaintModal')).show();
                    } else {
                        modalBody.innerHTML = '<div class="alert alert-danger">Error: ' + (data.message ||
                            'Unknown error occurred') + '</div>';
                        new bootstrap.Modal(document.getElementById('complaintModal')).show();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = '<div class="alert alert-danger">Error loading complaint details: ' + error
                        .message + '</div>';
                    new bootstrap.Modal(document.getElementById('complaintModal')).show();
                });
        }

        function editComplaint(complaintId) {
            // Redirect to edit page
            window.location.href = `/admin/complaints/${complaintId}/edit`;
        }

  function deleteComplaint(complaintId) {
    if (confirm('Are you sure you want to delete this complaint?')) {
      alert('Delete complaint functionality coming soon!');
    }
  }
</script>
@endpush
