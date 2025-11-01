@extends('layouts.sidebar')

@section('title', 'Complainant Management — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2" >Complainant Management</h2>
      <p class="text-light" >Manage Complainant information and relationships</p>
    </div>
    <a href="{{ route('admin.clients.create') }}" class="btn btn-accent">
      <i data-feather="plus" class="me-2"></i>Add Complainant
    </a>
  </div>
</div>

<!-- FILTERS -->
<div class="card-glass mb-4">
  <form id="clientsFiltersForm" method="GET" action="{{ route('admin.clients.index') }}">
  <div class="row g-3">
    <div class="col-md-3">
        <input type="text" class="form-control" id="searchInput" name="search" placeholder="Search clients..." 
               value="{{ request('search') }}" oninput="handleClientsSearchInput()">
    </div>
    <div class="col-md-2">
        <select class="form-select" name="status" onchange="submitClientsFilters()">
        <option value="">All Status</option>
          <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
      </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" name="city" onchange="submitClientsFilters()">
          <option value="">All Cities</option>
          <option value="karachi" {{ request('city') == 'karachi' ? 'selected' : '' }}>Karachi</option>
          <option value="lahore" {{ request('city') == 'lahore' ? 'selected' : '' }}>Lahore</option>
          <option value="islamabad" {{ request('city') == 'islamabad' ? 'selected' : '' }}>Islamabad</option>
          <option value="other" {{ request('city') == 'other' ? 'selected' : '' }}>Other</option>
      </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" name="state" onchange="submitClientsFilters()">
          <option value="">All States</option>
          <option value="sindh" {{ request('state') == 'sindh' ? 'selected' : '' }}>Sindh</option>
          <option value="punjab" {{ request('state') == 'punjab' ? 'selected' : '' }}>Punjab</option>
          <option value="kpk" {{ request('state') == 'kpk' ? 'selected' : '' }}>KPK</option>
          <option value="balochistan" {{ request('state') == 'balochistan' ? 'selected' : '' }}>Balochistan</option>
      </select>
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
          <th >Complainant</th>
          <th >Email</th>
          <th >Phone</th>
          <th >City</th>
          <th >Sector</th>
          <th >Address</th>
          <th >Status</th>
          <th >Complaints</th>
          <th >Actions</th>
        </tr>
      </thead>
      <tbody id="clientsTableBody">
        @forelse($clients as $client)
        <tr>
          <td >{{ $client->id }}</td>
          <td>
            <div class="d-flex align-items-center">
              {{-- <div class="avatar-sm me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold;">
                {{ substr($client->client_name ?? 'N', 0, 1) }}
              </div> --}}
              <div>
                <div class="fw-bold">{{ $client->client_name ?? 'No Client Name' }}</div>
              </div>
            </div>
          </td>
          <td>{{ $client->email ?? 'N/A' }}</td>
          <td>{{ $client->phone ?? 'N/A' }}</td>
          <td>{{ $client->city ?? 'N/A' }}</td>
          <td>{{ $client->sector ?? 'N/A' }}</td>
          <td>{{ Str::limit($client->address ?? 'N/A', 40) }}</td>
          <td>
            <span class="status-badge status-{{ strtolower($client->status) }}">
              {{ ucfirst($client->status) }}
            </span>
          </td>
          <td >{{ $client->complaints_count ?? 0 }}</td>
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
            <div>No Complainant found</div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <!-- PAGINATION -->
  <div class="d-flex justify-content-center mt-3" id="clientsPagination">
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



@push('scripts')
<script>
  feather.replace();

  // Debounced search input handler
  let clientsSearchTimeout = null;
  function handleClientsSearchInput() {
    if (clientsSearchTimeout) clearTimeout(clientsSearchTimeout);
    clientsSearchTimeout = setTimeout(() => {
      loadClients();
    }, 500);
  }

  // Auto-submit for select filters
  function submitClientsFilters() {
    loadClients();
  }

  // Load Clients via AJAX
  function loadClients(url = null) {
    const form = document.getElementById('clientsFiltersForm');
    if (!form) return;
    
    const formData = new FormData(form);
    const params = new URLSearchParams();
    
    if (url) {
      const urlObj = new URL(url, window.location.origin);
      urlObj.searchParams.forEach((value, key) => {
        params.append(key, value);
      });
    } else {
      for (const [key, value] of formData.entries()) {
        if (value) {
          params.append(key, value);
        }
      }
    }

    const tbody = document.getElementById('clientsTableBody');
    const paginationContainer = document.getElementById('clientsPagination');
    
    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4"><div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
    }

    fetch(`{{ route('admin.clients.index') }}?${params.toString()}`, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'text/html',
      },
      credentials: 'same-origin'
    })
    .then(response => response.text())
    .then(html => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      
      const newTbody = doc.querySelector('#clientsTableBody');
      const newPagination = doc.querySelector('#clientsPagination');
      
      if (newTbody && tbody) {
        tbody.innerHTML = newTbody.innerHTML;
        feather.replace();
      }
      
      if (newPagination && paginationContainer) {
        paginationContainer.innerHTML = newPagination.innerHTML;
      }

      const newUrl = `{{ route('admin.clients.index') }}?${params.toString()}`;
      window.history.pushState({path: newUrl}, '', newUrl);
    })
    .catch(error => {
      console.error('Error loading clients:', error);
      if (tbody) {
        tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-danger">Error loading data. Please refresh the page.</td></tr>';
      }
    });
  }

  // Handle pagination clicks
  document.addEventListener('click', function(e) {
    const paginationLink = e.target.closest('#clientsPagination a');
    if (paginationLink && paginationLink.href && !paginationLink.href.includes('javascript:')) {
      e.preventDefault();
      loadClients(paginationLink.href);
    }
  });

  // Handle browser back/forward buttons
  window.addEventListener('popstate', function(e) {
    if (e.state && e.state.path) {
      loadClients(e.state.path);
    } else {
      loadClients();
    }
  });

  // Client Functions
  function viewClient(clientId) {
    // Redirect to show page
    window.location.href = `/admin/clients/${clientId}`;
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