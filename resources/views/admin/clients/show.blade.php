@extends('layouts.sidebar')

@section('title', 'Client Details')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Client Details: {{ $client->client_name }}</h5>
          <div class="btn-group">
            <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-warning btn-sm">
              <i data-feather="edit"></i> Edit
            </a>
            <form action="{{ route('admin.clients.toggle-status', $client) }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-{{ $client->status === 'active' ? 'warning' : 'success' }} btn-sm">
                <i data-feather="{{ $client->status === 'active' ? 'user-x' : 'user-check' }}"></i>
                {{ $client->status === 'active' ? 'Deactivate' : 'Activate' }}
              </button>
            </form>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-muted">Basic Information</h6>
                <table class="table table-borderless">
                  <tr>
                    <td><strong>Client Name:</strong></td>
                    <td>{{ $client->client_name }}</td>
                  </tr>
                  <tr>
                    <td><strong>Contact Person:</strong></td>
                    <td>{{ $client->contact_person ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Phone:</strong></td>
                    <td>{{ $client->phone ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Email:</strong></td>
                    <td>{{ $client->email ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Status:</strong></td>
                    <td>
                      <span class="badge bg-{{ $client->status === 'active' ? 'success' : 'danger' }}">
                        {{ ucfirst($client->status) }}
                      </span>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-muted">Location Information</h6>
                <table class="table table-borderless">
                  <tr>
                    <td><strong>Address:</strong></td>
                    <td>{{ $client->address ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td><strong>City:</strong></td>
                    <td>{{ $client->city ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Total Complaints:</strong></td>
                    <td>
                      <span class="badge bg-primary">{{ $client->complaints->count() }} complaints</span>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Created:</strong></td>
                    <td>{{ $client->created_at->format('M d, Y H:i') }}</td>
                  </tr>
                  <tr>
                    <td><strong>Last Updated:</strong></td>
                    <td>{{ $client->updated_at->format('M d, Y H:i') }}</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>

          <!-- Statistics -->
          @if(isset($stats))
          <div class="row">
            <div class="col-12">
              <h6 class="text-muted">Client Statistics</h6>
              <div class="row">
                <div class="col-md-3">
                  <div class="card bg-primary text-white">
                    <div class="card-body">
                      <h5 class="card-title">{{ $stats['total_complaints'] ?? 0 }}</h5>
                      <p class="card-text">Total Complaints</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card bg-success text-white">
                    <div class="card-body">
                      <h5 class="card-title">{{ $stats['resolved_complaints'] ?? 0 }}</h5>
                      <p class="card-text">Resolved</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card bg-warning text-white">
                    <div class="card-title">{{ $stats['pending_complaints'] ?? 0 }}</h5>
                      <p class="card-text">Pending</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card bg-danger text-white">
                    <div class="card-body">
                      <h5 class="card-title">{{ $stats['overdue_complaints'] ?? 0 }}</h5>
                      <p class="card-text">Overdue</p>
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
                      <th>Type</th>
                      <th>Status</th>
                      <th>Created</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($recentComplaints as $complaint)
                    <tr>
                      <td>{{ $complaint->ticket_number }}</td>
                      <td>{{ ucfirst($complaint->complaint_type) }}</td>
                      <td>
                        <span class="badge bg-{{ $complaint->status === 'resolved' ? 'success' : ($complaint->status === 'closed' ? 'info' : 'warning') }}">
                          {{ ucfirst($complaint->status) }}
                        </span>
                      </td>
                      <td>{{ $complaint->created_at->format('M d, Y') }}</td>
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
                <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">
                  <i data-feather="arrow-left"></i> Back to Clients
                </a>
                <div class="btn-group">
                  <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-warning">
                    <i data-feather="edit"></i> Edit Client
                  </a>
                  <form action="{{ route('admin.clients.destroy', $client) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this client?')">
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
