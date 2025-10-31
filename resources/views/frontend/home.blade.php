@extends('frontend.layouts.app')

@section('title', 'Home')

@section('content')

<style>
  /* Hero Section with Background Pattern */
  .hero {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 50%, #3b82f6 100%);
    color: #fff;
    padding: 140px 0 120px;
    border-radius: 0;
    position: relative;
    overflow: hidden;
  }
  
  .hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
      radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
      radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    pointer-events: none;
  }
  
  .hero::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 100px;
    background: linear-gradient(to bottom, transparent, #fff);
    pointer-events: none;
  }
  
  .hero .container {
    position: relative;
    z-index: 1;
  }
  
  .hero h1 {
    font-weight: 800;
    font-size: 3.5rem;
    text-shadow: 0 2px 20px rgba(0,0,0,0.2);
    margin-bottom: 20px;
    line-height: 1.2;
  }
  
  .hero p.lead {
    font-size: 1.25rem;
    opacity: 0.95;
    max-width: 700px;
    margin: 0 auto 30px;
  }
  
  .hero-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 0.9rem;
    margin-bottom: 20px;
    font-weight: 500;
  }
  
  .hero .btn {
    padding: 14px 32px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
  }
  
  .hero .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(0,0,0,0.3);
  }

  /* Stats Section */
  .stats-section {
    margin-top: -50px;
    position: relative;
    z-index: 2;
  }
  
  .stat-card {
    background: #fff;
    padding: 35px 20px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
  }
  
  .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
  }
  
  .stat-number {
    font-size: 3rem;
    font-weight: 800;
    background: linear-gradient(135deg, #0d6efd, #3b82f6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 5px;
  }

  /* Features Section */
  .feature-card {
    transition: all 0.4s ease;
    border: 2px solid transparent;
    cursor: pointer;
  }
  
  .feature-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(13, 110, 253, 0.15);
    border-color: #0d6efd;
  }
  
  .feature-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #0d6efd, #3b82f6);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    font-size: 2rem;
  }
  
  .feature-card h5 {
    color: #1a1a1a;
    font-weight: 700;
  }

  /* Benefits Section */
  .benefits-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  }
  
  .benefit-item {
    display: flex;
    align-items: start;
    margin-bottom: 25px;
  }
  
  .benefit-icon {
    width: 45px;
    height: 45px;
    background: #0d6efd;
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    flex-shrink: 0;
  }

  /* Workflow Section */
  .workflow-step {
    text-align: center;
    position: relative;
  }
  
  .workflow-number {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #0d6efd, #3b82f6);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 1.5rem;
    font-weight: 700;
    box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
  }
  
  .workflow-step::after {
    content: '→';
    position: absolute;
    top: 30px;
    right: -15%;
    font-size: 2rem;
    color: #0d6efd;
    opacity: 0.3;
  }
  
  .workflow-step:last-child::after {
    display: none;
  }

  /* Testimonials */
  .testimonial {
    background: #fff;
    border-left: 5px solid #0d6efd;
    padding: 30px;
    border-radius: 12px;
    font-style: italic;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    height: 100%;
  }
  
  .testimonial:hover {
    box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    transform: translateY(-3px);
  }
  
  .testimonial-stars {
    color: #ffc107;
    font-size: 1.2rem;
    margin-bottom: 15px;
  }

  /* Use Cases Section */
  .use-case-card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    height: 100%;
    transition: all 0.3s ease;
  }
  
  .use-case-card:hover {
    box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    transform: translateY(-5px);
  }
  
  .use-case-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
  }

  /* CTA Section */
  .cta-section {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 50%, #3b82f6 100%);
    color: #fff;
    padding: 80px 20px;
    text-align: center;
    border-radius: 0;
    margin-top: 0;
    position: relative;
    overflow: hidden;
  }
  
  .cta-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
      radial-gradient(circle at 30% 40%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
      radial-gradient(circle at 70% 70%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    pointer-events: none;
  }
  
  .cta-section .container {
    position: relative;
    z-index: 1;
  }
  
  .cta-section h2 {
    font-size: 2.5rem;
    font-weight: 800;
  }
  
  .cta-section .btn {
    padding: 14px 32px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
  }

  /* Section Headings */
  .section-heading {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 15px;
  }
  
  .section-subheading {
    color: #6c757d;
    font-size: 1.1rem;
    max-width: 700px;
    margin: 0 auto 50px;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .hero h1 {
      font-size: 2.2rem;
    }
    
    .hero p.lead {
      font-size: 1rem;
    }
    
    .stat-number {
      font-size: 2.2rem;
    }
    
    .workflow-step::after {
      display: none;
    }
  }
</style>

<!-- Hero Section -->
<section class="hero text-center">
  <div class="container">
    <div class="hero-badge">
      ⚡ Trusted by 500+ Organizations
    </div>
    <h1>Transform Your Customer Service<br>with Smart Complaint Management</h1>
    <p class="lead">
      Streamline complaint resolution, automate workflows, and deliver exceptional customer experiences with our comprehensive complaint management system.
    </p>
    <div class="mt-4">
      <a href="{{ route('frontend.register') }}" class="btn btn-light btn-lg me-3">Get Started Free</a>
      <a href="{{ route('frontend.contact') }}" class="btn btn-outline-light btn-lg">Schedule a Demo</a>
    </div>
  </div>
</section>

<!-- Stats Section -->
<section class="stats-section py-5">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-3 col-6">
        <div class="stat-card">
          <div class="stat-number">5K+</div>
          <p class="text-muted mb-0 fw-semibold">Complaints Resolved</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="stat-card">
          <div class="stat-number">98%</div>
          <p class="text-muted mb-0 fw-semibold">Customer Satisfaction</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="stat-card">
          <div class="stat-number">50%</div>
          <p class="text-muted mb-0 fw-semibold">Faster Resolution</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="stat-card">
          <div class="stat-number">24/7</div>
          <p class="text-muted mb-0 fw-semibold">System Availability</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Features Section -->
<section class="py-5 mt-5">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-heading">Powerful Features for Complete Control</h2>
      <p class="section-subheading">Everything you need to manage complaints efficiently and deliver outstanding customer service</p>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="p-4 bg-white rounded shadow-sm h-100 feature-card">
          <div class="feature-icon">📋</div>
          <h5 class="mb-3">Smart Complaint Tracking</h5>
          <p class="text-muted">Real-time complaint logging with automated ticket generation, priority assignment, and SLA monitoring. Track every complaint from submission to resolution.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 bg-white rounded shadow-sm h-100 feature-card">
          <div class="feature-icon">⚙️</div>
          <h5 class="mb-3">Workflow Automation</h5>
          <p class="text-muted">Intelligent routing rules, automated escalations, and approval workflows. Reduce manual work and ensure complaints reach the right team instantly.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 bg-white rounded shadow-sm h-100 feature-card">
          <div class="feature-icon">👥</div>
          <h5 class="mb-3">Team Management</h5>
          <p class="text-muted">Manage technicians, track performance metrics, monitor workload distribution, and handle leave requests all in one centralized dashboard.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 bg-white rounded shadow-sm h-100 feature-card">
          <div class="feature-icon">📦</div>
          <h5 class="mb-3">Inventory Control</h5>
          <p class="text-muted">Track spare parts inventory, manage stock levels, automate reorder alerts, and control spare parts approval processes to prevent waste.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 bg-white rounded shadow-sm h-100 feature-card">
          <div class="feature-icon">📊</div>
          <h5 class="mb-3">Advanced Analytics</h5>
          <p class="text-muted">Generate comprehensive reports on complaint trends, resolution times, technician performance, and customer satisfaction metrics.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 bg-white rounded shadow-sm h-100 feature-card">
          <div class="feature-icon">🌐</div>
          <h5 class="mb-3">Customer Portal</h5>
          <p class="text-muted">Self-service portal for customers to log complaints, track status in real-time, communicate with technicians, and provide feedback.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Benefits Section -->
<section class="py-5 benefits-section">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 mb-4 mb-lg-0">
        <h2 class="section-heading mb-4">Why Choose Our CMS?</h2>
        <p class="text-muted mb-4">Built specifically for service-based businesses that need to manage customer complaints efficiently and maintain high satisfaction levels.</p>
        
        <div class="benefit-item">
          <div class="benefit-icon">✓</div>
          <div>
            <h6 class="fw-bold mb-1">Reduce Resolution Time</h6>
            <p class="text-muted mb-0">Cut average resolution time by 50% with automated routing and prioritization.</p>
          </div>
        </div>
        
        <div class="benefit-item">
          <div class="benefit-icon">✓</div>
          <div>
            <h6 class="fw-bold mb-1">Improve Customer Satisfaction</h6>
            <p class="text-muted mb-0">Keep customers informed with real-time updates and transparent communication.</p>
          </div>
        </div>
        
        <div class="benefit-item">
          <div class="benefit-icon">✓</div>
          <div>
            <h6 class="fw-bold mb-1">Increase Team Productivity</h6>
            <p class="text-muted mb-0">Empower your team with mobile access, automated workflows, and clear task prioritization.</p>
          </div>
        </div>
        
        <div class="benefit-item">
          <div class="benefit-icon">✓</div>
          <div>
            <h6 class="fw-bold mb-1">Data-Driven Decisions</h6>
            <p class="text-muted mb-0">Make informed decisions with comprehensive analytics and performance metrics.</p>
          </div>
        </div>
        
        <div class="benefit-item">
          <div class="benefit-icon">✓</div>
          <div>
            <h6 class="fw-bold mb-1">Cost Control</h6>
            <p class="text-muted mb-0">Manage spare parts inventory and prevent unnecessary expenses with approval workflows.</p>
          </div>
        </div>
      </div>
      
      <div class="col-lg-6">
        <div class="bg-white p-4 rounded shadow-lg">
          <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&h=400&fit=crop" alt="Dashboard Analytics" class="img-fluid rounded">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Workflow Section -->
<section class="py-5 mt-4">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-heading">How It Works</h2>
      <p class="section-subheading">Simple, streamlined process from complaint submission to resolution</p>
    </div>
    <div class="row g-4">
      <div class="col-md-3 workflow-step">
        <div class="workflow-number">1</div>
        <h6 class="fw-bold mb-2">Submit Complaint</h6>
        <p class="text-muted small">Customer logs complaint via portal, app, or phone with all necessary details and attachments.</p>
      </div>
      <div class="col-md-3 workflow-step">
        <div class="workflow-number">2</div>
        <h6 class="fw-bold mb-2">Auto-Assign</h6>
        <p class="text-muted small">System automatically assigns to the best-suited technician based on location, availability, and expertise.</p>
      </div>
      <div class="col-md-3 workflow-step">
        <div class="workflow-number">3</div>
        <h6 class="fw-bold mb-2">Resolve & Track</h6>
        <p class="text-muted small">Technician resolves issue with real-time status updates, spare parts management, and documentation.</p>
      </div>
      <div class="col-md-3 workflow-step">
        <div class="workflow-number">4</div>
        <h6 class="fw-bold mb-2">Feedback & Close</h6>
        <p class="text-muted small">Customer reviews service quality, provides rating, and complaint is closed with full audit trail.</p>
      </div>
    </div>
  </div>
</section>

<!-- Use Cases Section -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-heading">Perfect for Multiple Industries</h2>
      <p class="section-subheading">Versatile solution that adapts to your industry-specific needs</p>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="use-case-card">
          <div class="use-case-icon">🔌</div>
          <h5 class="fw-bold mb-3">Utilities & Energy</h5>
          <p class="text-muted mb-0">Manage service requests, outage complaints, meter issues, and infrastructure problems with field technician coordination.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="use-case-card">
          <div class="use-case-icon">📡</div>
          <h5 class="fw-bold mb-3">Telecom & ISP</h5>
          <p class="text-muted mb-0">Handle connectivity issues, installation requests, billing disputes, and technical support with SLA tracking.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="use-case-card">
          <div class="use-case-icon">🏢</div>
          <h5 class="fw-bold mb-3">Property Management</h5>
          <p class="text-muted mb-0">Track maintenance requests, tenant complaints, facility issues, and vendor management in one system.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="use-case-card">
          <div class="use-case-icon">🏥</div>
          <h5 class="fw-bold mb-3">Healthcare Services</h5>
          <p class="text-muted mb-0">Manage equipment maintenance, facility complaints, patient feedback, and medical device servicing.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="use-case-card">
          <div class="use-case-icon">🏭</div>
          <h5 class="fw-bold mb-3">Manufacturing</h5>
          <p class="text-muted mb-0">Handle equipment breakdowns, quality complaints, maintenance scheduling, and production issues.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="use-case-card">
          <div class="use-case-icon">🛒</div>
          <h5 class="fw-bold mb-3">Retail & E-commerce</h5>
          <p class="text-muted mb-0">Process product returns, quality issues, delivery complaints, and customer service requests efficiently.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Testimonials -->
<section class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-heading">What Our Clients Say</h2>
      <p class="section-subheading">Join hundreds of satisfied organizations improving their customer service</p>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="testimonial">
          <div class="testimonial-stars">★★★★★</div>
          "This CMS has revolutionized how we handle customer complaints. Resolution time dropped by 60% and our customer satisfaction scores are at an all-time high."
          <div class="text-muted small mt-3 fw-semibold">— Sarah Johnson, Operations Director, PowerGrid Solutions</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testimonial">
          <div class="testimonial-stars">★★★★★</div>
          "The inventory management and spare parts approval feature alone saved us thousands. We finally have visibility into resource usage and can control costs effectively."
          <div class="text-muted small mt-3 fw-semibold">— Michael Chen, Service Manager, TechServe Networks</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testimonial">
          <div class="testimonial-stars">★★★★★</div>
          "Implementation was smooth and the team was incredibly responsive. The mobile app is a game-changer for our field technicians. Highly recommend!"
          <div class="text-muted small mt-3 fw-semibold">— Priya Sharma, Customer Service Head, ConnectPlus Telecom</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
  <div class="container">
    <h2 class="fw-bold mb-3">Ready to Transform Your Customer Service?</h2>
    <p class="mb-4 fs-5">Join hundreds of organizations using our CMS to deliver faster resolutions, improve customer satisfaction, and optimize operations.</p>
    <div class="mt-4">
      <a href="{{ route('frontend.register') }}" class="btn btn-light btn-lg me-3">Start Free Trial</a>
      <a href="{{ route('frontend.contact') }}" class="btn btn-outline-light btn-lg">Contact Sales</a>
    </div>
    <p class="mt-4 mb-0 small opacity-75">No credit card required • 14-day free trial • Setup in minutes</p>
  </div>
</section>

@endsection