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
@endsection
