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
                  <label for="city_id" class="form-label text-white">City</label>
                  <select class="form-select @error('city_id') is-invalid @enderror" 
                          id="city_id" name="city_id">
                    <option value="">Select City</option>
                    @if(isset($cities) && $cities->count() > 0)
                      @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ (string)old('city_id', $defaultCityId ?? '') === (string)$city->id ? 'selected' : '' }}>
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
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="sector_id" class="form-label text-white">Sector</label>
                  <select class="form-select @error('sector_id') is-invalid @enderror" 
                          id="sector_id" name="sector_id" {{ (old('city_id', $defaultCityId ?? null)) ? '' : 'disabled' }}>
                    @php $hasCity = old('city_id', $defaultCityId ?? null); @endphp
                    <option value="">{{ $hasCity ? 'Loading sectors...' : 'Select City First' }}</option>
                    @if(isset($sectors) && $sectors->count() > 0)
                      @foreach($sectors as $sector)
                        <option value="{{ $sector->id }}" {{ (string)old('sector_id', $defaultSectorId ?? '') === (string)$sector->id ? 'selected' : '' }}>
                          {{ $sector->name }}
                        </option>
                      @endforeach
                    @endif
                  </select>
                  @error('sector_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
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
                          id="title" name="title" autocomplete="off" required>
                    <option value="">Select Complaint Title</option>
                    @if(old('title', $complaint->title))
                      <option value="{{ old('title', $complaint->title) }}" selected>{{ old('title', $complaint->title) }}</option>
                    @endif
                  </select>
                  <input type="text" class="form-select @error('title') is-invalid @enderror"
                          id="title_other" name="title_other" placeholder="Enter custom title..."
                          style="display: none;" value="{{ old('title_other', $complaint->title) }}">
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
                  <label for="description" class="form-label text-white">Description</label>
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

  // Auto-replace space with hyphen in address field
  if (addressInput) {
    addressInput.addEventListener('keydown', function(e) {
      // If space key is pressed
      if (e.key === ' ' || e.keyCode === 32) {
        e.preventDefault(); // Prevent default space
        
        // Get current cursor position
        const cursorPos = this.selectionStart;
        const currentValue = this.value;
        
        // Insert hyphen at cursor position
        const newValue = currentValue.substring(0, cursorPos) + '-' + currentValue.substring(cursorPos);
        this.value = newValue;
        
        // Set cursor position after the inserted hyphen
        this.setSelectionRange(cursorPos + 1, cursorPos + 1);
      }
    });
    
    // Also handle paste events to replace spaces with hyphens
    addressInput.addEventListener('paste', function(e) {
      e.preventDefault();
      const pastedText = (e.clipboardData || window.clipboardData).getData('text');
      const replacedText = pastedText.replace(/\s+/g, '-');
      
      // Get current cursor position
      const cursorPos = this.selectionStart;
      const currentValue = this.value;
      
      // Insert replaced text at cursor position
      const newValue = currentValue.substring(0, cursorPos) + replacedText + currentValue.substring(this.selectionEnd);
      this.value = newValue;
      
      // Set cursor position after the inserted text
      this.setSelectionRange(cursorPos + replacedText.length, cursorPos + replacedText.length);
    });
  }

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

  // Category -> Complaint Titles dynamic loading
  const titleSelect = document.getElementById('title');
  const titleOtherInput = document.getElementById('title_other');
  const currentTitle = '{{ old('title', $complaint->title) }}';
  
  // Handle "Other" option selection
  function handleTitleChange() {
    if (!titleSelect || !titleOtherInput) return;
    
    const selectedValue = titleSelect.value;
    
    if (selectedValue === 'other') {
      // Hide dropdown and show input field
      titleSelect.style.display = 'none';
      titleOtherInput.style.display = 'block';
      titleOtherInput.required = true;
      titleSelect.removeAttribute('required');
      // Focus on input field
      setTimeout(() => titleOtherInput.focus(), 100);
    } else {
      // Show dropdown and hide input field
      titleSelect.style.display = 'block';
      titleOtherInput.style.display = 'none';
      titleOtherInput.required = false;
      titleSelect.required = true;
    }
  }
  
  // Set up title change event listener
  if (titleSelect) {
    titleSelect.addEventListener('change', handleTitleChange);
  }
  
  if (categorySelect && titleSelect) {
    categorySelect.addEventListener('change', function() {
      const category = this.value;
      
      // Clear existing options and show loading state
      titleSelect.innerHTML = '<option value="">Loading titles...</option>';
      titleSelect.disabled = true;
      titleSelect.style.pointerEvents = 'none';
      
      // Ensure dropdown is visible
      if (titleSelect) {
        titleSelect.style.display = 'block';
      }
      if (titleOtherInput) {
        titleOtherInput.style.display = 'none';
        titleOtherInput.value = '';
      }
      
      if (!category) {
        titleSelect.innerHTML = '<option value="">Select Category first, then choose title</option>';
        titleSelect.disabled = false;
        titleSelect.removeAttribute('disabled');
        titleSelect.style.pointerEvents = 'auto';
        titleSelect.style.cursor = 'pointer';
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

        // Add "Other" option
        const otherOption = document.createElement('option');
        otherOption.value = 'other';
        otherOption.textContent = 'Other';
        titleSelect.appendChild(otherOption);

        // Enable dropdown and make it clickable
        titleSelect.disabled = false;
        titleSelect.removeAttribute('disabled');
        titleSelect.style.display = 'block';
        titleSelect.style.visibility = 'visible';
        titleSelect.style.pointerEvents = 'auto';
        titleSelect.style.opacity = '1';
        titleSelect.style.cursor = 'pointer';
        titleSelect.readOnly = false;
        titleSelect.removeAttribute('readonly');
        
        if (titleOtherInput) {
          titleOtherInput.style.display = 'none';
        }
        
        // Restore previously selected title if any
        const previous = titleSelect.getAttribute('data-prev');
        if (previous) {
          const opt = Array.from(titleSelect.options).find(o => o.value === previous);
          if (opt) {
            titleSelect.value = previous;
            if (previous === 'other') {
              handleTitleChange();
            }
          } else if (previous === 'other') {
            // If previous was "other", restore it
            titleSelect.value = 'other';
            handleTitleChange();
            if (titleOtherInput) {
              const oldOther = '{{ old('title_other', $complaint->title) }}';
              if (oldOther) {
                titleOtherInput.value = oldOther;
              }
            }
          }
        } else if (currentTitle) {
          // Check if current title is in the list
          const opt = Array.from(titleSelect.options).find(o => o.value === currentTitle);
          if (opt) {
            titleSelect.value = currentTitle;
          } else if (currentTitle) {
            // Current title not in list, select "Other" and show input
            titleSelect.value = 'other';
            handleTitleChange();
            if (titleOtherInput) {
              titleOtherInput.value = currentTitle;
            }
          }
        }
      })
      .catch(error => {
        console.error('Error loading complaint titles:', error);
        titleSelect.innerHTML = '<option value="">Failed to load titles. Please try again.</option>';
        titleSelect.disabled = false;
        titleSelect.removeAttribute('disabled');
        titleSelect.style.display = 'block';
        titleSelect.style.pointerEvents = 'auto';
        titleSelect.style.cursor = 'pointer';
      });
    });
    
    // Trigger on page load if category is pre-selected
    if (categorySelect && categorySelect.value) {
      // Preserve current title if present
      if (titleSelect && currentTitle) {
        titleSelect.setAttribute('data-prev', currentTitle);
      }
      // Trigger change event to load titles
      categorySelect.dispatchEvent(new Event('change'));
    } else if (currentTitle) {
      // If no category but has current title, check if it's a custom title
      // This handles the case where title might be custom
      const titleOptions = Array.from(titleSelect.options).map(opt => opt.value);
      if (!titleOptions.includes(currentTitle) && currentTitle !== 'other') {
        // Current title is not in dropdown, it's a custom title
        // Add "Other" option and select it
        const otherOption = document.createElement('option');
        otherOption.value = 'other';
        otherOption.textContent = 'Other';
        titleSelect.appendChild(otherOption);
        titleSelect.value = 'other';
        if (titleOtherInput) {
          titleOtherInput.value = currentTitle;
          titleSelect.style.display = 'none';
          titleOtherInput.style.display = 'block';
          titleOtherInput.required = true;
          titleSelect.removeAttribute('required');
        }
      }
    }
  }
  
  // Form submit handler: sync title_other to title when "Other" is selected
  const complaintForm = document.querySelector('form[action*="complaints.update"]');
  if (complaintForm && titleSelect && titleOtherInput) {
    complaintForm.addEventListener('submit', function(e) {
      if (titleSelect.value === 'other' || titleOtherInput.style.display !== 'none') {
        // User selected "Other" option
        if (!titleOtherInput.value || titleOtherInput.value.trim() === '') {
          e.preventDefault();
          alert('Please enter a custom complaint title.');
          titleOtherInput.focus();
          return false;
        }
        
        // Remove any existing hidden title input
        const existingHiddenTitle = document.getElementById('title_hidden');
        if (existingHiddenTitle) {
          existingHiddenTitle.remove();
        }
        
        // Remove name from select dropdown so it doesn't send "other"
        titleSelect.removeAttribute('name');
        titleSelect.disabled = true; // Disable to prevent sending value
        
        // Create hidden input with custom title value
        const hiddenTitle = document.createElement('input');
        hiddenTitle.type = 'hidden';
        hiddenTitle.id = 'title_hidden';
        hiddenTitle.name = 'title';
        hiddenTitle.value = titleOtherInput.value.trim();
        complaintForm.appendChild(hiddenTitle);
        
        // Also send title_other field explicitly
        if (!document.getElementById('title_other_field')) {
          const titleOtherField = document.createElement('input');
          titleOtherField.type = 'hidden';
          titleOtherField.id = 'title_other_field';
          titleOtherField.name = 'title_other';
          titleOtherField.value = titleOtherInput.value.trim();
          complaintForm.appendChild(titleOtherField);
        }
      } else {
        // Normal title selected - ensure select has name attribute
        titleSelect.setAttribute('name', 'title');
        titleSelect.disabled = false;
        
        // Remove hidden inputs if they exist
        const hiddenTitle = document.getElementById('title_hidden');
        if (hiddenTitle) {
          hiddenTitle.remove();
        }
        const titleOtherField = document.getElementById('title_other_field');
        if (titleOtherField) {
          titleOtherField.remove();
        }
      }
    });
  }

  // City -> Sector dynamic loading (mirror create view)
  const citySelect = document.getElementById('city_id');
  const sectorSelect = document.getElementById('sector_id');
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

    // Preselect defaults from server (existing complaint)
    const defaultCityId = '{{ old('city_id', $defaultCityId ?? '') }}';
    const defaultSectorId = '{{ old('sector_id', $defaultSectorId ?? '') }}';
    if (defaultCityId) {
      citySelect.value = defaultCityId;
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
          sectorSelect.innerHTML = '<option value=\"\">No sectors found for this city</option>';
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
});
</script>
@endpush



