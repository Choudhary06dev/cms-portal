@extends('layouts.sidebar')

@section('title', 'Complainant Details — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Complainant Details</h2>
      <p class="text-light">View and manage Complainant information</p>
    </div>
  </div>
</div>

<!-- Complainant INFORMATION -->
<div class="card-glass">
  <div class="card-header">
    <h5 class="card-title mb-0 text-white">
      <i data-feather="user" class="me-2"></i>Complainant Details: {{ $client->client_name }}
    </h5>
  </div>
  <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-white fw-bold">Basic Information</h6>
                <table class="table table-borderless">
                  <tr>
                    <td class="text-white"><strong>Complainant Name:</strong></td>
                    <td class="text-white">{{ $client->client_name }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Contact Person:</strong></td>
                    <td class="text-white">{{ $client->contact_person ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Phone:</strong></td>
                    <td class="text-white">{{ $client->phone ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Email:</strong></td>
                    <td class="text-white">{{ $client->email ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Status:</strong></td>
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
                <h6 class="text-white fw-bold">Location Information</h6>
                <table class="table table-borderless">
                  <tr>
                    <td class="text-white"><strong>Address:</strong></td>
                    <td class="text-white">{{ $client->address ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>City:</strong></td>
                    <td class="text-white">{{ $client->city ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Sector:</strong></td>
                    <td class="text-white">{{ $client->sector ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Total Complaints:</strong></td>
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
                      <td>{{ ucfirst($complaint->category) }}</td>
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
                  <i data-feather="arrow-left"></i> Back to Complainant
                </a>
               
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
  });
</script>
@endpush
