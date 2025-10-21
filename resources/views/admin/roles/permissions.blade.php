@extends('layouts.sidebar')

@section('title', 'Manage Role Permissions')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5 class="card-title mb-0">
                <i data-feather="shield" class="me-2"></i>Manage Permissions: {{ $role->role_name }}
              </h5>
              <p class="text-muted mb-0">Configure what this role can access in the system</p>
            </div>
            <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-secondary btn-sm">
              <i data-feather="arrow-left"></i> Back to Role
            </a>
          </div>
        </div>
        <div class="card-body">
          @if(session('error'))
            <div class="alert alert-danger">
              <i data-feather="alert-circle" class="me-2"></i>{{ session('error') }}
            </div>
          @endif
          
          @if(session('success'))
            <div class="alert alert-success">
              <i data-feather="check-circle" class="me-2"></i>{{ session('success') }}
            </div>
          @endif
          
          <form action="{{ route('admin.roles.update-permissions', $role) }}" method="POST">
            @csrf
            
            <div class="row">
              @php
              $modules = ['users', 'roles', 'employees', 'clients', 'complaints', 'spares', 'approvals', 'reports', 'sla'];
              $actions = ['view', 'add', 'edit', 'delete'];
              @endphp
              
              @foreach($modules as $module)
              <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                  <div class="card-header bg-primary text-white">
                    <h6 class="card-title mb-0">
                      <i data-feather="folder" class="me-2"></i>{{ ucfirst($module) }}
                    </h6>
                  </div>
                  <div class="card-body">
                    @foreach($actions as $action)
                    @php
                    $permission = $role->rolePermissions->where('module_name', $module)->first();
                    $isChecked = $permission ? $permission->{"can_{$action}"} : false;
                    @endphp
                    <div class="form-check mb-2">
                      <input class="form-check-input" type="checkbox" 
                             id="{{ $module }}_{{ $action }}" 
                             name="permissions[{{ $module }}][{{ $action }}]"
                             value="1" {{ $isChecked ? 'checked' : '' }}>
                      <label class="form-check-label" for="{{ $module }}_{{ $action }}">
                        <i data-feather="{{ $action === 'view' ? 'eye' : ($action === 'add' ? 'plus' : ($action === 'edit' ? 'edit' : 'trash-2')) }}" class="me-1"></i>
                        {{ ucfirst($action) }}
                      </label>
                    </div>
                    @endforeach
                  </div>
                </div>
              </div>
              @endforeach
            </div>

            <!-- Summary Section -->
            <div class="row mt-4">
              <div class="col-12">
                <div class="alert alert-info">
                  <h6 class="alert-heading">
                    <i data-feather="info" class="me-2"></i>Permission Summary
                  </h6>
                  <p class="mb-2">This role currently has <strong>{{ $role->rolePermissions->count() }}</strong> permission sets configured.</p>
                  <small class="text-muted">
                    Users with this role will inherit all the permissions you configure here. 
                    Make sure to review each module carefully before saving.
                  </small>
                </div>
              </div>
            </div>

            <!-- Debug Section (remove in production) -->
            @if(config('app.debug'))
            <div class="row mt-3">
              <div class="col-12">
                <div class="alert alert-warning">
                  <h6 class="alert-heading">Debug Information</h6>
                  <p><strong>Role ID:</strong> {{ $role->id }}</p>
                  <p><strong>Role Name:</strong> {{ $role->role_name }}</p>
                  <p><strong>Current Permissions:</strong> {{ $role->rolePermissions->count() }}</p>
                  <p><strong>Form Action:</strong> {{ route('admin.roles.update-permissions', $role) }}</p>
                  <p><strong>Available Modules:</strong> {{ json_encode($availableModules ?? []) }}</p>
                  <p><strong>Available Actions:</strong> {{ json_encode($availableActions ?? []) }}</p>
                  
                  @if($role->rolePermissions->count() > 0)
                    <h6>Current Permissions Data:</h6>
                    @foreach($role->rolePermissions as $perm)
                      <p><strong>{{ $perm->module_name }}:</strong> View={{ $perm->can_view ? 'Yes' : 'No' }}, Add={{ $perm->can_add ? 'Yes' : 'No' }}, Edit={{ $perm->can_edit ? 'Yes' : 'No' }}, Delete={{ $perm->can_delete ? 'Yes' : 'No' }}</p>
                    @endforeach
                  @else
                    <p><strong>No permissions found for this role.</strong></p>
                  @endif
                </div>
              </div>
            </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mt-4">
              <div>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectAllPermissions()">
                  <i data-feather="check-square" class="me-1"></i>Select All
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm ms-2" onclick="clearAllPermissions()">
                  <i data-feather="square" class="me-1"></i>Clear All
                </button>
              </div>
              <div class="d-flex gap-2">
                <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                  <i data-feather="save" class="me-1"></i>Update Permissions
                </button>
              </div>
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

// Global functions for select all and clear all
function selectAllPermissions() {
  const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');
  allCheckboxes.forEach(checkbox => {
    checkbox.checked = true;
  });
}

function clearAllPermissions() {
  const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');
  allCheckboxes.forEach(checkbox => {
    checkbox.checked = false;
  });
}

// Debug form submission
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form');
  if (form) {
    form.addEventListener('submit', function(e) {
      console.log('Form submitting...');
      const formData = new FormData(form);
      console.log('Form data:');
      for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
      }
    });
  }
});
</script>
@endsection
