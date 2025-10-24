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
    <button id="addSpareBtn" class="btn btn-accent">
      <i class="fas fa-plus me-2"></i>Add Spare Part
        </button>
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
            <option value="electrical" {{ request('category') == 'electrical' ? 'selected' : '' }}>Electrical</option>
            <option value="plumbing" {{ request('category') == 'plumbing' ? 'selected' : '' }}>Plumbing</option>
            <option value="kitchen" {{ request('category') == 'kitchen' ? 'selected' : '' }}>Kitchen</option>
            <option value="general" {{ request('category') == 'general' ? 'selected' : '' }}>General</option>
            <option value="tools" {{ request('category') == 'tools' ? 'selected' : '' }}>Tools</option>
            <option value="consumables" {{ request('category') == 'consumables' ? 'selected' : '' }}>Consumables</option>
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
          <th>Name</th>
              <th>Category</th>
          <th>Stock</th>
          <th>Price</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($spares as $spare)
            <tr>
          <td >{{ $spare->id }}</td>
              <td>
                <div class="d-flex align-items-center">
                <div class="avatar-sm me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold;">
                {{ substr($spare->item_name, 0, 1) }}
                  </div>
                  <div>
             <div style="color: #ffffff !important; font-weight: 600;">{{ $spare->item_name }}</div>
                <div style="color: #94a3b8 !important; font-size: 0.8rem;">{{ $spare->item_name ?? 'No description' }}</div>
                  </div>
                </div>
              </td>
              <td>
            <span class="category-badge category-{{ strtolower($spare->category) }}">
                  {{ ucfirst($spare->category) }}
                </span>
              </td>
          <td >{{ $spare->stock_quantity ?? 0 }}</td>
          <td >RS{{ number_format($spare->unit_price ?? 0, 2) }}</td>
          <td>
            <span class="status-badge status-{{ $spare->status ?? 'active' }}">
              {{ ucfirst($spare->status ?? 'active') }}
                </span>
              </td>
              <td>
            <div class="btn-group" role="group">
              <button class="btn btn-outline-info btn-sm" onclick="viewSpare({{ $spare->id }})" title="View Details" style="border-color: #17a2b8; color: #17a2b8;">
                    <i class="fas fa-eye"></i>
              </button>
              <button class="btn btn-outline-warning btn-sm" onclick="editSpare({{ $spare->id }})" title="Edit" style="border-color: #ffc107; color: #ffc107;">
                    <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-outline-danger btn-sm" onclick="deleteSpare({{ $spare->id }})" title="Delete" style="border-color: #dc3545; color: #dc3545;">
                      <i class="fas fa-trash"></i>
                    </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
          <td colspan="7" class="text-center py-4" >
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

<!-- Spare Part Modal -->
<div class="modal fade" id="spareModal" tabindex="-1" aria-labelledby="spareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card-glass">
            <div class="modal-header">
                <h5 class="modal-title " id="spareModalLabel">Add Spare Part</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <form id="spareForm" method="POST" autocomplete="off" novalidate>
                    @csrf
                    <div id="methodField"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="item_name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="item_name" name="item_name" required autocomplete="off">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="electrical">Electrical</option>
                                <option value="plumbing">Plumbing</option>
                                <option value="kitchen">Kitchen</option>
                                <option value="general">General</option>
                                <option value="tools">Tools</option>
                                <option value="consumables">Consumables</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="unit" class="form-label">Unit</label>
                            <input type="text" class="form-control" id="unit" name="unit" autocomplete="off">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" autocomplete="off">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="stock_quantity" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0" required autocomplete="off">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="threshold_level" class="form-label">Threshold Level <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="threshold_level" name="threshold_level" min="0" required autocomplete="off">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="supplier" class="form-label">Supplier</label>
                            <input type="text" class="form-control" id="supplier" name="supplier" autocomplete="off">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" autocomplete="off"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-accent" id="submitBtn">
                    <span id="submitText">Add Spare Part</span>
                    <span id="loadingSpinner" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                </button>
            </div>
        </div>
    </div>
  </div>
@endsection

