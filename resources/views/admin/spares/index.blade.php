@extends('layouts.sidebar')

@section('title', 'Spare Parts Management — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
      <div>
      <h2 class="text-white mb-2">Spare Parts Management</h2>
      <p class="text-light">Manage inventory and spare parts</p>
      </div>
    <a href="{{ route('admin.spares.create') }}" class="btn btn-accent">
      <i data-feather="plus" class="me-2"></i>Add Spare Part
        </a>
      </div>
    </div>

<!-- FILTERS -->
<div class="card-glass mb-4">
  <div class="row g-3">
    <div class="col-md-4">
      <input type="text" class="form-control" placeholder="Search spares...">
    </div>
    <div class="col-md-3">
      <select class="form-select" 
>
            <option value="">All Categories</option>
            <option value="electrical">Electrical</option>
            <option value="plumbing">Plumbing</option>
            <option value="kitchen">Kitchen</option>
            <option value="general">General</option>
            <option value="tools">Tools</option>
            <option value="consumables">Consumables</option>
          </select>
    </div>
    <div class="col-md-3">
      <select class="form-select" 
>
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="discontinued">Discontinued</option>
          </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-outline-light btn-sm w-100">
        <i data-feather="filter" class="me-1"></i>Filter
      </button>
    </div>
        </div>
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
                {{ substr($spare->name, 0, 1) }}
                  </div>
                  <div>
                <div style="color: #ffffff !important; font-weight: 600;">{{ $spare->name }}</div>
                <div style="color: #94a3b8 !important; font-size: 0.8rem;">{{ $spare->description ?? 'No description' }}</div>
                  </div>
                </div>
              </td>
              <td>
            <span class="category-badge category-{{ strtolower($spare->category) }}">
                  {{ ucfirst($spare->category) }}
                </span>
              </td>
          <td >{{ $spare->stock_quantity ?? 0 }}</td>
          <td >${{ number_format($spare->price ?? 0, 2) }}</td>
          <td>
            <span class="status-badge status-{{ $spare->status ?? 'active' }}">
              {{ ucfirst($spare->status ?? 'active') }}
                </span>
              </td>
              <td>
            <div class="btn-group" role="group">
              <button class="btn btn-outline-info btn-sm" onclick="viewSpare({{ $spare->id }})" title="View Details">
                    <i data-feather="eye"></i>
              </button>
              <button class="btn btn-outline-warning btn-sm" onclick="editSpare({{ $spare->id }})" title="Edit">
                    <i data-feather="edit"></i>
              </button>
              <button class="btn btn-outline-danger btn-sm" onclick="deleteSpare({{ $spare->id }})" title="Delete">
                      <i data-feather="trash-2"></i>
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
      <div class="d-flex justify-content-between align-items-center mt-3">
    <div >
      Showing {{ $spares->firstItem() ?? 0 }} to {{ $spares->lastItem() ?? 0 }} of {{ $spares->total() }} spare parts
        </div>
        <div>
          {{ $spares->links() }}
        </div>
      </div>
</div>

<!-- Spare Part Modal -->
<div class="modal fade" id="spareModal" tabindex="-1" aria-labelledby="spareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border: 1px solid rgba(59, 130, 246, 0.2);">
            <div class="modal-header" style="border-bottom: 1px solid rgba(59, 130, 246, 0.2);">
                <h5 class="modal-title text-white" id="spareModalLabel">Add Spare Part</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
            </div>
            <form id="spareForm" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label text-white">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required 
>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label text-white">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required
>
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
                            <label for="price" class="form-label text-white">Price</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0"
>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="stock_quantity" class="form-label text-white">Stock Quantity</label>
                            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0"
>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label text-white">Status</label>
                            <select class="form-select" id="status" name="status"
>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="discontinued">Discontinued</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="supplier" class="form-label text-white">Supplier</label>
                            <input type="text" class="form-control" id="supplier" name="supplier"
>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label text-white">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"
></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(59, 130, 246, 0.2);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" 
                            style="background: rgba(107, 114, 128, 0.8); border: 1px solid rgba(107, 114, 128, 0.3); color: #fff;">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn"
                            style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); border: none; color: #fff; font-weight: 600;">
                        <span id="submitText">Add Spare Part</span>
                        <span id="loadingSpinner" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
  </div>
@endsection

@push('styles')
<style>
  .category-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
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
    feather.replace();

  // Spare Modal Functions
  function viewSpare(spareId) {
    // Load spare details via AJAX
    fetch(`/admin/spares/${spareId}`)
      .then(response => response.json())
      .then(data => {
        alert('View spare details functionality coming soon!');
      })
      .catch(error => {
        console.error('Error loading spare details:', error);
        alert('Error loading spare details');
      });
  }

  function editSpare(spareId) {
    // Load spare data for editing
    fetch(`/admin/spares/${spareId}/edit-data`)
      .then(response => response.json())
      .then(data => {
        // Populate form with spare data
        document.getElementById('spareForm').action = `/admin/spares/${spareId}`;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('spareModalLabel').textContent = 'Edit Spare Part';
        document.getElementById('submitText').textContent = 'Update Spare Part';
        
        // Populate form fields
        document.getElementById('name').value = data.name;
        document.getElementById('category').value = data.category;
        document.getElementById('price').value = data.price || '';
        document.getElementById('stock_quantity').value = data.stock_quantity || '';
        document.getElementById('status').value = data.status;
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
      fetch(`/admin/spares/${spareId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json',
        },
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          location.reload();
        } else {
          alert('Error deleting spare part: ' + (data.message || 'Unknown error'));
        }
      })
      .catch(error => {
        console.error('Error deleting spare part:', error);
        alert('Error deleting spare part');
      });
    }
  }

  // Add Spare Button
  document.getElementById('addSpareBtn')?.addEventListener('click', function() {
    // Reset form for new spare
    document.getElementById('spareForm').action = '/admin/spares';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('spareModalLabel').textContent = 'Add Spare Part';
    document.getElementById('submitText').textContent = 'Add Spare Part';
    document.getElementById('spareForm').reset();
    
    new bootstrap.Modal(document.getElementById('spareModal')).show();
  });

  // Form submission
  document.getElementById('spareForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    
    // Show loading state
    submitBtn.disabled = true;
    submitText.style.display = 'none';
    loadingSpinner.style.display = 'inline-block';
    
    fetch(this.action, {
      method: this.method || 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('spareModal')).hide();
        location.reload();
      } else {
        // Handle validation errors
        if (data.errors) {
          Object.keys(data.errors).forEach(field => {
            const input = document.getElementById(field);
            if (input) {
              input.classList.add('is-invalid');
              input.nextElementSibling.textContent = data.errors[field][0];
            }
          });
        } else {
          alert('Error: ' + (data.message || 'Unknown error'));
        }
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error saving spare part');
    })
    .finally(() => {
      // Reset loading state
      submitBtn.disabled = false;
      submitText.style.display = 'inline';
      loadingSpinner.style.display = 'none';
    });
  });
  </script>
@endpush