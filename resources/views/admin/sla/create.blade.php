@extends('layouts.sidebar')

@section('title', 'Create SLA Rule')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">Create New SLA Rule</h5>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.sla.store') }}" method="POST">
            @csrf
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="complaint_type" class="form-label">Complaint Type <span class="text-danger">*</span></label>
                  <select class="form-select @error('complaint_type') is-invalid @enderror" 
                          id="complaint_type" name="complaint_type" required>
                    <option value="">Select Complaint Type</option>
                    @foreach($complaintTypes as $type)
                    <option value="{{ $type }}" {{ old('complaint_type') == $type ? 'selected' : '' }}>
                      {{ ucfirst($type) }}
                    </option>
                    @endforeach
                  </select>
                  @error('complaint_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="max_response_time" class="form-label">Max Response Time (Hours) <span class="text-danger">*</span></label>
                  <input type="number" class="form-control @error('max_response_time') is-invalid @enderror" 
                         id="max_response_time" name="max_response_time" value="{{ old('max_response_time') }}" required>
                  @error('max_response_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="max_resolution_time" class="form-label">Max Resolution Time (Hours) <span class="text-danger">*</span></label>
                  <input type="number" class="form-control @error('max_resolution_time') is-invalid @enderror" 
                         id="max_resolution_time" name="max_resolution_time" value="{{ old('max_resolution_time') }}" required>
                  @error('max_resolution_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="notify_to" class="form-label">Notify To</label>
                  <select class="form-select @error('notify_to') is-invalid @enderror" 
                          id="notify_to" name="notify_to">
                    <option value="">Select User</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('notify_to') == $user->id ? 'selected' : '' }}>
                      {{ $user->username }} ({{ $user->role->role_name ?? 'No Role' }})
                    </option>
                    @endforeach
                  </select>
                  @error('notify_to')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="escalation_level" class="form-label">Escalation Level</label>
                  <select class="form-select @error('escalation_level') is-invalid @enderror" 
                          id="escalation_level" name="escalation_level">
                    <option value="1" {{ old('escalation_level') == '1' ? 'selected' : '' }}>Level 1</option>
                    <option value="2" {{ old('escalation_level') == '2' ? 'selected' : '' }}>Level 2</option>
                    <option value="3" {{ old('escalation_level') == '3' ? 'selected' : '' }}>Level 3</option>
                  </select>
                  @error('escalation_level')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="status" class="form-label">Status</label>
                  <select class="form-select @error('status') is-invalid @enderror" 
                          id="status" name="status">
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                  </select>
                  @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <div class="mb-3">
                  <label for="description" class="form-label">Description</label>
                  <textarea class="form-control @error('description') is-invalid @enderror" 
                            id="description" name="description" rows="3">{{ old('description') }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
              <a href="{{ route('admin.sla.index') }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-primary">Create SLA Rule</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
