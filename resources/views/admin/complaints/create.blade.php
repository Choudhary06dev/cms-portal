@extends('layouts.sidebar')

@section('title', 'Create New Complaint — CMS Admin')

@section('content')
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
                         id="title" name="title" value="" autocomplete="off" required>
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
                    @foreach($clients as $client)
                      <option value="{{ $client->id }}">{{ $client->client_name }}</option>
                    @endforeach
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
                  <select class="form-select @error('category') is-invalid @enderror" 
                          id="category" name="category" required>
                    <option value="">Select Category</option>
                    <option value="technical">Technical</option>
                    <option value="service">Service</option>
                    <option value="billing">Billing</option>
                    <option value="other">Other</option>
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
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                  </select>
                  @error('priority')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <div class="mb-3">
                  <label for="description" class="form-label text-white">Description <span class="text-danger">*</span></label>
                  <textarea class="form-control @error('description') is-invalid @enderror" 
                            id="description" name="description" rows="4" autocomplete="off" required></textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="assigned_employee_id" class="form-label text-white">Assign Employee</label>
                  <select class="form-select @error('assigned_employee_id') is-invalid @enderror" 
                          id="assigned_employee_id" name="assigned_employee_id">
                    <option value="">Select Employee (Optional)</option>
                    @foreach($employees as $employee)
                      <option value="{{ $employee->id }}">{{ $employee->user->username }} - {{ $employee->user->name }}</option>
                    @endforeach
                  </select>
                  @error('assigned_employee_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="status" class="form-label text-white">Status</label>
                  <select class="form-select @error('status') is-invalid @enderror" 
                          id="status" name="status">
                    <option value="new">New</option>
                    <option value="assigned">Assigned</option>
                    <option value="in_progress">In Progress</option>
                  </select>
                  @error('status')
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
document.addEventListener('DOMContentLoaded', function() {
    // Clear form fields on page load
    function clearFields() {
        const inputs = document.querySelectorAll('input[type="text"], textarea');
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
    
    // Clear fields on focus if they have old values
    const inputs = document.querySelectorAll('input[type="text"], textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            if (this.value) {
                this.value = '';
            }
        });
    });
});
</script>
@endpush


