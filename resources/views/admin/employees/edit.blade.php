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
          <label for="category" class="form-label text-white">Category <span class="text-danger">*</span></label>
          <select class="form-select @error('category') is-invalid @enderror" 
                  id="category" name="category" required>
            <option value="">Select Category</option>
            @if(isset($categories) && $categories->count() > 0)
              @foreach ($categories as $cat)
                <option value="{{ $cat }}" {{ old('category', $employee->category) == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
              @endforeach
            @endif
          </select>
          @error('category')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
      <div class="col-md-6">
        <div class="mb-3">
          <label for="designation" class="form-label text-white">Designation</label>
          <select class="form-select @error('designation') is-invalid @enderror" 
                  id="designation" name="designation" {{ old('category', $employee->category) ? '' : 'disabled' }}>
            <option value="">{{ old('category', $employee->category) ? 'Loading...' : 'Select Category First' }}</option>
          </select>
          @error('designation')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
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
    const categorySelect = document.getElementById('category');
    const designationSelect = document.getElementById('designation');
    const currentCategory = '{{ old('category', $employee->category) }}';
    const currentDesignation = '{{ old('designation', $employee->designation) }}';
    const citySelect = document.getElementById('city_id');
    const sectorSelect = document.getElementById('sector_id');
    const currentCity = '{{ old('city_id', $employee->city_id) }}';
    const currentSector = '{{ old('sector_id', $employee->sector_id) }}';
    
    // Load designations on page load if category is already selected
    if (currentCategory && categorySelect && designationSelect) {
      // Enable dropdown immediately if category exists
      designationSelect.disabled = false;
      
      // Show loading state
      designationSelect.innerHTML = '<option value="">Loading...</option>';
      
      fetch(`{{ route('admin.employees.designations') }}?category=${encodeURIComponent(currentCategory)}`, {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        }
      })
      .then(response => response.json())
      .then(data => {
        // Start with empty select
        designationSelect.innerHTML = '<option value="">Select Designation</option>';
        
        // First, add current designation if it exists (to ensure it's always available)
        if (currentDesignation) {
          const currentOption = document.createElement('option');
          currentOption.value = currentDesignation;
          currentOption.textContent = currentDesignation;
          currentOption.selected = true;
          designationSelect.appendChild(currentOption);
        }
        
        // Then add all fetched designations
        if (data.designations && data.designations.length > 0) {
          data.designations.forEach(function(designation) {
            // Skip if this designation is the same as current designation (already added)
            if (currentDesignation && designation.name === currentDesignation) {
              return;
            }
            const option = document.createElement('option');
            option.value = designation.name;
            option.textContent = designation.name;
            designationSelect.appendChild(option);
          });
        }
        
        // If no designations found and no current designation, show message
        if ((!data.designations || data.designations.length === 0) && !currentDesignation) {
          designationSelect.innerHTML = '<option value="">No Designation Available</option>';
        }
        
        // Ensure current designation is still selected after adding all options
        if (currentDesignation) {
          designationSelect.value = currentDesignation;
        }
      })
      .catch(error => {
        console.error('Error fetching designations:', error);
        // If there's an error but we have current designation, show it
        if (currentDesignation) {
          designationSelect.innerHTML = '<option value="">Select Designation</option>';
          const currentOption = document.createElement('option');
          currentOption.value = currentDesignation;
          currentOption.textContent = currentDesignation;
          currentOption.selected = true;
          designationSelect.appendChild(currentOption);
        } else {
          designationSelect.innerHTML = '<option value="">Error Loading Designations</option>';
        }
      });
    }
    
    // Handle category change to load designations
    if (categorySelect && designationSelect) {
      categorySelect.addEventListener('change', function() {
        const category = this.value;
        designationSelect.innerHTML = '<option value="">Loading...</option>';
        designationSelect.disabled = true;
        
        if (category) {
          fetch(`{{ route('admin.employees.designations') }}?category=${encodeURIComponent(category)}`, {
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
          designationSelect.innerHTML = '<option value="">Select Category First</option>';
        }
      });
    }
    
    // Load sectors on page load if city is already selected
    if (currentCity && citySelect && sectorSelect) {
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
    if (citySelect && sectorSelect) {
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
  });
</script>
@endpush