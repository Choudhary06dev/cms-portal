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
    <button id="addClientBtn" class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#clientCreateModal">
      <i data-feather="plus" class="me-2"></i>Add Client
    </button>
  </div>
</div>

<!-- FILTERS -->
<div class="card-glass mb-4">
  <form method="GET" action="{{ route('admin.clients.index') }}">
  <div class="row g-3">
    <div class="col-md-3">
        <input type="text" class="form-control" name="search" placeholder="Search clients..." 
               value="{{ request('search') }}">
    </div>
    <div class="col-md-2">
        <select class="form-select" name="status">
        <option value="">All Status</option>
          <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
      </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" name="city">
          <option value="">All Cities</option>
          <option value="karachi" {{ request('city') == 'karachi' ? 'selected' : '' }}>Karachi</option>
          <option value="lahore" {{ request('city') == 'lahore' ? 'selected' : '' }}>Lahore</option>
          <option value="islamabad" {{ request('city') == 'islamabad' ? 'selected' : '' }}>Islamabad</option>
          <option value="other" {{ request('city') == 'other' ? 'selected' : '' }}>Other</option>
      </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" name="state">
          <option value="">All States</option>
          <option value="sindh" {{ request('state') == 'sindh' ? 'selected' : '' }}>Sindh</option>
          <option value="punjab" {{ request('state') == 'punjab' ? 'selected' : '' }}>Punjab</option>
          <option value="kpk" {{ request('state') == 'kpk' ? 'selected' : '' }}>KPK</option>
          <option value="balochistan" {{ request('state') == 'balochistan' ? 'selected' : '' }}>Balochistan</option>
      </select>
    </div>
    <div class="col-md-3">
      <div class="d-flex gap-2">
          <button type="submit" class="btn btn-outline-light btn-sm">
          <i data-feather="filter" class="me-1"></i>Apply
        </button>
          <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary btn-sm">
          <i data-feather="x" class="me-1"></i>Clear
          </a>
          <button type="button" class="btn btn-outline-primary btn-sm">
          <i data-feather="download" class="me-1"></i>Export
        </button>
      </div>
    </div>
  </div>
  </form>
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
          <th >Email</th>
          <th >Phone</th>
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
                {{ substr($client->client_name, 0, 1) }}
              </div>
              <div>
                <div style="color: #ffffff !important; font-weight: 600;">{{ $client->client_name }}</div>
                <div style="color: #94a3b8 !important; font-size: 0.8rem;">{{ $client->contact_person ?? 'No Contact Person' }}</div>
              </div>
            </div>
          </td>
          <td>
            <span class="badge bg-primary">
              Client
            </span>
          </td>
          <td>{{ $client->email ?? 'N/A' }}</td>
          <td>{{ $client->phone ?? 'N/A' }}</td>
          <td >{{ $client->city ?? 'N/A' }}</td>
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
          <td colspan="10" class="text-center py-4" >
            <i data-feather="briefcase" class="feather-lg mb-2"></i>
            <div>No clients found</div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <!-- PAGINATION -->
  <div class="d-flex justify-content-center mt-3">
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
  
  /* Pagination styles are now centralized in components/pagination.blade.php */
  
  .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
  .status-active { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
  .status-inactive { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
  .status-suspended { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
</style>
@endpush

<!-- Client Modal -->
<div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clientModalLabel">Client Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="clientModalBody">
                <!-- Client details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" id="editClientBtn" style="display: none;">
                    <i class="fas fa-edit"></i> Edit Client
                </button>
                <button type="button" class="btn btn-danger" id="deleteClientBtn" style="display: none;">
                    <i class="fas fa-trash"></i> Delete Client
                </button>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
  feather.replace();

  let currentClientId = null;

  // Client Functions
  function viewClient(clientId) {
    currentClientId = clientId;
    
    // Show only view button
    document.getElementById('editClientBtn').style.display = 'none';
    document.getElementById('deleteClientBtn').style.display = 'none';
    
    // Show loading state
    const modalBody = document.getElementById('clientModalBody');
    modalBody.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Fetch client data
    fetch(`/admin/clients/${clientId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        credentials: 'same-origin'
    })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                const client = data.client;
                const stats = data.stats;
                
                modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Client Information</h6>
                            <p><strong>Name:</strong> ${client.client_name || 'N/A'}</p>
                            <p><strong>Contact Person:</strong> ${client.contact_person || 'N/A'}</p>
                            <p><strong>Type:</strong> 
                                <span class="badge bg-primary">
                                    Client
                                </span>
                            </p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-${client.status === 'active' ? 'success' : 'danger'}">
                                    ${client.status ? client.status.charAt(0).toUpperCase() + client.status.slice(1) : 'Inactive'}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Contact Information</h6>
                            <p><strong>Email:</strong> ${client.email || 'N/A'}</p>
                            <p><strong>Phone:</strong> ${client.phone || 'N/A'}</p>
                            <p><strong>City:</strong> ${client.city || 'N/A'}</p>
                            <p><strong>State:</strong> ${client.state || 'N/A'}</p>
                            <p><strong>Pincode:</strong> ${client.pincode || 'N/A'}</p>
                            <p><strong>Address:</strong> ${client.address || 'N/A'}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Complaint Statistics</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h5>${stats.total_complaints}</h5>
                                            <small>Total Complaints</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h5>${stats.pending_complaints}</h5>
                                            <small>Pending</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h5>${stats.resolved_complaints}</h5>
                                            <small>Resolved</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body text-center">
                                            <h5>${stats.overdue_complaints}</h5>
                                            <small>Overdue</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                document.getElementById('clientModalLabel').textContent = 'Client Details';
                new bootstrap.Modal(document.getElementById('clientModal')).show();
            } else {
                modalBody.innerHTML = '<div class="alert alert-danger">Error: ' + (data.message || 'Unknown error occurred') + '</div>';
                new bootstrap.Modal(document.getElementById('clientModal')).show();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = '<div class="alert alert-danger">Error loading client details: ' + error.message + '</div>';
            new bootstrap.Modal(document.getElementById('clientModal')).show();
        });
  }

  function editClient(clientId) {
    // Redirect to edit page
    window.location.href = `/admin/clients/${clientId}/edit`;
  }

  function deleteClient(clientId) {
    if (confirm('Are you sure you want to delete this client? This action cannot be undone.')) {
      // Create a form and submit it
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/admin/clients/${clientId}`;
      
      // Add CSRF token
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      const csrfInput = document.createElement('input');
      csrfInput.type = 'hidden';
      csrfInput.name = '_token';
      csrfInput.value = csrfToken;
      form.appendChild(csrfInput);
      
      // Add DELETE method
      const methodInput = document.createElement('input');
      methodInput.type = 'hidden';
      methodInput.name = '_method';
      methodInput.value = 'DELETE';
      form.appendChild(methodInput);
      
      // Submit the form
      document.body.appendChild(form);
      form.submit();
    }
  }
</script>
@endpush

<!-- Client Create Modal -->
<div class="modal fade" id="clientCreateModal" tabindex="-1" aria-labelledby="clientCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="clientCreateModalLabel">Create New Client</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="clientCreateModalBody">
        <form id="clientCreateForm" method="POST" autocomplete="off" novalidate>
          @csrf
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="client_name_modal" class="form-label">Client Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="client_name_modal" name="client_name" required>
                <div class="invalid-feedback"></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="contact_person_modal" class="form-label">Contact Person</label>
                <input type="text" class="form-control" id="contact_person_modal" name="contact_person">
                <div class="invalid-feedback"></div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="phone_modal" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone_modal" name="phone">
                <div class="invalid-feedback"></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="email_modal" class="form-label">Email</label>
                <input type="email" class="form-control" id="email_modal" name="email">
                <div class="invalid-feedback"></div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="address_modal" class="form-label">Address</label>
                <textarea class="form-control" id="address_modal" name="address" rows="3"></textarea>
                <div class="invalid-feedback"></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="city_modal" class="form-label">City</label>
                <input type="text" class="form-control" id="city_modal" name="city">
                <div class="invalid-feedback"></div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="mb-3">
                <label for="state_modal" class="form-label">State <span class="text-danger">*</span></label>
                <select class="form-select" id="state_modal" name="state" required>
                  <option value="">Select State</option>
                  <option value="sindh">Sindh</option>
                  <option value="punjab">Punjab</option>
                  <option value="kpk">KPK</option>
                  <option value="balochistan">Balochistan</option>
                  <option value="other">Other</option>
                </select>
                <div class="invalid-feedback"></div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label for="pincode_modal" class="form-label">Pincode <small class="text-muted">(4 digits only)</small></label>
                <input type="tel" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" class="form-control" 
                       id="pincode_modal" name="pincode" placeholder="1234" 
                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,4)">
                <div class="invalid-feedback"></div>
                <div class="form-text text-muted">Enter exactly 4 digits</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label for="status_modal" class="form-label">Status</label>
                <select class="form-select" id="status_modal" name="status">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
                <div class="invalid-feedback"></div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="clientCreateSubmit">
          <span id="clientCreateText">Create Client</span>
          <span id="clientCreateSpinner" class="spinner-border spinner-border-sm ms-2" style="display:none"></span>
        </button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const addBtn = document.getElementById('addClientBtn');
  const modal = new bootstrap.Modal(document.getElementById('clientCreateModal'));
  const form = document.getElementById('clientCreateForm');
  const submitBtn = document.getElementById('clientCreateSubmit');
  const spinner = document.getElementById('clientCreateSpinner');

  addBtn.addEventListener('click', function() {
    clearForm(form);
    modal.show();
  });

  submitBtn.addEventListener('click', function() {
    clearValidationErrors(form);
    submitBtn.disabled = true;
    spinner.style.display = 'inline-block';

    const fd = new FormData(form);

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    fetch("{{ route('admin.clients.store') }}", {
      method: 'POST',
      body: fd,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      credentials: 'same-origin'
    })
    .then(async res => {
      const text = await res.text();
      try {
        return { status: res.status, data: JSON.parse(text) };
      } catch (err) {
        throw new Error('Unexpected response: ' + text);
      }
    })
    .then(result => {
      if (result.data.success) {
        location.reload();
      } else {
        if (result.data.errors) {
          showValidationErrors(form, result.data.errors);
        } else {
          alert('Error: ' + (result.data.message || 'Unknown error'));
        }
      }
    })
    .catch(err => {
      console.error('Create client error:', err);
      alert('Error creating client: ' + err.message);
    })
    .finally(() => {
      submitBtn.disabled = false;
      spinner.style.display = 'none';
    });
  });

  function clearForm(f) {
    f.reset();
    clearValidationErrors(f);
  }

  // reuse validation helpers from other views if available
  function clearValidationErrors(form) {
    const inputs = form.querySelectorAll('.is-invalid');
    inputs.forEach(i => i.classList.remove('is-invalid'));
    const feedbacks = form.querySelectorAll('.invalid-feedback');
    feedbacks.forEach(f => f.textContent = '');
  }

  function showValidationErrors(form, errors) {
    Object.keys(errors).forEach(field => {
      const input = form.querySelector(`[name="${field}"]`);
      const messages = errors[field];
      if (input) {
        input.classList.add('is-invalid');
        const fb = input.closest('.mb-3')?.querySelector('.invalid-feedback') || form.querySelector('.invalid-feedback');
        if (fb) fb.textContent = messages.join(' ');
      }
    });
  }
});
</script>
@endpush