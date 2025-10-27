@extends('layouts.sidebar')

@section('title', 'Edit Role — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Edit Role: {{ $role->role_name }}</h2>
      <p class="text-light">Update role information and permissions</p>
    </div>
  </div>
</div>

<!-- ROLE FORM -->
<div class="card-glass">
          <form action="{{ route('admin.roles.update', $role) }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="role_name" class="form-label text-white">Role Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('role_name') is-invalid @enderror" 
                         id="role_name" name="role_name" value="{{ old('role_name', $role->role_name) }}" required>
                  @error('role_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="description" class="form-label text-white">Description</label>
                  <input type="text" class="form-control @error('description') is-invalid @enderror" 
                         id="description" name="description" value="{{ old('description', $role->description) }}">
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <!-- Permissions Section -->
            <div class="mb-4">
              <h6 class="text-white fw-bold mb-3">Select Modules</h6>
              <div class="row">
                @php
                $modules = ['users', 'roles', 'employees', 'clients', 'complaints', 'spares', 'approvals', 'reports', 'sla'];
                @endphp
                
                @foreach($modules as $module)
                @php
                $hasPermission = $role->rolePermissions->where('module_name', $module)->first();
                @endphp
                <div class="col-md-6 col-lg-4 mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" 
                           id="{{ $module }}" 
                           name="permissions[]"
                           value="{{ $module }}" {{ $hasPermission ? 'checked' : '' }}>
                    <label class="form-check-label text-white" for="{{ $module }}">
                      {{ ucfirst($module) }}
                    </label>
                  </div>
                </div>
                @endforeach
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
              <a href="{{ route('admin.roles.index', $role) }}" class="btn btn-outline-secondary">
                <i data-feather="x" class="me-2"></i>Cancel
              </a>
              <button type="submit" class="btn btn-accent">
                <i data-feather="save" class="me-2"></i>Update Role
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
  feather.replace();
});
</script>
@endpush
@endsection
