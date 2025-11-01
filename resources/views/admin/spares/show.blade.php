@extends('layouts.sidebar')

@section('title', 'Spare Part Details — CMS Admin')

@section('content')


<!-- SPARE PART INFORMATION -->
<div class="card-glass">
  <div class="card-header">
    <h5 class="card-title mb-0 text-white">
      <i data-feather="package" class="me-2"></i>Product Details: {{ $spare->item_name }}
    </h5>
  </div>
  <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-white fw-bold">Basic Information</h6>
                <table class="table table-borderless">
                  <tr>
                    <td class="text-white"><strong>Item Name:</strong></td>
                    <td class="text-white">{{ $spare->item_name }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Product Code:</strong></td>
                    <td class="text-white">{{ $spare->product_code ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Brand Name:</strong></td>
                    <td class="text-white">{{ $spare->brand_name ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Category:</strong></td>
                    <td>
                      <span class="badge bg-info">{{ ucfirst($spare->category ?? 'N/A') }}</span>
                    </td>
                  </tr>
                  @if($spare->description)
                  <tr>
                    <td class="text-white"><strong>Description:</strong></td>
                    <td class="text-white">{{ $spare->description }}</td>
                  </tr>
                  @endif
                </table>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-white fw-bold">Stock Information</h6>
                <table class="table table-borderless">
                  <tr>
                    <td class="text-white"><strong>Total Received:</strong></td>
                    <td class="text-white">{{ number_format($spare->total_received_quantity ?? 0, 0) }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Issued Quantity:</strong></td>
                    <td class="text-white">{{ number_format($spare->issued_quantity ?? 0, 0) }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Stock Quantity:</strong></td>
                    <td>
                      <span class="badge bg-{{ ($spare->stock_quantity ?? 0) <= 0 ? 'danger' : (($spare->stock_quantity ?? 0) <= ($spare->threshold_level ?? 0) ? 'warning' : 'success') }}">
                        {{ number_format($spare->stock_quantity ?? 0, 0) }}
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Threshold Level:</strong></td>
                    <td class="text-white">{{ number_format($spare->threshold_level ?? 0, 0) }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Supplier:</strong></td>
                    <td class="text-white">{{ $spare->supplier ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Last Stock In:</strong></td>
                    <td class="text-white">{{ $spare->last_stock_in_at ? $spare->last_stock_in_at->format('M d, Y H:i') : 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Created:</strong></td>
                    <td class="text-white">{{ $spare->created_at->format('M d, Y H:i') }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Last Updated:</strong></td>
                    <td class="text-white">{{ $spare->updated_at->format('M d, Y H:i') }}</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>

          <div class="row mt-4">
            <div class="col-12">
              <div class="d-flex justify-content-between">
                <a href="{{ route('admin.spares.index') }}" class="btn btn-secondary">
                  <i data-feather="arrow-left"></i> Back to Product
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
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
