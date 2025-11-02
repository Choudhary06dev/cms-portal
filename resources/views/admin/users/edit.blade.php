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
            <label for="email" class="form-label text-white">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                   id="email" name="email" value="{{ old('email', $user->email) }}">
            @error('email')
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
                   id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
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
            <label for="city_id" class="form-label text-white">City</label>
            <select class="form-select @error('city_id') is-invalid @enderror" 
                    id="city_id" name="city_id">
              <option value="">Select City (if required)</option>
              @foreach($cities as $city)
                <option value="{{ $city->id }}" data-province="{{ $city->province ?? '' }}" {{ old('city_id', $user->city_id) == $city->id ? 'selected' : '' }}>
                  {{ $city->name }}{{ $city->province ? ' (' . $city->province . ')' : '' }}
                </option>
              @endforeach
            </select>
            <small class="text-muted">Required for: GE, Complaint Center, Department Staff</small>
            @error('city_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="mb-3">
            <label for="sector_id" class="form-label text-white">Sector</label>
            <select class="form-select @error('sector_id') is-invalid @enderror" 
                    id="sector_id" name="sector_id">
              <option value="">Select City first</option>
              @foreach($sectors as $sector)
                <option value="{{ $sector->id }}" {{ old('sector_id', $user->sector_id) == $sector->id ? 'selected' : '' }}>
                  {{ $sector->name }}
                </option>
              @endforeach
            </select>
            <small class="text-muted">Required for: Complaint Center, Department Staff</small>
            @error('sector_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

      <div class="row">
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
        <div class="col-md-6"></div>
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

  // Dynamic sector loading based on city
  const citySelect = document.getElementById('city_id');
  const sectorSelect = document.getElementById('sector_id');
  const roleSelect = document.getElementById('role_id');
  const currentCityId = '{{ old('city_id', $user->city_id) }}';
  const currentSectorId = '{{ old('sector_id', $user->sector_id) }}';

  if (citySelect && sectorSelect) {
    citySelect.addEventListener('change', function() {
      const cityId = this.value;
      const roleText = roleSelect ? roleSelect.options[roleSelect.selectedIndex].text.toLowerCase() : '';
      
      // Don't load sectors if role is GE (garrison_engineer) - GE sees all sectors
      if (roleText.includes('garrison engineer') || roleText.includes('garrison_engineer')) {
        sectorSelect.innerHTML = '<option value="">N/A (GE sees all sectors)</option>';
        sectorSelect.disabled = true;
        return;
      }
      
      if (!cityId) {
        sectorSelect.innerHTML = '<option value="">Select City first</option>';
        sectorSelect.disabled = true;
        return;
      }

      sectorSelect.innerHTML = '<option value="">Loading sectors...</option>';
      sectorSelect.disabled = true;

      fetch(`{{ route('admin.sectors.by-city') }}?city_id=${cityId}`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        credentials: 'same-origin'
      })
      .then(response => response.json())
      .then(data => {
        sectorSelect.innerHTML = '<option value="">Select Sector</option>';
        if (data && data.length > 0) {
          data.forEach(sector => {
            const option = document.createElement('option');
            option.value = sector.id;
            option.textContent = sector.name;
            if (currentSectorId && sector.id == currentSectorId) {
              option.selected = true;
            }
            sectorSelect.appendChild(option);
          });
        }
        sectorSelect.disabled = false;
      })
      .catch(error => {
        console.error('Error loading sectors:', error);
        sectorSelect.innerHTML = '<option value="">Error loading sectors</option>';
        sectorSelect.disabled = false;
      });
    });

    // Handle role change - show/hide city/sector fields
    if (roleSelect) {
      roleSelect.addEventListener('change', function() {
        const roleText = this.options[this.selectedIndex].text.toLowerCase();
        
        // Enable/disable city and sector based on role
        if (roleText.includes('director') || roleText.includes('admin')) {
          citySelect.disabled = true;
          sectorSelect.disabled = true;
          citySelect.value = '';
          sectorSelect.innerHTML = '<option value="">Select City first</option>';
          citySelect.required = false;
          sectorSelect.required = false;
        } else if (roleText.includes('garrison engineer') || roleText.includes('garrison_engineer')) {
          citySelect.disabled = false;
          sectorSelect.disabled = true;
          sectorSelect.innerHTML = '<option value="">N/A</option>';
          citySelect.required = true;
          sectorSelect.required = false;
        } else if (roleText.includes('complaint center') || roleText.includes('complaint_center') || 
                   roleText.includes('department staff') || roleText.includes('department_staff')) {
          citySelect.disabled = false;
          citySelect.required = true;
          sectorSelect.required = true;
          // Sector will be enabled when city is selected
        } else {
          citySelect.disabled = false;
          sectorSelect.disabled = false;
          citySelect.required = false;
          sectorSelect.required = false;
        }
      });

      // Trigger on page load if role is pre-selected
      if (roleSelect.value) {
        roleSelect.dispatchEvent(new Event('change'));
      }
    }

    // Trigger city change if pre-selected
    if (citySelect.value) {
      citySelect.dispatchEvent(new Event('change'));
    }
  }
</script>
@endpush