@extends('frontend.layouts.app')

@section('title', 'Home')

@section('content')

<style>
  /* Hero Section */
  .hero {
    background: linear-gradient(135deg, #0d6efd, #3b82f6);
    color: #fff;
    padding: 120px 0;
    border-radius: 12px;
  }
  .hero h1 {
    font-weight: 700;
    font-size: 3rem;
  }
  .hero p {
    font-size: 1.1rem;
  }

  /* Features */
  .feature-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
  }

  /* Workflow Section */
  .workflow-step {
    text-align: center;
  }
  .workflow-step img {
    width: 80px;
    margin-bottom: 10px;
  }

  /* Testimonials */
  .testimonial {
    background: #f8f9fa;
    border-left: 5px solid #0d6efd;
    padding: 20px;
    border-radius: 8px;
    font-style: italic;
  }

  /* CTA Section */
  .cta-section {
    background: linear-gradient(135deg, #0d6efd, #3b82f6);
    color: #fff;
    padding: 60px 20px;
    text-align: center;
    border-radius: 10px;
    margin-top: 50px;
  }
  .cta-section a {
    font-size: 1.1rem;
    padding: 12px 25px;
  }
</style>

<!-- Hero Section -->
<section class="hero text-center text-lg-start d-flex align-items-center">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-12">
        <h1>Smart Complaint Management System</h1>
        <p class="lead mb-4">
          Manage, track, and resolve customer complaints with speed and transparency.
          Empower your support teams <br>
          with automated workflows and real-time dashboards.
        </p>
        <a href="{{ route('frontend.register') }}" class="btn btn-primary btn-lg me-2">Get Started</a>
        <a href="{{ route('frontend.contact') }}" class="btn btn-outline-light btn-lg">Contact Support</a>
      </div>
    </div>
  </div>
</section>

<!-- Stats Section -->
<section class="py-5">
  <div class="container">
    <div class="row text-center">
      <div class="col-md-4">
        <h2 class="fw-bold text-primary">2,000+</h2>
        <p class="text-muted">Complaints Resolved</p>
      </div>
      <div class="col-md-4">
        <h2 class="fw-bold text-primary">98%</h2>
        <p class="text-muted">Customer Satisfaction Rate</p>
      </div>
      <div class="col-md-4">
        <h2 class="fw-bold text-primary">50+</h2>
        <p class="text-muted">Active Service Agents</p>
      </div>
    </div>
  </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
  <div class="container">
    <h3 class="text-center fw-bold mb-5">Core Features</h3>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="p-4 bg-white rounded shadow-sm h-100 feature-card">
          <h5 class="mb-3">Complaint Tracking</h5>
          <p class="text-muted">Log, assign, and monitor complaints in real-time with status updates and SLA adherence tracking.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 bg-white rounded shadow-sm h-100 feature-card">
          <h5 class="mb-3">Workflow Automation</h5>
          <p class="text-muted">Automate complaint routing, approvals, and notifications to improve resolution time and efficiency.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 bg-white rounded shadow-sm h-100 feature-card">
          <h5 class="mb-3">Team & Performance</h5>
          <p class="text-muted">Track employee KPIs, leaves, and performance through visual reports and analytics dashboards.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 bg-white rounded shadow-sm h-100 feature-card">
          <h5 class="mb-3">Inventory & Spares</h5>
          <p class="text-muted">Manage spare parts stock, approvals, and usage to prevent shortages and overspending.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 bg-white rounded shadow-sm h-100 feature-card">
          <h5 class="mb-3">Reports & Analytics</h5>
          <p class="text-muted">Generate detailed reports on complaints, technician performance, and resolution timelines.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 bg-white rounded shadow-sm h-100 feature-card">
          <h5 class="mb-3">Customer Portal</h5>
          <p class="text-muted">Allow customers to log and track complaints, view updates, and rate service experiences.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Workflow Section -->
<section class="py-5">
  <div class="container">
    <h3 class="text-center fw-bold mb-5">How It Works</h3>
    <div class="row text-center g-4">
      <div class="col-md-3 workflow-step">
        <h6 class="fw-bold">1. Log Complaint</h6>
        <p class="text-muted small">Customer submits complaint via web or app.</p>
      </div>
      <div class="col-md-3 workflow-step">
        <h6 class="fw-bold">2. Assign & Track</h6>
        <p class="text-muted small">System assigns technician and tracks progress.</p>
      </div>
      <div class="col-md-3 workflow-step">
        <h6 class="fw-bold">3. Resolve & Approve</h6>
        <p class="text-muted small">Technician resolves and manager approves work.</p>
      </div>
      <div class="col-md-3 workflow-step">
        <h6 class="fw-bold">4. Feedback</h6>
        <p class="text-muted small">Customer rates service and closes complaint.</p>
      </div>
    </div>
  </div>
</section>

<!-- Testimonials -->
<section class="py-5 bg-light">
  <div class="container">
    <h3 class="text-center fw-bold mb-5">What Our Clients Say</h3>
    <div class="row g-4">
      <div class="col-md-6">
        <div class="testimonial">
          “CMS helped us cut resolution time by half. The automated workflow keeps our teams organized.”
          <div class="text-muted small mt-2">— Operations Lead, Alpha Networks</div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="testimonial">
          “The spare parts approval process reduced unnecessary inventory usage and improved accountability.”
          <div class="text-muted small mt-2">— Inventory Manager, TechServ</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
  <div class="container">
    <h2 class="fw-bold mb-3">Ready to Transform Your Complaint Management?</h2>
    <p class="mb-4">Join hundreds of organizations using CMS to deliver faster resolutions and better customer satisfaction.</p>
    <a href="{{ route('frontend.register') }}" class="btn btn-light me-2">Get Started Now</a>
    <a href="{{ route('frontend.contact') }}" class="btn btn-outline-light">Talk to Us</a>
  </div>
</section>

@endsection
