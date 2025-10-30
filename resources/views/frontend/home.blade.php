@extends('frontend.layouts.app')

@section('title', 'Home')

@section('content')
  <section class="p-5 bg-white rounded shadow-sm mb-4">
    <div class="row align-items-center">
      <div class="col-lg-7">
        <h1 class="display-5 fw-bold mb-3">Smart Customer Management</h1>
        <p class="lead text-muted">Streamline complaints, track performance, and deliver great support with our CMS platform.</p>
        <div class="mt-3">
          <a href="{{ route('frontend.register') }}" class="btn btn-primary btn-lg me-2">Get Started</a>
          <a href="{{ route('frontend.contact') }}" class="btn btn-outline-secondary btn-lg">Contact Sales</a>
        </div>
      </div>
      <div class="col-lg-5 mt-4 mt-lg-0">
        <div class="bg-light rounded p-4 border">
          <div class="stat mb-2"><span class="num">2K+</span><span class="label">Complaints processed</span></div>
          <div class="stat mb-2"><span class="num">98%</span><span class="label">SLA adherence</span></div>
          <div class="stat"><span class="num">50+</span><span class="label">Active technicians</span></div>
        </div>
      </div>
    </div>
  </section>

  <section id="features" class="row g-3">
    <div class="col-md-4">
      <div class="p-4 bg-white rounded shadow-sm h-100 feature-card">
        <h5 class="mb-2">Complaint Management</h5>
        <p class="text-muted mb-0">Log, assign, track, and resolve complaints with clear SLAs and audit logs.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="p-4 bg-white rounded shadow-sm h-100 feature-card">
        <h5 class="mb-2">Team & Performance</h5>
        <p class="text-muted mb-0">Manage employees, leaves, and view performance metrics in real time.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="p-4 bg-white rounded shadow-sm h-100 feature-card">
        <h5 class="mb-2">Approvals & Spares</h5>
        <p class="text-muted mb-0">Control spare parts usage with approval performas and stock visibility.</p>
      </div>
    </div>
  </section>

  <section class="mt-4">
    <div class="p-4 bg-white rounded shadow-sm">
      <h5 class="mb-3">What customers say</h5>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="testimonial">“CMS helped us cut resolution time by half.” <div class="text-muted small">— Operations Lead</div></div>
        </div>
        <div class="col-md-6">
          <div class="testimonial">“The approval workflow keeps stock under control.” <div class="text-muted small">— Inventory Manager</div></div>
        </div>
      </div>
    </div>
  </section>
@endsection


