@extends('layouts.sidebar')

@section('title', 'Spare Parts Management — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class=" mb-2">Product Management</h2>
      <p class="text-light">Manage inventory and Product</p>
    </div>
    <a href="{{ route('admin.spares.create') }}" class="btn btn-accent">
      <i class="fas fa-plus me-2"></i>Add Product
    </a>
  </div>
</div>

<!-- FILTERS -->
<div class="card-glass mb-4">
  <form id="sparesFiltersForm" method="GET" action="{{ route('admin.spares.index') }}">
    <div class="row g-2 align-items-end">
      <div class="col-auto">
        <label class="form-label small text-muted mb-1" style="font-size: 0.8rem;">Search</label>
        <input type="text" class="form-control" id="searchInput" name="search" placeholder="Search..." 
               value="{{ request('search') }}" oninput="handleSparesSearchInput()" style="font-size: 0.9rem; width: 180px;">
      </div>
      <div class="col-auto">
        <label class="form-label small text-muted mb-1" style="font-size: 0.8rem;">Category</label>
        <select class="form-select" name="category" onchange="submitSparesFilters()" style="font-size: 0.9rem; width: 140px;">
          <option value="">All</option>
          @php($catOptions = \Illuminate\Support\Facades\Schema::hasTable('complaint_categories') ? \App\Models\ComplaintCategory::orderBy('name')->pluck('name') : collect())
          @foreach($catOptions as $cat)
          <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-auto">
        <label class="form-label small text-muted mb-1" style="font-size: 0.8rem;">Stock</label>
        <select class="form-select" name="stock_status" onchange="submitSparesFilters()" style="font-size: 0.9rem; width: 130px;">
          <option value="">All</option>
          <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
          <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
          <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
        </select>
      </div>
      <div class="col-auto">
        <label class="form-label small text-muted mb-1" style="font-size: 0.8rem;">&nbsp;</label>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetSparesFilters()" style="font-size: 0.9rem; padding: 0.35rem 0.8rem;">
          <i data-feather="refresh-cw" class="me-1" style="width: 14px; height: 14px;"></i>Reset
        </button>
      </div>
    </div>
  </form>
</div>

<!-- SPARES TABLE -->
<div class="card-glass">
  <div class="table-responsive" style="overflow-x: auto;">
    <table class="table table-dark table-sm" style="margin-bottom: 0;">
      <thead>
        <tr>
          <th style="padding: 0.4rem 0.5rem; font-size: 0.8rem; white-space: nowrap;">Sr.No</th>
          <th style="padding: 0.4rem 0.5rem; font-size: 0.8rem; white-space: nowrap;">Product Code</th>
          <th style="padding: 0.4rem 0.5rem; font-size: 0.8rem; white-space: nowrap;">Brand Name</th>
          <th style="padding: 0.4rem 0.5rem; font-size: 0.8rem; white-space: nowrap;">Product Name</th>
          <th style="padding: 0.4rem 0.5rem; font-size: 0.8rem; white-space: nowrap;">Category</th>
          <th style="padding: 0.4rem 0.5rem; font-size: 0.8rem; white-space: nowrap;">Total Received</th>
          <th style="padding: 0.4rem 0.5rem; font-size: 0.8rem; white-space: nowrap;">Issued Qty</th>
          <th style="padding: 0.4rem 0.5rem; font-size: 0.8rem; white-space: nowrap;">Balance Qty</th>
          <th style="padding: 0.4rem 0.5rem; font-size: 0.8rem; white-space: nowrap;">% Utilized</th>
          <th style="padding: 0.4rem 0.5rem; font-size: 0.8rem; white-space: nowrap;">Stock Status</th>
          <th style="padding: 0.4rem 0.5rem; font-size: 0.8rem; white-space: nowrap;">Last Stock Out</th>
          <th style="padding: 0.4rem 0.5rem; font-size: 0.8rem; white-space: nowrap;">Actions</th>
        </tr>
      </thead>
      <tbody id="sparesTableBody">
        @forelse($spares as $spare)
        <tr>
          <td class="text-muted" style="padding: 0.4rem 0.5rem;">{{ $loop->iteration }}</td>
          <td style="padding: 0.4rem 0.5rem;">{{ $spare->product_code ?? 'N/A' }}</td>
          <td style="padding: 0.4rem 0.5rem;">{{ $spare->brand_name ?? 'N/A' }}</td>
          <td style="padding: 0.4rem 0.5rem;">{{ $spare->item_name }}</td>
          <td style="padding: 0.4rem 0.5rem;">{{ ucfirst($spare->category ?? 'N/A') }}</td>
          <td style="padding: 0.4rem 0.5rem;"><span class="text-success">{{ number_format((float)($spare->total_received_quantity ?? 0), 0) }}</span></td>
          <td style="padding: 0.4rem 0.5rem;"><span class="text-danger">{{ number_format((float)($spare->issued_quantity ?? 0), 0) }}</span></td>
          <td style="padding: 0.4rem 0.5rem;">{{ number_format((float)($spare->stock_quantity ?? 0), 0) }}</td>
          <td style="padding: 0.4rem 0.5rem;">{{ number_format((float)($spare->utilization_percent ?? 0), 0) }}%</td>
          <td style="padding: 0.4rem 0.5rem;">
            @if(($spare->stock_quantity ?? 0) <= 0)
              <span class="badge bg-danger" style="font-size: 0.75rem;">Out</span>
            @elseif(($spare->stock_quantity ?? 0) <= ($spare->threshold_level ?? 0))
              <span class="badge bg-warning text-dark" style="font-size: 0.75rem;">Low</span>
            @else
              <span class="badge bg-success" style="font-size: 0.75rem;">In</span>
            @endif
          </td>
          <td style="padding: 0.4rem 0.5rem; font-size: 0.8rem;">
            @if(($spare->stock_quantity ?? 0) <= 0 && $spare->last_stock_out)
              <span class="text-danger">{{ $spare->last_stock_out->format('d M Y') }}</span>
            @elseif($spare->last_stock_out)
              {{ $spare->last_stock_out->format('d M Y') }}
            @else
              <span class="text-muted">Never</span>
            @endif
          </td>
          <td style="padding: 0.4rem 0.5rem;">
            <div class="btn-group" role="group">
              <button class="btn btn-outline-success btn-sm" style="padding: 3px 8px;" onclick="viewSpare('{{ $spare->id }}')" title="View">
                <i data-feather="eye" style="width: 16px; height: 16px;"></i>
              </button>
              <a href="{{ route('admin.spares.edit', $spare) }}" class="btn btn-outline-primary btn-sm" style="padding: 3px 8px;" title="Edit">
                <i data-feather="edit" style="width: 16px; height: 16px;"></i>
              </a>
              <button class="btn btn-outline-danger btn-sm" style="padding: 3px 8px;" onclick="deleteSpare('{{ $spare->id }}')" title="Delete">
                <i data-feather="trash-2" style="width: 16px; height: 16px;"></i>
              </button>
            </div>
          </td>
        </tr>
@empty
<tr>
  <td colspan="14" class="text-center py-4">
    <i data-feather="package" class="feather-lg mb-2"></i>
    <div>No Product found</div>
  </td>
</tr>
@endforelse
</tbody>
</table>
</div>

<!-- PAGINATION -->
<div class="d-flex justify-content-center mt-3" id="sparesPagination">
  <div>
    {{ $spares->links() }}
  </div>
</div>
</div>


@endsection

@push('styles')
<style>
  .category-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
  }

  /* Pagination styles are now centralized in components/pagination.blade.php */

  /* Modal theme styling */
  .modal-content.card-glass {
    background: rgba(255, 255, 255, 0.95) !important;
    border: 1px solid rgba(59, 130, 246, 0.2) !important;
    backdrop-filter: blur(10px) !important;
  }

  .theme-dark .modal-content.card-glass,
  .theme-night .modal-content.card-glass {
    background: rgba(30, 41, 59, 0.95) !important;
    border: 1px solid rgba(59, 130, 246, 0.3) !important;
  }

  .modal-header {
    border-bottom: 1px solid rgba(59, 130, 246, 0.2) !important;
  }

  .theme-dark .modal-header,
  .theme-night .modal-header {
    border-bottom: 1px solid rgba(59, 130, 246, 0.3) !important;
  }

  .modal-footer {
    border-top: 1px solid rgba(59, 130, 246, 0.2) !important;
  }

  .theme-dark .modal-footer,
  .theme-night .modal-footer {
    border-top: 1px solid rgba(59, 130, 246, 0.3) !important;
  }

  .modal-title {
    color: #1e293b !important;
  }

  .theme-dark .modal-title,
  .theme-night .modal-title {
    color: #fff !important;
  }

  .modal-body .form-label {
    color: #1e293b !important;
  }

  .theme-dark .modal-body .form-label,
  .theme-night .modal-body .form-label {
    color: #fff !important;
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

  .category-sanitary {
    background: rgba(20, 184, 166, 0.2);
    color: #14b8a6;
  }

  .category-electric {
    background: rgba(245, 158, 11, 0.2);
    color: #f59e0b;
  }

  .category-kitchen {
    background: rgba(139, 92, 246, 0.2);
    color: #8b5cf6;
  }

  .category-plumbing {
    background: rgba(59, 130, 246, 0.2);
    color: #3b82f6;
  }

  .category-other {
    background: rgba(107, 114, 128, 0.2);
    color: #6b7280;
  }

  .status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
  }

  .status-active {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
  }

  .status-inactive {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
  }

  .status-discontinued {
    background: rgba(245, 158, 11, 0.2);
    color: #f59e0b;
  }

  /* Print button styling */
  #printSparesBtn {
    transition: all 0.3s ease;
  }

  #printSparesBtn:hover {
    background-color: rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
    color: #3b82f6;
  }

  /* Action buttons styling */
  .btn-group .btn {
    margin-right: 2px;
  }

  .btn-group .btn:last-child {
    margin-right: 0;
  }

  /* Print button in actions */
  .btn-outline-primary {
    border-color: #3b82f6;
    color: #3b82f6;
  }

  .btn-outline-primary:hover {
    background-color: #3b82f6;
    border-color: #3b82f6;
    color: white;
  }
