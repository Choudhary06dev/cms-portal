@extends('layouts.sidebar')

@section('title', 'Complaint Details')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Complaint Details: {{ $complaint->ticket_number }}</h5>
          <div class="btn-group">
            <a href="{{ route('admin.complaints.edit', $complaint) }}" class="btn btn-warning btn-sm">
              <i data-feather="edit"></i> Edit
            </a>
            <a href="{{ route('admin.complaints.print-slip', $complaint) }}" class="btn btn-info btn-sm" target="_blank">
              <i data-feather="printer"></i> Print Slip
            </a>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-muted">Complaint Information</h6>
                <table class="table table-borderless">
                  <tr>
                    <td><strong>Ticket Number:</strong></td>
                    <td>{{ $complaint->ticket_number }}</td>
                  </tr>
                  <tr>
                    <td><strong>Type:</strong></td>
                    <td>
                      <span class="badge bg-info">{{ ucfirst($complaint->complaint_type) }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Status:</strong></td>
                    <td>
                      <span class="badge bg-{{ $complaint->status === 'resolved' ? 'success' : ($complaint->status === 'closed' ? 'info' : 'warning') }}">
                        {{ ucfirst($complaint->status) }}
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Priority:</strong></td>
                    <td>
                      <span class="badge bg-{{ $complaint->priority === 'high' ? 'danger' : ($complaint->priority === 'medium' ? 'warning' : 'success') }}">
                        {{ ucfirst($complaint->priority) }}
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Location:</strong></td>
                    <td>{{ $complaint->location ?? 'N/A' }}</td>
                  </tr>
                </table>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-muted">Client & Assignment</h6>
                <table class="table table-borderless">
                  <tr>
                    <td><strong>Client:</strong></td>
                    <td>{{ $complaint->client->client_name ?? 'N/A' }}</td>
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
                <div class="btn-group">
                  <a href="{{ route('admin.complaints.edit', $complaint) }}" class="btn btn-warning">
                    <i data-feather="edit"></i> Edit Complaint
                  </a>
                  <a href="{{ route('admin.complaints.print-slip', $complaint) }}" class="btn btn-info" target="_blank">
                    <i data-feather="printer"></i> Print Slip
                  </a>
                  <form action="{{ route('admin.complaints.destroy', $complaint) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this complaint?')">
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
@endsection
