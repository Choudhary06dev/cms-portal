@extends('layouts.sidebar')

@section('title', 'Edit Product — CMS Admin')

@section('content')
    <!-- PAGE HEADER -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white mb-2">Edit Product</h2>
                <p class="text-light">Update Product information</p>
            </div>
           
        </div>
    </div>

    <!-- Product FORM -->
    <div class="card-glass">
        <div class="card-header">
            <h5 class="card-title mb-0 text-white">
                <i data-feather="edit" class="me-2"></i>Edit Product: {{ $spare->item_name }}
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.spares.update', $spare) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="item_name" class="form-label text-white">Item Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('item_name') is-invalid @enderror"
                                id="item_name" name="item_name" value="{{ old('item_name', $spare->item_name) }}" required>
                            @error('item_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="product_code" class="form-label text-white">Product Code</label>
                            <input type="text" class="form-control @error('product_code') is-invalid @enderror"
                                id="product_code" name="product_code"
                                value="{{ old('product_code', $spare->product_code) }}">
                            @error('product_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="brand_name" class="form-label text-white">Brand Name</label>
                            <input type="text" class="form-control @error('brand_name') is-invalid @enderror"
                                id="brand_name" name="brand_name" value="{{ old('brand_name', $spare->brand_name) }}">
                            @error('brand_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="category" class="form-label text-white">Category <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                <option value="">Select Category</option>
                                @if(isset($categories) && $categories->count() > 0)
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat }}" {{ old('category', $spare->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="city_id" class="form-label text-white">City</label>
                            <select class="form-select @error('city_id') is-invalid @enderror" 
                                    id="city_id" name="city_id">
                                <option value="">Select City</option>
                                @if(isset($cities) && $cities->count() > 0)
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('city_id', $spare->city_id) == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }}{{ $city->province ? ' (' . $city->province . ')' : '' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('city_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="sector_id" class="form-label text-white">Sector</label>
                            <select class="form-select @error('sector_id') is-invalid @enderror" 
                                    id="sector_id" name="sector_id" @if(!$spare->city_id && !old('city_id')) disabled @endif>
                                <option value="">@if($spare->city_id || old('city_id')) Select Sector @else Select City First @endif</option>
                                @if(isset($sectors) && $sectors->count() > 0)
                                    @foreach ($sectors as $sector)
                                        <option value="{{ $sector->id }}" {{ old('sector_id', $spare->sector_id) == $sector->id ? 'selected' : '' }}>
                                            {{ $sector->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('sector_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="stock_quantity" class="form-label text-white">Stock Quantity <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                                id="stock_quantity" name="stock_quantity"
                                value="{{ old('stock_quantity', $spare->stock_quantity) }}" required>
                            @error('stock_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="threshold_level" class="form-label text-white">Threshold Level <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('threshold_level') is-invalid @enderror"
                                id="threshold_level" name="threshold_level"
                                value="{{ old('threshold_level', $spare->threshold_level) }}" required>
                            @error('threshold_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="supplier" class="form-label text-white">Supplier</label>
                            <input type="text" class="form-control @error('supplier') is-invalid @enderror"
                                id="supplier" name="supplier" value="{{ old('supplier', $spare->supplier) }}">
                            @error('supplier')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="total_received_quantity" class="form-label text-white">Total Received
                                Quantity</label>
                            <input type="number"
                                class="form-control @error('total_received_quantity') is-invalid @enderror"
                                id="total_received_quantity" name="total_received_quantity"
                                value="{{ old('total_received_quantity', $spare->total_received_quantity) }}">
                            @error('total_received_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="description" class="form-label text-white">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="3">{{ old('description', $spare->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.spares.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-accent">Update Product</button>
                </div>
            </form>
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
    // Handle city change to filter sectors
    const citySelect = document.getElementById('city_id');
    const sectorSelect = document.getElementById('sector_id');
    
    // Store the original selected sector ID
    const originalSectorId = @json($spare->sector_id ?? old('sector_id', ''));
    
    if (citySelect && sectorSelect) {
        citySelect.addEventListener('change', function() {
            const cityId = this.value;
            
            // Clear and disable sector dropdown
            sectorSelect.innerHTML = '<option value="">Loading...</option>';
            sectorSelect.disabled = true;
            
            if (cityId) {
                // Fetch sectors for this city
                const url = `{{ route('admin.clients.sectors') }}?city_id=${cityId}`;
                
                fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    sectorSelect.innerHTML = '<option value="">Select Sector</option>';
                    
                    if (data.sectors && data.sectors.length > 0) {
                        data.sectors.forEach(function(sector) {
                            const option = document.createElement('option');
                            option.value = sector.id;
                            option.textContent = sector.name;
                            // Preserve original selection if city hasn't changed
                            if (cityId == @json($spare->city_id ?? old('city_id', '')) && sector.id == originalSectorId) {
                                option.selected = true;
                            }
                            sectorSelect.appendChild(option);
                        });
                        sectorSelect.disabled = false;
                    } else {
                        sectorSelect.innerHTML = '<option value="">No Sector Available</option>';
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
        
        // If city is already selected, load sectors on page load
        @if($spare->city_id || old('city_id'))
            citySelect.dispatchEvent(new Event('change'));
        @endif
    }
});
</script>
@endpush
