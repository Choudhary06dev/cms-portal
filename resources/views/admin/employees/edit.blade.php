@extends('layouts.sidebar')

@section('title', 'Edit Employee — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Edit Employee</h2>
      <p class="text-light">Update employee information</p>
    </div>
  </div>
</div>

<!-- EMPLOYEE FORM -->
<div class="card-glass">
  <form action="{{ route('admin.employees.update', $employee) }}" method="POST" autocomplete="off" novalidate>
    @csrf
    @method('PUT')
    
    <div class="row">
      <div class="col-md-6">
        <div class="mb-3">
          <label for="username" class="form-label text-white">Username <span class="text-danger">*</span></label>
          <input type="text" class="form-control @error('username') is-invalid @enderror" 
                 id="username" name="username" value="{{ old('username', $employee->user->username) }}" required>
          @error('username')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="mb-3">
          <label for="email" class="form-label text-white">Email <span class="text-danger">*</span></label>
          <input type="email" class="form-control @error('email') is-invalid @enderror" 
                 id="email" name="email" value="{{ old('email', $employee->user->email) }}" required>
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
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
          <label for="department" class="form-label text-white">Department <span class="text-danger">*</span></label>
          <input type="text" class="form-control @error('department') is-invalid @enderror" 
                 id="department" name="department" value="{{ old('department', $employee->department) }}" required>
          @error('department')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="mb-3">
          <label for="designation" class="form-label text-white">Designation <span class="text-danger">*</span></label>
          <input type="text" class="form-control @error('designation') is-invalid @enderror" 
                 id="designation" name="designation" value="{{ old('designation', $employee->designation) }}" required>
          @error('designation')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
    </div>
    
    <div class="row">
      <div class="col-md-6">
        <div class="mb-3">
          <label for="phone" class="form-label text-white">Phone</label>
          <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                 id="phone" name="phone" value="{{ old('phone', $employee->phone) }}">
          @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="mb-3">
          <label for="status" class="form-label text-white">Status</label>
          <select class="form-select @error('status') is-invalid @enderror" 
                  id="status" name="status" required>
            <option value="">Select Status</option>
            <option value="active" {{ old('status', $employee->user->status) == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $employee->user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
          <label for="role_id" class="form-label text-white">Role <span class="text-danger">*</span></label>
          <select class="form-select @error('role_id') is-invalid @enderror" 
                  id="role_id" name="role_id" required>
            <option value="">Select Role</option>
            @foreach($roles as $role)
              <option value="{{ $role->id }}" {{ old('role_id', $employee->user->role_id) == $role->id ? 'selected' : '' }}>
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
          <label for="biometric_id" class="form-label text-white">Biometric ID</label>
          <input type="text" class="form-control @error('biometric_id') is-invalid @enderror" 
                 id="biometric_id" name="biometric_id" value="{{ old('biometric_id', $employee->biometric_id) }}">
          @error('biometric_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
    </div>
    
    <div class="row">
      <div class="col-md-6">
        <div class="mb-3">
          <label for="date_of_hire" class="form-label text-white">Date of Hire</label>
          <input type="date" class="form-control @error('date_of_hire') is-invalid @enderror" 
                 id="date_of_hire" name="date_of_hire" value="{{ old('date_of_hire', $employee->date_of_hire ? $employee->date_of_hire->format('Y-m-d') : '') }}">
          @error('date_of_hire')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="mb-3">
          <label for="leave_quota" class="form-label text-white">Leave Quota (Days) <span class="text-danger">*</span></label>
          <input type="number" class="form-control @error('leave_quota') is-invalid @enderror" 
                 id="leave_quota" name="leave_quota" value="{{ old('leave_quota', $employee->leave_quota) }}" 
                 min="0" max="365" required>
          @error('leave_quota')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
    </div>
    
    <div class="row">
      <div class="col-md-12">
        <div class="mb-3">
          <label for="address" class="form-label text-white">Address</label>
          <textarea class="form-control @error('address') is-invalid @enderror" 
                    id="address" name="address" rows="3">{{ old('address', $employee->address) }}</textarea>
          @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
    </div>
    
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-accent">
        <i data-feather="save" class="me-2"></i>Update Employee
      </button>
      <a href="{{ route('admin.employees.index', $employee) }}" class="btn btn-outline-secondary">
        <i data-feather="x" class="me-2"></i>Cancel
      </a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  feather.replace();
</script>
@endpush