@extends('layouts.sidebar')

@section('title', 'Create New Complainant — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Create New Complainant</h2>
      <p class="text-light">Add a new Complainant to the system</p>
    </div>
  </div>
</div>

<!-- CLIENT FORM -->
<div class="card-glass">
  <div class="card-header">
    <h5 class="card-title mb-0 text-white">
      <i data-feather="user-plus" class="me-2"></i>Complainant Information
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
                  <label for="client_name" class="form-label text-white">Complainant<span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('client_name') is-invalid @enderror" 
                         id="client_name" name="client_name" value="{{ old('client_name', '') }}" autocomplete="off" required>
                  @error('client_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="contact_person" class="form-label text-white">Contact Person</label>
                  <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                         id="contact_person" name="contact_person" value="{{ old('contact_person', '') }}" autocomplete="off">
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
                         id="phone" name="phone" value="{{ old('phone', '') }}" autocomplete="off">
                  @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="email" class="form-label text-white">Email</label>
                  <input type="email" class="form-control @error('email') is-invalid @enderror" 
                         id="email" name="email" value="{{ old('email', '') }}" autocomplete="off">
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="city" class="form-label text-white">City <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('city') is-invalid @enderror" 
                         id="city" name="city" value="{{ old('city', '') }}" autocomplete="off" required>
                  @error('city')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="sector" class="form-label text-white">Sector <span class="text-danger">*</span></label>
                  <select class="form-select @error('sector') is-invalid @enderror" id="sector" name="sector" required>
                    <option value="">Select Sector</option>
                    @foreach ($sectors as $sectorName)
                      <option value="{{ $sectorName }}" {{ old('sector') == $sectorName ? 'selected' : '' }}>{{ $sectorName }}</option>
                    @endforeach
                  </select>
                  @error('sector')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="state" class="form-label text-white">State <span class="text-danger">*</span></label>
                  <select class="form-select @error('state') is-invalid @enderror" 
                          id="state" name="state" required>
                    <option value="">Select State</option>
                    <option value="sindh" {{ old('state') == 'sindh' ? 'selected' : '' }}>Sindh</option>
                    <option value="punjab" {{ old('state') == 'punjab' ? 'selected' : '' }}>Punjab</option>
                    <option value="kpk" {{ old('state') == 'kpk' ? 'selected' : '' }}>KPK</option>
                    <option value="balochistan" {{ old('state') == 'balochistan' ? 'selected' : '' }}>Balochistan</option>
                    <option value="other" {{ old('state') == 'other' ? 'selected' : '' }}>Other</option>
                  </select>
                  @error('state')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="status" class="form-label text-white">Status</label>
                  <select class="form-select @error('status') is-invalid @enderror" 
                          id="status" name="status">
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                  </select>
                  @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="mb-3">
                  <label for="address" class="form-label text-white">Address <span class="text-danger">*</span></label>
                  <textarea class="form-control @error('address') is-invalid @enderror" 
                            id="address" name="address" rows="3" autocomplete="off" required>{{ old('address', '') }}</textarea>
                  @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
              <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">Cancel</a>
              <button type="submit" class="btn btn-accent">Create Complainant</button>
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
</script>
@endpush
