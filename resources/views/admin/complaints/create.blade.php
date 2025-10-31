@extends('layouts.sidebar')

@section('title', 'Create New Complaint — CMS Admin')

@section('content')
<!-- Flash Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <strong>Success!</strong> {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <strong>Error!</strong> {{ session('error') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <strong>Validation Errors:</strong>
  <ul class="mb-0">
    @foreach($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Create New Complaint</h2>
      <p class="text-light">Add a new complaint to the system</p>
    </div>
</div>

<!-- COMPLAINT FORM -->
<div class="card-glass">
  <div class="card-header">
    <h5 class="card-title mb-0 text-white">
      <i data-feather="alert-triangle" class="me-2"></i>Complaint Information
    </h5>
  </div>
  <div class="card-body">
          <form action="{{ route('admin.complaints.store') }}" method="POST" autocomplete="off" novalidate>
            @csrf
            
            <!-- Hidden dummy fields to prevent browser autofill -->
            <div style="display: none;">
                <input type="text" name="fake_title" autocomplete="off">
                <input type="text" name="fake_description" autocomplete="off">
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="title" class="form-label text-white">Complaint Title <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('title') is-invalid @enderror" 
                         id="title" name="title" value="{{ old('title') }}" autocomplete="off" required>
                  @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="client_id" class="form-label text-white">Client <span class="text-danger">*</span></label>
                  <select class="form-select @error('client_id') is-invalid @enderror" 
                          id="client_id" name="client_id" required>
                    <option value="">Select Client</option>
                    @if(isset($clients) && $clients->count() > 0)
                      @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ (string)old('client_id') === (string)$client->id ? 'selected' : '' }}>{{ $client->client_name }}</option>
                      @endforeach
                    @else
                      <option value="" disabled>No clients available</option>
                    @endif
                  </select>
                  @error('client_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="category" class="form-label text-white">Category <span class="text-danger">*</span></label>
                  <select id="category" name="category" class="form-select @error('category') is-invalid @enderror" required>
                    <option value="">Select Category</option>
                    @if(isset($categories) && $categories->count() > 0)
                      @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                      @endforeach
                    @endif
                  </select>
                  @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="priority" class="form-label text-white">Priority <span class="text-danger">*</span></label>
                  <select class="form-select @error('priority') is-invalid @enderror" 
                          id="priority" name="priority" required>
                    <option value="">Select Priority</option>
                    <option value="low" {{ old('priority')=='low' ? 'selected' : '' }}>Low - Can wait</option>
                    <option value="medium" {{ old('priority')=='medium' ? 'selected' : '' }}>Medium - Normal</option>
                    <option value="high" {{ old('priority')=='high' ? 'selected' : '' }}>High - Important</option>
                    <option value="urgent" {{ old('priority')=='urgent' ? 'selected' : '' }}>Urgent - Critical</option>
                    <option value="emergency" {{ old('priority')=='emergency' ? 'selected' : '' }}>Emergency - Immediate</option>
                  </select>
                  @error('priority')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="department" class="form-label text-white">Department <span class="text-danger">*</span></label>
                  <select id="department" name="department" class="form-select @error('department') is-invalid @enderror" required>
                    <option value="">Select Department</option>
                    @if(isset($departments) && $departments->count() > 0)
                      @foreach($departments as $dep)
                        <option value="{{ $dep }}" {{ old('department') == $dep ? 'selected' : '' }}>{{ $dep }}</option>
                      @endforeach
                    @endif
                  </select>
                  @error('department')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="assigned_employee_id" class="form-label text-white">Assign Employee</label>
                  <select class="form-select @error('assigned_employee_id') is-invalid @enderror" 
                          id="assigned_employee_id" name="assigned_employee_id">
                    <option value="">Select Employee (Optional)</option>
                    @if(isset($employees) && $employees->count() > 0)
                      @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" data-department="{{ $employee->department }}" {{ (string)old('assigned_employee_id') === (string)$employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                      @endforeach
                    @else
                      <option value="" disabled>No employees available</option>
                    @endif
                  </select>
                  @error('assigned_employee_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
             

            <!-- Spare Parts Section -->
            <div class="row">
              <div class="col-12">                
                <div class="row g-2 align-items-end">
                  <div class="col-md-3">
                    <label for="status" class="form-label text-white">Status</label>
                    <select class="form-select @error('status') is-invalid @enderror" 
                            id="status" name="status">
                      <option value="new" {{ old('status','new')=='new' ? 'selected' : '' }}>New</option>
                      <option value="assigned" {{ old('status')=='assigned' ? 'selected' : '' }}>Assigned</option>
                      <option value="in_progress" {{ old('status')=='in_progress' ? 'selected' : '' }}>In Progress</option>
                    </select>
                    @error('status')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  
                  <div class="col-md-3">
                    <label class="form-label text-white">Product <span class="text-danger">*</span></label>
                    <select class="form-select @error('spare_parts.0.spare_id') is-invalid @enderror" 
                            name="spare_parts[0][spare_id]" id="spare_select" required>
                      <option value="">Select Product</option>
                      @foreach(\App\Models\Spare::orderBy('item_name')->get() as $spare)
                        <option value="{{ $spare->id }}" data-stock="{{ $spare->stock_quantity }}" {{ (string)old('spare_parts.0.spare_id') === (string)$spare->id ? 'selected' : '' }}>
                          {{ $spare->item_name }} (Stock: {{ $spare->stock_quantity }})
                        </option>
                      @endforeach
                    </select>
                    @error('spare_parts.0.spare_id')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label text-white">Quantity <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('spare_parts.0.quantity') is-invalid @enderror" 
                           name="spare_parts[0][quantity]" id="quantity_input" min="1" value="{{ old('spare_parts.0.quantity') }}" required>
                    <div id="stock_warning" class="text-warning mt-1" style="display: none; font-size: 0.875rem;"></div>
                    @error('spare_parts.0.quantity')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                
                <div class="alert alert-info mt-3">
                  <strong>Note:</strong> Stock will be checked and deducted during approval process. If quantity exceeds available stock, it will be automatically adjusted.
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <div class="mb-3">
                  <label for="description" class="form-label text-white">Description <span class="text-danger">*</span></label>
                  <textarea class="form-control @error('description') is-invalid @enderror" 
                            id="description" name="description" rows="4" autocomplete="off" required>{{ old('description') }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
              <a href="{{ route('admin.complaints.index') }}" class="btn btn-outline-secondary">Cancel</a>
              <button type="submit" class="btn btn-accent">Create Complaint</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
/* Form control styling for all themes */
.form-control {
  background-color: rgba(255, 255, 255, 0.1) !important;
  border: 1px solid rgba(59, 130, 246, 0.3) !important;
  color: #1e293b !important;
}
.form-control::placeholder {
  color: rgba(30, 41, 59, 0.6) !important;
}
.form-control:focus {
  background-color: rgba(255, 255, 255, 0.1) !important;
  border-color: #3b82f6 !important;
  color: #1e293b !important;
  box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25) !important;
}
.form-select {
  background-color: rgba(255, 255, 255, 0.1) !important;
  border: 1px solid rgba(59, 130, 246, 0.3) !important;
  color: #1e293b !important;
}
.form-select:focus {
  background-color: rgba(255, 255, 255, 0.1) !important;
  border-color: #3b82f6 !important;
  color: #1e293b !important;
  box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25) !important;
}
/* Light theme dropdown styling */
.theme-light .form-select {
  background-color: #fff !important;
  color: #1e293b !important;
}
.theme-light .form-select option {
  background-color: #fff !important;
  color: #1e293b !important;
}
.theme-light .form-select option:hover {
  background-color: #f8fafc !important;
  color: #1e293b !important;
}
.theme-light .form-select option:checked {
  background-color: #3b82f6 !important;
  color: #fff !important;
}
/* Dark and Night theme dropdown styling */
.theme-dark .form-select,
.theme-night .form-select {
  background-color: rgba(255, 255, 255, 0.1) !important;
  color: #fff !important;
}
.theme-dark .form-select option,
.theme-night .form-select option {
  background-color: #1e293b !important;
  color: #fff !important;
}
.theme-dark .form-select option:hover,
.theme-night .form-select option:hover {
  background-color: #334155 !important;
  color: #fff !important;
}
.theme-dark .form-select option:checked,
.theme-night .form-select option:checked {
  background-color: #3b82f6 !important;
  color: #fff !important;
}
</style>
@endpush

@push('scripts')
<script>
// Stock validation and auto-adjustment
document.addEventListener('DOMContentLoaded', function() {
  const spareSelect = document.getElementById('spare_select');
  const quantityInput = document.getElementById('quantity_input');
  const stockWarning = document.getElementById('stock_warning');
  const departmentSelect = document.getElementById('department');
  const employeeSelect = document.getElementById('assigned_employee_id');
  
  if (!spareSelect || !quantityInput) return;

  function updateStockWarning() {
    if (!spareSelect.value) {
      stockWarning.style.display = 'none';
      return;
    }

    const selectedOption = spareSelect.options[spareSelect.selectedIndex];
    const stock = selectedOption ? parseInt(selectedOption.getAttribute('data-stock') || 0) : 0;
    const requestedQty = parseInt(quantityInput.value) || 0;

    if (requestedQty > stock && stock > 0) {
      // Auto-adjust quantity to available stock
      quantityInput.value = stock;
      stockWarning.textContent = `Insufficient stock! Quantity adjusted to available stock: ${stock}`;
      stockWarning.style.display = 'block';
      stockWarning.className = 'text-warning mt-1';
    } else if (stock === 0) {
      stockWarning.textContent = 'Warning: This product has zero stock available.';
      stockWarning.style.display = 'block';
      stockWarning.className = 'text-danger mt-1';
    } else {
      stockWarning.style.display = 'none';
    }
  }

  // Update warning when product or quantity changes
  spareSelect.addEventListener('change', updateStockWarning);
  quantityInput.addEventListener('input', updateStockWarning);
  quantityInput.addEventListener('change', updateStockWarning);

  // Form submission validation
  const form = document.querySelector('form[action*="complaints.store"]');
  if (form) {
    console.log('Form found, attaching submit listener');
    form.addEventListener('submit', function(e) {
      console.log('Form submit event triggered');
      console.log('Spare ID:', spareSelect.value);
      console.log('Quantity:', quantityInput.value);
      
      if (!spareSelect.value || !quantityInput.value || parseInt(quantityInput.value) <= 0) {
        e.preventDefault();
        alert('Please select a spare part and enter quantity.');
        return false;
      }
      
      console.log('Form validation passed, submitting...');
      // Let form submit naturally
    });
  } else {
    console.error('Form not found!');
  }

  // Department -> Employee filter
  function filterEmployees() {
    if (!departmentSelect || !employeeSelect) return;
    const dep = departmentSelect.value;
    let firstVisible = null;
    Array.from(employeeSelect.options).forEach(opt => {
      if (!opt.value) return; // placeholder
      const od = opt.getAttribute('data-department') || '';
      const show = !dep || od === dep;
      opt.hidden = !show;
      if (show && !firstVisible) firstVisible = opt;
    });
    // If selected option is hidden, clear selection
    if (employeeSelect.selectedOptions.length) {
      const sel = employeeSelect.selectedOptions[0];
      if (sel && sel.hidden) employeeSelect.value = '';
    }
  }
  if (departmentSelect && employeeSelect) {
    departmentSelect.addEventListener('change', filterEmployees);
    filterEmployees();
  }
});
</script>
@endpush


