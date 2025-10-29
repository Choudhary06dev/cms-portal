@extends('layouts.sidebar')

@section('title', 'Complaint Details — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Complaint Details</h2>
      <p class="text-light">View and manage complaint information</p>
    </div>
   
  </div>
</div>

<!-- COMPLAINT INFORMATION -->
<div class="card-glass">
  <div class="card-header">
    <h5 class="card-title mb-0 text-white">
      <i data-feather="alert-triangle" class="me-2"></i>Complaint Details: {{ $complaint->ticket_number }}
    </h5>
  </div>
  <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-white fw-bold">Complaint Information</h6>
                <table class="table table-borderless">
                  <tr>
                    <td class="text-white"><strong>Ticket Number:</strong></td>
                    <td class="text-white">{{ $complaint->ticket_number }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Type:</strong></td>
                    <td>
                      <span class="category-badge category-{{ strtolower($complaint->category) }}">{{ ucfirst($complaint->category) }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Status:</strong></td>
                    <td>
                      <span class="badge bg-{{ $complaint->status === 'resolved' ? 'success' : ($complaint->status === 'closed' ? 'info' : 'warning') }}">
                        {{ ucfirst($complaint->status) }}
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Priority:</strong></td>
                    <td>
                      <span class="badge bg-{{ $complaint->priority === 'high' ? 'danger' : ($complaint->priority === 'medium' ? 'warning' : 'success') }}">
                        {{ ucfirst($complaint->priority) }}
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Location:</strong></td>
                    <td class="text-white">{{ $complaint->location ?? 'N/A' }}</td>
                  </tr>
                </table>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-white fw-bold">Client & Assignment</h6>
                <table class="table table-borderless">
                  <tr>
                    <td class="text-white"><strong>Client:</strong></td>
                    <td class="text-white">{{ $complaint->client->client_name ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Assigned To:</strong></td>
                    <td>{{ $complaint->assignedEmployee->user->username ?? 'Unassigned' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Created:</strong></td>
                    <td>{{ $complaint->created_at->format('M d, Y H:i') }}</td>
                  </tr>
                  <tr>
                    <td><strong>Last Updated:</strong></td>
                    <td>{{ $complaint->updated_at->format('M d, Y H:i') }}</td>
                  </tr>
                  @if($complaint->closed_at)
                  <tr>
                    <td><strong>Closed:</strong></td>
                    <td>{{ $complaint->closed_at->format('M d, Y H:i') }}</td>
                  </tr>
                  @endif
                </table>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <h6 class="text-muted">Description</h6>
              <div class="card">
                <div class="card-body">
                  <p>{{ $complaint->description }}</p>
                </div>
              </div>
            </div>
          </div>

          @if($complaint->attachments->count() > 0)
          <div class="row mt-4">
            <div class="col-12">
              <h6 class="text-muted">Attachments</h6>
              <div class="row">
                @foreach($complaint->attachments as $attachment)
                <div class="col-md-3 mb-3">
                  <div class="card">
                    <div class="card-body text-center">
                      <i data-feather="file" class="mb-2"></i>
                      <p class="card-text small">{{ $attachment->original_name }}</p>
                      <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        View
                      </a>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
          </div>
          @endif

          <!-- Spare Parts Section -->
          <div class="row mt-4">
            <div class="col-12">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="text-muted mb-0">Spare Parts Used</h6>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSparePartsModal">
                  <i data-feather="plus"></i> Add Spare Parts
                </button>
              </div>
              
              @if($complaint->spareParts->count() > 0)
                <div class="table-responsive">
                  <table class="table table-sm table-striped">
                    <thead>
                      <tr>
                        <th>Spare Part</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total Cost</th>
                        <th>Used By</th>
                        <th>Used At</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($complaint->spareParts as $sparePart)
                      <tr>
                        <td>{{ $sparePart->spare->item_name ?? 'N/A' }}</td>
                        <td>{{ $sparePart->quantity }}</td>
                        <td>PKR {{ number_format($sparePart->spare->unit_price ?? 0, 2) }}</td>
                        <td>PKR {{ number_format($sparePart->getTotalCostAttribute(), 2) }}</td>
                        <td>{{ $sparePart->usedBy->user->username ?? 'N/A' }}</td>
                        <td>{{ $sparePart->used_at->format('M d, Y H:i') }}</td>
                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                      <tr class="table-primary">
                        <th colspan="3">Total Cost:</th>
                        <th>PKR {{ number_format($complaint->getTotalSpareCostAttribute(), 2) }}</th>
                        <th colspan="2"></th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              @else
                <div class="text-center py-4 text-muted">
                  <i data-feather="package" class="feather-lg mb-2"></i>
                  <div>No spare parts used yet</div>
                </div>
              @endif
            </div>
          </div>

          @if($complaint->logs->count() > 0)
          <div class="row mt-4">
            <div class="col-12">
              <h6 class="text-muted">Activity Log</h6>
              <div class="table-responsive">
                <table class="table table-sm table-striped">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Action</th>
                      <th>User</th>
                      <th>Notes</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($complaint->logs as $log)
                    <tr>
                      <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                      <td>{{ ucfirst($log->action) }}</td>
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
                <a href="{{ route('admin.complaints.index') }}" class="btn btn-secondary">
                  <i data-feather="arrow-left"></i> Back to Complaints
                </a>
               
                 
                 
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Spare Parts Modal -->
<div class="modal fade" id="addSparePartsModal" tabindex="-1" aria-labelledby="addSparePartsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSparePartsModalLabel">Add Spare Parts</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.complaints.add-spare-parts', $complaint) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div id="sparePartsContainer">
            <div class="spare-part-row mb-3">
              <div class="row">
                <div class="col-md-5">
                  <label class="form-label">Spare Part</label>
                  <select class="form-select spare-part-select" name="spare_parts[0][spare_id]" required>
                    <option value="">Select Spare Part</option>
                    @foreach(\App\Models\Spare::where('stock_quantity', '>', 0)->get() as $spare)
                      <option value="{{ $spare->id }}" data-price="{{ $spare->unit_price }}" data-stock="{{ $spare->stock_quantity }}">
                        {{ $spare->item_name }} (Stock: {{ $spare->stock_quantity }}, Price: PKR {{ number_format($spare->unit_price, 2) }})
                      </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Quantity</label>
                  <input type="number" class="form-control quantity-input" name="spare_parts[0][quantity]" min="1" required>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Total Cost</label>
                  <input type="text" class="form-control total-cost" readonly>
                </div>
                <div class="col-md-1">
                  <label class="form-label">&nbsp;</label>
                  <button type="button" class="btn btn-danger btn-sm remove-row" style="display: none;">
                    <i data-feather="trash-2"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          <div class="mb-3">
            <button type="button" class="btn btn-outline-primary btn-sm" id="addSparePartRow">
              <i data-feather="plus"></i> Add Another Part
            </button>
          </div>
          
          <div class="mb-3">
            <label for="remarks" class="form-label">Remarks (Optional)</label>
            <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Enter any remarks about the spare parts usage..."></textarea>
          </div>
          
          <div class="alert alert-info">
            <strong>Note:</strong> Stock will be automatically deducted when you save the spare parts.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Spare Parts</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
let sparePartRowIndex = 0;

// Add new spare part row
document.getElementById('addSparePartRow').addEventListener('click', function() {
    sparePartRowIndex++;
    const container = document.getElementById('sparePartsContainer');
    const newRow = document.querySelector('.spare-part-row').cloneNode(true);
    
    // Update form field names
    newRow.querySelector('.spare-part-select').name = `spare_parts[${sparePartRowIndex}][spare_id]`;
    newRow.querySelector('.quantity-input').name = `spare_parts[${sparePartRowIndex}][quantity]`;
    newRow.querySelector('.quantity-input').value = '';
    newRow.querySelector('.total-cost').value = '';
    
    // Show remove button
    newRow.querySelector('.remove-row').style.display = 'block';
    
    container.appendChild(newRow);
    
    // Re-initialize feather icons
    feather.replace();
});

// Remove spare part row
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-row')) {
        e.target.closest('.spare-part-row').remove();
    }
});

// Calculate total cost when spare part or quantity changes
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('spare-part-select') || e.target.classList.contains('quantity-input')) {
        const row = e.target.closest('.spare-part-row');
        const select = row.querySelector('.spare-part-select');
        const quantityInput = row.querySelector('.quantity-input');
        const totalCostInput = row.querySelector('.total-cost');
        
        if (select.value && quantityInput.value) {
            const price = parseFloat(select.options[select.selectedIndex].dataset.price);
            const quantity = parseInt(quantityInput.value);
            const totalCost = price * quantity;
            totalCostInput.value = 'PKR ' + totalCost.toFixed(2);
        } else {
            totalCostInput.value = '';
        }
    }
});

// Validate stock availability
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('spare-part-select')) {
        const row = e.target.closest('.spare-part-row');
        const select = e.target;
        const quantityInput = row.querySelector('.quantity-input');
        
        if (select.value) {
            const stock = parseInt(select.options[select.selectedIndex].dataset.stock);
            quantityInput.max = stock;
            
            if (quantityInput.value && parseInt(quantityInput.value) > stock) {
                alert(`Insufficient stock! Available: ${stock}`);
                quantityInput.value = stock;
            }
        }
    }
});
</script>
@endpush
