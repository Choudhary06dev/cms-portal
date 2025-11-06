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
@php
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
@endphp
<div class="d-flex justify-content-center">
  <div class="card-glass" style="max-width: 900px; width: 100%;">
    <div class="row">
      <div class="col-12">
      <div class="row">
        <div class="col-md-6">
          <h6 class="text-white fw-bold mb-3" style="font-size: 1rem; font-weight: 700;"><i data-feather="user" class="me-2" style="width: 16px; height: 16px;"></i>Complainant Information</h6>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Complainant Name:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $complaint->client->client_name ?? 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">City:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $complaint->city ?? 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Sector:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $complaint->sector ?? 'N/A' }}</span>
          </div>
         
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Address:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $complaint->client->address ?? 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Phone No:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $complaint->client->phone ?? 'N/A' }}</span>
          </div>
          
          <h6 class="text-white fw-bold mb-3 mt-4" style="font-size: 1rem; font-weight: 700;">Additional Information</h6>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Complaint Nature & Type:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem; font-weight: normal;">
              @php
                $category = $complaint->category ?? 'N/A';
                $designation = $complaint->assignedEmployee->designation ?? 'N/A';
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
                $displayText = $catDisplay . ' - ' . $designation;
              @endphp
              {{ $displayText }}
            </span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Status:</span>
            <span class="badge ms-2" style="background-color: {{ $currentStatusColor['bg'] }}; color: #ffffff !important; padding: 6px 12px; font-size: 0.875rem; font-weight: 600; border-radius: 6px; border: 1px solid {{ $currentStatusColor['border'] }};">
              {{ $statusDisplay }}
            </span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Priority:</span>
            <span class="badge bg-{{ $complaint->priority === 'high' ? 'danger' : ($complaint->priority === 'medium' ? 'warning' : 'success') }} ms-2" style="color: #ffffff !important; font-size: 0.875rem;">
              {{ ucfirst($complaint->priority) }}
            </span>
          </div>
        </div>
        
        <div class="col-md-6">
          <h6 class="text-white fw-bold mb-3" style="font-size: 1rem; font-weight: 700;"><i data-feather="alert-triangle" class="me-2" style="width: 16px; height: 16px;"></i>Complaint Information</h6>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Complaint ID:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ str_pad($complaint->complaint_id ?? $complaint->id, 4, '0', STR_PAD_LEFT) }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Complaint Title:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $complaint->title ?? 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Registration Date/Time:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $complaint->created_at ? $complaint->created_at->timezone('Asia/Karachi')->format('M d, Y H:i:s') : 'N/A' }}</span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Completion Time:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">
              @if($complaint->closed_at)
                {{ $complaint->closed_at->timezone('Asia/Karachi')->format('M d, Y H:i:s') }}
              @elseif($complaint->status == 'resolved' || $complaint->status == 'closed')
                {{ $complaint->updated_at->timezone('Asia/Karachi')->format('M d, Y H:i:s') }}
              @else
                -
              @endif
            </span>
          </div>
          <div class="mb-3">
            <span class="text-muted fw-bold" style="font-size: 0.875rem;">Assigned Employee:</span>
            <span class="text-white ms-2" style="font-size: 0.875rem;">{{ $complaint->assignedEmployee->name ?? 'N/A' }}</span>
          </div>
           
      <div class="row mt-3">
        <div class="col-12">
          <h6 class="text-white fw-bold mb-3" style="font-size: 1rem; font-weight: 700;">Description</h6>
          <p class="text-white mb-0" style="font-size: 0.875rem;">{{ $complaint->description ?? 'N/A' }}</p>
        </div>
      </div>
    </div>
  </div>
        </div>
      </div>
   
  
  @if($complaint->attachments->count() > 0)
  <hr class="my-4">
  <div class="row">
    <div class="col-12">
      <h6 class="text-white fw-bold mb-3">Attachments</h6>
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
  
    <hr class="my-4">
    
  
  </div>
</div>

<!-- REQUESTED ITEMS SECTION -->
@php
  // Get stock logs for this complaint (where reference_id = complaint_id)
  $stockLogs = $complaint->stockLogs()->with('spare')->get();
  
  // Also get spare parts from complaint_spares table
  $spareParts = $complaint->spareParts()->with('spare')->get();
  
  // Combine both sources - prefer stock logs, then spare parts
  $requestedItems = collect();
  
  // First, add items from stock logs
  foreach ($stockLogs as $log) {
    if ($log->spare) {
      $requestedItems->push((object)[
        'id' => $log->id,
        'spare' => $log->spare,
        'quantity' => $log->quantity,
        'quantity_requested' => $log->quantity,
        'change_type' => $log->change_type,
        'created_at' => $log->created_at,
        'source' => 'stock_log'
      ]);
    }
  }
  
  // Then, add items from spare parts that are not already in stock logs
  foreach ($spareParts as $sparePart) {
    if ($sparePart->spare) {
      $existsInLogs = $stockLogs->contains(function($log) use ($sparePart) {
        return $log->spare_id == $sparePart->spare_id;
      });
      
      if (!$existsInLogs) {
        $requestedItems->push((object)[
          'id' => $sparePart->id,
          'spare' => $sparePart->spare,
          'quantity' => $sparePart->quantity,
          'quantity_requested' => $sparePart->quantity,
          'change_type' => 'out',
          'created_at' => $sparePart->created_at,
          'source' => 'spare_part'
        ]);
      }
    }
  }
@endphp

