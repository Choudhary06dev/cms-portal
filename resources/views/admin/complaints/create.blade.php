@extends('layouts.sidebar')

@section('title', 'Create New Complaint')

@push('head')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@endpush

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">Create New Complaint</h5>
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
                  <label for="title" class="form-label">Complaint Title <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('title') is-invalid @enderror" 
                         id="title" name="title" value="" autocomplete="off" required>
                  @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
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
                  <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
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
                  <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
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
                  <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
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
                  <label for="assigned_employee_id" class="form-label">Assign Employee</label>
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
                  <label for="status" class="form-label">Status</label>
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
              <a href="{{ route('admin.complaints.index') }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-primary">Create Complaint</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

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


