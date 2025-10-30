@extends('layouts.sidebar')

@section('title', 'Create New User — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Create New User</h2>
      <p class="text-light">Add a new user to the system</p>
    </div>
    
  </div>
</div>

<!-- CREATE USER FORM -->
<div class="card-glass">
  <div class="card-body">
    <form action="{{ route('admin.users.store') }}" method="POST">
      @csrf
      
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="username" class="form-label text-white">Username <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                   id="username" name="username" value="{{ old('username') }}" required>
            @error('username')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="mb-3">
            <label for="email" class="form-label text-white">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                   id="email" name="email" value="{{ old('email') }}">
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>
      
      <!-- Make all rows two-column like Username/Email -->
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="phone" class="form-label text-white">Phone</label>
            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                   id="phone" name="phone" value="{{ old('phone') }}">
            @error('phone')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="col-md-6">
          <div class="mb-3">
            <label for="role_id" class="form-label text-white">Role <span class="text-danger">*</span></label>
            <select class="form-select @error('role_id') is-invalid @enderror" 
                    id="role_id" name="role_id" required>
              <option value="">Select a role</option>
              @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                  {{ $role->role_name }}
                </option>
              @endforeach
            </select>
            @error('role_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="password" class="form-label text-white">Password <span class="text-danger">*</span></label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                   id="password" name="password" required>
            @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="col-md-6">
          <div class="mb-3">
            <label for="password_confirmation" class="form-label text-white">Confirm Password <span class="text-danger">*</span></label>
            <input type="password" class="form-control" 
                   id="password_confirmation" name="password_confirmation" required>
          </div>
        </div>
      </div>

      

      <div class="row">
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
        <div class="col-md-6"></div>
      </div>
      
      <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
          <i data-feather="x" class="me-2"></i>Cancel
        </a>
        <button type="submit" class="btn btn-accent">
          <i data-feather="user-plus" class="me-2"></i>Create User
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('styles')
<style>
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
  feather.replace();
</script>
@endpush