@push('styles')
<style>
  .category-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  
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
  .category-electrical { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
  .category-plumbing { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
  .category-kitchen { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
  .category-general { background: rgba(139, 92, 246, 0.2); color: #8b5cf6; }
  .category-tools { background: rgba(107, 114, 128, 0.2); color: #6b7280; }
  .category-consumables { background: rgba(20, 184, 166, 0.2); color: #14b8a6; }
  
  .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  .status-active { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
  .status-inactive { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
  .status-discontinued { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
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
          
          // Hide the form inside modal body
          const spareForm = modalBody.querySelector('#spareForm');
          if (spareForm) {
            spareForm.style.display = 'none';
          }
          
          console.log('Modal body found:', modalBody);
          
          modalBody.innerHTML = `
            <div class="row">
              <div class="col-md-6">
                <strong>Name:</strong> ${data.name || 'N/A'}<br>
                <strong>Category:</strong> ${data.category || 'N/A'}<br>
                <strong>Unit:</strong> ${data.unit || 'N/A'}<br>
                <strong>Price:</strong> $${data.price || '0.00'}<br>
              </div>
              <div class="col-md-6">
                <strong>Stock Quantity:</strong> ${data.stock_quantity || 'N/A'}<br>
                <strong>Threshold Level:</strong> ${data.threshold_level || 'N/A'}<br>
                <strong>Status:</strong> ${data.status || 'N/A'}<br>
                <strong>Last Updated:</strong> ${data.updated_at || 'N/A'}<br>
              </div>
              <div class="col-12 mt-3">
                <strong>Supplier:</strong> ${data.supplier || 'N/A'}<br>
                <strong>Description:</strong> ${data.description || 'N/A'}<br>
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
          
          // Hide modal footer for view mode after modal is shown
          setTimeout(() => {
            const modalFooter = document.querySelector('.modal-footer');
            if (modalFooter) {
              modalFooter.style.display = 'none';
              console.log('Modal footer hidden');
            }
          }, 100);
        }, 100);
      })
      .catch(error => {
        console.error('Error loading spare details:', error);
        alert('Error loading spare details: ' + error.message);
      });
  }

  function editSpare(spareId) {
    console.log('Editing spare ID:', spareId);
    
    // Load spare data for editing
    fetch(`/admin/spares/${spareId}/edit-data`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      credentials: 'same-origin'
    })
      .then(response => {
        console.log('Edit response status:', response.status);
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        console.log('Edit data received:', data);
        // Reset modal to form mode
        document.getElementById('spareModalLabel').textContent = 'Edit Spare Part';
        
        // Show modal footer for edit mode
        const modalFooter = document.querySelector('.modal-footer');
        modalFooter.style.display = 'block';
        
        // Reset modal body to original form structure
        const modalBody = document.getElementById('modalBody');
        modalBody.innerHTML = `
          <form id="spareForm" method="POST" autocomplete="off" novalidate>
            @csrf
            <div id="methodField"></div>
            <div class="row">
            <div class="col-md-6 mb-3">
              <label for="item_name" class="form-label">Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="item_name" name="item_name" required autocomplete="off">
              <div class="invalid-feedback"></div>
            </div>
            
            <div class="col-md-6 mb-3">
              <label for="category" class="form-label ">Category <span class="text-danger">*</span></label>
              <select class="form-select" id="category" name="category" required>
                <option value="">Select Category</option>
                <option value="electrical">Electrical</option>
                <option value="plumbing">Plumbing</option>
                <option value="kitchen">Kitchen</option>
                <option value="general">General</option>
                <option value="tools">Tools</option>
                <option value="consumables">Consumables</option>
              </select>
              <div class="invalid-feedback"></div>
            </div>
            
            <div class="col-md-6 mb-3">
              <label for="unit" class="form-label ">Unit</label>
              <input type="text" class="form-control" id="unit" name="unit" autocomplete="off">
              <div class="invalid-feedback"></div>
            </div>
            
            <div class="col-md-6 mb-3">
              <label for="price" class="form-label ">Price</label>
              <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" autocomplete="off">
              <div class="invalid-feedback"></div>
            </div>
            
            <div class="col-md-6 mb-3">
              <label for="stock_quantity" class="form-label ">Stock Quantity <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0" required autocomplete="off">
              <div class="invalid-feedback"></div>
            </div>
            
            <div class="col-md-6 mb-3">
              <label for="threshold_level" class="form-label ">Threshold Level <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="threshold_level" name="threshold_level" min="0" required autocomplete="off">
              <div class="invalid-feedback"></div>
            </div>
            
            <div class="col-md-6 mb-3">
              <label for="supplier" class="form-label ">Supplier</label>
              <input type="text" class="form-control" id="supplier" name="supplier" autocomplete="off">
              <div class="invalid-feedback"></div>
            </div>
            
            <div class="col-12 mb-3">
              <label for="description" class="form-label ">Description</label>
              <textarea class="form-control" id="description" name="description" rows="3" autocomplete="off"></textarea>
              <div class="invalid-feedback"></div>
            </div>
          </div>
          </form>
        `;
        
        // Configure form after it's created
        document.getElementById('spareForm').action = `/admin/spares/${spareId}`;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('submitText').textContent = 'Update Spare Part';
        
        // Submit button will be handled by event delegation
        
        // Populate form fields with data
        document.getElementById('item_name').value = data.name || '';
        document.getElementById('category').value = data.category || '';
        document.getElementById('unit').value = data.unit || '';
        document.getElementById('price').value = data.price || '';
        document.getElementById('stock_quantity').value = data.stock_quantity || '';
        document.getElementById('threshold_level').value = data.threshold_level || '';
        document.getElementById('supplier').value = data.supplier || '';
        document.getElementById('description').value = data.description || '';
        
        new bootstrap.Modal(document.getElementById('spareModal')).show();
      })
      .catch(error => {
        console.error('Error loading spare data:', error);
        alert('Error loading spare data');
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
        return { response, data };
      })
      .then(({ response, data }) => {
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

  // Add Spare Button
  document.getElementById('addSpareBtn')?.addEventListener('click', function() {
    // Reset modal to form mode
    document.getElementById('spareModalLabel').textContent = 'Add Spare Part';
    document.getElementById('spareForm').style.display = 'block';
    document.getElementById('spareForm').action = '/admin/spares';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('submitText').textContent = 'Add Spare Part';
    
    // Show modal footer for add mode
    const modalFooter = document.querySelector('.modal-footer');
    modalFooter.style.display = 'block';
    
    // Reset modal body to original form structure
    const modalBody = document.getElementById('modalBody');
    modalBody.innerHTML = `
      <form id="spareForm" method="POST" autocomplete="off" novalidate>
        @csrf
        <div id="methodField"></div>
        <div class="row">
        <div class="col-md-6 mb-3">
          <label for="item_name" class="form-label">Name <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="item_name" name="item_name" required autocomplete="off">
          <div class="invalid-feedback"></div>
        </div>
        
        <div class="col-md-6 mb-3">
          <label for="category" class="form-label ">Category <span class="text-danger">*</span></label>
          <select class="form-select" id="category" name="category" required>
            <option value="">Select Category</option>
            <option value="electrical">Electrical</option>
            <option value="plumbing">Plumbing</option>
            <option value="kitchen">Kitchen</option>
            <option value="general">General</option>
            <option value="tools">Tools</option>
            <option value="consumables">Consumables</option>
          </select>
          <div class="invalid-feedback"></div>
        </div>
        
        <div class="col-md-6 mb-3">
          <label for="unit" class="form-label ">Unit</label>
          <input type="text" class="form-control" id="unit" name="unit" autocomplete="off">
          <div class="invalid-feedback"></div>
        </div>
        
        <div class="col-md-6 mb-3">
          <label for="price" class="form-label ">Price</label>
          <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" autocomplete="off">
          <div class="invalid-feedback"></div>
        </div>
        
        <div class="col-md-6 mb-3">
          <label for="stock_quantity" class="form-label ">Stock Quantity <span class="text-danger">*</span></label>
          <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0" required autocomplete="off">
          <div class="invalid-feedback"></div>
        </div>
        
        <div class="col-md-6 mb-3">
          <label for="threshold_level" class="form-label ">Threshold Level <span class="text-danger">*</span></label>
          <input type="number" class="form-control" id="threshold_level" name="threshold_level" min="0" required autocomplete="off">
          <div class="invalid-feedback"></div>
        </div>
        
        <div class="col-md-6 mb-3">
          <label for="supplier" class="form-label ">Supplier</label>
          <input type="text" class="form-control" id="supplier" name="supplier" autocomplete="off">
          <div class="invalid-feedback"></div>
        </div>
        
        <div class="col-12 mb-3">
          <label for="description" class="form-label ">Description</label>
          <textarea class="form-control" id="description" name="description" rows="3" autocomplete="off"></textarea>
          <div class="invalid-feedback"></div>
        </div>
      </div>
      </form>
    `;
    
    // Configure form after it's created
    document.getElementById('spareForm').action = '/admin/spares';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('submitText').textContent = 'Add Spare Part';
    
    // Submit button will be handled by event delegation
    
    // Clear all form fields
    document.getElementById('spareForm').reset();
    
    new bootstrap.Modal(document.getElementById('spareModal')).show();
  });

  // Form submission - using event delegation for dynamically created forms
  document.addEventListener('submit', function(e) {
    if (e.target.id === 'spareForm') {
      e.preventDefault();
      // Show spinner / disable submit
      setSubmitting(true);

  // Clear previous validation errors
  clearValidationErrors(e.target);
  const formData = new FormData(e.target);
      const url = e.target.action;
      const overrideMethod = e.target.querySelector('input[name="_method"]')?.value;
      // If there's a method override (PUT/DELETE), send as POST so PHP parses form data correctly
      const fetchMethod = overrideMethod ? 'POST' : (overrideMethod || 'POST');
      console.log('Submitting form to', url, 'with fetch method', fetchMethod, 'and override', overrideMethod);

    fetch(url, {
      method: fetchMethod,
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(response => {
      console.log('Response status:', response.status);
      return response.json();
    })
      .then(data => {
      console.log('Response data:', data);
      if (data.success) {
        location.reload();
      } else {
        setSubmitting(false);
        alert('Error: ' + (data.message || 'Unknown error'));
        if (data.errors) {
          console.error('Validation errors:', data.errors);
          // show inline validation messages
          showValidationErrors(e.target, data.errors);
        }
      }
    })
    .catch(error => {
      console.error('Error submitting form:', error);
      setSubmitting(false);
      alert('Error submitting form');
    });
    }
  });

  // Direct event listener for submit button
  document.addEventListener('DOMContentLoaded', function() {
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
      submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Submit button clicked via event listener');
        
        const form = document.getElementById('spareForm');
          if (form) {
          console.log('Form found, submitting...');
          // Prefer requestSubmit when available (submits and triggers submit event listeners).
          if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
          } else {
            // Dispatch a bubbling, cancelable submit event so the document-level listener catches it
            form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
          }
        } else {
          console.log('Form not found, using direct submission');
          handleFormSubmission();
        }
      });
    } else {
      console.error('Submit button not found!');
    }
  });

  // Direct form submission handler
  function handleFormSubmission() {
    const form = document.getElementById('spareForm');
    if (!form) {
      console.error('No form found for submission');
      return;
    }
    
    console.log('Handling form submission directly');
    setSubmitting(true);
  const formData = new FormData(form);
  const url = form.action;
  const overrideMethod = form.querySelector('input[name="_method"]')?.value;
  const fetchMethod = overrideMethod ? 'POST' : (overrideMethod || 'POST');
  console.log('Direct submit to', url, 'using fetch method', fetchMethod, 'override', overrideMethod);
    
    console.log('URL:', url);
    console.log('Method:', method);
    console.log('Form data:', Object.fromEntries(formData));
    
    fetch(url, {
      method: fetchMethod,
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(response => {
      console.log('Response status:', response.status);
      return response.json();
    })
    .then(data => {
      console.log('Response data:', data);
      if (data.success) {
        location.reload();
      } else {
        setSubmitting(false);
        alert('Error: ' + (data.message || 'Unknown error'));
        if (data.errors) {
          console.error('Validation errors:', data.errors);
        }
      }
    })
    .catch(error => {
      console.error('Error submitting form:', error);
      setSubmitting(false);
      alert('Error submitting form');
    });
  }

  // Toggle submit button and spinner
  function setSubmitting(isSubmitting) {
    const btn = document.getElementById('submitBtn');
    const spinner = document.getElementById('loadingSpinner');
    if (!btn) return;
    btn.disabled = !!isSubmitting;
    if (spinner) {
      spinner.style.display = isSubmitting ? 'inline-block' : 'none';
    }
  }

  // Clear previous validation errors inside a form
  function clearValidationErrors(form) {
    const inputs = form.querySelectorAll('.is-invalid');
    inputs.forEach(i => i.classList.remove('is-invalid'));
    const feedbacks = form.querySelectorAll('.invalid-feedback');
    feedbacks.forEach(f => f.textContent = '');
  }

  // Show validation errors returned from server: { fieldName: [messages] }
  function showValidationErrors(form, errors) {
    Object.keys(errors).forEach(field => {
      const input = form.querySelector(`[name="${field}"]`);
      const messages = errors[field];
      if (input) {
        input.classList.add('is-invalid');
        const fb = input.closest('.mb-3')?.querySelector('.invalid-feedback') || form.querySelector('.invalid-feedback');
        if (fb) fb.textContent = messages.join(' ');
      } else {
        // fallback: log
        console.warn('No input found for validation field:', field, messages);
      }
    });
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