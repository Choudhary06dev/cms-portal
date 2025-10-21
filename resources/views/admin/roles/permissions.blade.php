@extends('layouts.admin')

@section('title', 'Manage Role Permissions')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Manage Permissions: {{ $role->role_name }}</h5>
          <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-secondary btn-sm">
            <i data-feather="arrow-left"></i> Back to Role
          </a>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.roles.update-permissions', $role) }}" method="POST">
            @csrf
            
            <div class="row">
              @php
              $modules = ['users', 'roles', 'employees', 'clients', 'complaints', 'spares', 'approvals', 'reports', 'sla'];
              $actions = ['view', 'add', 'edit', 'delete'];
              @endphp
              
              @foreach($modules as $module)
              <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                  <div class="card-header">
                    <h6 class="card-title mb-0">{{ ucfirst($module) }}</h6>
                  </div>
                  <div class="card-body">
                    @foreach($actions as $action)
                    @php
                    $permission = $role->rolePermissions->where('module_name', $module)->first();
                    $isChecked = $permission ? $permission->{"can_{$action}"} : false;
                    @endphp
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" 
                             id="{{ $module }}_{{ $action }}" 
                             name="permissions[{{ $module }}][{{ $action }}]"
                             value="1" {{ $isChecked ? 'checked' : '' }}>
                      <label class="form-check-label" for="{{ $module }}_{{ $action }}">
                        {{ ucfirst($action) }}
                      </label>
                    </div>
                    @endforeach
                  </div>
                </div>
              </div>
              @endforeach
            </div>

            <div class="d-flex justify-content-end gap-2">
              <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-primary">Update Permissions</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

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
</script>
@endsection
