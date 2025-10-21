@extends('layouts.admin')

@section('title', 'SLA Rule Details')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">SLA Rule Details: {{ ucfirst($slaRule->complaint_type) }}</h5>
          <div class="btn-group">
            <a href="{{ route('admin.sla.edit', $slaRule) }}" class="btn btn-warning btn-sm">
              <i data-feather="edit"></i> Edit
            </a>
            <form action="{{ route('admin.sla.toggle-status', $slaRule) }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-{{ $slaRule->status === 'active' ? 'warning' : 'success' }} btn-sm">
                <i data-feather="{{ $slaRule->status === 'active' ? 'pause' : 'play' }}"></i>
                {{ $slaRule->status === 'active' ? 'Deactivate' : 'Activate' }}
              </button>
            </form>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-muted">SLA Configuration</h6>
                <table class="table table-borderless">
                  <tr>
                    <td><strong>Complaint Type:</strong></td>
                    <td>
                      <span class="badge bg-info">{{ ucfirst($slaRule->complaint_type) }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Max Response Time:</strong></td>
                    <td>{{ $slaRule->max_response_time }} hours</td>
                  </tr>
                  <tr>
                    <td><strong>Max Resolution Time:</strong></td>
                    <td>{{ $slaRule->max_resolution_time }} hours</td>
                  </tr>
                  <tr>
                    <td><strong>Escalation Level:</strong></td>
                    <td>Level {{ $slaRule->escalation_level }}</td>
                  </tr>
                  <tr>
                    <td><strong>Status:</strong></td>
                    <td>
                      <span class="badge bg-{{ $slaRule->status === 'active' ? 'success' : 'danger' }}">
                        {{ ucfirst($slaRule->status) }}
                      </span>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-muted">Notification & Timeline</h6>
                <table class="table table-borderless">
                  <tr>
                    <td><strong>Notify To:</strong></td>
                    <td>{{ $slaRule->notifyTo->username ?? 'Not Set' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Created:</strong></td>
                    <td>{{ $slaRule->created_at->format('M d, Y H:i') }}</td>
                  </tr>
                  <tr>
                    <td><strong>Last Updated:</strong></td>
                    <td>{{ $slaRule->updated_at->format('M d, Y H:i') }}</td>
                  </tr>
                  <tr>
                    <td><strong>Total Complaints:</strong></td>
                    <td>
                      <span class="badge bg-primary">{{ $slaRule->complaints->count() }} complaints</span>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>

          @if($slaRule->description)
          <div class="row">
            <div class="col-12">
              <h6 class="text-muted">Description</h6>
              <div class="card">
                <div class="card-body">
                  <p>{{ $slaRule->description }}</p>
                </div>
              </div>
            </div>
          </div>
          @endif

          <!-- Performance Metrics -->
          @if(isset($metrics))
          <div class="row mt-4">
            <div class="col-12">
              <h6 class="text-muted">Performance Metrics</h6>
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
              <h6 class="text-muted">Recent Complaints</h6>
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

          <div class="row mt-4">
            <div class="col-12">
              <div class="d-flex justify-content-between">
                <a href="{{ route('admin.sla.index') }}" class="btn btn-secondary">
                  <i data-feather="arrow-left"></i> Back to SLA Rules
                </a>
                <div class="btn-group">
                  <a href="{{ route('admin.sla.edit', $slaRule) }}" class="btn btn-warning">
                    <i data-feather="edit"></i> Edit SLA Rule
                  </a>
                  <form action="{{ route('admin.sla.destroy', $slaRule) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this SLA rule?')">
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
