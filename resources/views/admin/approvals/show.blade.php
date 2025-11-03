@extends('layouts.sidebar')

@section('title', 'Approval Details — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Approval Details</h2>
      <p class="text-light">View approval and complaint information</p>
    </div>
    <div>
      <a href="{{ route('admin.approvals.index') }}" class="btn btn-outline-secondary">
        <i data-feather="arrow-left" class="me-2"></i>Back to Approvals
      </a>
    </div>
  </div>
</div>

@php
  $complaint = $approval->complaint ?? null;
  if ($complaint) {
    $category = $complaint->category ?? 'N/A';
    $department = $complaint->department ?? '';
    
    // Get product name from spare parts
    $productName = '';
    if ($complaint->spareParts && $complaint->spareParts->count() > 0) {
      $firstSpare = $complaint->spareParts->first();
      if ($firstSpare && $firstSpare->spare) {
        $productName = $firstSpare->spare->item_name ?? '';
        if ($complaint->spareParts->count() > 1) {
          $productName .= ' (+' . ($complaint->spareParts->count() - 1) . ')';
        }
      }
    }
    
    // Category to REQ type mapping
    $reqTypeMap = [
      'electric' => 'ELECTRECION REQ',
      'technical' => 'TECHNICAL REQ',
      'service' => 'SERVICE REQ',
      'billing' => 'BILLING REQ',
      'water' => 'PIPE FITTER REQ',
      'sanitary' => 'SANITARY REQ',
      'plumbing' => 'PLUMBING REQ',
      'kitchen' => 'KITCHEN REQ',
      'other' => 'OTHER REQ',
    ];
    
    $reqType = $reqTypeMap[strtolower($category)] ?? strtoupper($category) . ' REQ';
    
    // Format display text with category and product name
    if ($department) {
      if (strpos(strtoupper($department), 'B&R') !== false) {
        if ($productName) {
          $displayText = $department . ' - ' . $productName . ' - MASSON REQ';
        } else {
          $displayText = $department . ' - MASSON REQ';
        }
      } else {
        if ($productName) {
          $displayText = $department . ' - ' . $productName . ' - ' . $reqType;
        } else {
          $displayText = $department . ' - ' . $reqType;
        }
      }
    } else {
      $categoryDisplay = [
        'electric' => 'Electric',
        'technical' => 'Technical',
        'service' => 'Service',
        'billing' => 'Billing',
        'water' => 'Water Supply',
        'sanitary' => 'Sanitary',
        'plumbing' => 'Plumbing',
        'kitchen' => 'Kitchen',
        'other' => 'Other',
      ];
      $catDisplay = $categoryDisplay[strtolower($category)] ?? ucfirst($category);
      
      if ($productName) {
        $displayText = $catDisplay . ' - ' . $productName . ' - ' . $reqType;
      } else {
        $displayText = $catDisplay . ' - ' . $reqType;
      }
    }
    
    $complaintStatus = $complaint->status ?? 'new';
    $statusDisplay = $complaintStatus == 'in_progress' ? 'In-Process' : 
                    ($complaintStatus == 'resolved' || $complaintStatus == 'closed' ? 'Addressed' : 
                    ucfirst(str_replace('_', ' ', $complaintStatus)));
  }
@endphp

@if($complaint)
<!-- COMPLAINT INFORMATION -->
<div class="card-glass mb-4">
  <div class="card-header">
    <h5 class="card-title mb-0 text-white">
      <i data-feather="file-text" class="me-2"></i>Complaint Information
    </h5>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <table class="table table-borderless">
          <tr>
            <td class="text-white"><strong>Registration Date/Time:</strong></td>
            <td class="text-white">{{ $complaint->created_at ? $complaint->created_at->format('M d, Y H:i:s') : 'N/A' }}</td>
          </tr>
          <tr>
            <td class="text-white"><strong>Addressed Date/Time:</strong></td>
            <td class="text-white">{{ $complaint->closed_at ? $complaint->closed_at->format('M d, Y H:i:s') : '-' }}</td>
          </tr>
          <tr>
            <td class="text-white"><strong>Complaint ID:</strong></td>
            <td>
              <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="text-decoration-none" style="color: #3b82f6;">
                {{ str_pad($complaint->complaint_id ?? $complaint->id, 4, '0', STR_PAD_LEFT) }}
              </a>
            </td>
          </tr>
          <tr>
            <td class="text-white"><strong>Complainant Name:</strong></td>
            <td class="text-white">{{ $complaint->client->client_name ?? 'N/A' }}</td>
          </tr>
          <tr>
            <td class="text-white"><strong>Address:</strong></td>
            <td class="text-white">{{ $complaint->client->address ?? 'N/A' }}</td>
          </tr>
        </table>
      </div>
      <div class="col-md-6">
        <table class="table table-borderless">
          <tr>
            <td class="text-white"><strong>Complaint Nature & Type:</strong></td>
            <td>
              <div class="text-white">{{ $displayText }}</div>
            </td>
          </tr>
          <tr>
            <td class="text-white"><strong>Mobile No.:</strong></td>
            <td class="text-white">{{ $complaint->client->phone ?? 'N/A' }}</td>
          </tr>
          <tr>
            <td class="text-white"><strong>Status:</strong></td>
            <td>
              @if($complaintStatus == 'resolved' || $complaintStatus == 'closed')
                <span class="badge" style="background-color: #22c55e; color: white; padding: 8px 16px; font-size: 13px; font-weight: 600; border-radius: 6px;">
                  Addressed
                </span>
              @else
                <span class="badge bg-secondary">{{ $statusDisplay }}</span>
              @endif
            </td>
          </tr>
          @if($complaint->city)
          <tr>
            <td class="text-white"><strong>City:</strong></td>
            <td class="text-white">{{ $complaint->city }}</td>
          </tr>
          @endif
          @if($complaint->sector)
          <tr>
            <td class="text-white"><strong>Sector:</strong></td>
            <td class="text-white">{{ $complaint->sector }}</td>
          </tr>
          @endif
        </table>
      </div>
    </div>
    @if($complaint->description)
    <div class="row mt-3">
      <div class="col-md-12">
        <div class="card-glass" style="background-color: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3);">
          <div class="card-body">
            <h6 class="text-white mb-3" style="color: #93c5fd; font-weight: 600;">
              <i data-feather="file-text" class="me-2"></i>Description
            </h6>
            <p class="text-white mb-0" style="color: #dbeafe; line-height: 1.6;">
              {{ $complaint->description }}
            </p>
          </div>
        </div>
      </div>
    </div>
    @endif
  </div>
</div>
@endif

{{-- Approval Information section removed as requested --}}

<!-- REQUESTED ITEMS -->
<div class="card-glass">
  <div class="card-header">
    <h5 class="card-title mb-0 text-white">
      <i data-feather="package" class="me-2"></i>Requested Items ({{ $approval->items->count() }})
    </h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table" style="margin-bottom: 0;">
        <thead>
          <tr style="background-color: rgba(59, 130, 246, 0.2); border-bottom: 2px solid rgba(59, 130, 246, 0.5);">
            <th style="color: #ffffff; font-weight: 600; padding: 12px; border: none;">#</th>
            <th style="color: #ffffff; font-weight: 600; padding: 12px; border: none;">Item Name</th>
            <th style="color: #ffffff; font-weight: 600; padding: 12px; border: none; text-align: center;">Quantity Requested</th>
            <th style="color: #ffffff; font-weight: 600; padding: 12px; border: none; text-align: center;">
              @if($approval->status === 'pending')
                Quantity Approved (Editable)
              @else
                Quantity Approved
              @endif
            </th>
            <th style="color: #ffffff; font-weight: 600; padding: 12px; border: none; text-align: center;">Available Stock</th>
          </tr>
        </thead>
        <tbody>
          @forelse($approval->items as $index => $item)
          <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
            <td style="color: #e2e8f0; padding: 12px; border: none; font-weight: 500;">{{ $index + 1 }}</td>
            <td style="color: #ffffff; padding: 12px; border: none; font-weight: 500;">{{ $item->spare->item_name ?? 'N/A' }}</td>
            <td style="color: #e2e8f0; padding: 12px; border: none; text-align: center;">
              <span class="badge" style="background-color: rgba(245, 158, 11, 0.2); color: #fbbf24; padding: 6px 12px; font-weight: 600;">
                {{ $item->quantity_requested }}
              </span>
            </td>
            <td style="color: #e2e8f0; padding: 12px; border: none; text-align: center;">
              @if($approval->status === 'pending')
                @php
                  $availableQty = $item->spare->stock_quantity ?? 0;
                  $requestedQty = (int)$item->quantity_requested;
                  $maxQty = min($requestedQty, $availableQty);
                  $approvedQty = $item->quantity_approved !== null ? (int)$item->quantity_approved : ($availableQty > 0 ? min($requestedQty, $availableQty) : 0);
                @endphp
                <input type="number" 
                       class="form-control form-control-sm text-center approved-qty-input" 
                       name="items[{{ $item->id }}][quantity_approved]"
                       value="{{ $approvedQty }}"
                       min="0"
                       max="{{ $maxQty }}"
                       data-item-id="{{ $item->id }}"
                       data-requested="{{ $requestedQty }}"
                       data-available="{{ $availableQty }}"
                       style="width: 80px; display: inline-block; background-color: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); color: #ffffff;"
                       onchange="updateApprovedQuantity({{ $item->id }}, this.value, {{ $requestedQty }}, {{ $availableQty }})">
                <small class="d-block text-muted mt-1" style="font-size: 10px; color: #9ca3af !important;">
                  Max: {{ $maxQty }}
                </small>
              @else
                @if($item->quantity_approved !== null)
                  <span class="badge" style="background-color: rgba(34, 197, 94, 0.2); color: #22c55e; padding: 6px 12px; font-weight: 600;">
                    {{ $item->quantity_approved }}
                  </span>
                @else
                  <span class="badge" style="background-color: rgba(107, 114, 128, 0.2); color: #9ca3af; padding: 6px 12px; font-weight: 600;">
                    {{ $item->quantity_requested }}
                  </span>
                @endif
              @endif
            </td>
            <td style="color: #e2e8f0; padding: 12px; border: none; text-align: center;">
              @php
                $stockQty = $item->spare->stock_quantity ?? 0;
                $isLowStock = $stockQty <= 0;
                $stockColor = $isLowStock ? 'danger' : 'success';
              @endphp
              <span class="badge bg-{{ $stockColor }}" style="padding: 6px 12px; font-weight: 600;">
                {{ number_format($stockQty, 0) }}
              </span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="text-center text-muted py-4" style="color: #94a3b8;">No items found</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@if($approval->status === 'pending')
<!-- APPROVAL ACTIONS -->
<div class="card-glass mt-4">
  <div class="card-body">
    <form id="approveForm" action="{{ route('admin.approvals.approve', $approval->id) }}" method="POST" onsubmit="return confirmApproval()">
      @csrf
      <div class="row mb-3">
        <div class="col-md-12">
          <label for="remarks" class="form-label text-white">
            <i data-feather="message-square" class="me-2"></i>Remarks (Optional)
          </label>
          <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Enter remarks about this approval..." style="background-color: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); color: #ffffff;">{{ old('remarks') }}</textarea>
          <small class="text-muted">Add any remarks about this approval (optional)</small>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <button type="submit" class="btn btn-success w-100" style="padding: 12px; font-size: 15px; font-weight: 600;">
            <i data-feather="check-circle" class="me-2"></i>Approve
          </button>
        </div>
        <div class="col-md-6">
          <button type="button" class="btn btn-danger w-100" id="rejectBtn" style="padding: 12px; font-size: 15px; font-weight: 600;">
            <i data-feather="x-circle" class="me-2"></i>Reject
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
@endif

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Attach event listener to reject button
    const rejectBtn = document.getElementById('rejectBtn');
    if (rejectBtn) {
      rejectBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        rejectApproval(e);
        return false;
      });
    }
  });

  function updateApprovedQuantity(itemId, value, requested, available) {
    const input = document.querySelector(`input[data-item-id="${itemId}"]`);
    const val = parseInt(value) || 0;
    const maxQty = Math.min(requested, available);
    
    // Validate input
    if (val < 0) {
      input.value = 0;
      showWarning('Quantity cannot be negative');
      return;
    }
    
    if (val > maxQty) {
      input.value = maxQty;
      showWarning('Quantity cannot exceed available stock (' + maxQty + ')');
      return;
    }
    
    // Update max attribute
    input.max = maxQty;
    
    // Update reason field if quantity is less than requested
    if (val < requested && available > 0) {
      // Reason will be auto-updated on approval
      console.log('Quantity adjusted: Requested ' + requested + ', Approved ' + val + ', Available ' + available);
    }
  }

  function confirmApproval() {
    const form = document.getElementById('approveForm');
    if (!form) return false;
    
    const inputs = form.querySelectorAll('.approved-qty-input');
    let hasError = false;
    let message = 'Confirm approval with the following quantities:\n\n';
    
    inputs.forEach(input => {
      const itemId = input.dataset.itemId;
      const requested = parseInt(input.dataset.requested);
      const available = parseInt(input.dataset.available);
      const approved = parseInt(input.value) || 0;
      
      if (approved > available) {
        hasError = true;
        input.classList.add('is-invalid');
      } else {
        input.classList.remove('is-invalid');
        const itemName = input.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
        message += itemName + ': ' + approved + ' / ' + requested + ' (Available: ' + available + ')\n';
        
        if (approved < requested) {
          message += '  ⚠️ Less than requested\n';
        }
      }
    });
    
    if (hasError) {
      alert('Error: Some quantities exceed available stock. Please adjust.');
      return false;
    }
    
    return confirm(message + '\nProceed with approval?');
  }

  function showWarning(message) {
    // Simple alert for now - can be enhanced with toast notifications
    alert(message);
  }

  function rejectApproval(e) {
    // Prevent any default behavior
    if (e) {
      e.preventDefault();
      e.stopPropagation();
    }
    
    const remarks = prompt('Please enter rejection reason (required):');
    if (remarks === null) {
      return false; // User cancelled
    }
    if (!remarks || remarks.trim() === '') {
      alert('Rejection reason is required');
      return false;
    }
    
    // Use fetch API for POST request with form data
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
      alert('CSRF token not found. Please refresh the page.');
      return false;
    }
    
    // Create form data
    const formData = new FormData();
    formData.append('remarks', remarks.trim());
    formData.append('_token', csrfToken);
    
    const rejectUrl = '{{ route("admin.approvals.reject", $approval->id) }}';
    console.log('Rejecting approval via POST to:', rejectUrl);
    
    fetch(rejectUrl, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: formData,
      credentials: 'same-origin'
    })
    .then(response => {
      console.log('Response status:', response.status);
      if (!response.ok && response.status === 422) {
        return response.json().then(data => {
          throw new Error(data.message || 'Validation failed');
        });
      }
      if (!response.ok) {
        throw new Error('HTTP error! status: ' + response.status);
      }
      return response.json();
    })
    .then(data => {
      console.log('Response data:', data);
      if (data.success || !data.errors) {
        alert('Approval rejected successfully!');
        window.location.reload();
      } else {
        alert('Failed to reject: ' + (data.message || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error rejecting request: ' + error.message);
    });
    
    return false;
  }
</script>
@endpush
@endsection
