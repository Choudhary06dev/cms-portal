@extends('frontend.layouts.app')

@section('title', 'About Us')

@section('content')
  <div class="p-4 bg-white rounded shadow-sm mb-4">
    <h1 class="mb-3">About CMS</h1>
    <p class="text-muted">We build tools that help operations teams respond faster, resolve smarter, and get full visibility on customer issues.</p>
  </div>

  <div class="row g-3">
    <div class="col-md-6">
      <div class="p-4 bg-white rounded shadow-sm h-100">
        <h5>Our Mission</h5>
        <p class="mb-0">Deliver a modern, reliable complaint management experience that scales with your business.</p>
      </div>
    </div>
    <div class="col-md-6">
      <div class="p-4 bg-white rounded shadow-sm h-100">
        <h5>What We Offer</h5>
        <ul class="mb-0">
          <li>End-to-end complaint workflow</li>
          <li>Employee management and leaves</li>
          <li>Approvals with inventory insights</li>
          <li>Reports and analytics</li>
        </ul>
      </div>
    </div>
  </div>
@endsection


