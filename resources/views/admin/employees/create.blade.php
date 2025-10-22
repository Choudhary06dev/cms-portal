@extends('layouts.sidebar')

@section('title', 'Add Employee — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Add New Employee</h2>
      <p class="text-light">Create a new employee record</p>
    </div>
    <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
      <i data-feather="arrow-left" class="me-2"></i>Back to Employees
    </a>
  </div>
</div>

<!-- EMPLOYEE FORM -->
<div class="card-glass">
  <form action="{{ route('admin.employees.store') }}" method="POST" autocomplete="off" novalidate>
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
          <label for="email" class="form-label text-white">Email <span class="text-danger">*</span></label>
          <input type="email" class="form-control @error('email') is-invalid @enderror" 
                 id="email" name="email" value="{{ old('email') }}" required>
          @error('email')
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
          <label for="department" class="form-label text-white">Department <span class="text-danger">*</span></label>
          <input type="text" class="form-control @error('department') is-invalid @enderror" 
                 id="department" name="department" value="{{ old('department') }}" required>
          @error('department')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="mb-3">
          <label for="designation" class="form-label text-white">Designation <span class="text-danger">*</span></label>
          <input type="text" class="form-control @error('designation') is-invalid @enderror" 
                 id="designation" name="designation" value="{{ old('designation') }}" required>
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
                 id="phone" name="phone" value="{{ old('phone') }}">
          @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
      
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
    </div>
    
    <div class="row">
      <div class="col-md-12">
        <div class="mb-3">
          <label for="address" class="form-label text-white">Address</label>
          <textarea class="form-control @error('address') is-invalid @enderror" 
                    id="address" name="address" rows="3">{{ old('address') }}</textarea>
          @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
    </div>
    
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-accent">
        <i data-feather="save" class="me-2"></i>Create Employee
      </button>
      <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
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