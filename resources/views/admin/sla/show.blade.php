@extends('layouts.sidebar')

@section('title', 'SLA Rule Details — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">SLA Rule Details</h2>
      <p class="text-light">View SLA rule: {{ ucfirst($sla->complaint_type) }}</p>
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
  <div class="card-header">
    <h5 class="card-title mb-0 text-white">
      <i data-feather="clock" class="me-2"></i>SLA Rule Information
    </h5>
  </div>
  <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-white fw-bold">SLA Configuration</h6>
                <table class="table table-borderless">
                  <tr>
                    <td class="text-white"><strong>Complaint Type:</strong></td>
                    <td>
                      <span class="badge bg-info">{{ ucfirst($sla->complaint_type) }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Max Response Time:</strong></td>
                    <td class="text-white">{{ $sla->max_response_time }} hours</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Max Resolution Time:</strong></td>
                    <td class="text-white">{{ $sla->max_resolution_time }} hours</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Escalation Level:</strong></td>
                    <td class="text-white">Level {{ $sla->escalation_level }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Status:</strong></td>
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
                <h6 class="text-white fw-bold">Notification & Timeline</h6>
                <table class="table table-borderless">
                  <tr>
                    <td class="text-white"><strong>Notify To:</strong></td>
                    <td class="text-white">{{ $sla->notifyTo->name ?? 'Not Set' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Created:</strong></td>
                    <td class="text-white">{{ $sla->created_at->format('M d, Y H:i') }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Last Updated:</strong></td>
                    <td class="text-white">{{ $sla->updated_at->format('M d, Y H:i') }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Total Complaints:</strong></td>
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
              <h6 class="text-white fw-bold">Description</h6>
              <div class="card">
                <div class="card-body">
                  <p class="text-white">{{ $sla->description }}</p>
                </div>
              </div>
            </div>
          </div>
          @endif

          <!-- Performance Metrics -->
          @if(isset($metrics))
          <div class="row mt-4">
            <div class="col-12">
              <h6 class="text-white fw-bold">Performance Metrics</h6>
              <div class="row">
                <div class="col-md-3">
                  <div class="card bg-primary text-white">
                    <div class="card-body">
                      <h5 class="card-title">{{ $metrics['total_complaints'] ?? 0 }}</h5>
                      <p class="card-text">Total Complaints</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card bg-success text-white">
                    <div class="card-body">
                      <h5 class="card-title">{{ $metrics['within_sla'] ?? 0 }}</h5>
                      <p class="card-text">Within SLA</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card bg-danger text-white">
                    <div class="card-body">
                      <h5 class="card-title">{{ $metrics['breached_sla'] ?? 0 }}</h5>
                      <p class="card-text">SLA Breached</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card bg-info text-white">
                    <div class="card-body">
                      <h5 class="card-title">{{ $metrics['compliance_rate'] ?? 0 }}%</h5>
                      <p class="card-text">Compliance Rate</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @endif

          <!-- Recent Complaints -->
          @if(isset($recentComplaints) && $recentComplaints->count() > 0)
          <div class="row mt-4">
            <div class="col-12">
              <h6 class="text-white fw-bold">Recent Complaints</h6>
              <div class="table-responsive">
                <table class="table table-sm table-striped">
                  <thead>
                    <tr>
                      <th>Ticket</th>
                      <th>Client</th>
                      <th>Status</th>
                      <th>Age</th>
                      <th>SLA Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($recentComplaints as $complaint)
                    <tr>
                      <td>{{ $complaint->ticket_number }}</td>
                      <td>{{ $complaint->client->client_name ?? 'N/A' }}</td>
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
