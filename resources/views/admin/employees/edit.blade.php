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
          <label for="name" class="form-label text-white">Name <span class="text-danger">*</span></label>
          <input type="text" class="form-control @error('name') is-invalid @enderror" 
                 id="name" name="name" value="{{ old('name', $employee->name) }}" required>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
      <div class="col-md-6">
        <div class="mb-3">
          <label for="emp_id" class="form-label text-white">Employee ID</label>
          <input type="text" class="form-control @error('emp_id') is-invalid @enderror" 
                 id="emp_id" name="emp_id" value="{{ old('emp_id', $employee->emp_id) }}">
          @error('emp_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="mb-3">
          <label for="email" class="form-label text-white">Email</label>
          <input type="email" class="form-control @error('email') is-invalid @enderror" 
                 id="email" name="email" value="{{ old('email', $employee->email) }}">
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
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
    </div>

    <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="department" class="form-label text-white">Department <span class="text-danger">*</span></label>
            <select class="form-select @error('department') is-invalid @enderror" 
                    id="department" name="department" required>
              <option value="">Select Department</option>
              @foreach ($departments as $dept)
                <option value="{{ $dept->name }}" data-id="{{ $dept->id }}" {{ old('department', $employee->department) == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
              @endforeach
            </select>
            @error('department')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="designation" class="form-label text-white">Designation <span class="text-danger">*</span></label>
            <select class="form-select @error('designation') is-invalid @enderror" 
                    id="designation" name="designation" required>
              <option value="">Select Department First</option>
            </select>
            @error('designation')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="city_id" class="form-label text-white">City</label>
            <select class="form-select @error('city_id') is-invalid @enderror" 
                    id="city_id" name="city_id">
              <option value="">Select City</option>
              @if(isset($cities) && $cities->count() > 0)
                @foreach ($cities as $city)
                  <option value="{{ $city->id }}" data-id="{{ $city->id }}" data-province="{{ $city->province ?? '' }}" {{ old('city_id', $employee->city_id) == $city->id ? 'selected' : '' }}>{{ $city->name }}{{ $city->province ? ' (' . $city->province . ')' : '' }}</option>
                @endforeach
              @endif
            </select>
            @error('city_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="col-md-6">
          <div class="mb-3">
            <label for="sector_id" class="form-label text-white">Sector</label>
            <select class="form-select @error('sector_id') is-invalid @enderror" 
                    id="sector_id" name="sector_id" disabled>
              <option value="">Select City First</option>
            </select>
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
                    id="status" name="status" required>
              <option value="">Select Status</option>
              <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Active</option>
              <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
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
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="leave_quota" class="form-label text-white">Leave Quota (Days)</label>
            <input type="number" class="form-control @error('leave_quota') is-invalid @enderror" 
                   id="leave_quota" name="leave_quota" value="{{ old('leave_quota', $employee->leave_quota) }}" 
                   min="0" max="365">
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
  
  document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('department');
    const designationSelect = document.getElementById('designation');
    const citySelect = document.getElementById('city_id');
    const sectorSelect = document.getElementById('sector_id');
    const currentDepartment = '{{ old('department', $employee->department) }}';
    const currentDesignation = '{{ old('designation', $employee->designation) }}';
    const currentCity = '{{ old('city_id', $employee->city_id) }}';
    const currentSector = '{{ old('sector_id', $employee->sector_id) }}';
    
    // Load sectors on page load if city is already selected
    if (currentCity && citySelect) {
      const selectedOption = citySelect.options[citySelect.selectedIndex];
      const cityIdFromData = selectedOption ? selectedOption.getAttribute('data-id') : null;
      const cityId = cityIdFromData || currentCity;
      
      console.log('Loading sectors on page load - currentCity:', currentCity, 'cityId:', cityId);
      
      if (cityId) {
        fetch(`{{ route('admin.employees.sectors') }}?city_id=${cityId}`, {
          method: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
          }
        })
        .then(response => response.json())
        .then(data => {
          console.log('Sectors loaded on page load:', data);
          sectorSelect.innerHTML = '<option value="">Select Sector</option>';
          
          if (data.sectors && data.sectors.length > 0) {
            data.sectors.forEach(function(sector) {
              const option = document.createElement('option');
              option.value = sector.id;
              option.textContent = sector.name;
              if (sector.id == currentSector) {
                option.selected = true;
              }
              sectorSelect.appendChild(option);
            });
            sectorSelect.disabled = false;
            console.log('Sectors loaded on page load:', data.sectors.length);
          }
        })
        .catch(error => {
          console.error('Error fetching sectors:', error);
        });
      }
    }
    
    // Handle city change
    if (citySelect) {
      citySelect.addEventListener('change', function() {
        // Get the actual city ID value - make sure we're using the value attribute, not text
        const cityId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const cityIdFromData = selectedOption ? selectedOption.getAttribute('data-id') : null;
        
        // Use data-id if available, otherwise use value
        const actualCityId = cityIdFromData || cityId;
        
        console.log('City selected - value:', cityId, 'data-id:', cityIdFromData, 'using:', actualCityId);
        
        // Clear and disable sector dropdown
        sectorSelect.innerHTML = '<option value="">Loading...</option>';
        sectorSelect.disabled = true;
        
        if (actualCityId) {
          // Fetch sectors for this city
          const url = `{{ route('admin.employees.sectors') }}?city_id=${actualCityId}`;
          console.log('Fetching sectors from:', url);
          
          fetch(url, {
            method: 'GET',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json',
            }
          })
          .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            return response.json();
          })
          .then(data => {
            console.log('Sectors data received:', data);
            console.log('Number of sectors for city:', data.sectors ? data.sectors.length : 0);
            sectorSelect.innerHTML = '<option value="">Select Sector</option>';
            
            if (data.sectors && data.sectors.length > 0) {
              data.sectors.forEach(function(sector) {
                const option = document.createElement('option');
                option.value = sector.id;
                option.textContent = sector.name;
                sectorSelect.appendChild(option);
              });
              sectorSelect.disabled = false;
              console.log('Sectors loaded successfully:', data.sectors.length);
            } else {
              sectorSelect.innerHTML = '<option value="">No Sector Available</option>';
              console.log('No sectors found for city ID:', actualCityId);
            }
          })
          .catch(error => {
            console.error('Error fetching sectors:', error);
            sectorSelect.innerHTML = '<option value="">Error Loading Sectors</option>';
          });
        } else {
          sectorSelect.innerHTML = '<option value="">Select City First</option>';
        }
      });
    }
    
    // Load designations on page load if department is already selected
    if (currentDepartment && departmentSelect) {
      const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
      const departmentId = selectedOption ? selectedOption.getAttribute('data-id') : null;
      
      if (departmentId) {
        fetch(`{{ route('admin.employees.designations') }}?department_id=${departmentId}`, {
          method: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
          }
        })
        .then(response => response.json())
        .then(data => {
          designationSelect.innerHTML = '<option value="">Select Designation</option>';
          
          if (data.designations && data.designations.length > 0) {
            data.designations.forEach(function(designation) {
              const option = document.createElement('option');
              option.value = designation.name;
              option.textContent = designation.name;
              if (designation.name === currentDesignation) {
                option.selected = true;
              }
              designationSelect.appendChild(option);
            });
            designationSelect.disabled = false;
          }
        })
        .catch(error => {
          console.error('Error fetching designations:', error);
        });
      }
    }
    
    // Handle department change
    if (departmentSelect) {
      departmentSelect.addEventListener('change', function() {
        const departmentName = this.value;
        
        // Clear and disable designation dropdown
        designationSelect.innerHTML = '<option value="">Loading...</option>';
        designationSelect.disabled = true;
        
        if (departmentName) {
          // Get department ID from selected option
          const selectedOption = this.options[this.selectedIndex];
          const departmentId = selectedOption.getAttribute('data-id');
          
          if (departmentId) {
            // Fetch designations for this department
            fetch(`{{ route('admin.employees.designations') }}?department_id=${departmentId}`, {
              method: 'GET',
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
              }
            })
            .then(response => response.json())
            .then(data => {
              designationSelect.innerHTML = '<option value="">Select Designation</option>';
              
              if (data.designations && data.designations.length > 0) {
                data.designations.forEach(function(designation) {
                  const option = document.createElement('option');
                  option.value = designation.name;
                  option.textContent = designation.name;
                  designationSelect.appendChild(option);
                });
                designationSelect.disabled = false;
              } else {
                designationSelect.innerHTML = '<option value="">No Designation Available</option>';
              }
            })
            .catch(error => {
              console.error('Error fetching designations:', error);
              designationSelect.innerHTML = '<option value="">Error Loading Designations</option>';
            });
          } else {
            // Try to find department by name as fallback
            fetch(`{{ route('admin.employees.designations') }}?department_name=${encodeURIComponent(departmentName)}`, {
              method: 'GET',
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
              }
            })
            .then(response => response.json())
            .then(data => {
              designationSelect.innerHTML = '<option value="">Select Designation</option>';
              
              if (data.designations && data.designations.length > 0) {
                data.designations.forEach(function(designation) {
                  const option = document.createElement('option');
                  option.value = designation.name;
                  option.textContent = designation.name;
                  designationSelect.appendChild(option);
                });
                designationSelect.disabled = false;
              } else {
                designationSelect.innerHTML = '<option value="">No Designation Available</option>';
              }
            })
            .catch(error => {
              console.error('Error fetching designations:', error);
              designationSelect.innerHTML = '<option value="">Error Loading Designations</option>';
            });
          }
        } else {
          designationSelect.innerHTML = '<option value="">Select Department First</option>';
        }
      });
    }
  });
</script>
@endpush