@if($requestedItems->count() > 0)
<div class="d-flex justify-content-center mt-4">
  <div style="max-width: 900px; width: 100%;">
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
                <th style="color: #ffffff; font-weight: 600; padding: 12px; border: none; text-align: center;">Quantity</th>
                <th style="color: #ffffff; font-weight: 600; padding: 12px; border: none; text-align: center;">Available Stock</th>
                <th style="color: #ffffff; font-weight: 600; padding: 12px; border: none; text-align: center;">Date Added</th>
              </tr>
            </thead>
            <tbody>
              @foreach($requestedItems as $index => $item)
              @php
                $spareModel = $item->spare ?? null;
                $itemName = $spareModel->item_name ?? 'N/A';
                $availableQty = $spareModel->stock_quantity ?? 0;
                $requestedQty = $item->quantity_requested ?? $item->quantity ?? 0;
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
                  <span class="badge bg-{{ ((int)$availableQty <= 0) ? 'danger' : 'success' }}" style="padding: 6px 12px; font-weight: 600;">
                    {{ number_format((int)$availableQty, 0) }}
                  </span>
                </td>
                <td style="color: #e2e8f0; padding: 12px; border: none; text-align: center;">
                  <small>{{ $item->created_at ? $item->created_at->timezone('Asia/Karachi')->format('M d, Y H:i') : 'N/A' }}</small>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<!-- FEEDBACK SECTION -->
@if($complaint->status == 'resolved' || $complaint->status == 'closed')
<div class="d-flex justify-content-center mt-4">
  <div style="max-width: 900px; width: 100%;">
    <div class="card-glass">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 text-white">
          <i data-feather="message-circle" class="me-2"></i>Complainant Feedback
        </h5>
        @if(!$complaint->feedback)
          <a href="{{ route('admin.feedback.create', $complaint->id) }}" class="btn btn-primary btn-sm" title="Add Feedback" style="padding: 3px 8px;">
            <i data-feather="plus-circle" style="width: 16px; height: 16px;"></i>
          </a>
        @else
          <div class="btn-group" role="group">
            <a href="{{ route('admin.feedback.edit', $complaint->feedback->id) }}" class="btn btn-outline-primary btn-sm" title="Edit Feedback" style="padding: 3px 8px;">
              <i data-feather="edit" style="width: 16px; height: 16px;"></i>
            </a>
            <form action="{{ route('admin.feedback.destroy', $complaint->feedback->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this feedback?');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Feedback" style="padding: 3px 8px;">
                <i data-feather="trash-2" style="width: 16px; height: 16px;"></i>
              </button>
            </form>
          </div>
        @endif
      </div>
      <div class="card-body">
        @if($complaint->feedback)
          <div class="row">
            <div class="col-md-6">
              <table class="table table-borderless">
                <tr>
                  <td class="text-white"><strong>Overall Rating:</strong></td>
                  <td>
                    <span class="badge bg-{{ $complaint->feedback->rating_badge_color }}" style="color: #ffffff !important;">
                      {{ $complaint->feedback->overall_rating_display }}
                    </span>
                    @if($complaint->feedback->rating_score)
                      <span class="text-white ms-2">({{ $complaint->feedback->rating_score }}/5)</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <td class="text-white"><strong>Feedback Date:</strong></td>
                  <td class="text-white">{{ $complaint->feedback->created_at ? $complaint->feedback->created_at->format('M d, Y H:i:s') : 'N/A' }}</td>
                </tr>
                <tr>
                  <td class="text-white"><strong>Entered By:</strong></td>
                  <td class="text-white">{{ $complaint->feedback->enteredBy->username ?? 'N/A' }}</td>
                </tr>
                @php
                  $geUser = null;
                  if ($complaint->city) {
                    $city = \App\Models\City::where('name', $complaint->city)->first();
                    if ($city) {
                      $geUser = \App\Models\User::where('city_id', $city->id)
                        ->whereHas('role', function($q) {
                          $q->where('role_name', 'garrison_engineer');
                        })
                        ->first();
                    }
                  }
                @endphp
                @if($geUser)
                <tr>
                  <td class="text-white"><strong>GE (City):</strong></td>
                  <td class="text-white">{{ $geUser->username ?? 'N/A' }}</td>
                </tr>
                @endif
                <tr>
                  <td class="text-white"><strong>Entered At:</strong></td>
                  <td class="text-white">{{ $complaint->feedback->entered_at ? $complaint->feedback->entered_at->format('M d, Y H:i:s') : 'N/A' }}</td>
                </tr>
              </table>
            </div>
          </div>
          @if($complaint->feedback->comments)
          <div class="row mt-2">
            <div class="col-12">
              <h6 class="text-white fw-bold mb-1" style="font-size: 0.9rem;">Complainant Comments:</h6>
              <div class="card-glass" style="background-color: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3);">
                <div class="card-body">
                  <p class="text-white mb-0" style="color: #dbeafe; line-height: 1.6;">
                    {{ $complaint->feedback->comments }}
                  </p>
                </div>
              </div>
            </div>
          </div>
          @endif
        @else
          <div class="text-center py-4">
            <i data-feather="message-circle" class="feather-lg mb-3 text-muted"></i>
            <p class="text-muted mb-3">No feedback has been recorded for this complaint.</p>
            <a href="{{ route('admin.feedback.create', $complaint->id) }}" class="btn btn-primary">
              <i data-feather="plus-circle" class="me-2"></i>Add Complainant Feedback
            </a>
          </div>
        @endif
      </div>
    </div>
  </div>
  </div>
</div>
@endif

<!-- BACK BUTTON -->


@endsection
