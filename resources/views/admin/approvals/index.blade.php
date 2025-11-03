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
<div class="card-glass mb-4">
  <form id="approvalsFiltersForm" method="GET" action="{{ route('admin.approvals.index') }}" onsubmit="event.preventDefault(); submitApprovalsFilters(event); return false;">
  <div class="row g-2 align-items-end">
    <div class="col-auto">
      <label class="form-label small text-muted mb-1" style="font-size: 0.8rem;">Search</label>
      <input type="text" class="form-control" id="searchInput" name="search" placeholder="Complaint ID or Address..." 
             value="{{ request('search') }}" autocomplete="off" style="font-size: 0.9rem; width: 200px;">
    </div>
    <div class="col-auto">
      <label class="form-label small text-muted mb-1" style="font-size: 0.8rem;">From Date</label>
      <input type="date" class="form-control" name="complaint_date" 
             value="{{ request('complaint_date') }}" placeholder="Select Date" autocomplete="off" style="font-size: 0.9rem; width: 150px;">
    </div>
    <div class="col-auto">
      <label class="form-label small text-muted mb-1" style="font-size: 0.8rem;">To Date</label>
      <input type="date" class="form-control" name="date_to" 
             value="{{ request('date_to') }}" placeholder="End Date" autocomplete="off" style="font-size: 0.9rem; width: 150px;">
    </div>
    <div class="col-auto">
      <label class="form-label small text-muted mb-1" style="font-size: 0.8rem;">Category</label>
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
          <th>Mobile No.</th>
          <th>Status</th>
          <th>Actions</th>
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
          $department = $complaint->department ?? '';
          
          // Get product name from spare parts
          $productName = '';
          if ($complaint->spareParts && $complaint->spareParts->count() > 0) {
            $firstSpare = $complaint->spareParts->first();
            if ($firstSpare && $firstSpare->spare) {
              $productName = $firstSpare->spare->item_name ?? '';
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
          
          // Convert 'new' status to 'assigned' for display
          $rawStatus = $complaint->status ?? 'new';
          $complaintStatus = ($rawStatus == 'new') ? 'assigned' : $rawStatus;
          $statusDisplay = $complaintStatus == 'in_progress' ? 'In-Process' : 
                          ($complaintStatus == 'resolved' ? 'Addressed' : 
                          ucfirst(str_replace('_', ' ', $complaintStatus)));
        @endphp
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $complaint->created_at ? $complaint->created_at->format('d-m-Y H:i:s') : 'N/A' }}</td>
          <td>{{ $complaint->closed_at ? $complaint->closed_at->format('d-m-Y H:i:s') : '' }}</td>
          <td>
            <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="text-decoration-none" style="color: #3b82f6;">
              {{ $complaint->complaint_id ?? $complaint->id }}
            </a>
          </td>
          <td>{{ $complaint->client->client_name ?? 'N/A' }}</td>
          <td>{{ $complaint->client->address ?? 'N/A' }}</td>
          <td>
            <div class="text-white fw-bold">{{ $displayText }}</div>
          </td>
          <td>{{ $complaint->client->phone ?? 'N/A' }}</td>
          <td>
            @if($complaintStatus == 'resolved')
              <span class="badge" style="background-color: #22c55e; color: white; padding: 8px 16px; font-size: 13px; font-weight: 600; border-radius: 6px;">
                Addressed
              </span>
            @else
              <select class="form-select form-select-sm status-select" 
                      data-complaint-id="{{ $complaint->id }}"
                      data-actual-status="{{ $rawStatus }}"
                      style="min-width: 120px; background-color: rgba(239, 68, 68, 0.25); color: #ef4444; border: 1px solid #ef4444; font-weight: 600; border-radius: 4px;">
                <option value="assigned" {{ $complaintStatus == 'assigned' ? 'selected' : '' }}>Assigned</option>
                <option value="in_progress" {{ $complaintStatus == 'in_progress' ? 'selected' : '' }}>In-Process</option>
                <option value="resolved" {{ $complaintStatus == 'resolved' ? 'selected' : '' }}>Addressed</option>
              </select>
            @endif
          </td>
          <td>
            <div class="btn-group" role="group">
              <a href="{{ route('admin.approvals.show', $approval->id) }}" class="btn btn-outline-info btn-sm" title="View Details">
                <i data-feather="eye"></i>
              </a>
            </div>
          </td>
        </tr>
        @endif
          @empty
        <tr>
          <td colspan="10" class="text-center py-4">
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

