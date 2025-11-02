@extends('layouts.sidebar')

@section('title', 'Edit Client — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Edit Complainant</h2>
      <p class="text-light">Update Complainant information</p>
    </div>
  </div>
</div>

<!-- Complainant FORM -->
<div class="card-glass">
  <div class="card-header">
    <h5 class="card-title mb-0 text-white">
      <i data-feather="user-edit" class="me-2"></i>Edit Complainant: {{ $client->client_name }}
    </h5>
  </div>
  <div class="card-body">
          <form action="{{ route('admin.clients.update', $client) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="client_name" class="form-label text-white">Complainant Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('client_name') is-invalid @enderror" 
                         id="client_name" name="client_name" value="{{ old('client_name', $client->client_name) }}" required>
                  @error('client_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="contact_person" class="form-label text-white">Contact Person</label>
                  <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                         id="contact_person" name="contact_person" value="{{ old('contact_person', $client->contact_person) }}">
                  @error('contact_person')
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
                         id="phone" name="phone" value="{{ old('phone', $client->phone) }}">
                  @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="email" class="form-label text-white">Email</label>
                  <input type="email" class="form-control @error('email') is-invalid @enderror" 
                         id="email" name="email" value="{{ old('email', $client->email) }}">
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="city" class="form-label text-white">City <span class="text-danger">*</span></label>
                  <select class="form-select @error('city') is-invalid @enderror" 
                          id="city" name="city" required>
                    <option value="">Select City</option>
                    @if(isset($cities) && $cities->count() > 0)
                      @foreach ($cities as $city)
                        <option value="{{ $city->name }}" data-id="{{ $city->id }}" data-province="{{ $city->province ?? '' }}" {{ old('city', $client->city) == $city->name ? 'selected' : '' }}>{{ $city->name }}{{ $city->province ? ' (' . $city->province . ')' : '' }}</option>
                      @endforeach
                    @endif
                  </select>
                  @error('city')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="sector" class="form-label text-white">Sector <span class="text-danger">*</span></label>
                  <select class="form-select @error('sector') is-invalid @enderror" 
                          id="sector" name="sector" required disabled>
                    <option value="">Select City First</option>
                  </select>
                  @error('sector')
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
                    <option value="active" {{ old('status', $client->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $client->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                  <label for="address" class="form-label text-white">Address <span class="text-danger">*</span></label>
                  <textarea class="form-control @error('address') is-invalid @enderror" 
                            id="address" name="address" rows="3" required>{{ old('address', $client->address) }}</textarea>
                  @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
              <a href="{{ route('admin.clients.index', $client) }}" class="btn btn-outline-secondary">Cancel</a>
              <button type="submit" class="btn btn-accent">Update Client</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
/* Form control styling for all themes */
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
document.addEventListener('DOMContentLoaded', function() {
    // Form validation enhancement
    const form = document.querySelector('form');
    const submitBtn = document.querySelector('button[type="submit"]');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            // Add loading state to submit button
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';
            submitBtn.disabled = true;
        });
    }
    
    // Auto-save draft functionality (optional)
    const inputs = document.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            // Save form data to localStorage as draft
            const formData = new FormData(form);
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            localStorage.setItem('client_edit_draft', JSON.stringify(data));
        });
    });
    
    // Load draft data if exists
    const draft = localStorage.getItem('client_edit_draft');
    if (draft) {
        try {
            const data = JSON.parse(draft);
            Object.keys(data).forEach(key => {
                const input = document.querySelector(`[name="${key}"]`);
                if (input && input.type !== 'file') {
                    input.value = data[key];
                }
            });
        } catch (e) {
            console.log('No valid draft data found');
        }
    }
    
    // Clear draft on successful form submission
    form.addEventListener('submit', function() {
        localStorage.removeItem('client_edit_draft');
    });
    
    // Handle city change to filter sectors
    const citySelect = document.getElementById('city');
    const sectorSelect = document.getElementById('sector');
    const currentCity = '{{ old('city', $client->city) }}';
    const currentSector = '{{ old('sector', $client->sector ?? '') }}';
    
    // Load sectors on page load if city is already selected
    if (currentCity && citySelect) {
      const selectedOption = citySelect.options[citySelect.selectedIndex];
      const cityIdFromData = selectedOption ? selectedOption.getAttribute('data-id') : null;
      const cityId = cityIdFromData || (selectedOption ? selectedOption.value : null);
      
      // Find city by name if we have city name but not ID
      if (!cityIdFromData && currentCity) {
        // Try to find the city option that matches current city name
        for (let i = 0; i < citySelect.options.length; i++) {
          if (citySelect.options[i].value === currentCity) {
            const foundOption = citySelect.options[i];
            const foundCityId = foundOption.getAttribute('data-id');
            if (foundCityId) {
              // Load sectors for this city
              fetch(`{{ route('admin.clients.sectors') }}?city_id=${foundCityId}`, {
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
                    option.value = sector.name;
                    option.textContent = sector.name;
                    if (sector.name === currentSector) {
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
              break;
            }
          }
        }
      } else if (cityIdFromData) {
        // Load sectors using city ID
        fetch(`{{ route('admin.clients.sectors') }}?city_id=${cityIdFromData}`, {
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
              option.value = sector.name;
              option.textContent = sector.name;
              if (sector.name === currentSector) {
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
        // Get the actual city ID value
        const cityName = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const cityId = selectedOption ? selectedOption.getAttribute('data-id') : null;
        
        console.log('City selected - name:', cityName, 'ID:', cityId);
        
        // Clear and disable sector dropdown
        sectorSelect.innerHTML = '<option value="">Loading...</option>';
        sectorSelect.disabled = true;
        
        if (cityId) {
          // Fetch sectors for this city
          const url = `{{ route('admin.clients.sectors') }}?city_id=${cityId}`;
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
                option.value = sector.name;
                option.textContent = sector.name;
                sectorSelect.appendChild(option);
              });
              sectorSelect.disabled = false;
              console.log('Sectors loaded successfully:', data.sectors.length);
            } else {
              sectorSelect.innerHTML = '<option value="">No Sector Available</option>';
              console.log('No sectors found for city ID:', cityId);
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
