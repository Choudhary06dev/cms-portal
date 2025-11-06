@extends('layouts.sidebar')

@section('title', 'Approval Details — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Complaint Details</h2>
    </div>
  </div>
</div>

@php
  $complaint = $approval->complaint ?? null;
  if ($complaint) {
    $category = $complaint->category ?? 'N/A';

    // Map category for display
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

    // Use assigned employee designation like index
    $designation = $complaint->assignedEmployee->designation ?? 'N/A';
    $displayText = $catDisplay . ' - ' . $designation;

    $rawStatus = $complaint->status ?? 'new';
    $complaintStatus = ($rawStatus == 'new') ? 'assigned' : $rawStatus;
    $statusDisplay = $complaintStatus == 'in_progress' ? 'In-Process' : 
                    ($complaintStatus == 'resolved' ? 'Addressed' : 
                    ucfirst(str_replace('_', ' ', $complaintStatus)));
    $statusColors = [
      'in_progress' => ['bg' => '#dc2626', 'text' => '#ffffff', 'border' => '#b91c1c'],
      'resolved' => ['bg' => '#16a34a', 'text' => '#ffffff', 'border' => '#15803d'],
      'work_performa' => ['bg' => '#0ea5e9', 'text' => '#ffffff', 'border' => '#0284c7'],
      'maint_performa' => ['bg' => '#fef08a', 'text' => '#ffffff', 'border' => '#eab308'],
      'assigned' => ['bg' => '#64748b', 'text' => '#ffffff', 'border' => '#475569'],
    ];
    $currentStatusColor = $statusColors[$complaintStatus] ?? $statusColors['assigned'];
  }
@endphp

@if($complaint)
<!-- COMPLAINT INFORMATION -->
<div class="d-flex justify-content-center">
  <div class="card-glass mb-4" style="max-width: 900px; width: 100%;">
    <div class="row">
      <div class="col-12">
      <div class="row">
        <div class="col-md-6">
          <h6 class="text-white fw-bold mb-3"><i data-feather="user" class="me-2" style="width: 16px; height: 16px;"></i>Complainant Information</h6>
          <div class="mb-3">
            <span class="text-muted">Complainant Name:</span>
            <span class="text-white ms-2">{{ $complaint->client->client_name ?? 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Address:</span>
            <span class="text-white ms-2">{{ $complaint->client->address ?? 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Phone No.:</span>
            <span class="text-white ms-2">{{ $complaint->client->phone ?? 'N/A' }}</span>
          </div>
          @if($complaint->city)
          <div class="mb-3">
            <span class="text-muted">City:</span>
            <span class="text-white ms-2">{{ $complaint->city }}</span>
          </div>
          @endif
          @if($complaint->sector)
          <div class="mb-3">
            <span class="text-muted">Sector:</span>
            <span class="text-white ms-2">{{ $complaint->sector }}</span>
          </div>
          @endif
          @if($complaint->description)
          <div class="mb-3">
            <span class="text-muted">Description:</span>
            <span class="text-white ms-2">{{ $complaint->description }}</span>
          </div>
          @endif
        </div>
        
        <div class="col-md-6">
          <h6 class="text-white fw-bold mb-3"><i data-feather="alert-triangle" class="me-2" style="width: 16px; height: 16px;"></i>Complaint Information</h6>
          <div class="mb-3">
            <span class="text-muted">Complaint ID:</span>
            <span class="text-white ms-2">
              <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="text-decoration-none" style="color: #3b82f6;">
                {{ str_pad($complaint->complaint_id ?? $complaint->id, 4, '0', STR_PAD_LEFT) }}
              </a>
            </span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Complaint Title:</span>
            <span class="text-white ms-2">{{ $complaint->title ?? 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Registration Date/Time:</span>
            <span class="text-white ms-2">{{ $complaint->created_at ? $complaint->created_at->timezone('Asia/Karachi')->format('M d, Y H:i:s') : 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Addressed Date/Time:</span>
            <span class="text-white ms-2">{{ $complaint->closed_at ? $complaint->closed_at->timezone('Asia/Karachi')->format('M d, Y H:i:s') : '-' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Complaint Nature & Type:</span>
            <span class="text-white ms-2" style="font-weight: normal;">{{ $displayText }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Assigned Employee:</span>
            <span class="text-white ms-2">{{ $complaint->assignedEmployee->name ?? 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted">Status:</span>
            <span class="badge ms-2" style="background-color: {{ $currentStatusColor['bg'] }}; color: #ffffff !important; padding: 6px 12px; font-size: 12px; font-weight: 600; border-radius: 6px; border: 1px solid {{ $currentStatusColor['border'] }};">
              {{ $statusDisplay }}
            </span>
          </div>
          @if($complaintStatus === 'in_progress')
          <div class="mb-3">
            <span class="text-muted">In-Process Reason:</span>
            <select class="form-select form-select-sm mt-2 in-process-reason-select" 
                    data-approval-id="{{ $approval->id }}"
                    data-complaint-id="{{ $complaint->id }}"
                    style="max-width: 400px; background-color: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); color: #ffffff;">
              <option value="">Select Reason...</option>
              <option value="Work Performa In-Progress" {{ $approval->remarks === 'Work Performa In-Progress' ? 'selected' : '' }}>Work Performa In-Progress</option>
              <option value="Maintenance Performa In-Progress" {{ $approval->remarks === 'Maintenance Performa In-Progress' ? 'selected' : '' }}>Maintenance Performa In-Progress</option>
              <option value="Technician Busy Somewhere else" {{ $approval->remarks === 'Technician Busy Somewhere else' ? 'selected' : '' }}>Technician Busy Somewhere else</option>
              <option value="Material Awaited" {{ $approval->remarks === 'Material Awaited' ? 'selected' : '' }}>Material Awaited</option>
              <option value="Waiting for Resident Availability" {{ $approval->remarks === 'Waiting for Resident Availability' ? 'selected' : '' }}>Waiting for Resident Availability</option>
              <option value="Work Delayed due to Weather Conditions" {{ $approval->remarks === 'Work Delayed due to Weather Conditions' ? 'selected' : '' }}>Work Delayed due to Weather Conditions</option>
              <option value="Equipment under Repair" {{ $approval->remarks === 'Equipment under Repair' ? 'selected' : '' }}>Equipment under Repair</option>
              <option value="Parts Not Available in Stock" {{ $approval->remarks === 'Parts Not Available in Stock' ? 'selected' : '' }}>Parts Not Available in Stock</option>
              <option value="Waiting for Vendor Delivery" {{ $approval->remarks === 'Waiting for Vendor Delivery' ? 'selected' : '' }}>Waiting for Vendor Delivery</option>
              <option value="Site Access Issues" {{ $approval->remarks === 'Site Access Issues' ? 'selected' : '' }}>Site Access Issues</option>
              <option value="Pending Approval from Management" {{ $approval->remarks === 'Pending Approval from Management' ? 'selected' : '' }}>Pending Approval from Management</option>
              <option value="Technical Investigation Required" {{ $approval->remarks === 'Technical Investigation Required' ? 'selected' : '' }}>Technical Investigation Required</option>
            </select>
            @if($approval->remarks)
            <div class="mt-2">
              <small class="text-muted">Current Reason: <span class="text-white">{{ $approval->remarks }}</span></small>
            </div>
            @endif
          </div>
          @elseif($approval->remarks)
          <div class="mb-3">
            <span class="text-muted">Previous Reason:</span>
            <span class="text-white ms-2">{{ $approval->remarks }}</span>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
  
  <hr class="my-4">
  </div>
</div>
@endif

{{-- Approval Information section removed as requested --}}

@php
  // Build unified requested items: prefer approval items; else robust fallbacks from complaint
  // Also include stock logs from spare_stock_logs table
  $stockLogs = $complaint ? $complaint->stockLogs()->with('spare')->get() : collect();
  
  $collections = [
    ($approval->items ?? null),
    ($complaint->spareParts ?? null),
    ($stockLogs->count() > 0 ? $stockLogs : null),
    ($complaint->requestedItems ?? null),
    ($complaint->items ?? null),
    ($complaint->stocks ?? null),
    ($complaint->stockItems ?? null),
    ($complaint->stock_ins ?? null),
  ];
  $requestedItems = collect();
  foreach ($collections as $col) {
    if ($col && $col->count() > 0) { 
      $requestedItems = $col; 
      break; 
    }
  }
  
  // If we got stock logs, convert them to the expected format
  if ($requestedItems->count() > 0 && $requestedItems->first() && isset($requestedItems->first()->change_type)) {
    // This is stock logs collection, convert to expected format
    $convertedItems = collect();
    foreach ($requestedItems as $log) {
      if ($log->spare) {
        $convertedItems->push((object)[
          'id' => $log->id,
          'spare' => $log->spare,
          'quantity_requested' => $log->quantity,
          'quantity_approved' => $log->quantity,
          'spare_id' => $log->spare_id,
        ]);
      }
    }
    $requestedItems = $convertedItems;
  }
@endphp

<!-- REQUESTED ITEMS -->
<div class="card-glass">
  <div class="card-header">
    <h5 class="card-title mb-0 text-white">
      <i data-feather="package" class="me-2"></i>Requested Items ({{ $requestedItems->count() }})
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
              @if($approval->status === 'pending' && ($approval->items && $approval->items->count() > 0))
                Quantity Approved (Editable)
              @else
                Quantity Approved
              @endif
            </th>
            <th style="color: #ffffff; font-weight: 600; padding: 12px; border: none; text-align: center;">Available Stock</th>
          </tr>
        </thead>
        <tbody>
          @forelse($requestedItems as $index => $item)
          @php
            // Try to resolve an attached spare/product model
            $spareModel = $item->spare ?? $item->product ?? $item->item ?? null;
            $itemName = $spareModel->item_name ?? $spareModel->name ?? $item->spare_name ?? $item->item_name ?? $item->name ?? 'N/A';
            $availableQty = $spareModel->stock_quantity ?? $spareModel->available_quantity ?? 0;
            // Normalize requested/approved quantity field names
            $requestedQty = $item->quantity_requested ?? $item->requested_quantity ?? $item->qty ?? $item->quantity ?? 0;
            $approvedQty = $item->quantity_approved ?? $item->approved_quantity ?? null;
          @endphp
          <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
            <td style="color: #e2e8f0; padding: 12px; border: none; font-weight: 500;">{{ $index + 1 }}</td>
            <td style="color: #ffffff; padding: 12px; border: none; font-weight: 500;">{{ $itemName }}</td>
            <td style="color: #e2e8f0; padding: 12px; border: none; text-align: center;">
              <span class="badge" style="background-color: rgba(245, 158, 11, 0.2); color: #fbbf24; padding: 6px 12px; font-weight: 600;">
                {{ number_format((int)$requestedQty, 0) }}
              </span>
            </td>
            <td style="color: #e2e8f0; padding: 12px; border: none; text-align: center;">
              @if($approval->status === 'pending' && ($approval->items && $approval->items->count() > 0))
                @php
                  $maxQty = min((int)$requestedQty, (int)$availableQty);
                  $inputVal = $approvedQty !== null ? (int)$approvedQty : ($availableQty > 0 ? $maxQty : 0);
                @endphp
                <input type="number" 
                       class="form-control form-control-sm text-center approved-qty-input" 
                       name="items[{{ $item->id }}][quantity_approved]"
                       value="{{ $inputVal }}"
                       min="0"
                       max="{{ $maxQty }}"
                       data-item-id="{{ $item->id }}"
                       data-requested="{{ (int)$requestedQty }}"
                       data-available="{{ (int)$availableQty }}"
                       style="width: 80px; display: inline-block; background-color: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); color: #ffffff;">
              @else
                <span class="badge" style="background-color: rgba(34, 197, 94, 0.2); color: #22c55e; padding: 6px 12px; font-weight: 600;">
                  {{ $approvedQty !== null ? number_format((int)$approvedQty, 0) : number_format((int)$requestedQty, 0) }}
                </span>
              @endif
            </td>
            <td style="color: #e2e8f0; padding: 12px; border: none; text-align: center;">
              <span class="badge bg-{{ ((int)$availableQty <= 0) ? 'danger' : 'success' }}" style="padding: 6px 12px; font-weight: 600;">
                {{ number_format((int)$availableQty, 0) }}
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

{{-- Approval Actions section removed as per user request --}}

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    feather.replace();

    // Handle In-Process Reason dropdown change
    const reasonSelect = document.querySelector('.in-process-reason-select');
    if (reasonSelect) {
      reasonSelect.addEventListener('change', function() {
        const select = this;
        const approvalId = select.getAttribute('data-approval-id');
        const complaintId = select.getAttribute('data-complaint-id');
        const reason = select.value;

        if (!reason) {
          return;
        }

        // Show loading state
        const originalValue = select.value;
        select.disabled = true;
        select.style.opacity = '0.6';

        // Save reason to approval
        fetch(`/admin/approvals/${approvalId}/update-reason`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            reason: reason,
            complaint_id: complaintId
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Show success message
            const successMsg = document.createElement('div');
            successMsg.className = 'alert alert-success alert-dismissible fade show mt-2';
            successMsg.style.cssText = 'padding: 8px 12px; font-size: 12px; margin-top: 8px;';
            successMsg.innerHTML = `
              <i data-feather="check-circle" style="width: 14px; height: 14px;"></i> Reason saved successfully!
              <button type="button" class="btn-close" data-bs-dismiss="alert" style="padding: 4px; font-size: 10px;"></button>
            `;
            
            // Remove existing alerts
            const existingAlert = select.parentElement.querySelector('.alert');
            if (existingAlert) {
              existingAlert.remove();
            }
            
            select.parentElement.appendChild(successMsg);
            feather.replace();
            
            // Auto-hide after 3 seconds
            setTimeout(() => {
              successMsg.remove();
            }, 3000);
          } else {
            throw new Error(data.message || 'Failed to save reason');
          }
        })
        .catch(error => {
          console.error('Error saving reason:', error);
          alert('Error saving reason: ' + (error.message || 'Unknown error'));
          select.value = originalValue;
        })
        .finally(() => {
          select.disabled = false;
          select.style.opacity = '1';
        });
      });
    }
  });
</script>
@endpush
@endsection
