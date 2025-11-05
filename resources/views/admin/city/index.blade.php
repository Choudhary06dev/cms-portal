@extends('layouts.sidebar')

@section('title', 'Cities — CMS Admin')

@section('content')
<div class="container-narrow">
<div class="mb-4 d-flex justify-content-between align-items-center">
  <div>
    <h2 class="text-white mb-1">Cities</h2>
    <p class="text-light mb-0">Manage cities for employee selection</p>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif
@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif
@if($errors->any())
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<div class="card-glass mb-3">
  <div class="card-header">
    <h5 class="card-title mb-0 text-white"><i data-feather="plus" class="me-2"></i>Add City</h5>
  </div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.city.store') }}" class="d-flex flex-wrap align-items-end gap-2">
      @csrf
      <div style="min-width: 220px; flex: 0 0 260px;">
        <label class="form-label small mb-1" style="color: #000000 !important; font-weight: 500;">Name</label>
        <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="City name" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div style="min-width: 200px; flex: 0 0 240px;">
        <label class="form-label small mb-1" style="color: #000000 !important; font-weight: 500;">Province</label>
        <select name="province" class="form-select @error('province') is-invalid @enderror">
          <option value="">Select Province</option>
                    <option value="Punjab" {{ old('province')==='Punjab'?'selected':'' }}>Punjab</option>
          <option value="Sindh" {{ old('province')==='Sindh'?'selected':'' }}>Sindh</option>
                    <option value="Balochistan" {{ old('province')==='Balochistan'?'selected':'' }}>Balochistan</option>
          <option value="KPK" {{ old('province')==='KPK'?'selected':'' }}>KPK</option>
          <option value="Federal" {{ old('province')==='Federal'?'selected':'' }}>Federal</option>
          <option value="Azad Kashmir" {{ old('province')==='Azad Kashmir'?'selected':'' }}>Azad Kashmir</option>
        </select>
        @error('province')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div style="min-width: 260px; flex: 1 1 380px;">
        <label class="form-label small mb-1" style="color: #000000 !important; font-weight: 500;">Description</label>
        <input type="text" name="description" value="{{ old('description') }}" class="form-control @error('description') is-invalid @enderror" placeholder="Short description (optional)">
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div style="min-width: 160px; flex: 0 0 180px;">
        <label class="form-label small mb-1" style="color: #000000 !important; font-weight: 500;">Status</label>
        <select name="status" class="form-select">
          <option value="active" {{ old('status','active')==='active'?'selected':'' }}>Active</option>
          <option value="inactive" {{ old('status')==='inactive'?'selected':'' }}>Inactive</option>
        </select>
      </div>
      <div class="d-grid" style="flex: 0 0 140px;">
        <button class="btn btn-accent" type="submit" style="width: 100%;">Add</button>
      </div>
    </form>
  </div>
@push('styles')
<style>
  .container-narrow { max-width: 960px; margin: 0 auto; }
  .table.compact-table th, .table.compact-table td { padding: .55rem .75rem; font-size: .95rem; }
  .card-glass { padding: 16px; }
  .form-control, .form-select { padding: .48rem .7rem; font-size: .96rem; }
  .btn.btn-sm { padding: .32rem .6rem; font-size: .85rem; }
  .card-title { font-size: 1.05rem; }
</style>
@endpush

</div>

<div class="card-glass">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0 text-white"><i data-feather="list" class="me-2"></i>Cities</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table  align-middle compact-table">
        <thead>
          <tr>
            <th style="width:70px">#</th>
            <th>Name</th>
            <th>Province</th>
            <th>Description</th>
            <th style="width:140px">Status</th>
            <th style="width:180px">Actions</th>
          </tr>
        </thead>
        <tbody>
        @forelse($cities as $city)
          <tr>
            <td>{{ $city->id }}</td>
            <td>{{ $city->name }}</td>
            <td>{{ $city->province ?: '-' }}</td>
            <td>{{ $city->description ? Str::limit($city->description, 80) : '-' }}</td>
            <td>
              <span class="badge {{ $city->status==='active' ? 'bg-success' : 'bg-secondary' }}" style="color: #ffffff !important;">{{ ucfirst($city->status) }}</span>
            </td>
            <td>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#editCityModal" 
                        data-id="{{ $city->id }}" data-name="{{ $city->name }}" data-province="{{ $city->province }}" data-status="{{ $city->status }}" data-description="{{ $city->description }}">
                  Edit
                </button>
                <form action="{{ route('admin.city.destroy', $city) }}" method="POST" class="city-delete-form" onsubmit="return confirm('Delete this city?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="text-center text-muted">No cities yet.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      {{ $cities->links() }}
    </div>
  </div>
</div>

<!-- Edit City Modal -->
<div class="modal fade" id="editCityModal" tabindex="-1" aria-labelledby="editCityModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title" id="editCityModalLabel">Edit City</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editCityForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" id="editCityName" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Province</label>
            <select name="province" id="editCityProvince" class="form-select">
              <option value="">Select Province</option>
              <option value="Sindh">Sindh</option>
              <option value="Punjab">Punjab</option>
              <option value="KPK">KPK</option>
              <option value="Balochistan">Balochistan</option>
              <option value="Federal">Federal</option>
              <option value="Azad Kashmir">Azad Kashmir</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" id="editCityDescription" class="form-control" rows="2" placeholder="Optional"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" id="editCityStatus" class="form-select">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-accent">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
  
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // AJAX delete to remove only from table (not DB hard delete)
  document.querySelectorAll('form.city-delete-form').forEach(function(form){
    form.addEventListener('submit', function(e){
      e.preventDefault();
      const row = form.closest('tr');
      const url = form.action;
      const token = form.querySelector('input[name="_token"]').value;
      const method = form.querySelector('input[name="_method"]').value || 'DELETE';

      const formData = new FormData();
      formData.append('_method', method);
      formData.append('_token', token);

      fetch(url, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: formData
      })
      .then(res => res.ok ? res.json() : Promise.reject())
      .then(() => {
        if (row) {
          row.style.opacity = '0.4';
          row.style.transition = 'opacity .2s ease';
          setTimeout(() => { row.remove(); }, 180);
        }
      })
      .catch(() => {
        // Fallback: submit normally
        form.submit();
      });
    });
  });

  const modalEl = document.getElementById('editCityModal');
  if (!modalEl) return;
  modalEl.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    const name = button.getAttribute('data-name');
    const province = button.getAttribute('data-province') || '';
    const status = button.getAttribute('data-status');
    const description = button.getAttribute('data-description') || '';

    const form = document.getElementById('editCityForm');
    const nameInput = document.getElementById('editCityName');
    const provinceSelect = document.getElementById('editCityProvince');
    const statusSelect = document.getElementById('editCityStatus');

    if (form && id) {
      form.action = `${window.location.origin}/admin/city/${id}`;
    }
    if (nameInput) nameInput.value = name || '';
    if (provinceSelect) provinceSelect.value = province || '';
    const descInput = document.getElementById('editCityDescription');
    if (descInput) descInput.value = description;
    if (statusSelect) statusSelect.value = status || 'active';
  });
});
</script>
@endpush
@endsection
