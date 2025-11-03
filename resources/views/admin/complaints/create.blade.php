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
            
            <!-- Complainant Information Section (matching index file columns) -->
            <div class="row mb-4">
              <div class="col-12">
                <h6 class="text-white fw-bold mb-3"><i data-feather="user" class="me-2" style="width: 16px; height: 16px;"></i>Complainant Information</h6>
              </div>
              <div class="col-md-3">
                <div class="mb-3">
                  <label for="client_name" class="form-label text-white">Complainant Name <span class="text-danger">*</span></label>
                  <input type="text" 
                         class="form-control @error('client_name') is-invalid @enderror" 
                         id="client_name" 
                         name="client_name" 
                         value="{{ old('client_name') }}"
                         placeholder="Enter complainant name"
                         required>
                  @error('client_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-3">
                <div class="mb-3">
                  <label for="city_id" class="form-label text-white">City</label>
                  <select class="form-select @error('city_id') is-invalid @enderror" 
                          id="city_id" name="city_id">
                    <option value="">Select City</option>
                    @if(isset($cities) && $cities->count() > 0)
                      @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ old('city_id', $defaultCityId ?? null) == $city->id ? 'selected' : '' }}>
                          {{ $city->name }}{{ $city->province ? ' (' . $city->province . ')' : '' }}
                        </option>
                      @endforeach
                    @endif
                  </select>
                  @error('city_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-3">
                <div class="mb-3">
                  <label for="sector_id" class="form-label text-white">Sector</label>
                  <select class="form-select @error('sector_id') is-invalid @enderror" 
                          id="sector_id" name="sector_id" {{ (old('city_id', $defaultCityId ?? null)) ? '' : 'disabled' }}>
                    <option value="">{{ (old('city_id', $defaultCityId ?? null)) ? 'Loading sectors...' : 'Select City First' }}</option>
                  </select>
                  @error('sector_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-3">
                <div class="mb-3">
                  <label for="address" class="form-label text-white">Address</label>
                  <input type="text"
                         class="form-control @error('address') is-invalid @enderror"
                         id="address"
                         name="address"
                         value="{{ old('address') }}"
                         placeholder="e.g., 00/0-ST-0-B-0">
                  @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <!-- Complaint Details Section (matching index file columns) -->
            <div class="row mb-4">
              <div class="col-12">
                <h6 class="text-white fw-bold mb-3"><i data-feather="alert-triangle" class="me-2" style="width: 16px; height: 16px;"></i>Complaint Nature & Type</h6>
              </div>
              <div class="col-md-4">
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
              
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="title" class="form-label text-white">Complaint Title <span class="text-danger">*</span></label>
                  <select class="form-select @error('title') is-invalid @enderror" 
                          id="title" name="title" autocomplete="off" required>
                  </select>
                  {{-- <small class="text-muted">Select category above to see complaint titles</small> --}}
                  @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              

              <!-- Product selection for Complaint Nature & Type -->
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label text-white">Product (for Complaint Nature & Type)</label>
                  <select class="form-select @error('spare_parts.0.spare_id') is-invalid @enderror" 
                          name="spare_parts[0][spare_id]" id="spare_select">
                    <option value="">Select Product (Optional)</option>
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
              </div>
              
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label text-white">Quantity</label>
                  <input type="number" class="form-control @error('spare_parts.0.quantity') is-invalid @enderror" 
                         name="spare_parts[0][quantity]" id="quantity_input" min="1" value="{{ old('spare_parts.0.quantity') }}">
                  <div id="stock_warning" class="text-warning mt-1" style="display: none; font-size: 0.875rem;"></div>
                  @error('spare_parts.0.quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-4">
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

              <div class="col-md-4">
                <div class="mb-3">
                  <label for="assigned_employee_id" class="form-label text-white">Assign Employee</label>
                  <select class="form-select @error('assigned_employee_id') is-invalid @enderror" 
                          id="assigned_employee_id" name="assigned_employee_id">
                    <option value="">Select Employee (Optional)</option>
                    @if(isset($employees) && $employees->count() > 0)
                      @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" data-category="{{ $employee->department }}" {{ (string)old('assigned_employee_id') === (string)$employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
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
            </div>

            <div class="alert alert-info mt-3">
              <strong>Note:</strong> Product and quantity are optional. If provided, they will be used in "Complaint Nature & Type" display and stock will be checked during approval process.
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
  const categorySelect = document.getElementById('category');
  
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
      
      // Validate quantity only if product is selected
      if (spareSelect.value && (!quantityInput.value || parseInt(quantityInput.value) <= 0)) {
        e.preventDefault();
        alert('Please enter quantity for selected product.');
        return false;
      }
      
      // If quantity is entered but no product selected
      if (quantityInput.value && parseInt(quantityInput.value) > 0 && !spareSelect.value) {
        e.preventDefault();
        alert('Please select a product for the quantity.');
        return false;
      }
      
      console.log('Form validation passed, submitting...');
      // Let form submit naturally
    });
  } else {
    console.error('Form not found!');
  }

  const employeeSelect = document.getElementById('assigned_employee_id');

  // Category -> Employee filter
  function filterEmployees() {
    if (!categorySelect || !employeeSelect) return;
    const category = categorySelect.value;
    let firstVisible = null;
    Array.from(employeeSelect.options).forEach(opt => {
      if (!opt.value) return; // placeholder
      const optCategory = opt.getAttribute('data-category') || '';
      const show = !category || optCategory === category;
      opt.hidden = !show;
      if (show && !firstVisible) firstVisible = opt;
    });
    // If selected option is hidden, clear selection
    if (employeeSelect.selectedOptions.length) {
      const sel = employeeSelect.selectedOptions[0];
      if (sel && sel.hidden) employeeSelect.value = '';
    }
  }
  if (categorySelect && employeeSelect) {
    categorySelect.addEventListener('change', filterEmployees);
    filterEmployees();
  }

  // Category -> Complaint Titles dynamic loading
  const titleSelect = document.getElementById('title');
  
  if (categorySelect && titleSelect) {
    categorySelect.addEventListener('change', function() {
      const category = this.value;
      
      // Clear existing options
      titleSelect.innerHTML = '<option value="">Loading titles...</option>';
      titleSelect.disabled = true;
      
      if (!category) {
        titleSelect.innerHTML = '<option value="">Select Category first, then choose title</option>';
        titleSelect.disabled = false;
        return;
      }
      
      // Fetch complaint titles by category
      fetch(`{{ route('admin.complaint-titles.by-category') }}?category=${encodeURIComponent(category)}`, {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        credentials: 'same-origin'
      })
      .then(response => response.json())
      .then(data => {
        // Clear options
        titleSelect.innerHTML = '<option value="">Select Complaint Title</option>';
        
        if (data && data.length > 0) {
          data.forEach(title => {
            const option = document.createElement('option');
            option.value = title.title;
            option.textContent = title.title;
            if (title.description) {
              option.setAttribute('title', title.description);
            }
            titleSelect.appendChild(option);
          });
        } else {
          const option = document.createElement('option');
          option.value = '';
          option.textContent = 'No titles found for this category';
          titleSelect.appendChild(option);
        }
        
        titleSelect.disabled = false;
        // Restore previously selected title if any
        const previous = titleSelect.getAttribute('data-prev');
        if (previous) {
          const opt = Array.from(titleSelect.options).find(o => o.value === previous);
          if (opt) titleSelect.value = previous;
        }
      })
      .catch(error => {
        console.error('Error loading complaint titles:', error);
        titleSelect.innerHTML = '<option value="">Failed to load titles. Please try again.</option>';
        titleSelect.disabled = false;
      });
    });
    
    // Trigger on page load if category is pre-selected
    if (categorySelect.value) {
      // Preserve old title if present
      if (titleSelect && titleSelect.value) {
        titleSelect.setAttribute('data-prev', titleSelect.value);
      } else if ('{{ old('title') }}') {
        titleSelect.setAttribute('data-prev', @json(old('title')));
      }
      categorySelect.dispatchEvent(new Event('change'));
    }
  }

  // City -> Sector dynamic loading
  const citySelect = document.getElementById('city_id');
  const sectorSelect = document.getElementById('sector_id');
  const addressInput = document.getElementById('address');
  
  if (citySelect && sectorSelect) {
    citySelect.addEventListener('change', function() {
      const cityId = this.value;
      
      if (!cityId) {
        sectorSelect.innerHTML = '<option value="">Select City First</option>';
        sectorSelect.disabled = true;
        return;
      }

      sectorSelect.innerHTML = '<option value="">Loading sectors...</option>';
      sectorSelect.disabled = true;

      fetch(`{{ route('admin.sectors.by-city') }}?city_id=${cityId}`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        credentials: 'same-origin'
      })
      .then(response => response.json())
      .then(data => {
        sectorSelect.innerHTML = '<option value="">Select Sector</option>';
        if (data && data.length > 0) {
          data.forEach(sector => {
            const option = document.createElement('option');
            option.value = sector.id;
            option.textContent = sector.name;
            sectorSelect.appendChild(option);
          });
        } else {
          sectorSelect.innerHTML = '<option value="">No sectors found for this city</option>';
        }
        sectorSelect.disabled = false;
      })
      .catch(error => {
        console.error('Error loading sectors:', error);
        sectorSelect.innerHTML = '<option value="">Error loading sectors</option>';
        sectorSelect.disabled = false;
      });
    });
    
    // If city is pre-selected (e.g., for Department Staff), load sectors and select default
    const defaultCityId = '{{ old('city_id', $defaultCityId ?? '') }}';
    const defaultSectorId = '{{ old('sector_id', $defaultSectorId ?? '') }}';
    if (defaultCityId) {
      citySelect.value = defaultCityId;
      // Trigger fetch to load sectors, then select default
      fetch(`{{ route('admin.sectors.by-city') }}?city_id=${defaultCityId}`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        credentials: 'same-origin'
      })
      .then(response => response.json())
      .then(data => {
        sectorSelect.innerHTML = '<option value="">Select Sector</option>';
        if (data && data.length > 0) {
          data.forEach(sector => {
            const option = document.createElement('option');
            option.value = sector.id;
            option.textContent = sector.name;
            if (defaultSectorId && String(sector.id) === String(defaultSectorId)) {
              option.selected = true;
            }
            sectorSelect.appendChild(option);
          });
        } else {
          sectorSelect.innerHTML = '<option value="">No sectors found for this city</option>';
        }
        sectorSelect.disabled = false;
      })
      .catch(error => {
        console.error('Error loading sectors:', error);
        sectorSelect.innerHTML = '<option value="">Error loading sectors</option>';
        sectorSelect.disabled = false;
      });
    }
  }

  // Address auto-format: e.g., 17/2-ST-9-B-9
  if (addressInput) {
    function formatAddress(val) {
      if (!val) return '';
      let v = val.toUpperCase();
      // Remove spaces
      v = v.replace(/\s+/g, '');
      // Ensure hyphen after number/number pattern like 17/2 → 17/2-
      v = v.replace(/(\d+\/\d+)(?!-)/g, '$1-');
      // Ensure hyphen after ST-<num> like ST-9 → ST-9-
      v = v.replace(/(ST-\d+)(?!-)/g, '$1-');
      // Collapse multiple hyphens
      v = v.replace(/-+/g, '-');
      // Prevent leading hyphen
      v = v.replace(/^-+/, '');
      return v;
    }

    let lastAddress = addressInput.value;
    const applyFormat = () => {
      const formatted = formatAddress(addressInput.value);
      if (formatted !== addressInput.value) {
        const pos = addressInput.selectionStart;
        addressInput.value = formatted;
        // Best-effort caret restore: move to end when structure changed
        try { addressInput.setSelectionRange(formatted.length, formatted.length); } catch (_) {}
      }
      lastAddress = addressInput.value;
    };

    addressInput.addEventListener('input', applyFormat);
    addressInput.addEventListener('blur', applyFormat);
    // Initialize on load
    applyFormat();
  }
});
</script>
@endpush


