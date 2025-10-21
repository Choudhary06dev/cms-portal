@extends('layouts.sidebar')

@section('title', 'Edit Spare Part')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">Edit Spare Part: {{ $spare->part_name }}</h5>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.spares.update', $spare) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="part_name" class="form-label">Part Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('part_name') is-invalid @enderror" 
                         id="part_name" name="part_name" value="{{ old('part_name', $spare->part_name) }}" required>
                  @error('part_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="part_number" class="form-label">Part Number</label>
                  <input type="text" class="form-control @error('part_number') is-invalid @enderror" 
                         id="part_number" name="part_number" value="{{ old('part_number', $spare->part_number) }}">
                  @error('part_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                  <select class="form-select @error('category') is-invalid @enderror" 
                          id="category" name="category" required>
                    <option value="">Select Category</option>
                    <option value="electric" {{ old('category', $spare->category) == 'electric' ? 'selected' : '' }}>Electric</option>
                    <option value="sanitary" {{ old('category', $spare->category) == 'sanitary' ? 'selected' : '' }}>Sanitary</option>
                    <option value="kitchen" {{ old('category', $spare->category) == 'kitchen' ? 'selected' : '' }}>Kitchen</option>
                    <option value="general" {{ old('category', $spare->category) == 'general' ? 'selected' : '' }}>General</option>
                  </select>
                  @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="unit_price" class="form-label">Unit Price <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" class="form-control @error('unit_price') is-invalid @enderror" 
                         id="unit_price" name="unit_price" value="{{ old('unit_price', $spare->unit_price) }}" required>
                  @error('unit_price')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="stock_quantity" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                  <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" 
                         id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $spare->stock_quantity) }}" required>
                  @error('stock_quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="min_stock_level" class="form-label">Minimum Stock Level</label>
                  <input type="number" class="form-control @error('min_stock_level') is-invalid @enderror" 
                         id="min_stock_level" name="min_stock_level" value="{{ old('min_stock_level', $spare->min_stock_level) }}">
                  @error('min_stock_level')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="supplier" class="form-label">Supplier</label>
                  <input type="text" class="form-control @error('supplier') is-invalid @enderror" 
                         id="supplier" name="supplier" value="{{ old('supplier', $spare->supplier) }}">
                  @error('supplier')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="status" class="form-label">Status</label>
                  <select class="form-select @error('status') is-invalid @enderror" 
                          id="status" name="status">
                    <option value="active" {{ old('status', $spare->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $spare->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                            id="description" name="description" rows="3">{{ old('description', $spare->description) }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
              <a href="{{ route('admin.spares.index') }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-primary">Update Spare Part</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
