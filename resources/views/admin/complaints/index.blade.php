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
    <div class="card-glass mb-4" style="display: inline-block; width: fit-content;">
        <div class="card-header">
            <h5 class="card-title mb-0 text-white">
                <i data-feather="filter" class="me-2"></i>Filters
            </h5>
        </div>
        <div class="card-body">
            <form id="complaintsFiltersForm" method="GET" action="{{ route('admin.complaints.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label small mb-1" style="font-size: 0.8rem; color: #000000 !important; font-weight: 500;">Search</label>
                        <input type="text" class="form-control" id="searchInput" name="search" placeholder="Name or ID..." 
                               value="{{ request('search') }}" oninput="handleComplaintsSearchInput()" style="font-size: 0.9rem; width: 180px;">
                    </div>
                    <div class="col-auto">
                        <label class="form-label small mb-1" style="font-size: 0.8rem; color: #000000 !important; font-weight: 500;">Priority</label>
                        <select class="form-select" name="priority" onchange="submitComplaintsFilters()" style="font-size: 0.9rem; width: 140px;">
                            <option value="" {{ request('priority') ? '' : 'selected' }}>All</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label small mb-1" style="font-size: 0.8rem; color: #000000 !important; font-weight: 500;">Category</label>
                        <select class="form-select" name="category" onchange="submitComplaintsFilters()" style="font-size: 0.9rem; width: 140px;">
                            <option value="" {{ request('category') ? '' : 'selected' }}>All</option>
                            @if(isset($categories) && $categories->count() > 0)
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label small mb-1" style="font-size: 0.8rem; color: #000000 !important; font-weight: 500;">Employee</label>
                        <select class="form-select" name="assigned_employee_id" onchange="submitComplaintsFilters()" style="font-size: 0.9rem; width: 170px;">
                            <option value="" {{ request('assigned_employee_id') ? '' : 'selected' }}>All</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('assigned_employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}@if($employee->designation) ({{ $employee->designation }})@endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label small mb-1" style="font-size: 0.8rem; color: #000000 !important; font-weight: 500;">From</label>
                        <input type="date" class="form-control" name="date_from" 
                               value="{{ request('date_from') }}" onchange="submitComplaintsFilters()" style="font-size: 0.9rem; width: 150px;">
                    </div>
                    <div class="col-auto">
                        <label class="form-label small mb-1" style="font-size: 0.8rem; color: #000000 !important; font-weight: 500;">To</label>
                        <input type="date" class="form-control" name="date_to" 
                               value="{{ request('date_to') }}" onchange="submitComplaintsFilters()" style="font-size: 0.9rem; width: 150px;">
                    </div>
                    <div class="col-auto">
                        <label class="form-label small mb-1" style="font-size: 0.8rem; color: #000000 !important; font-weight: 500;">Status</label>
                        <select class="form-select" name="status" onchange="submitComplaintsFilters()" style="font-size: 0.9rem; width: 140px;">
                            <option value="" {{ request('status') ? '' : 'selected' }}>All</option>
                            <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                            <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="work_performa" {{ request('status') == 'work_performa' ? 'selected' : '' }}>Work Performa</option>
                            <option value="maint_performa" {{ request('status') == 'maint_performa' ? 'selected' : '' }}>Maintenance Performa</option>
                            <option value="work_priced_performa" {{ request('status') == 'work_priced_performa' ? 'selected' : '' }}>Work Performa Priced</option>
                            <option value="maint_priced_performa" {{ request('status') == 'maint_priced_performa' ? 'selected' : '' }}>Maintenance Performa Priced</option>
                            <option value="product_na" {{ request('status') == 'product_na' ? 'selected' : '' }}>Product N/A</option>
                            <option value="un_authorized" {{ request('status') == 'un_authorized' ? 'selected' : '' }}>Un-Authorized</option>
                            <option value="pertains_to_ge_const_isld" {{ request('status') == 'pertains_to_ge_const_isld' ? 'selected' : '' }}>Pertains to GE(N) Const Isld</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label small text-muted mb-1" style="font-size: 0.8rem;">&nbsp;</label>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetComplaintsFilters()" style="font-size: 0.9rem; padding: 0.35rem 0.8rem;">
                            <i data-feather="refresh-cw" class="me-1" style="width: 14px; height: 14px;"></i>Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- COMPLAINTS TABLE -->
    <div class="card-glass" style="padding: 0 !important; border-radius: 12px; overflow: hidden;">
        <div class="card-header" style="padding: 12px 18px; border-radius: 12px 12px 0 0;">
            <h5 class="card-title mb-0 text-white">
                <i data-feather="list" class="me-2"></i>Complaints List
            </h5>
        </div>
        <div class="card-body" style="padding: 0 !important; margin: 0 !important;">
            <div class="table-responsive-xl" style="margin: 0 !important; padding: 0 !important; border-radius: 0 0 12px 12px; overflow: hidden;">
            <table class="table table-dark table-sm table-compact" style="margin: 0 !important; border-left: none !important; border-radius: 0 0 12px 12px;">
                <thead>
                    <tr>
                        <th style="width: 100px;">Complaint ID</th>
                        <th style="width: 130px;">Registration Date/Time</th>
                        <th style="width: 130px; text-align: center;">Completion Time</th>
                        <th style="width: 120px;">Complainant Name</th>
                        <th style="width: 150px;">Address</th>
                        <th style="width: 250px;">Complaint Nature & Type</th>
                        <th style="width: 100px;">Phone No.</th>
                        <th style="width: 80px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="complaintsTableBody">
                    @forelse($complaints as $complaint)
                        <tr>
                            <td style="white-space: nowrap;">
                                <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="text-decoration-none" style="color: #3b82f6;">
                                    {{ (int)($complaint->complaint_id ?? $complaint->id) }}
                                </a>
                            </td>
                            <td style="white-space: nowrap;">{{ $complaint->created_at ? $complaint->created_at->timezone('Asia/Karachi')->format('M d, Y H:i:s') : '' }}</td>
                            <td style="white-space: nowrap; text-align: {{ $complaint->closed_at ? 'left' : 'center' }};">{{ $complaint->closed_at ? $complaint->closed_at->timezone('Asia/Karachi')->format('M d, Y H:i:s') : '-' }}</td>
                            <td style="white-space: nowrap;">{{ $complaint->client->client_name ?? 'N/A' }}</td>
                            <td>{{ $complaint->client->address ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $category = $complaint->category ?? 'N/A';
                                    $designation = $complaint->assignedEmployee->designation ?? 'N/A';
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
                                    $displayText = $catDisplay . ' - ' . $designation;
                                @endphp
                                <div class="text-white" style="font-weight: normal;">{{ $displayText }}</div>
                            </td>
                            <td>{{ $complaint->client->phone ?? 'N/A' }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button onclick="viewComplaint({{ $complaint->id }})" class="btn btn-outline-success btn-sm" title="View Details" style="padding: 3px 8px;">
                                        <i data-feather="eye" style="width: 16px; height: 16px;"></i>
                                    </button>
                                    <a href="{{ route('admin.complaints.edit', $complaint->id) }}" class="btn btn-outline-primary btn-sm" title="Edit" style="padding: 3px 8px;">
                                        <i data-feather="edit" style="width: 16px; height: 16px;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i data-feather="alert-circle" class="feather-lg mb-2"></i>
                                <div>No complaints found</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>

            <!-- TOTAL RECORDS -->
            <div id="complaintsTableFooter" class="text-center py-2 mt-2" style="background-color: rgba(59, 130, 246, 0.2); border-top: 2px solid #3b82f6; border-radius: 0 0 8px 8px;">
                <strong style="color: #ffffff; font-size: 14px;">
                    Total Records: {{ $complaints->total() }}
                </strong>
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
            border-radius: 0 0 12px 12px;
            overflow: hidden;
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
            border-radius: 12px !important;
            overflow: hidden !important;
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
        /* Completion Time column - center align header */
        .table-compact th:nth-child(3) {
            text-align: center;
        }
        /* Completion Time cells - default to center, but inline style will override for dates */
        .table-compact td:nth-child(3) {
            text-align: center;
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
        
        /* Blur effect for background when modal is open */
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
        
        /* Completely remove/hide Bootstrap backdrop */
        body.modal-open-blur .modal-backdrop,
        #complaintModal.modal.show ~ .modal-backdrop,
        #complaintModal.modal.show + .modal-backdrop,
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
        #complaintModal {
            z-index: 1055 !important;
        }
        
        #complaintModal .modal-dialog {
            z-index: 1055 !important;
            position: relative;
        }
        
        #complaintModal .modal-content {
            max-height: 90vh;
            overflow-y: auto;
            z-index: 1055 !important;
            position: relative;
        }
        
        #complaintModal .modal-body {
            padding: 1.5rem;
        }
        
        #complaintModal .btn-close {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            padding: 0.5rem !important;
            opacity: 1 !important;
        }
        
        #complaintModal .btn-close:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
    </style>
@endpush

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
        
        let currentComplaintId = null;

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
            const footerContainer = document.getElementById('complaintsTableFooter');
            
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4"><div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
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
                const newFooter = doc.querySelector('#complaintsTableFooter');
                
                if (newTbody && tbody) {
                    tbody.innerHTML = newTbody.innerHTML;
                    feather.replace();
                }
                
                if (newPagination && paginationContainer) {
                    paginationContainer.innerHTML = newPagination.innerHTML;
                    // Re-initialize feather icons after pagination update
                    feather.replace();
                }
                
                // Update total records footer with filtered count
                if (newFooter && footerContainer) {
                    footerContainer.innerHTML = newFooter.innerHTML;
                }

                const newUrl = `{{ route('admin.complaints.index') }}?${params.toString()}`;
                window.history.pushState({path: newUrl}, '', newUrl);
            })
            .catch(error => {
                console.error('Error loading complaints:', error);
                if (tbody) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger">Error loading data. Please refresh the page.</td></tr>';
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

        // Reset filters function
        function resetComplaintsFilters() {
            const form = document.getElementById('complaintsFiltersForm');
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
            window.location.href = '{{ route('admin.complaints.index') }}';
        }

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
                    const seenComments = new Set();
                    
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
