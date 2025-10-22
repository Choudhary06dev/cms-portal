@extends('layouts.sidebar')

@section('title', 'Edit Client')

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
          <h5 class="card-title mb-0">Edit Client: {{ $client->client_name }}</h5>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.clients.update', $client) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="client_name" class="form-label">Client Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('client_name') is-invalid @enderror" 
                         id="client_name" name="client_name" value="{{ old('client_name', $client->client_name) }}" required>
                  @error('client_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="contact_person" class="form-label">Contact Person</label>
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
                  <label for="phone" class="form-label">Phone</label>
                  <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                         id="phone" name="phone" value="{{ old('phone', $client->phone) }}">
                  @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
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
                  <label for="address" class="form-label">Address</label>
                  <textarea class="form-control @error('address') is-invalid @enderror" 
                            id="address" name="address" rows="3">{{ old('address', $client->address) }}</textarea>
                  @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="city" class="form-label">City</label>
                  <input type="text" class="form-control @error('city') is-invalid @enderror" 
                         id="city" name="city" value="{{ old('city', $client->city) }}">
                  @error('city')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="state" class="form-label">State</label>
                  <select class="form-select @error('state') is-invalid @enderror" 
                          id="state" name="state">
                    <option value="">Select State</option>
                    <option value="sindh" {{ old('state', $client->state) == 'sindh' ? 'selected' : '' }}>Sindh</option>
                    <option value="punjab" {{ old('state', $client->state) == 'punjab' ? 'selected' : '' }}>Punjab</option>
                    <option value="kpk" {{ old('state', $client->state) == 'kpk' ? 'selected' : '' }}>KPK</option>
                    <option value="balochistan" {{ old('state', $client->state) == 'balochistan' ? 'selected' : '' }}>Balochistan</option>
                    <option value="other" {{ old('state', $client->state) == 'other' ? 'selected' : '' }}>Other</option>
                  </select>
                  @error('state')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="pincode" class="form-label">Pincode</label>
                  <input type="text" class="form-control @error('pincode') is-invalid @enderror" 
                         id="pincode" name="pincode" value="{{ old('pincode', $client->pincode) }}">
                  @error('pincode')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="status" class="form-label">Status</label>
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

            <div class="d-flex justify-content-end gap-2">
              <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-primary">Update Client</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

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
});
</script>
@endpush
