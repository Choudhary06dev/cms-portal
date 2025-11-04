@extends('layouts.sidebar')

@section('title', 'Complaint Titles — CMS Admin')

@section('content')
<div class="container-narrow">
<div class="mb-4 d-flex justify-content-between align-items-center">
  <div>
    <h2 class="text-white mb-1">Complaint Titles</h2>
    <p class="text-light mb-0">Manage complaint titles by category</p>
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

<!-- Filters -->
<div class="card-glass mb-3" style="display: inline-block; width: fit-content;">
  <form method="GET" action="{{ route('admin.complaint-titles.index') }}" class="d-flex flex-wrap align-items-end gap-2">
    <div style="min-width: 200px; flex: 0 0 240px;">
      <label class="form-label small text-muted mb-1">Category</label>
      <select name="category" class="form-select" onchange="this.form.submit()">
        <option value="">All Categories</option>
        @foreach($categories as $cat)
          <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
        @endforeach
      </select>
    </div>
    <div style="min-width: 240px; flex: 1 1 320px;">
      <label class="form-label small text-muted mb-1">Search</label>
      <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search titles or description...">
    </div>
    <div class="d-grid" style="flex: 0 0 120px;">
      <button class="btn btn-accent" type="submit" style="width: 100%;">Search</button>
    </div>
  </form>
</div>

<div class="card-glass mb-3">
  <div class="card-header">
    <h5 class="card-title mb-0 text-white"><i data-feather="plus" class="me-2"></i>Add Complaint Title</h5>
  </div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.complaint-titles.store') }}" class="d-flex flex-wrap align-items-end gap-2">
      @csrf
      <div style="min-width: 200px; flex: 0 0 240px;">
        <label class="form-label small text-muted mb-1">Category <span class="text-danger">*</span></label>
        <select name="category" class="form-select @error('category') is-invalid @enderror" required>
          <option value="">Select Category</option>
          @foreach($categories as $cat)
            <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
          @endforeach
        </select>
        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div style="min-width: 280px; flex: 1 1 400px;">
        <label class="form-label small text-muted mb-1">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" placeholder="Complaint title" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div style="min-width: 260px; flex: 1 1 380px;">
        <label class="form-label small text-muted mb-1">Description</label>
        <input type="text" name="description" value="{{ old('description') }}" class="form-control @error('description') is-invalid @enderror" placeholder="Description (optional)">
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="d-grid" style="flex: 0 0 140px;">
        <button class="btn btn-accent" type="submit" style="width: 100%;">Add</button>
      </div>
    </form>
  </div>
</div>

<div class="card-glass">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0 text-white"><i data-feather="list" class="me-2"></i>Complaint Titles</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-dark align-middle compact-table">
        <thead>
          <tr>
            <th style="width:70px">#</th>
            <th>Category</th>
            <th>Title</th>
            <th>Description</th>
            <th style="width:180px">Actions</th>
          </tr>
        </thead>
        <tbody>
        @forelse($complaintTitles as $title)
          <tr>
            <td>{{ $title->id }}</td>
            <td>
              <span class="badge bg-info">{{ ucfirst($title->category) }}</span>
            </td>
            <td><strong>{{ $title->title }}</strong></td>
            <td>{{ $title->description ? Str::limit($title->description, 80) : '-' }}</td>
            <td>
              <div class="btn-group" role="group">
                <button class="btn btn-outline-info btn-sm" onclick="editTitle({{ $title->id }}, '{{ $title->category }}', '{{ addslashes($title->title) }}', '{{ addslashes($title->description ?? '') }}')" title="Edit">
                  <i data-feather="edit"></i>
                </button>
                <form action="{{ route('admin.complaint-titles.destroy', $title) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this complaint title?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                    <i data-feather="trash-2"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center py-4">
              <i data-feather="alert-circle" class="feather-lg mb-2"></i>
              <div>No complaint titles found</div>
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-3">
      {{ $complaintTitles->links() }}
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Complaint Title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Category <span class="text-danger">*</span></label>
            <select name="category" id="edit_category" class="form-select" required>
              <option value="">Select Category</option>
              @foreach($categories as $cat)
                <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" id="edit_title" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <input type="text" name="description" id="edit_description" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-accent">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>

@push('styles')
<style>
  .container-narrow { max-width: 1200px; margin: 0 auto; }
  .table.compact-table th, .table.compact-table td { padding: .55rem .75rem; font-size: .95rem; }
  .card-glass { padding: 16px; }
  .form-control, .form-select { padding: .48rem .7rem; font-size: .96rem; }
  .btn.btn-sm { padding: .32rem .6rem; font-size: .85rem; }
  .card-title { font-size: 1.05rem; }
</style>
@endpush

@push('scripts')
<script>
  feather.replace();

  function editTitle(id, category, title, description) {
    document.getElementById('editForm').action = '{{ url("admin/complaint-titles") }}/' + id;
    document.getElementById('edit_category').value = category;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_description').value = description;
    
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
  }
</script>
@endpush
@endsection

