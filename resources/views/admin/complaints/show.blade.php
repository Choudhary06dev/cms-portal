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
      <i data-feather="alert-triangle" class="me-2"></i>Complaint Details: {{ str_pad($complaint->complaint_id ?? $complaint->id, 4, '0', STR_PAD_LEFT) }} - {{ $complaint->title ?? 'N/A' }}
    </h5>
  </div>
  <div class="card-body" style="margin-top: 20px;">
    <div class="row">
      <div class="col-12">
        <div class="row">
          <div class="col-md-6">
            <div class="mb-4">
              <h6 class="text-white fw-bold mb-3">Complaint Information</h6>
              <table class="table table-borderless">
                <tr>
                  <td class="text-white"><strong>Complaint ID:</strong></td>
                  <td class="text-white">{{ str_pad($complaint->complaint_id ?? $complaint->id, 4, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                  <td class="text-white" style="text-align: left;"><strong>Complainant Name:</strong></td>
                  <td class="text-white" style="text-align: left;">{{ $complaint->client->client_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                  <td class="text-white"><strong>Complaint Title:</strong></td>
                  <td class="text-white">{{ $complaint->title ?? 'N/A' }}</td>
                </tr>
                <tr>
                  <td class="text-white"><strong>Registration Date/Time:</strong></td>
                  <td class="text-white">{{ $complaint->created_at ? $complaint->created_at->format('M d, Y H:i:s') : 'N/A' }}</td>
                </tr>
                <tr>
                  <td class="text-white"><strong>Completion Time:</strong></td>
                  <td class="text-white">
                    @if($complaint->closed_at)
                      {{ $complaint->closed_at->format('Y-m-d H:i:s') }}
                    @elseif($complaint->status == 'resolved' || $complaint->status == 'closed')
                      {{ $complaint->updated_at->format('Y-m-d H:i:s') }}
                    @else
                      -
                    @endif
                  </td>
                </tr>
              </table>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-4">
              <h6 class="text-white fw-bold mb-3">Additional Information</h6>
              <table class="table table-borderless">
                <tr>
                  <td class="text-white"><strong>Complaint Nature & Type:</strong></td>
                  <td>
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
                    <span class="text-white">{{ $displayText }}</span>
                  </td>
                </tr>
                <tr>
                  <td class="text-white"><strong>Status:</strong></td>
                  <td>
                    @php
                      $statusDisplay = $complaint->status == 'in_progress' ? 'In-Process' : 
                                      ($complaint->status == 'resolved' || $complaint->status == 'closed' ? 'Addressed' : 
                                      ucfirst(str_replace('_', ' ', $complaint->status)));
                      $statusBadgeClass = ($complaint->status == 'in_progress' || $complaint->status == 'new' || $complaint->status == 'assigned') ? 
                                        'danger' : 'success';
                    @endphp
                    <span class="badge bg-{{ $statusBadgeClass }}" style="color: #ffffff !important;">
                      {{ $statusDisplay }}
                    </span>
                  </td>
                </tr>
                <tr>
                  <td class="text-white"><strong>Priority:</strong></td>
                  <td>
                    <span class="badge bg-{{ $complaint->priority === 'high' ? 'danger' : ($complaint->priority === 'medium' ? 'warning' : 'success') }}" style="color: #ffffff !important;">
                      {{ ucfirst($complaint->priority) }}
                    </span>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="mb-4">
              <h6 class="text-white fw-bold">Description</h6>
              <p class="text-white fw-bold mb-0">{{ $complaint->description }}</p>
            </div>
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
  </div>
</div>

<!-- FEEDBACK SECTION -->
@if($complaint->status == 'resolved' || $complaint->status == 'closed')
<div class="row mt-4">
  <div class="col-12">
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
@endif

<!-- BACK BUTTON -->
<div class="row mt-4">
  <div class="col-12">
    <div class="d-flex justify-content-start">
      <a href="{{ route('admin.complaints.index') }}" class="btn btn-secondary">
        <i data-feather="arrow-left"></i> Back to Complaints
      </a>
    </div>
  </div>
</div>

@endsection
