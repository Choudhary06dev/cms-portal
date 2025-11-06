@extends('layouts.sidebar')

@section('title', 'Spare Part Details — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Product Details</h2>
      <p class="text-light">View product information and stock records</p>
    </div>
  </div>
</div>

<!-- PRODUCT INFORMATION -->
<div class="d-flex justify-content-center">
  <div class="card-glass" style="max-width: 900px; width: 100%;">
    <div class="row">
      <div class="col-12">
      <div class="row">
        <div class="col-md-6">
          <h6 class="text-white fw-bold mb-3" style="font-size: 1rem; font-weight: 700;">Basic Information</h6>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Item Name:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $spare->item_name ?? 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Product Code:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $spare->product_code ?? 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Brand Name:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $spare->brand_name ?? 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Category:</span>
            <span class="badge bg-info ms-2" style="color: #ffffff !important; font-size: 0.875rem;">{{ ucfirst($spare->category ?? 'N/A') }}</span>
          </div>
          @if($spare->description)
          <div class="mb-3 mt-4">
            <h6 class="text-white fw-bold mb-3" style="font-size: 1rem; font-weight: 700;">Description</h6>
            <p class="text-light" style="font-size: 0.875rem;">{{ $spare->description }}</p>
          </div>
          @endif
        </div>
        
        <div class="col-md-6">
          <h6 class="text-white fw-bold mb-3" style="font-size: 1rem; font-weight: 700;">Stock Information</h6>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Stock Quantity:</span>
            <span class="badge bg-{{ ($spare->stock_quantity ?? 0) <= 0 ? 'danger' : (($spare->stock_quantity ?? 0) <= ($spare->threshold_level ?? 0) ? 'warning' : 'success') }} ms-2" style="color: #ffffff !important; font-size: 0.875rem;">
              {{ number_format($spare->stock_quantity ?? 0, 0) }}
            </span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Total Received:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ number_format($spare->total_received_quantity ?? 0, 0) }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Issued Quantity:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ number_format($spare->issued_quantity ?? 0, 0) }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Threshold Level:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ number_format($spare->threshold_level ?? 0, 0) }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Supplier:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $spare->supplier ?? 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Created:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $spare->created_at ? $spare->created_at->timezone('Asia/Karachi')->format('M d, Y h:i:s A') : 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Last Updated:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $spare->updated_at ? $spare->updated_at->timezone('Asia/Karachi')->format('M d, Y h:i:s A') : 'N/A' }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  
    <hr class="my-4">
    
  
  </div>
</div>

<!-- Add Stock Modal -->
<div class="modal fade" id="addStockModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Stock</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('admin.spares.add-stock', $spare) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="quantity" class="form-label">Quantity to Add</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required>
          </div>
          <div class="mb-3">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Stock</button>
        </div>
      </form>
    </div>
  </div>
</div>


@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
  });

  function addStock() {
    const modal = new bootstrap.Modal(document.getElementById('addStockModal'));
    modal.show();
  }
</script>
@endpush
@endsection
