@extends('layouts.sidebar')

@section('title', 'Create New Role — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Create New Role</h2>
      <p class="text-light">Define a new role and its permissions</p>
    </div>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
      <i data-feather="arrow-left" class="me-2"></i>Back to Roles
    </a>
  </div>
</div>

<!-- ROLE FORM -->
<div class="card-glass">
          <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="role_name" class="form-label text-white">Role Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('role_name') is-invalid @enderror" 
                         id="role_name" name="role_name" value="{{ old('role_name') }}" required>
                  @error('role_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="description" class="form-label text-white">Description</label>
                  <input type="text" class="form-control @error('description') is-invalid @enderror" 
                         id="description" name="description" value="{{ old('description') }}">
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <!-- Permissions Section -->
            <div class="mb-4">
              <h6 class="text-white fw-bold">Permissions</h6>
              <div class="row">
                @php
                $modules = ['users', 'roles', 'employees', 'clients', 'complaints', 'spares', 'approvals', 'reports', 'sla'];
                $actions = ['view', 'add', 'edit', 'delete'];
                @endphp
                
                @foreach($modules as $module)
                <div class="col-md-6 col-lg-4 mb-3">
                  <div class="card-glass">
                    <div class="card-header">
                      <h6 class="card-title mb-0 text-white">{{ ucfirst($module) }}</h6>
                    </div>
                    <div class="card-body">
                      @foreach($actions as $action)
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" 
                               id="{{ $module }}_{{ $action }}" 
                               name="permissions[{{ $module }}][{{ $action }}]"
                               value="1" {{ old("permissions.{$module}.{$action}") ? 'checked' : '' }}>
                        <label class="form-check-label text-white" for="{{ $module }}_{{ $action }}">
                          {{ ucfirst($action) }}
                        </label>
                      </div>
                      @endforeach
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
              <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                <i data-feather="x" class="me-2"></i>Cancel
              </a>
              <button type="submit" class="btn btn-accent">
                <i data-feather="save" class="me-2"></i>Create Role
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Add select all functionality for each module
  const moduleCards = document.querySelectorAll('.card');
  
  moduleCards.forEach(card => {
    const checkboxes = card.querySelectorAll('input[type="checkbox"]');
    const header = card.querySelector('.card-header');
    
    // Create select all checkbox
    const selectAllCheckbox = document.createElement('input');
    selectAllCheckbox.type = 'checkbox';
    selectAllCheckbox.className = 'form-check-input';
    selectAllCheckbox.id = 'select_all_' + card.querySelector('h6').textContent.toLowerCase().replace(' ', '_');
    
    const selectAllLabel = document.createElement('label');
    selectAllLabel.className = 'form-check-label';
    selectAllLabel.htmlFor = selectAllCheckbox.id;
    selectAllLabel.textContent = 'Select All';
    
    const selectAllDiv = document.createElement('div');
    selectAllDiv.className = 'form-check';
    selectAllDiv.appendChild(selectAllCheckbox);
    selectAllDiv.appendChild(selectAllLabel);
    
    header.appendChild(selectAllDiv);
    
    // Handle select all functionality
    selectAllCheckbox.addEventListener('change', function() {
      checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
    });
    
    // Handle individual checkbox changes
    checkboxes.forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        const noneChecked = Array.from(checkboxes).every(cb => !cb.checked);
        
        if (allChecked) {
          selectAllCheckbox.checked = true;
          selectAllCheckbox.indeterminate = false;
        } else if (noneChecked) {
          selectAllCheckbox.checked = false;
          selectAllCheckbox.indeterminate = false;
        } else {
          selectAllCheckbox.checked = false;
          selectAllCheckbox.indeterminate = true;
        }
      });
    });
  });
});
  feather.replace();
</script>
@endpush
@endsection