</style>
@endpush

@push('scripts')
<script>
  // Spare parts JavaScript loaded
  feather.replace();

  // Debounced search input handler
  let sparesSearchTimeout = null;
  function handleSparesSearchInput() {
    if (sparesSearchTimeout) clearTimeout(sparesSearchTimeout);
    sparesSearchTimeout = setTimeout(() => {
      loadSpares();
    }, 500);
  }

  // Auto-submit for select filters
  function submitSparesFilters() {
    loadSpares();
  }

  // Reset filters function
  function resetSparesFilters() {
    const form = document.getElementById('sparesFiltersForm');
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
    window.location.href = '{{ route('admin.spares.index') }}';
  }

  // Load Spares via AJAX
  function loadSpares(url = null) {
    const form = document.getElementById('sparesFiltersForm');
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

    const tbody = document.getElementById('sparesTableBody');
    const paginationContainer = document.getElementById('sparesPagination');
    
    if (tbody) {
      tbody.innerHTML = '<tr><td colspan="13" class="text-center py-4"><div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
    }

    fetch(`{{ route('admin.spares.index') }}?${params.toString()}`, {
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
      
      const newTbody = doc.querySelector('#sparesTableBody');
      const newPagination = doc.querySelector('#sparesPagination');
      
      if (newTbody && tbody) {
        tbody.innerHTML = newTbody.innerHTML;
        feather.replace();
      }
      
      if (newPagination && paginationContainer) {
        paginationContainer.innerHTML = newPagination.innerHTML;
      }

      const newUrl = `{{ route('admin.spares.index') }}?${params.toString()}`;
      window.history.pushState({path: newUrl}, '', newUrl);
    })
    .catch(error => {
      console.error('Error loading spares:', error);
      if (tbody) {
        tbody.innerHTML = '<tr><td colspan="13" class="text-center py-4 text-danger">Error loading data. Please refresh the page.</td></tr>';
      }
    });
  }

  // Handle pagination clicks
  document.addEventListener('click', function(e) {
    const paginationLink = e.target.closest('#sparesPagination a');
    if (paginationLink && paginationLink.href && !paginationLink.href.includes('javascript:')) {
      e.preventDefault();
      loadSpares(paginationLink.href);
    }
  });

  // Handle browser back/forward buttons
  window.addEventListener('popstate', function(e) {
    if (e.state && e.state.path) {
      loadSpares(e.state.path);
    } else {
      loadSpares();
    }
  });

  // Spare Functions
  function viewSpare(spareId) {
    // Redirect to show page
    window.location.href = `/admin/spares/${spareId}`;
  }


  function deleteSpare(spareId) {
    if (confirm('Are you sure you want to delete this spare part?')) {
      // Use POST + _method=DELETE so Laravel receives form data and CSRF properly
      const fd = new FormData();
      fd.append('_method', 'DELETE');
      fd.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

      fetch(`/admin/spares/${spareId}`, {
          method: 'POST',
          credentials: 'same-origin',
          body: fd,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          }
        })
        .then(async response => {
          const text = await response.text();
          let data = null;
          try {
            data = JSON.parse(text);
          } catch (err) {
            console.error('Non-JSON response:', text);
            throw new Error('Unexpected response from server (not JSON). Status: ' + response.status);
          }
          return {
            response,
            data
          };
        })
        .then(({
          response,
          data
        }) => {
          if (data.success) {
            showNotification('Spare part deleted successfully!', 'success');
            // Remove the row from table
            const row = document.querySelector(`button[onclick="deleteSpare(${spareId})"]`).closest('tr');
            if (row) {
              row.remove();
            }
            // Reload page after a short delay
            setTimeout(() => {
              location.reload();
            }, 1000);
          } else {
            showNotification('Error deleting spare part: ' + (data.message || 'Unknown error'), 'error');
          }
        })
        .catch(error => {
          console.error('Error deleting spare part:', error);
          showNotification('Error deleting spare part: ' + (error.message || 'Unknown error'), 'error');
        });
    }
  }

  // Create/Edit functionality moved to separate create.blade.php and edit.blade.php files


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


  // Print all spares list
  function printAllSpares() {
    console.log('Printing all spares list');

    // Get current table data
    const table = document.querySelector('.table-responsive table');
    if (!table) {
      showNotification('No data to print', 'error');
      return;
    }

    // Create print content
    const printContent = `
      <!DOCTYPE html>
      <html>
      <head>
        <title>Spare Parts List - ${new Date().toLocaleDateString()}</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; }
          .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
          table { width: 100%; border-collapse: collapse; margin: 20px 0; }
          th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
          th { background-color: #f2f2f2; font-weight: bold; }
          .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
          @media print {
            body { margin: 0; }
            .no-print { display: none; }
          }
        </style>
      </head>
      <body>
        <div class="header">
          <h1>Spare Parts Inventory Report</h1>
          <p>Generated on: ${new Date().toLocaleString()}</p>
        </div>
        
        ${table.outerHTML}
        
        <div class="footer">
          <p>This document was generated from CMS Portal</p>
        </div>
      </body>
      </html>
    `;

    // Open print window
    const printWindow = window.open('', '_blank');
    printWindow.document.write(printContent);
    printWindow.document.close();

    // Wait for content to load then print
    printWindow.onload = function() {
      printWindow.print();
      printWindow.close();
    };
  }

  // Add event listener for print button
  document.addEventListener('DOMContentLoaded', function() {
    const printBtn = document.getElementById('printSparesBtn');
    if (printBtn) {
      printBtn.addEventListener('click', function() {
        printAllSpares();
      });
    }
  });
</script>
@endpush