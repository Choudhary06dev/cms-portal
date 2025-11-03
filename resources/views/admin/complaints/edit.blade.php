@extends('layouts.sidebar')

@section('title', 'Edit Complaint — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Edit Complaint</h2>
      <p class="text-light">Update complaint information</p>
    </div>
   
  </div>
</div>

<!-- COMPLAINT FORM -->
<div class="card-glass">
  <div class="card-header">
    <h5 class="card-title mb-0 text-white">
      <i data-feather="edit" class="me-2"></i>Edit Complaint: #{{ $complaint->id }}
    </h5>
  </div>
  <div class="card-body">
          <form action="{{ route('admin.complaints.update', $complaint) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Complainant Information Section (matching index file columns) -->
            <div class="row mb-4">
              <div class="col-12">
                <h6 class="text-white fw-bold mb-3"><i data-feather="user" class="me-2" style="width: 16px; height: 16px;"></i>Complainant Information</h6>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="client_name" class="form-label text-white">Complainant Name <span class="text-danger">*</span></label>
                  <input type="text" 
                         class="form-control @error('client_name') is-invalid @enderror" 
                         id="client_name" 
                         name="client_name" 
                         value="{{ old('client_name', $complaint->client->client_name ?? '') }}"
                         placeholder="Enter complainant name"
                         required>
                  @error('client_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label text-white">City</label>
                  <input type="text" class="form-control" id="client_city" name="city" value="{{ old('city', $complaint->city ?? $complaint->client->city ?? '') }}" placeholder="Enter city">
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label text-white">Sector</label>
                  <input type="text" class="form-control" id="client_sector" name="sector" value="{{ old('sector', $complaint->sector ?? $complaint->client->sector ?? '') }}" placeholder="Enter sector">
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label text-white">Address</label>
                  <input type="text" class="form-control" id="client_address" name="address" value="{{ old('address', $complaint->client->address ?? '') }}" placeholder="e.g., 00/0-ST-0-B-0">
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label text-white">Phone No.</label>
                  <input type="text" class="form-control" id="client_phone" name="phone" value="{{ old('phone', $complaint->client->phone ?? '') }}" placeholder="Enter phone number">
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
                        <option value="{{ $cat }}" {{ old('category', $complaint->category) == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
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
                          id="title" name="title" required>
                    <option value="{{ old('title', $complaint->title) }}">{{ old('title', $complaint->title) }}</option>
                  </select>
                  @error('title')
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
                    <option value="low" {{ old('priority', $complaint->priority) == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority', $complaint->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('priority', $complaint->priority) == 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ old('priority', $complaint->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                  </select>
                  @error('priority')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="assigned_employee_id" class="form-label text-white">Assign Employee</label>
                  <select class="form-select @error('assigned_employee_id') is-invalid @enderror" 
                          id="assigned_employee_id" name="assigned_employee_id">
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" data-category="{{ $emp->department }}"
                            {{ old('assigned_employee_id', $complaint->assigned_employee_id) == $emp->id ? 'selected' : '' }}>
                      {{ $emp->name ?? 'Employee #' . $emp->id }}
                    </option>
                    @endforeach
                  </select>
                  @error('assigned_employee_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <!-- Description moved below product section -->
            <div class="row mt-3">
              <div class="col-12">
                <div class="mb-3">
                  <label for="description" class="form-label text-white">Description <span class="text-danger">*</span></label>
                  <textarea class="form-control @error('description') is-invalid @enderror" 
                            id="description" name="description" rows="4" >{{ old('description', $complaint->description) }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
              <a href="{{ route('admin.complaints.index', $complaint) }}" class="btn btn-outline-secondary">Cancel</a>
              <button type="submit" class="btn btn-accent">Update Complaint</button>
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
document.addEventListener('DOMContentLoaded', function() {
  const categorySelect = document.getElementById('category');
  const employeeSelect = document.getElementById('assigned_employee_id');
  const addressInput = document.getElementById('client_address');

  function filterEmployees() {
    if (!categorySelect || !employeeSelect) return;
    const category = categorySelect.value;
    Array.from(employeeSelect.options).forEach(opt => {
      if (!opt.value) return;
      const optCategory = opt.getAttribute('data-category') || '';
      opt.hidden = category && optCategory !== category;
    });
    const sel = employeeSelect.selectedOptions[0];
    if (sel && sel.hidden) employeeSelect.value = '';
  }
  if (categorySelect && employeeSelect) {
    categorySelect.addEventListener('change', filterEmployees);
    filterEmployees();
  }

  // Address auto-format: ensure patterns like 00/0-ST-4-B-5
  if (addressInput) {
    function formatAddress(val) {
      if (!val) return '';
      let v = val.toUpperCase();
      v = v.replace(/\s+/g, '');
      // Ensure hyphen after number/number pattern e.g., 17/2 -> 17/2-
      v = v.replace(/(\d+\/\d+)(?!-)/g, '$1-');
      // Ensure hyphen after ST-<num> e.g., ST-4 -> ST-4-
      v = v.replace(/(ST-\d+)(?!-)/g, '$1-');
      // Collapse multiple hyphens
      v = v.replace(/-+/g, '-');
      // Trim leading hyphens
      v = v.replace(/^-+/, '');
      return v;
    }

    const applyFormat = () => {
      const formatted = formatAddress(addressInput.value);
      if (formatted !== addressInput.value) {
        addressInput.value = formatted;
        try { addressInput.setSelectionRange(formatted.length, formatted.length); } catch (_) {}
      }
    };

    addressInput.addEventListener('input', applyFormat);
    addressInput.addEventListener('blur', applyFormat);
    // Initialize once on load
    applyFormat();
  }
  // Category -> Complaint Titles dynamic loading
  const titleSelect = document.getElementById('title');
  const currentTitle = '{{ old('title', $complaint->title) }}';
  
  if (categorySelect && titleSelect) {
    categorySelect.addEventListener('change', function() {
      const category = this.value;
      
      // Clear existing options except current value
      titleSelect.innerHTML = '';
      titleSelect.disabled = true;
      
      if (!category) {
        const option = document.createElement('option');
        option.value = currentTitle;
        option.textContent = currentTitle || 'Select Category first, then choose title';
        titleSelect.appendChild(option);
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
        // Add current title as first option if it exists
        if (currentTitle) {
          const currentOption = document.createElement('option');
          currentOption.value = currentTitle;
          currentOption.textContent = currentTitle;
          currentOption.selected = true;
          titleSelect.appendChild(currentOption);
        }
        
        if (data && data.length > 0) {
          data.forEach(title => {
            // Skip if it's already added as current title
            if (title.title !== currentTitle) {
              const option = document.createElement('option');
              option.value = title.title;
              option.textContent = title.title;
              if (title.description) {
                option.setAttribute('title', title.description);
              }
              titleSelect.appendChild(option);
            }
          });
        }
        
        if (!currentTitle && (!data || data.length === 0)) {
          const option = document.createElement('option');
          option.value = '';
          option.textContent = 'No titles found for this category';
          titleSelect.appendChild(option);
        }
        
        titleSelect.disabled = false;
      })
      .catch(error => {
        console.error('Error loading complaint titles:', error);
        // Keep current title if available
        if (currentTitle) {
          const option = document.createElement('option');
          option.value = currentTitle;
          option.textContent = currentTitle;
          titleSelect.appendChild(option);
        } else {
          titleSelect.innerHTML = '<option value="">Error loading titles</option>';
        }
        titleSelect.disabled = false;
      });
    });
    
    // Trigger on page load if category is pre-selected
    if (categorySelect.value) {
      categorySelect.dispatchEvent(new Event('change'));
    }
  }
});
</script>
@endpush