@endsection

@push('styles')
<style>
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
  
  .type-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  .type-spare { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
  
  .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  .status-pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
  .status-approved { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
  .status-rejected { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
  
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
  
</style>
@endpush

@push('scripts')
<script>
  feather.replace();

  // Global variables
  let currentApprovalId = null;
  let isProcessing = false;

  // viewApproval function removed - using direct link to show.blade.php page instead

  function approveRequest(approvalId) {
    if (confirm('Are you sure you want to approve this request?')) {
      const remarks = prompt('Enter approval remarks (optional):');
      
      fetch(`/admin/approvals/${approvalId}/approve`, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          remarks: remarks || ''
        })
      })
      .then(response => {
        return response.json().then(data => {
          if (data.success || response.ok) {
            showSuccess('Approval approved successfully!');
            location.reload();
          } else {
            showError(data.message || 'Failed to approve request');
          }
        });
      })
      .catch(error => {
        console.error('Error:', error);
        showError('Error approving request');
      });
    }
  }

  function rejectRequest(approvalId) {
    const remarks = prompt('Please enter rejection reason (required):');
    if (remarks === null) return; // User cancelled
    if (!remarks || remarks.trim() === '') {
      showError('Rejection reason is required');
      return;
    }
    
    fetch(`/admin/approvals/${approvalId}/reject`, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        remarks: remarks
      })
    })
    .then(response => {
      return response.json().then(data => {
        if (data.success || response.ok) {
          showSuccess('Approval rejected successfully!');
          location.reload();
        } else {
          showError(data.message || 'Failed to reject request');
        }
      });
    })
    .catch(error => {
      console.error('Error:', error);
      showError('Error rejecting request');
    });
  }

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
      tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4"><div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
    }

    const fetchUrl = `{{ route('admin.approvals.index') }}?${params.toString()}`;
    console.log('Fetching URL:', fetchUrl);
    console.log('Params:', params.toString());
    
    // Show loading state
    if (tbody) {
      tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4"><div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
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
      } else if (paginationContainer) {
        const extractedPagination = doc.querySelector('#approvalsPagination') || 
          (html.includes('approvalsPagination') ? (() => {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            return tempDiv.querySelector('#approvalsPagination');
          })() : null);
        
        if (extractedPagination) {
          paginationContainer.innerHTML = extractedPagination.innerHTML;
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
        tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-danger">' +
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
    
    // Auto remove after 5 seconds
    setTimeout(() => {
      if (alertDiv.parentNode) {
        alertDiv.style.animation = 'fadeOut 0.3s ease-in forwards';
        setTimeout(() => {
          if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
          }
        }, 300);
      }
    }, 5000);
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

  // Handle complaint status update from approvals view
  document.addEventListener('change', function(e) {
    if (e.target.classList.contains('status-select')) {
      const select = e.target;
      
      const complaintId = select.getAttribute('data-complaint-id');
      const newStatus = select.value;
      let oldStatus = select.dataset.oldStatus || select.value;
      
      // Get actual status from data attribute (in case it was 'new' which is displayed as 'assigned')
      const actualOldStatus = select.getAttribute('data-actual-status') || oldStatus;
      
      // Prevent changing from resolved if already addressed
      if (actualOldStatus === 'resolved' && newStatus !== 'resolved') {
        select.value = oldStatus;
        showError('Cannot change status - Complaint is already addressed and cannot be modified.');
        return;
      }
      
      if (!complaintId || !newStatus) {
        return;
      }
      
      // Confirm status change
      const statusLabels = {
        'assigned': 'Assigned',
        'in_progress': 'In-Process',
        'resolved': 'Addressed'
      };
      
      oldStatus = select.dataset.oldStatus || select.value;
      
      // Confirm status change
      const confirmMsg = `Are you sure you want to change status to "${statusLabels[newStatus]}"?`;
      if (!confirm(confirmMsg)) {
        // Revert selection
        select.value = oldStatus;
        return;
      }
      
      // Store old value for revert
      select.dataset.oldStatus = select.value;
      
      // Show loading state
      select.style.opacity = '0.6';
      select.disabled = true;
      
      // Get CSRF token
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      
      // Update complaint status
      fetch(`/admin/complaints/${complaintId}/update-status`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          status: newStatus,
          notes: `Status updated from approvals view`
        })
      })
      .then(response => {
        if (!response.ok && response.status === 422) {
          return response.json().then(data => {
            throw new Error(data.message || 'Validation failed');
          });
        }
        return response.json();
      })
      .then(data => {
        if (data.success || !data.errors) {
          // Update select styling based on new status
          const isActiveStatus = ['assigned', 'in_progress'].includes(newStatus);
          select.style.backgroundColor = isActiveStatus ? 'rgba(239, 68, 68, 0.25)' : 'rgba(34, 197, 94, 0.25)';
          select.style.color = isActiveStatus ? '#ef4444' : '#22c55e';
          select.style.borderColor = isActiveStatus ? '#ef4444' : '#22c55e';
          
          // Update addressed date/time cell if status is resolved
          if (newStatus === 'resolved' && data.complaint && data.complaint.closed_at) {
            const row = select.closest('tr');
            const addressedDateCell = row.querySelector('td:nth-child(3)'); // 3rd column is Addressed Date/Time
            if (addressedDateCell) {
              addressedDateCell.textContent = data.complaint.closed_at;
            }
            
            // Replace dropdown with green "Addressed" badge when status becomes addressed
            const statusCell = select.closest('td');
            const badge = document.createElement('span');
            badge.className = 'badge';
            badge.style.cssText = 'background-color: #22c55e; color: white; padding: 8px 16px; font-size: 13px; font-weight: 600; border-radius: 6px;';
            badge.textContent = 'Addressed';
            
            // Store old status before replacing (use oldStatus from outer scope if available)
            const oldStatusValue = oldStatus || select.value;
            
            // Replace select with badge
            select.replaceWith(badge);
            
            // Update old status tracker (though select is now replaced)
            // This is for reference only since badge doesn't have dataset
          } else if (newStatus !== 'resolved') {
            // Clear addressed date/time if status changed from resolved to something else
            const row = select.closest('tr');
            const addressedDateCell = row.querySelector('td:nth-child(3)');
            if (addressedDateCell) {
              addressedDateCell.textContent = '';
            }
          }
          
          showSuccess('Complaint status updated successfully!');
          
          // Update old status (only if select still exists)
          if (select.isConnected) {
            select.dataset.oldStatus = newStatus;
          }
        } else {
          // Revert selection on error
          select.value = oldStatus;
          showError(data.message || 'Failed to update complaint status.');
        }
      })
      .catch(error => {
        console.error('Error updating status:', error);
        // Revert selection on error
        select.value = oldStatus;
        showError(error.message || 'Failed to update complaint status.');
        select.style.opacity = '1';
        select.disabled = false;
      })
      .finally(() => {
        // Only re-enable if select still exists in DOM and is not addressed
        if (select.isConnected && select.value !== 'resolved') {
          select.style.opacity = '1';
          select.disabled = false;
        }
      });
    }
  });
  
  // Event delegation for approval buttons (document level for dynamic content)
  document.addEventListener('click', function(e) {
    // Approve Request button
    if (e.target.closest('.btn-approve-request')) {
      e.preventDefault();
      const button = e.target.closest('.btn-approve-request');
      const approvalId = button.getAttribute('data-approval-id');
      if (approvalId) {
        approveRequest(parseInt(approvalId));
      }
    }
    
    // Reject Request button
    if (e.target.closest('.btn-reject-request')) {
      e.preventDefault();
      const button = e.target.closest('.btn-reject-request');
      const approvalId = button.getAttribute('data-approval-id');
      if (approvalId) {
        rejectRequest(parseInt(approvalId));
      }
    }
  });

  // Store initial status on page load
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.status-select').forEach(select => {
      select.dataset.oldStatus = select.value;
    });
    
    // Replace feather icons
    feather.replace();
  });

</script>
@endpush
