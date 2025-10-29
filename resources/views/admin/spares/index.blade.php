@extends('layouts.sidebar')

@section('title', 'Spare Parts Management — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class=" mb-2">Spare Parts Management</h2>
      <p class="text-light">Manage inventory and spare parts</p>
    </div>
    <a href="{{ route('admin.spares.create') }}" class="btn btn-accent">
      <i class="fas fa-plus me-2"></i>Add Spare Part
    </a>
  </div>
</div>

<!-- FILTERS -->
<div class="card-glass mb-4">
  <form method="GET" action="{{ route('admin.spares.index') }}">
    <div class="row g-3">
      <div class="col-md-4">
        <input type="text" class="form-control" name="search" placeholder="Search spares..." value="{{ request('search') }}">
      </div>
      <div class="col-md-3">
        <select class="form-select" name="category">
          <option value="">All Categories</option>
          @foreach(App\Models\Spare::getCategories() as $key => $label)
          <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <select class="form-select" name="stock_status">
          <option value="">All Status</option>
          <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
          <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
          <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-outline-light btn-sm w-100">
          <i class="fas fa-filter me-1"></i>Filter
        </button>
      </div>
    </div>
  </form>
</div>

<!-- SPARES TABLE -->
<div class="card-glass">
  <div class="table-responsive">
    <table class="table table-dark">
      <thead>
        <tr>
          <th>#</th>
          <th>Product Code</th>
          <th>Brand Name</th>
          <th>Product Name</th>
          <th>Product Nature</th>
          <th>Unit</th>
          <th>Total Received</th>
          <th>issued Quantity</th>
          <th>Balance Quantity</th>
          <th>%age Utilized</th>
          <th>Last Stock In</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($spares as $spare)
        <tr>
          <td class="text-muted">{{ $loop->iteration }}</td>
          <td>{{ $spare->product_code ?? 'N/A' }}</td>
          <td>{{ $spare->brand_name ?? 'N/A' }}</td>
          <td>{{ $spare->item_name }}</td>
          <td>{{ $spare->product_nature ?? 'N/A' }}</td>
          <td>{{ $spare->unit ?? 'N/A' }}</td>
          <td class="text-end"><span class="text-success">{{ number_format((int)($spare->total_received_quantity ?? 0), 2) }}</span></td>
          <td class="text-end"><span class="text-danger">{{ number_format((int)($spare->issued_quantity ?? 0), 2) }}</span></td>
          <td class="text-end">{{ number_format((int)($spare->stock_quantity ?? 0), 2) }}</td>
          <td class="text-end">{{ number_format((float)($spare->utilization_percent ?? 0), 2) }}%</td>
          <td>{{ $spare->last_stock_in_at ? $spare->last_stock_in_at->format('d M Y h:i A') : 'N/A' }}</td>
          <td>
            <div class="btn-group" role="group">
              <button class="btn btn-outline-info btn-sm" onclick="viewSpare('{{ $spare->id }}')" title="View Details">
                <i data-feather="eye"></i>
              </button>
              <a href="{{ route('admin.spares.edit', $spare) }}" class="btn btn-outline-warning btn-sm" title="Edit">
                <i data-feather="edit"></i>
              </a>
              <button class="btn btn-outline-primary btn-sm" onclick="printSpare('{{ $spare->id }}')" title="Print Spare Part Details">
                <i data-feather="printer"></i>
              </button>
              <button class="btn btn-outline-danger btn-sm" onclick="deleteSpare('{{ $spare->id }}')" title="Delete">
                <i data-feather="trash-2"></i>
              </button>
            </div>
          </td>

        </tr>
@empty
<tr>
  <td colspan="12" class="text-center py-4">
    <i data-feather="package" class="feather-lg mb-2"></i>
    <div>No spare parts found</div>
  </td>
</tr>
@endforelse
</tbody>
</table>
</div>

<!-- PAGINATION -->
<div class="d-flex justify-content-center mt-3">
  <div>
    {{ $spares->links() }}
  </div>
</div>
</div>

<!-- View Modal (for viewing spare details only) -->
<div class="modal fade" id="spareModal" tabindex="-1" aria-labelledby="spareModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card-glass">
      <div class="modal-header">
        <h5 class="modal-title" id="spareModalLabel">View Spare Part</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalBody">
        <!-- Content will be populated by viewSpare function -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
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

  // Spare Modal Functions
  function viewSpare(spareId) {
    console.log('Viewing spare ID:', spareId);

    // Load spare details via AJAX
    fetch(`/admin/spares/${spareId}`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
        console.log('Received data:', data);

        // Wait for DOM to be ready
        setTimeout(() => {
          // Show spare details in modal
          const modalLabel = document.getElementById('spareModalLabel');
          const modalBody = document.getElementById('modalBody');

          if (!modalLabel || !modalBody) {
            console.error('Modal elements not found');
            console.log('ModalLabel:', modalLabel);
            console.log('ModalBody:', modalBody);
            alert('Error: Modal elements not found');
            return;
          }

          modalLabel.textContent = 'View Spare Part';

          console.log('Modal body found:', modalBody);

          modalBody.innerHTML = `
            <div class="row" style="color: var(--text-primary);">
              <div class="col-md-6">
                <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Product Code:</strong> <span style="color: var(--text-secondary);">${data.product_code || 'N/A'}</span></p>
                <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Brand Name:</strong> <span style="color: var(--text-secondary);">${data.brand_name || 'N/A'}</span></p>
                <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Product Name:</strong> <span style="color: var(--text-secondary);">${data.name || 'N/A'}</span></p>
                <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Product Nature:</strong> <span style="color: var(--text-secondary);">${data.product_nature || 'N/A'}</span></p>
                <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Category:</strong> <span style="color: var(--text-secondary);">${data.category || 'N/A'}</span></p>
                <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Unit:</strong> <span style="color: var(--text-secondary);">${data.unit || 'N/A'}</span></p>
              </div>
              <div class="col-md-6">
                <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Total Received:</strong> <span style="color: var(--text-secondary);">${data.total_received_quantity || 'N/A'}</span></p>
                <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Issued Quantity:</strong> <span style="color: var(--text-secondary);">${data.issued_quantity || 'N/A'}</span></p>
                <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Balance Quantity:</strong> <span style="color: var(--text-secondary);">${data.stock_quantity || 'N/A'}</span></p>
                <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Threshold Level:</strong> <span style="color: var(--text-secondary);">${data.threshold_level || 'N/A'}</span></p>
                <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Last Stock In:</strong> <span style="color: var(--text-secondary);">${data.last_stock_in_at || 'N/A'}</span></p>
                <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Last Updated:</strong> <span style="color: var(--text-secondary);">${data.updated_at || 'N/A'}</span></p>
              </div>
              <div class="col-12 mt-3">
                <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Supplier:</strong> <span style="color: var(--text-secondary);">${data.supplier || 'N/A'}</span></p>
                <p style="color: var(--text-primary);"><strong style="color: var(--text-primary);">Description:</strong> <span style="color: var(--text-secondary);">${data.description || 'N/A'}</span></p>
              </div>
            </div>
          `;

          console.log('Modal body content updated');

          // Show the modal first
          const modalElement = document.getElementById('spareModal');
          if (!modalElement) {
            console.error('Spare modal element not found');
            alert('Error: Modal not found');
            return;
          }

          const modal = new bootstrap.Modal(modalElement);
          modal.show();
          console.log('Modal shown');
          
          // Footer remains visible with Close button
        }, 100);
      })
      .catch(error => {
        console.error('Error loading spare details:', error);
        alert('Error loading spare details: ' + error.message);
      });
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

  // Print Functions
  function printSpare(spareId) {
    console.log('Printing spare ID:', spareId);

    // Load spare details for printing
    fetch(`/admin/spares/${spareId}`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
        // Create print content
        const printContent = `
          <!DOCTYPE html>
          <html>
          <head>
            <title>Spare Part Details - ${data.name || 'N/A'}</title>
            <style>
              body { font-family: Arial, sans-serif; margin: 20px; }
              .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
              .details { margin: 20px 0; }
              .row { display: flex; margin: 10px 0; }
              .label { font-weight: bold; width: 200px; }
              .value { flex: 1; }
              .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
              @media print {
                body { margin: 0; }
                .no-print { display: none; }
              }
            </style>
          </head>
          <body>
            <div class="header">
              <h1>Spare Part Details</h1>
              <p>Generated on: ${new Date().toLocaleString()}</p>
            </div>
            
            <div class="details">
              <div class="row">
                <div class="label">Product Code:</div>
                <div class="value">${data.product_code || 'N/A'}</div>
              </div>
              <div class="row">
                <div class="label">Brand Name:</div>
                <div class="value">${data.brand_name || 'N/A'}</div>
              </div>
              <div class="row">
                <div class="label">Product Name:</div>
                <div class="value">${data.name || 'N/A'}</div>
              </div>
              <div class="row">
                <div class="label">Product Nature:</div>
                <div class="value">${data.product_nature || 'N/A'}</div>
              </div>
              <div class="row">
                <div class="label">Category:</div>
                <div class="value">${data.category || 'N/A'}</div>
              </div>
              <div class="row">
                <div class="label">Unit:</div>
                <div class="value">${data.unit || 'N/A'}</div>
              </div>
              <div class="row">
                <div class="label">Total Received:</div>
                <div class="value">${data.total_received_quantity || '0'}</div>
              </div>
              <div class="row">
                <div class="label">Issued Quantity:</div>
                <div class="value">${data.issued_quantity || '0'}</div>
              </div>
              <div class="row">
                <div class="label">Balance Quantity:</div>
                <div class="value">${data.stock_quantity || '0'}</div>
              </div>
              <div class="row">
                <div class="label">Threshold Level:</div>
                <div class="value">${data.threshold_level || '0'}</div>
              </div>
              <div class="row">
                <div class="label">Supplier:</div>
                <div class="value">${data.supplier || 'N/A'}</div>
              </div>
              <div class="row">
                <div class="label">Last Stock In:</div>
                <div class="value">${data.last_stock_in_at || 'N/A'}</div>
              </div>
              <div class="row">
                <div class="label">Description:</div>
                <div class="value">${data.description || 'N/A'}</div>
              </div>
            </div>
            
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
      })
      .catch(error => {
        console.error('Error loading spare details for print:', error);
        showNotification('Error loading spare details for printing', 'error');
      });
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