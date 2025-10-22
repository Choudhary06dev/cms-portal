@extends('layouts.sidebar')

@section('title', 'Edit Employee')

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
                    <h5 class="card-title mb-0">Edit Employee: {{ $employee->user->username ?? 'N/A' }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.employees.update', $employee) }}" method="POST" autocomplete="off" novalidate>
                        @csrf
                        @method('PUT')
                        
                        <!-- Hidden dummy fields to prevent browser autofill -->
                        <div style="display: none;">
                            <input type="text" name="fake_username" autocomplete="off">
                            <input type="password" name="fake_password" autocomplete="off">
                            <input type="email" name="fake_email" autocomplete="off">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                           id="username" name="username" value="{{ old('username', $employee->user->username) }}" autocomplete="off" required>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $employee->user->email) }}" autocomplete="off">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $employee->user->phone) }}" autocomplete="off">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select class="form-select @error('role_id') is-invalid @enderror" 
                                            id="role_id" name="role_id" required>
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" 
                                                    {{ old('role_id', $employee->user->role_id) == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
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
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" autocomplete="new-password">
                                    <small class="form-text text-muted">Leave blank to keep current password</small>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('department') is-invalid @enderror" 
                                           id="department" name="department" value="{{ old('department', $employee->department) }}" autocomplete="off" required>
                                    @error('department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="designation" class="form-label">Designation <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('designation') is-invalid @enderror" 
                                           id="designation" name="designation" value="{{ old('designation', $employee->designation) }}" autocomplete="off" required>
                                    @error('designation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="biometric_id" class="form-label">Biometric ID</label>
                                    <input type="text" class="form-control @error('biometric_id') is-invalid @enderror" 
                                           id="biometric_id" name="biometric_id" value="{{ old('biometric_id', $employee->biometric_id) }}" autocomplete="off">
                                    @error('biometric_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="leave_quota" class="form-label">Leave Quota <span class="text-danger">*</span></label>
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status">
                                        <option value="active" {{ old('status', $employee->user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $employee->user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Employee</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Clear form fields on focus to prevent browser autofill interference
document.addEventListener('DOMContentLoaded', function() {
    // Clear on focus events for text and email fields
    const inputs = document.querySelectorAll('input[type="text"], input[type="email"]');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            // Only clear if the field has been auto-filled by browser
            if (this.value && this.value !== this.defaultValue) {
                // Don't clear if it's the original value from the database
                if (this.name === 'username' && this.value === '{{ $employee->user->username }}') return;
                if (this.name === 'email' && this.value === '{{ $employee->user->email }}') return;
                if (this.name === 'phone' && this.value === '{{ $employee->user->phone }}') return;
                if (this.name === 'department' && this.value === '{{ $employee->department }}') return;
                if (this.name === 'designation' && this.value === '{{ $employee->designation }}') return;
                if (this.name === 'biometric_id' && this.value === '{{ $employee->biometric_id }}') return;
            }
        });
    });
});
</script>
@endsection
