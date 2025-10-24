@extends('layouts.sidebar')

@section('title', 'Edit User — CMS Admin')

@section('content')
<!-- EDIT USER FORM -->
<div class="card-glass">
  <div class="card-body">
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
      @csrf
      @method('PUT')
      
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="username" class="form-label text-white">Username <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                   id="username" name="username" value="{{ old('username', $user->username) }}" required>
            @error('username')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="mb-3">
            <label for="email" class="form-label text-white">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="full_name" class="form-label text-white">Full Name</label>
            <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                   id="full_name" name="full_name" value="{{ old('full_name', $user->full_name) }}">
            @error('full_name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="mb-3">
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="password" class="form-label text-white">New Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                   id="password" name="password">
            <div class="form-text text-muted">Leave blank to keep current password</div>
            @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="mb-3">
            <label for="password_confirmation" class="form-label text-white">Confirm New Password</label>
            <input type="password" class="form-control" 
                   id="password_confirmation" name="password_confirmation">
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="role_id" class="form-label text-white">Role <span class="text-danger">*</span></label>
            <select class="form-select @error('role_id') is-invalid @enderror" 
                    id="role_id" name="role_id" required>
              <option value="">Select a role</option>
              @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                  {{ $role->role_name }}
                </option>
              @endforeach
            </select>
            @error('role_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="mb-3">
            <label for="status" class="form-label text-white">Status</label>
            <select class="form-select @error('status') is-invalid @enderror" 
                    id="status" name="status">
              <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
              <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="address" class="form-label text-white">Address</label>
            <textarea class="form-control @error('address') is-invalid @enderror" 
                      id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
            @error('address')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="mb-3">
            <label for="city" class="form-label text-white">City</label>
            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                   id="city" name="city" value="{{ old('city', $user->city) }}">
            @error('city')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="mb-3">
            <label for="country" class="form-label text-white">Country</label>
            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                   id="country" name="country" value="{{ old('country', $user->country) }}">
            @error('country')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>
      
      <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('admin.users.index', $user) }}" class="btn btn-outline-secondary">
          <i data-feather="x" class="me-2"></i>Cancel
        </a>
        <button type="submit" class="btn btn-accent">
          <i data-feather="save" class="me-2"></i>Update User
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