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
              <h6 class="text-white fw-bold mb-3">Select Modules</h6>
              <div class="row">
                @php
                $modules = ['users', 'roles', 'employees', 'clients', 'complaints', 'spares', 'approvals', 'reports', 'sla'];
                @endphp
                
                @foreach($modules as $module)
                <div class="col-md-6 col-lg-4 mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" 
                           id="{{ $module }}" 
                           name="permissions[]"
                           value="{{ $module }}" {{ old('permissions') && in_array($module, old('permissions')) ? 'checked' : '' }}>
                    <label class="form-check-label text-white" for="{{ $module }}">
                      {{ ucfirst($module) }}
                    </label>
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
  feather.replace();
});
</script>
@endpush
@endsection
