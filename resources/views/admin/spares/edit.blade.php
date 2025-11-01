@extends('layouts.sidebar')

@section('title', 'Edit Spare Part — CMS Admin')

@section('content')
    <!-- PAGE HEADER -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white mb-2">Edit Spare Part</h2>
                <p class="text-light">Update spare part information</p>
            </div>
           
        </div>
    </div>

    <!-- SPARE PART FORM -->
    <div class="card-glass">
        <div class="card-header">
            <h5 class="card-title mb-0 text-white">
                <i data-feather="edit" class="me-2"></i>Edit Spare Part: {{ $spare->item_name }}
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
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="category" class="form-label text-white">Category <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}" {{ old('category', $spare->category) == $category ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="product_nature" class="form-label text-white">Product Nature</label>
                            <input type="text" class="form-control @error('product_nature') is-invalid @enderror"
                                id="product_nature" name="product_nature"
                                value="{{ old('product_nature', $spare->product_nature) }}">
                            @error('product_nature')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="unit" class="form-label text-white">Unit</label>
                            <input type="text" class="form-control @error('unit') is-invalid @enderror" id="unit"
                                name="unit" value="{{ old('unit', $spare->unit) }}">
                            @error('unit')
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

                    <div class="col-md-6">
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
                </div>


                <div class="row">
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

                    
                </div>

                <div class="row">

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
                    <button type="submit" class="btn btn-accent">Update Spare Part</button>
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
