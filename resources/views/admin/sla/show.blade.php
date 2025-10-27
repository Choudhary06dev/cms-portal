@extends('layouts.sidebar')

@section('title', 'SLA Rule Details — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 style="color: var(--text-primary);" class="mb-2">SLA Rule Details</h2>
      <p style="color: var(--text-secondary);">View SLA rule: {{ ucfirst($sla->complaint_type) }}</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.sla.index') }}" class="btn btn-outline-secondary">
        <i data-feather="arrow-left" class="me-2"></i>Back to SLA Rules
      </a>
      <form action="{{ route('admin.sla.toggle-status', $sla) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-{{ $sla->status === 'active' ? 'outline-warning' : 'outline-success' }}">
          <i data-feather="{{ $sla->status === 'active' ? 'pause' : 'play' }}" class="me-2"></i>
          {{ $sla->status === 'active' ? 'Deactivate' : 'Activate' }}
        </button>
      </form>
    </div>
  </div>
</div>

<!-- SLA RULE INFORMATION -->
<div class="card-glass">
  <div class="card-header" style="background-color: var(--bg-elevated); border-bottom: 1px solid var(--border-primary);">
    <h5 class="card-title mb-0" style="color: var(--text-primary);">
      <i data-feather="clock" class="me-2"></i>SLA Rule Information
    </h5>
  </div>
  <div class="card-body" style="background-color: var(--bg-elevated); color: var(--text-primary);">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-4">
                <h6 style="color: var(--text-primary);" class="fw-bold">SLA Configuration</h6>
                <table class="table table-borderless" style="color: var(--text-primary);">
                  <tr>
                    <td style="color: var(--text-muted);"><strong>Complaint Type:</strong></td>
                    <td>
                      <span class="badge bg-info">{{ ucfirst($sla->complaint_type) }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td style="color: var(--text-muted);"><strong>Max Response Time:</strong></td>
                    <td style="color: var(--text-secondary);">{{ $sla->max_response_time }} hours</td>
                  </tr>
                  <tr>
                    <td style="color: var(--text-muted);"><strong>Max Resolution Time:</strong></td>
                    <td style="color: var(--text-secondary);">{{ $sla->max_resolution_time }} hours</td>
                  </tr>
                  <tr>
                    <td style="color: var(--text-muted);"><strong>Escalation Level:</strong></td>
                    <td style="color: var(--text-secondary);">Level {{ $sla->escalation_level }}</td>
                  </tr>
                  <tr>
                    <td style="color: var(--text-muted);"><strong>Status:</strong></td>
                    <td>
                      <span class="badge bg-{{ $sla->status === 'active' ? 'success' : 'danger' }}">
                        {{ ucfirst($sla->status) }}
                      </span>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="mb-4">
                <h6 style="color: var(--text-primary);" class="fw-bold">Notification & Timeline</h6>
                <table class="table table-borderless" style="color: var(--text-primary);">
                  <tr>
                    <td style="color: var(--text-muted);"><strong>Notify To:</strong></td>
                    <td style="color: var(--text-secondary);">{{ $sla->notifyTo->username ?? 'Not Set' }}</td>
                  </tr>
                  <tr>
                    <td style="color: var(--text-muted);"><strong>Created:</strong></td>
                    <td style="color: var(--text-secondary);">{{ $sla->created_at->format('M d, Y H:i') }}</td>
                  </tr>
                  <tr>
                    <td style="color: var(--text-muted);"><strong>Last Updated:</strong></td>
                    <td style="color: var(--text-secondary);">{{ $sla->updated_at->format('M d, Y H:i') }}</td>
                  </tr>
                  <tr>
                    <td style="color: var(--text-muted);"><strong>Total Complaints:</strong></td>
                    <td>
                      <span class="badge bg-primary">{{ $sla->complaints->count() }} complaints</span>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>

          @if($sla->description)
          <div class="row">
            <div class="col-12">
              <h6 style="color: var(--text-primary);" class="fw-bold">Description</h6>
              <div class="card" style="background-color: var(--bg-secondary); border: 1px solid var(--border-primary);">
                <div class="card-body" style="color: var(--text-primary);">
                  <p style="color: var(--text-primary);">{{ $sla->description }}</p>
                </div>
              </div>
            </div>
          </div>
          @endif

          <!-- Recent Complaints -->
          @if(isset($recentComplaints) && $recentComplaints->count() > 0)
          <div class="row mt-4">
            <div class="col-12">
              <h6 class="fw-bold">Recent Complaints</h6>
              <div class="table-responsive">
                <table class="table table-sm table-striped">
                  <thead>
                    <tr>
                      <th style="color: var(--text-primary);">Ticket</th>
                      <th style="color: var(--text-primary);">Client</th>
                      <th style="color: var(--text-primary);">Status</th>
                      <th style="color: var(--text-primary);">Age</th>
                      <th style="color: var(--text-primary);">SLA Status</th>
                      <th style="color: var(--text-primary);">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($recentComplaints as $complaint)
                    <tr>
                      <td style="color: var(--text-secondary);">{{ $complaint->ticket_number }}</td>
                      <td style="color: var(--text-secondary);">{{ $complaint->client->client_name ?? 'N/A' }}</td>
                      <td>
                        <span class="badge bg-{{ $complaint->status === 'resolved' ? 'success' : ($complaint->status === 'closed' ? 'info' : 'warning') }}">
                          {{ ucfirst($complaint->status) }}
                        </span>
                      </td>
                      <td>
                        <span class="badge bg-{{ $complaint->isOverdue() ? 'danger' : 'success' }}">
                          {{ $complaint->hours_elapsed }}h
                        </span>
                      </td>
                      <td>
                        <span class="badge bg-{{ $complaint->isSlaBreached() ? 'danger' : 'success' }}">
                          {{ $complaint->isSlaBreached() ? 'Breached' : 'Within SLA' }}
                        </span>
                      </td>
                      <td>
                        <a href="{{ route('admin.complaints.show', $complaint) }}" class="btn btn-outline-info btn-sm">
                          <i data-feather="eye"></i>
                        </a>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          @endif

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
