@extends('layouts.sidebar')

@section('title', 'Create Approval Request — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Create Approval Request</h2>
      <p class="text-light">Request spare parts approval for a complaint</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.approvals.index') }}" class="btn btn-outline-secondary">
        <i data-feather="arrow-left" class="me-2"></i>Back to Approvals
      </a>
    </div>
  </div>
</div>

<!-- APPROVAL FORM -->
<div class="card-glass">
  <form method="POST" action="{{ route('admin.approvals.store') }}" id="approvalForm">
    @csrf
    
    <!-- Complaint Selection -->
    <div class="row mb-4">
      <div class="col-12">
        <h5 class="text-white mb-3">Complaint Information</h5>
        <div class="row">
          <div class="col-md-6">
            <label for="complaint_id" class="form-label">Select Complaint <span class="text-danger">*</span></label>
            <select class="form-select" id="complaint_id" name="complaint_id" required>
              <option value="">Choose a complaint...</option>
              @if(isset($complaints) && $complaints->count() > 0)
              @foreach($complaints as $complaint)
              <option value="{{ $complaint->id }}" 
                      data-client="{{ $complaint->client->client_name }}"
                      data-title="{{ $complaint->title }}"
                      data-status="{{ $complaint->status }}">
                #{{ $complaint->getTicketNumberAttribute() }} - {{ $complaint->client->client_name }} - {{ $complaint->title }}
              </option>
              @else
              <option value="" disabled>No complaints available</option>
              @endif
            </select>
            @error('complaint_id')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6">
            <div id="complaint-details" class="mt-4" style="display: none;">
              <div class="card bg-dark">
                <div class="card-body">
                  <h6 class="text-white">Complaint Details</h6>
                  <p class="text-muted mb-1"><strong>Client:</strong> <span id="client-name"></span></p>
                  <p class="text-muted mb-1"><strong>Title:</strong> <span id="complaint-title"></span></p>
                  <p class="text-muted mb-0"><strong>Status:</strong> <span id="complaint-status" class="badge bg-warning"></span></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Spare Parts Selection -->
    <div class="row mb-4">
      <div class="col-12">
        <h5 class="text-white mb-3">Requested Spare Parts</h5>
        <div id="spare-items-container">
          <div class="spare-item-row row g-3 mb-3" data-index="0">
            <div class="col-md-4">
              <label class="form-label">Spare Part <span class="text-danger">*</span></label>
              <select class="form-select spare-select" name="items[0][spare_id]" required>
                <option value="">Select spare part...</option>
                @if(isset($spares) && $spares->count() > 0)
                @foreach($spares as $spare)
                <option value="{{ $spare->id }}" 
                        data-stock="{{ $spare->current_stock }}"
                        data-unit="{{ $spare->unit }}">
                  {{ $spare->item_name }} (Stock: {{ $spare->current_stock }})
                </option>
                @endforeach
                @else
                <option value="" disabled>No spare parts available</option>
                @endif
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Quantity <span class="text-danger">*</span></label>
              <input type="number" class="form-control quantity-input" name="items[0][quantity_requested]" min="1" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Reason</label>
              <input type="text" class="form-control" name="items[0][reason]" placeholder="Why is this spare needed?">
            </div>
            <div class="col-md-2">
              <label class="form-label">Actions</label>
              <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeSpareItem(this)" disabled>
                <i data-feather="trash-2"></i>
              </button>
            </div>
            <div class="col-12">
              <div class="spare-item-details" style="display: none;">
                <div class="card bg-dark">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-4">
                        <strong>Available Stock:</strong> <span class="available-stock">-</span>
                      </div>
                      <div class="col-md-4">
                        <strong>Stock Status:</strong> <span class="stock-status">-</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="text-center">
          <button type="button" class="btn btn-outline-primary" onclick="addSpareItem()">
            <i data-feather="plus" class="me-2"></i>Add Another Spare Part
          </button>
        </div>
      </div>
    </div>

    <!-- Remarks -->
    <div class="row mb-4">
      <div class="col-12">
        <h5 class="text-white mb-3">Additional Information</h5>
        <div class="row">
          <div class="col-md-8">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Any additional notes or comments..."></textarea>
          </div>
          <div class="col-md-4">
            <div class="card bg-dark">
              <div class="card-body">
                <h6 class="text-white">Request Summary</h6>
                <p class="text-muted mb-1"><strong>Total Items:</strong> <span id="total-items">0</span></p>
                <p class="text-muted mb-0"><strong>Total Quantity:</strong> <span id="total-quantity">0</span></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Submit Buttons -->
    <div class="row">
      <div class="col-12">
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-accent">
            <i data-feather="send" class="me-2"></i>Submit Approval Request
          </button>
          <a href="{{ route('admin.approvals.index') }}" class="btn btn-outline-secondary">
            <i data-feather="x" class="me-2"></i>Cancel
          </a>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@push('styles')
<style>
  .spare-item-row {
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    background: rgba(255, 255, 255, 0.05);
  }
  
  .spare-item-details {
    margin-top: 10px;
  }
  
  .stock-status {
    font-weight: bold;
  }
  
  .stock-available {
    color: #22c55e;
  }
  
  .stock-low {
    color: #f59e0b;
  }
  
  .stock-out {
    color: #ef4444;
  }
</style>
@endpush

@push('scripts')
<script>
  feather.replace();
  
  let itemIndex = 0;

  // Complaint selection handler
  document.getElementById('complaint_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const detailsDiv = document.getElementById('complaint-details');
    
    if (this.value) {
      document.getElementById('client-name').textContent = selectedOption.dataset.client;
      document.getElementById('complaint-title').textContent = selectedOption.dataset.title;
      document.getElementById('complaint-status').textContent = selectedOption.dataset.status;
      detailsDiv.style.display = 'block';
    } else {
      detailsDiv.style.display = 'none';
    }
  });

  // Add new spare item
  function addSpareItem() {
    itemIndex++;
    const container = document.getElementById('spare-items-container');
    const newRow = document.createElement('div');
    newRow.className = 'spare-item-row row g-3 mb-3';
    newRow.setAttribute('data-index', itemIndex);
    
    newRow.innerHTML = `
      <div class="col-md-4">
        <label class="form-label">Spare Part <span class="text-danger">*</span></label>
        <select class="form-select spare-select" name="items[${itemIndex}][spare_id]" required>
          <option value="">Select spare part...</option>
          @if(isset($spares) && $spares->count() > 0)
          @foreach($spares as $spare)
          <option value="{{ $spare->id }}" 
                  data-stock="{{ $spare->current_stock }}"
                  data-unit="{{ $spare->unit }}">
            {{ $spare->item_name }} (Stock: {{ $spare->current_stock }})
          </option>
          @endforeach
          @else
          <option value="" disabled>No spare parts available</option>
          @endif
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Quantity <span class="text-danger">*</span></label>
        <input type="number" class="form-control quantity-input" name="items[${itemIndex}][quantity_requested]" min="1" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Reason</label>
        <input type="text" class="form-control" name="items[${itemIndex}][reason]" placeholder="Why is this spare needed?">
      </div>
      <div class="col-md-2">
        <label class="form-label">Actions</label>
        <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeSpareItem(this)">
          <i data-feather="trash-2"></i>
        </button>
      </div>
      <div class="col-12">
        <div class="spare-item-details" style="display: none;">
          <div class="card bg-dark">
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">
                  <strong>Available Stock:</strong> <span class="available-stock">-</span>
                </div>
                <div class="col-md-4">
                  <strong>Stock Status:</strong> <span class="stock-status">-</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
    
    container.appendChild(newRow);
    feather.replace();
    updateSummary();
  }

  // Remove spare item
  function removeSpareItem(button) {
    const row = button.closest('.spare-item-row');
    row.remove();
    updateSummary();
  }

  // Spare selection handler
  document.addEventListener('change', function(e) {
    if (e.target.classList.contains('spare-select')) {
      updateSpareDetails(e.target);
    }
  });

  // Quantity change handler
  document.addEventListener('input', function(e) {
    if (e.target.classList.contains('quantity-input')) {
      updateSpareDetails(e.target.closest('.spare-item-row').querySelector('.spare-select'));
    }
  });

  // Update spare item details
  function updateSpareDetails(selectElement) {
    const row = selectElement.closest('.spare-item-row');
    const detailsDiv = row.querySelector('.spare-item-details');
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    
    if (selectElement.value) {
      const stock = parseInt(selectedOption.dataset.stock || 0);
      const quantityInput = row.querySelector('.quantity-input');
      const quantity = parseInt(quantityInput.value) || 0;

      // Update details (price removed)
      row.querySelector('.available-stock').textContent = stock;

      // Update stock status
      const stockStatus = row.querySelector('.stock-status');
      if (stock >= quantity && quantity > 0) {
        stockStatus.textContent = 'Available';
        stockStatus.className = 'stock-status stock-available';
      } else if (stock > 0) {
        stockStatus.textContent = 'Low Stock';
        stockStatus.className = 'stock-status stock-low';
      } else {
        stockStatus.textContent = 'Out of Stock';
        stockStatus.className = 'stock-status stock-out';
      }

      detailsDiv.style.display = 'block';
    } else {
      detailsDiv.style.display = 'none';
    }
    
    updateSummary();
  }

  // Update summary
  function updateSummary() {
    const rows = document.querySelectorAll('.spare-item-row');
    let totalItems = 0;
    let totalQuantity = 0;
    let totalCost = 0;
    
    rows.forEach(row => {
      const select = row.querySelector('.spare-select');
      const quantityInput = row.querySelector('.quantity-input');
      
      if (select.value && quantityInput.value) {
        totalItems++;
        totalQuantity += parseInt(quantityInput.value) || 0;
      }
    });
    
    document.getElementById('total-items').textContent = totalItems;
    document.getElementById('total-quantity').textContent = totalQuantity;
    // Estimated cost removed (price no longer tracked in approvals). If you want to keep a cost estimate, re-introduce price handling.
    const est = document.getElementById('estimated-cost');
    if (est) est.textContent = `PKR 0.00`;
  }

  // Form validation
  document.getElementById('approvalForm').addEventListener('submit', function(e) {
    const rows = document.querySelectorAll('.spare-item-row');
    let hasValidItems = false;
    
    rows.forEach(row => {
      const select = row.querySelector('.spare-select');
      const quantity = row.querySelector('.quantity-input');
      
      if (select.value && quantity.value) {
        hasValidItems = true;
      }
    });
    
    if (!hasValidItems) {
      e.preventDefault();
      alert('Please add at least one spare part to the request.');
      return false;
    }
  });

  // Initialize
  document.addEventListener('DOMContentLoaded', function() {
    updateSummary();
  });
</script>
@endpush
