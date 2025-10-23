@extends('layouts.sidebar')

@section('title', 'Create New Client — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Create New Client</h2>
      <p class="text-light">Add a new client to the system</p>
    </div>
    <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">
      <i data-feather="arrow-left" class="me-2"></i>Back to Clients
    </a>
  </div>
</div>

<!-- CLIENT FORM -->
<div class="card-glass">
  <div class="card-header">
    <h5 class="card-title mb-0 text-white">
      <i data-feather="user-plus" class="me-2"></i>Client Information
    </h5>
  </div>
  <div class="card-body">
          <form action="{{ route('admin.clients.store') }}" method="POST" autocomplete="off" novalidate>
            @csrf
            
            <!-- Hidden dummy fields to prevent browser autofill -->
            <div style="display: none;">
                <input type="text" name="fake_name" autocomplete="off">
                <input type="email" name="fake_email" autocomplete="off">
                <input type="tel" name="fake_phone" autocomplete="off">
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="client_name" class="form-label text-white">Client Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('client_name') is-invalid @enderror" 
                         id="client_name" name="client_name" value="" autocomplete="off" required>
                  @error('client_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="contact_person" class="form-label text-white">Contact Person</label>
                  <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                         id="contact_person" name="contact_person" value="" autocomplete="off">
                  @error('contact_person')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="phone" class="form-label text-white">Phone</label>
                  <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                         id="phone" name="phone" value="" autocomplete="off">
                  @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="email" class="form-label text-white">Email</label>
                  <input type="email" class="form-control @error('email') is-invalid @enderror" 
                         id="email" name="email" value="" autocomplete="off">
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="address" class="form-label text-white">Address</label>
                  <textarea class="form-control @error('address') is-invalid @enderror" 
                            id="address" name="address" rows="3" autocomplete="off"></textarea>
                  @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="city" class="form-label text-white">City</label>
                  <input type="text" class="form-control @error('city') is-invalid @enderror" 
                         id="city" name="city" value="" autocomplete="off">
                  @error('city')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="state" class="form-label text-white">State <span class="text-danger">*</span></label>
                  <select class="form-select @error('state') is-invalid @enderror" 
                          id="state" name="state" required>
                    <option value="">Select State</option>
                    <option value="sindh">Sindh</option>
                    <option value="punjab">Punjab</option>
                    <option value="kpk">KPK</option>
                    <option value="balochistan">Balochistan</option>
                    <option value="other">Other</option>
                  </select>
                  @error('state')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="pincode" class="form-label text-white">Pincode <small class="text-muted">(4 digits only)</small></label>
                    <input type="tel" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" class="form-control @error('pincode') is-invalid @enderror" 
                           id="pincode" name="pincode" value="{{ old('pincode', '') }}" autocomplete="off" placeholder="1234" 
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,4)">
                  @error('pincode')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <div class="form-text text-muted">Enter exactly 4 digits</div>
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="status" class="form-label text-white">Status</label>
                  <select class="form-select @error('status') is-invalid @enderror" 
                          id="status" name="status">
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                  </select>
                  @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
              <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">Cancel</a>
              <button type="submit" class="btn btn-accent">Create Client</button>
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
    // Clear form fields on page load only once
    function clearFields() {
        const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], textarea');
        inputs.forEach(input => {
            if (input.value === '') {
                input.value = '';
                input.setAttribute('value', '');
            }
        });
        
        const selects = document.querySelectorAll('select');
        selects.forEach(select => {
            if (select.selectedIndex === 0) {
                select.selectedIndex = 0;
            }
        });
    }
    
    // Clear fields only once on page load
    clearFields();
    
    // Don't clear fields on focus to allow user input
});
</script>
@endpush

@push('scripts')
<script>
// Enforce pincode numeric-only and exactly 4 digits
document.addEventListener('DOMContentLoaded', function() {
  const pincodeInput = document.querySelector('input[name="pincode"]');
  if (pincodeInput) {
    // Function to validate and format pincode
    const validatePincode = (value) => {
      // Remove all non-numeric characters
      let cleanValue = value.replace(/[^0-9]/g, '');
      // Limit to 4 digits
      cleanValue = cleanValue.slice(0, 4);
      return cleanValue;
    };
    
    // Function to update input value and visual feedback
    const updatePincode = (value) => {
      const cleanValue = validatePincode(value);
      pincodeInput.value = cleanValue;
      
      // Visual feedback
      pincodeInput.classList.remove('is-valid', 'is-invalid');
      if (cleanValue.length === 4) {
        pincodeInput.classList.add('is-valid');
      } else if (cleanValue.length > 0) {
        pincodeInput.classList.add('is-invalid');
      }
    };
    
    // Handle input event
    pincodeInput.addEventListener('input', function(e) {
      updatePincode(e.target.value);
    });
    
    // Handle paste event
    pincodeInput.addEventListener('paste', function(e) {
      e.preventDefault();
      const pastedText = (e.clipboardData || window.clipboardData).getData('text');
      updatePincode(pastedText);
    });
    
    // Handle keydown event - prevent non-numeric input
    pincodeInput.addEventListener('keydown', function(e) {
      // Allow: backspace, delete, tab, escape, enter, arrow keys
      if ([8, 9, 27, 13, 46, 37, 38, 39, 40].indexOf(e.keyCode) !== -1 ||
          // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X, Ctrl+Z
          (e.ctrlKey && [65, 67, 86, 88, 90].indexOf(e.keyCode) !== -1)) {
        return;
      }
      
      // Allow only numeric keys (0-9)
      if ((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
        return false;
      }
      
      // If we already have 4 digits and user is not deleting, prevent input
      if (pincodeInput.value.length >= 4 && ![8, 46].includes(e.keyCode)) {
        e.preventDefault();
        return false;
      }
    });
    
    // Handle keypress event as additional safety
    pincodeInput.addEventListener('keypress', function(e) {
      // Only allow numeric characters
      if (!/[0-9]/.test(e.key)) {
        e.preventDefault();
        return false;
      }
      
      // Prevent input if already 4 digits
      if (pincodeInput.value.length >= 4) {
        e.preventDefault();
        return false;
      }
    });
  }
});
</script>
@endpush
