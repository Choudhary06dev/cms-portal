@extends('frontend.layouts.app')

@section('title', 'Features')

@section('content')
  <style>
    :root { --blue:#0d6efd; --blue2:#3b82f6; --slate:#64748b; }
    .page-hero { background: linear-gradient(135deg, var(--blue), var(--blue2)); color:#fff; padding:60px 20px; border-radius:12px; margin-bottom:24px; text-align:center; }
    .page-hero h1 { font-weight: 800; letter-spacing: -0.02em; }
    .card-lite { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:20px; transition: box-shadow .3s, transform .3s; }
    .card-lite:hover { box-shadow: 0 12px 24px rgba(0,0,0,.06); transform: translateY(-3px); }
    .section-sub { color:#64748b; }
    .tag { display:inline-block; padding:4px 10px; border-radius:999px; background:#eef2ff; color:#3b82f6; font-weight:600; font-size:.8rem; }
    .feature-grid .icon { width:36px; height:36px; border-radius:8px; background:rgba(13,110,253,.1); display:flex; align-items:center; justify-content:center; color:#0d6efd; font-weight:700; }
    .reveal { opacity:0; transform: translateY(12px); animation: r .6s ease-out forwards; }
    .reveal.d2 { animation-delay:.1s } .reveal.d3 { animation-delay:.2s } .reveal.d4 { animation-delay:.3s }
    @keyframes r { to { opacity:1; transform: translateY(0);} }
  </style>

  <section class="page-hero">
    <span class="tag mb-2">Product Capabilities</span>
    <h1 class="mb-2">Features</h1>
    <p class="mb-0">Everything you need to run customer operations smoothly.</p>
  </section>

  <div class="row g-3">
    <div class="col-md-6 reveal">
      <div class="card-lite h-100">
        <h5 class="mb-2">Complaint Management</h5>
        <ul class="mb-0 text-muted">
          <li>Log, assign, and track complaints end-to-end</li>
          <li>SLA tracking with statuses and timelines</li>
          <li>Printable slips and activity logs</li>
        </ul>
      </div>
    </div>
    <div class="col-md-6 reveal d2">
      <div class="card-lite h-100">
        <h5 class="mb-2">Approvals & Spares</h5>
        <ul class="mb-0 text-muted">
          <li>Spare approval performas with items and cost</li>
          <li>Stock-aware approvals (no over-issuance)</li>
          <li>CSV export for records</li>
        </ul>
      </div>
    </div>
    <div class="col-md-6 reveal d3">
      <div class="card-lite h-100">
        <h5 class="mb-2">Employees</h5>
        <ul class="mb-0 text-muted">
          <li>Employee profiles and status</li>
          <li>Leaves tracking and approvals</li>
          <li>Performance metrics</li>
        </ul>
      </div>
    </div>
    <div class="col-md-6 reveal d4">
      <div class="card-lite h-100">
        <h5 class="mb-2">Reports</h5>
        <ul class="mb-0 text-muted">
          <li>Complaints, Employees, Spares, SLA</li>
          <li>Printable reports</li>
          <li>Summary metrics</li>
        </ul>
      </div>
    </div>
  </div>

  <div class="row g-3 mt-3">
    <div class="col-md-4 reveal">
      <div class="card-lite h-100">
        <div class="d-flex align-items-center mb-2"><div class="icon me-2">1</div><h6 class="mb-0">Real-time dashboards</h6></div>
        <p class="mb-0 text-muted">Visualize statuses, SLA breaches, and trends without manual refresh.</p>
      </div>
    </div>
    <div class="col-md-4 reveal d2">
      <div class="card-lite h-100">
        <div class="d-flex align-items-center mb-2"><div class="icon me-2">2</div><h6 class="mb-0">Role-based access</h6></div>
        <p class="mb-0 text-muted">Grant granular permissions for technicians, managers, and admins.</p>
      </div>
    </div>
    <div class="col-md-4 reveal d3">
      <div class="card-lite h-100">
        <div class="d-flex align-items-center mb-2"><div class="icon me-2">3</div><h6 class="mb-0">Exports & prints</h6></div>
        <p class="mb-0 text-muted">Create printable slips and CSV reports for audits and analysis.</p>
      </div>
    </div>
  </div>
@endsection


