@extends('layouts.sidebar')

@section('title', 'Edit Complaint')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">Edit Complaint: #{{ $complaint->id }}</h5>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.complaints.update', $complaint) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('type') is-invalid @enderror" 
                         id="type" name="type" value="{{ old('type', $complaint->type) }}" required>
                  @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="location" class="form-label">Location</label>
                  <input type="text" class="form-control @error('location') is-invalid @enderror" 
                         id="location" name="location" value="{{ old('location', $complaint->location) }}">
                  @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <div class="mb-3">
                  <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                  <textarea class="form-control @error('description') is-invalid @enderror" 
                            id="description" name="description" rows="4" required>{{ old('description', $complaint->description) }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="assigned_employee_id" class="form-label">Assign Employee</label>
                  <select class="form-select @error('assigned_employee_id') is-invalid @enderror" 
                          id="assigned_employee_id" name="assigned_employee_id">
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" 
                            {{ old('assigned_employee_id', $complaint->assigned_employee_id) == $emp->id ? 'selected' : '' }}>
                      {{ $emp->user->username ?? 'Employee #' . $emp->id }}
                    </option>
                    @endforeach
                  </select>
                  @error('assigned_employee_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                  <select class="form-select @error('status') is-invalid @enderror" 
                          id="status" name="status" required>
                    @foreach(['NEW','IN_PROGRESS','RESOLVED','CLOSED','REOPENED'] as $st)
                    <option value="{{ $st }}" 
                            {{ old('status', $complaint->status) == $st ? 'selected' : '' }}>
                      {{ $st }}
                    </option>
                    @endforeach
                  </select>
                  @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
              <a href="{{ route('admin.complaints.index') }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-primary">Update Complaint</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection


