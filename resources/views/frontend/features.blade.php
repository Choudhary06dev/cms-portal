@extends('frontend.layouts.app')

@section('title', 'Features')

@section('content')
  <div class="p-5 bg-white rounded shadow-sm mb-4">
    <h1 class="mb-2">Features</h1>
    <p class="text-muted mb-0">Everything you need to run customer operations smoothly.</p>
  </div>

  <div class="row g-3">
    <div class="col-md-6">
      <div class="p-4 bg-white rounded shadow-sm h-100">
        <h5 class="mb-2">Complaint Management</h5>
        <ul class="mb-0 text-muted">
          <li>Log, assign, and track complaints end-to-end</li>
          <li>SLA tracking with statuses and timelines</li>
          <li>Printable slips and activity logs</li>
        </ul>
      </div>
    </div>
    <div class="col-md-6">
      <div class="p-4 bg-white rounded shadow-sm h-100">
        <h5 class="mb-2">Approvals & Spares</h5>
        <ul class="mb-0 text-muted">
          <li>Spare approval performas with items and cost</li>
          <li>Stock-aware approvals (no over-issuance)</li>
          <li>CSV export for records</li>
        </ul>
      </div>
    </div>
    <div class="col-md-6">
      <div class="p-4 bg-white rounded shadow-sm h-100">
        <h5 class="mb-2">Employees</h5>
        <ul class="mb-0 text-muted">
          <li>Employee profiles and status</li>
          <li>Leaves tracking and approvals</li>
          <li>Performance metrics</li>
        </ul>
      </div>
    </div>
    <div class="col-md-6">
      <div class="p-4 bg-white rounded shadow-sm h-100">
        <h5 class="mb-2">Reports</h5>
        <ul class="mb-0 text-muted">
          <li>Complaints, Employees, Spares, SLA</li>
          <li>Printable reports</li>
          <li>Summary metrics</li>
        </ul>
      </div>
    </div>
  </div>
@endsection


