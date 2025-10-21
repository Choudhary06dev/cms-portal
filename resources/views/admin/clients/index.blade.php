@extends('layouts.sidebar')

@section('title', 'Clients Management — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2" >Clients Management</h2>
      <p class="text-light" >Manage client information and relationships</p>
    </div>
    <a href="{{ route('admin.clients.create') }}" class="btn btn-accent">
      <i data-feather="plus" class="me-2"></i>Add Client
    </a>
  </div>
</div>

<!-- FILTERS -->
<div class="card-glass mb-4">
  <div class="row g-3">
    <div class="col-md-3">
      <input type="text" class="form-control" placeholder="Search clients..." 
>
    </div>
    <div class="col-md-2">
      <select class="form-select" 
>
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
        <option value="suspended">Suspended</option>
      </select>
    </div>
    <div class="col-md-2">
      <select class="form-select" 
>
        <option value="">All Types</option>
        <option value="individual">Individual</option>
        <option value="company">Company</option>
        <option value="government">Government</option>
      </select>
    </div>
    <div class="col-md-2">
      <select class="form-select" 
>
        <option value="">All Locations</option>
        <option value="karachi">Karachi</option>
        <option value="lahore">Lahore</option>
        <option value="islamabad">Islamabad</option>
        <option value="other">Other</option>
      </select>
    </div>
    <div class="col-md-3">
      <div class="d-flex gap-2">
        <button class="btn btn-outline-light btn-sm">
          <i data-feather="filter" class="me-1"></i>Apply
        </button>
        <button class="btn btn-outline-secondary btn-sm">
          <i data-feather="x" class="me-1"></i>Clear
        </button>
        <button class="btn btn-outline-primary btn-sm">
          <i data-feather="download" class="me-1"></i>Export
        </button>
      </div>
    </div>
  </div>
</div>

<!-- CLIENTS TABLE -->
<div class="card-glass">
  <div class="table-responsive">
        <table class="table table-dark">
      <thead>
        <tr>
          <th >#</th>
          <th >Client</th>
          <th >Type</th>
          <th >Contact</th>
          <th >Location</th>
          <th >Status</th>
          <th >Complaints</th>
          <th >Created</th>
          <th >Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($clients as $client)
        <tr>
          <td >{{ $client->id }}</td>
          <td>
            <div class="d-flex align-items-center">
              <div class="avatar-sm me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold;">
                {{ substr($client->name, 0, 1) }}
              </div>
              <div>
                <div style="color: #ffffff !important; font-weight: 600;">{{ $client->name }}</div>
                <div style="color: #94a3b8 !important; font-size: 0.8rem;">{{ $client->company ?? 'Individual' }}</div>
              </div>
            </div>
          </td>
          <td>
            <span class="type-badge type-{{ strtolower($client->type) }}">
              {{ ucfirst($client->type) }}
            </span>
          </td>
          <td>
            <div >{{ $client->email ?? 'N/A' }}</div>
            <div style="color: #94a3b8 !important; font-size: 0.8rem;">{{ $client->phone ?? 'N/A' }}</div>
          </td>
          <td >{{ $client->location ?? 'N/A' }}</td>
          <td>
            <span class="status-badge status-{{ strtolower($client->status) }}">
              {{ ucfirst($client->status) }}
            </span>
          </td>
          <td >{{ $client->complaints_count ?? 0 }}</td>
          <td >{{ $client->created_at->format('M d, Y') }}</td>
          <td>
            <div class="btn-group" role="group">
              <button class="btn btn-outline-info btn-sm" onclick="viewClient({{ $client->id }})" title="View Details">
                <i data-feather="eye"></i>
              </button>
              <button class="btn btn-outline-warning btn-sm" onclick="editClient({{ $client->id }})" title="Edit">
                <i data-feather="edit"></i>
              </button>
              <button class="btn btn-outline-danger btn-sm" onclick="deleteClient({{ $client->id }})" title="Delete">
                <i data-feather="trash-2"></i>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9" class="text-center py-4" >
            <i data-feather="briefcase" class="feather-lg mb-2"></i>
            <div>No clients found</div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <!-- PAGINATION -->
  <div class="d-flex justify-content-between align-items-center mt-3">
    <div >
      Showing {{ $clients->firstItem() ?? 0 }} to {{ $clients->lastItem() ?? 0 }} of {{ $clients->total() }} clients
    </div>
    <div>
      {{ $clients->links() }}
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .type-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  .type-individual { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
  .type-company { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
  .type-government { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
  
  .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  .status-active { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
  .status-inactive { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
  .status-suspended { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
</style>
@endpush

@push('scripts')
<script>
  feather.replace();

  // Client Functions
  function viewClient(clientId) {
    alert('View client details functionality coming soon!');
  }

  function editClient(clientId) {
    alert('Edit client functionality coming soon!');
  }

  function deleteClient(clientId) {
    if (confirm('Are you sure you want to delete this client?')) {
      alert('Delete client functionality coming soon!');
    }
  }
</script>
@endpush