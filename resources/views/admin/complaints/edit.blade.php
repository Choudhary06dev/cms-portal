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
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="title" class="form-label text-white">Title <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('title') is-invalid @enderror" 
                         id="title" name="title" value="{{ old('title', $complaint->title) }}" required>
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
                    <option value="{{ $client->id }}" 
                            {{ old('client_id', $complaint->client_id) == $client->id ? 'selected' : '' }}>
                      {{ $client->client_name }}
                    </option>
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
            </div>

            

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="department" class="form-label text-white">Department <span class="text-danger">*</span></label>
                  <select id="department" name="department" class="form-select @error('department') is-invalid @enderror" required>
                    <option value="">Select Department</option>
                    @if(isset($departments) && $departments->count() > 0)
                      @foreach($departments as $dep)
                        <option value="{{ $dep }}" {{ old('department', $complaint->department) == $dep ? 'selected' : '' }}>{{ $dep }}</option>
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
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" data-department="{{ $emp->department }}"
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

            <!-- Spare Parts Section -->
            <div class="row mt-1">
              <div class="col-12">                
                <div class="row g-2 align-items-end">
                  <div class="col-md-3">
                    <label for="status" class="form-label text-white">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" 
                            id="status" name="status" required>
                      @foreach(\App\Models\Complaint::getStatuses() as $statusValue => $statusLabel)
                        <option value="{{ $statusValue }}" {{ old('status', $complaint->status) == $statusValue ? 'selected' : '' }}>{{ $statusLabel }}</option>
                      @endforeach
                    </select>
                    @error('status')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-3">
                    <label class="form-label text-white">Product <span class="text-danger">*</span></label>
                    <select class="form-select @error('spare_parts.0.spare_id') is-invalid @enderror" 
                            name="spare_parts[0][spare_id]" required>
                      <option value="">Select Product</option>
                      @foreach(\App\Models\Spare::where('stock_quantity', '>', 0)->get() as $spare)
                        <option value="{{ $spare->id }}" data-stock="{{ $spare->stock_quantity }}"
                                {{ old('spare_parts.0.spare_id', $complaint->spareParts->first()?->spare_id) == $spare->id ? 'selected' : '' }}>
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
                           name="spare_parts[0][quantity]" min="1" 
                           value="{{ old('spare_parts.0.quantity', $complaint->spareParts->first()?->quantity) }}" required>
                    @error('spare_parts.0.quantity')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>
            </div>

           

            <!-- Description moved below product section -->
            <div class="row mt-3">
              <div class="col-12">
                <div class="mb-3">
                  <label for="description" class="form-label text-white">Description <span class="text-danger">*</span></label>
                  <textarea class="form-control @error('description') is-invalid @enderror" 
                            id="description" name="description" rows="4" required>{{ old('description', $complaint->description) }}</textarea>
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
  const departmentSelect = document.getElementById('department');
  const employeeSelect = document.getElementById('assigned_employee_id');
  function filterEmployees() {
    if (!departmentSelect || !employeeSelect) return;
    const dep = departmentSelect.value;
    Array.from(employeeSelect.options).forEach(opt => {
      if (!opt.value) return;
      const od = opt.getAttribute('data-department') || '';
      opt.hidden = dep && od !== dep;
    });
    const sel = employeeSelect.selectedOptions[0];
    if (sel && sel.hidden) employeeSelect.value = '';
  }
  if (departmentSelect && employeeSelect) {
    departmentSelect.addEventListener('change', filterEmployees);
    filterEmployees();
  }
});
</script>
@endpush


