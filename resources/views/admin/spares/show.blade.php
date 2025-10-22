@extends('layouts.sidebar')

@section('title', 'Spare Part Details — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Spare Part Details</h2>
      <p class="text-light">View and manage spare part information</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.spares.index') }}" class="btn btn-outline-secondary">
        <i data-feather="arrow-left" class="me-2"></i>Back to Spares
      </a>
      <a href="{{ route('admin.spares.edit', $spare) }}" class="btn btn-accent">
        <i data-feather="edit" class="me-2"></i>Edit Spare Part
      </a>
      <button class="btn btn-outline-info" onclick="addStock()">
        <i data-feather="plus" class="me-2"></i>Add Stock
      </button>
    </div>
  </div>
</div>

<!-- SPARE PART INFORMATION -->
<div class="card-glass">
  <div class="card-header">
    <h5 class="card-title mb-0 text-white">
      <i data-feather="package" class="me-2"></i>Spare Part Details: {{ $spare->part_name }}
    </h5>
  </div>
  <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-white fw-bold">Basic Information</h6>
                <table class="table table-borderless">
                  <tr>
                    <td class="text-white"><strong>Part Name:</strong></td>
                    <td class="text-white">{{ $spare->part_name }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Part Number:</strong></td>
                    <td class="text-white">{{ $spare->part_number ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Category:</strong></td>
                    <td>
                      <span class="badge bg-info">{{ ucfirst($spare->category) }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Unit Price:</strong></td>
                    <td class="text-white">₹{{ number_format($spare->unit_price, 2) }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Status:</strong></td>
                    <td>
                      <span class="badge bg-{{ $spare->status === 'active' ? 'success' : 'danger' }}">
                        {{ ucfirst($spare->status) }}
                      </span>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-white fw-bold">Stock Information</h6>
                <table class="table table-borderless">
                  <tr>
                    <td class="text-white"><strong>Current Stock:</strong></td>
                    <td>
                      <span class="badge bg-{{ $spare->stock_quantity <= $spare->min_stock_level ? 'danger' : ($spare->stock_quantity <= ($spare->min_stock_level * 2) ? 'warning' : 'success') }}">
                        {{ $spare->stock_quantity }} units
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Minimum Level:</strong></td>
                    <td>{{ $spare->min_stock_level }} units</td>
                  </tr>
                  <tr>
                    <td><strong>Total Value:</strong></td>
                    <td>₹{{ number_format($spare->stock_quantity * $spare->unit_price, 2) }}</td>
                  </tr>
                  <tr>
                    <td><strong>Supplier:</strong></td>
                    <td>{{ $spare->supplier ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Created:</strong></td>
                    <td>{{ $spare->created_at->format('M d, Y H:i') }}</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>

          @if($spare->description)
          <div class="row">
            <div class="col-12">
              <h6 class="text-muted">Description</h6>
              <div class="card">
                <div class="card-body">
                  <p>{{ $spare->description }}</p>
                </div>
              </div>
            </div>
          </div>
          @endif

          <!-- Stock Movement History -->
          @if($spare->stockLogs->count() > 0)
          <div class="row mt-4">
            <div class="col-12">
              <h6 class="text-muted">Stock Movement History</h6>
              <div class="table-responsive">
                <table class="table table-sm table-striped">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Type</th>
                      <th>Quantity</th>
                      <th>Previous Stock</th>
                      <th>New Stock</th>
                      <th>User</th>
                      <th>Notes</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($spare->stockLogs as $log)
                    <tr>
                      <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                      <td>
                        <span class="badge bg-{{ $log->movement_type === 'in' ? 'success' : 'danger' }}">
                          {{ ucfirst($log->movement_type) }}
                        </span>
                      </td>
                      <td>{{ $log->quantity }}</td>
                      <td>{{ $log->previous_stock }}</td>
                      <td>{{ $log->new_stock }}</td>
                      <td>{{ $log->user->username ?? 'System' }}</td>
                      <td>{{ $log->notes ?? '-' }}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          @endif

          <div class="row mt-4">
            <div class="col-12">
              <div class="d-flex justify-content-between">
                <a href="{{ route('admin.spares.index') }}" class="btn btn-secondary">
                  <i data-feather="arrow-left"></i> Back to Spare Parts
                </a>
                <div class="btn-group">
                  <a href="{{ route('admin.spares.edit', $spare) }}" class="btn btn-warning">
                    <i data-feather="edit"></i> Edit Spare Part
                  </a>
                  <button class="btn btn-info" onclick="addStock()">
                    <i data-feather="plus"></i> Add Stock
                  </button>
                  <form action="{{ route('admin.spares.destroy', $spare) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this spare part?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                      <i data-feather="trash-2"></i> Delete
                    </button>
                  </form>
                </div>
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
            <label for="notes" class="form-label">Notes</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
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

<script>
function addStock() {
  const modal = new bootstrap.Modal(document.getElementById('addStockModal'));
  modal.show();
}
</script>
@endsection
