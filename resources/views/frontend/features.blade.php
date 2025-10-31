@extends('frontend.layouts.app')

@section('title', 'Features')

@section('content')
<style>
  :root { 
    --blue:#0d6efd; 
    --blue2:#3b82f6; 
    --slate:#64748b; 
    --dark:#1a1a1a;
  }
  
  /* Hero Section */
  .page-hero { 
    background: linear-gradient(135deg, var(--blue) 0%, #0a58ca 50%, var(--blue2) 100%); 
    color:#fff; 
    padding:100px 20px; 
    border-radius:0; 
    margin-bottom:0; 
    text-align:center;
    position:relative;
    overflow:hidden;
  }
  
  .page-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
      radial-gradient(circle at 25% 35%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
      radial-gradient(circle at 75% 65%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    pointer-events: none;
  }
  
  .page-hero .container {
    position: relative;
    z-index: 1;
  }
  
  .page-hero h1 { 
    font-weight: 800; 
    letter-spacing: -0.02em;
    font-size: 3.5rem;
    text-shadow: 0 2px 20px rgba(0,0,0,0.2);
    margin-bottom: 20px;
  }
  
  .page-hero p {
    font-size: 1.25rem;
    max-width: 700px;
    margin: 0 auto;
    opacity: 0.95;
  }
  
  .tag { 
    display:inline-block; 
    padding:8px 20px; 
    border-radius:999px; 
    background:rgba(255,255,255,0.2); 
    backdrop-filter: blur(10px);
    color:#fff; 
    font-weight:600; 
    font-size:.9rem;
    margin-bottom: 15px;
  }
  
  /* Feature Cards */
  .card-lite { 
    background:#fff; 
    border:none;
    border-radius:16px; 
    padding:35px; 
    transition: all .3s ease;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    height: 100%;
    position: relative;
    overflow: hidden;
  }
  
  .card-lite::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 0;
    background: linear-gradient(180deg, var(--blue), var(--blue2));
    transition: height 0.3s ease;
  }
  
  .card-lite:hover { 
    box-shadow: 0 15px 40px rgba(13, 110, 253, 0.15); 
    transform: translateY(-8px); 
  }
  
  .card-lite:hover::before {
    height: 100%;
  }
  
  .card-lite h5 {
    color: var(--dark);
    font-weight: 700;
    font-size: 1.4rem;
    margin-bottom: 20px;
  }
  
  .card-lite ul {
    padding-left: 0;
    list-style: none;
    margin-bottom: 0;
  }
  
  .card-lite ul li {
    padding-left: 28px;
    position: relative;
    margin-bottom: 12px;
    color: #64748b;
    line-height: 1.6;
  }
  
  .card-lite ul li::before {
    content: "✓";
    position: absolute;
    left: 0;
    color: var(--blue);
    font-weight: bold;
    font-size: 1.1rem;
  }
  
  /* Feature Icon Cards */
  .feature-icon-card {
    background: #fff;
    border: none;
    border-radius: 16px;
    padding: 30px;
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    height: 100%;
    text-align: center;
  }
  
  .feature-icon-card:hover {
    box-shadow: 0 15px 40px rgba(13, 110, 253, 0.15);
    transform: translateY(-8px);
  }
  
  .feature-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--blue), var(--blue2));
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 2rem;
    box-shadow: 0 10px 25px rgba(13, 110, 253, 0.3);
  }
  
  .feature-icon-card h6 {
    font-weight: 700;
    color: var(--dark);
    font-size: 1.1rem;
    margin-bottom: 10px;
  }
  
  .feature-icon-card p {
    color: #64748b;
    margin: 0;
    line-height: 1.6;
  }
  
  /* Numbered Cards */
  .numbered-card {
    background: #fff;
    border: none;
    border-radius: 16px;
    padding: 30px;
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    height: 100%;
  }
  
  .numbered-card:hover {
    box-shadow: 0 15px 40px rgba(13, 110, 253, 0.15);
    transform: translateY(-8px);
  }
  
  .number-badge {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--blue), var(--blue2));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.3rem;
    margin-bottom: 15px;
    box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
  }
  
  .numbered-card h6 {
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 10px;
    font-size: 1.1rem;
  }
  
  .numbered-card p {
    color: #64748b;
    margin: 0;
    line-height: 1.6;
  }
  
  /* Section Headings */
  .section-heading {
    font-size: 2.2rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 15px;
    text-align: center;
  }
  
  .section-description {
    text-align: center;
    color: #64748b;
    max-width: 700px;
    margin: 0 auto 50px;
    font-size: 1.1rem;
  }
  
  /* Category Tabs */
  .category-tabs {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 40px;
  }
  
  .category-tab {
    padding: 12px 28px;
    border-radius: 50px;
    background: #f8f9fa;
    color: #64748b;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
  }
  
  .category-tab:hover {
    background: #e9ecef;
  }
  
  .category-tab.active {
    background: linear-gradient(135deg, var(--blue), var(--blue2));
    color: white;
    border-color: var(--blue);
  }
  
  /* Integration Section */
  .integration-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 20px;
    margin-top: 30px;
  }
  
  .integration-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 3px 15px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
    border: 2px solid #f0f0f0;
  }
  
  .integration-card:hover {
    box-shadow: 0 8px 25px rgba(13, 110, 253, 0.12);
    border-color: var(--blue);
    transform: translateY(-3px);
  }
  
  .integration-icon {
    font-size: 2.5rem;
    margin-bottom: 10px;
  }
  
  .integration-card h6 {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--dark);
    margin: 0;
  }
  
  /* CTA Section */
  .cta-box {
    background: linear-gradient(135deg, var(--blue), var(--blue2));
    color: white;
    padding: 60px 40px;
    border-radius: 16px;
    text-align: center;
    margin-top: 60px;
    position: relative;
    overflow: hidden;
  }
  
  .cta-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
      radial-gradient(circle at 30% 40%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    pointer-events: none;
  }
  
  .cta-box .container {
    position: relative;
    z-index: 1;
  }
  
  .cta-box h3 {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 15px;
  }
  
  .cta-box p {
    font-size: 1.1rem;
    margin-bottom: 30px;
    opacity: 0.95;
  }
  
  .cta-box .btn {
    padding: 14px 32px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
  }
  
  /* Animations */
  .reveal { 
    opacity:0; 
    transform: translateY(20px); 
    animation: r .6s ease-out forwards; 
  }
  
  .reveal.d2 { animation-delay:.1s; } 
  .reveal.d3 { animation-delay:.2s; } 
  .reveal.d4 { animation-delay:.3s; }
  .reveal.d5 { animation-delay:.4s; }
  .reveal.d6 { animation-delay:.5s; }
  
  @keyframes r { 
    to { 
      opacity:1; 
      transform: translateY(0);
    } 
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .page-hero h1 {
      font-size: 2.2rem;
    }
    
    .page-hero p {
      font-size: 1rem;
    }
    
    .section-heading {
      font-size: 1.8rem;
    }
    
    .card-lite {
      padding: 25px;
    }
    
    .category-tabs {
      gap: 10px;
    }
    
    .category-tab {
      padding: 10px 20px;
      font-size: 0.9rem;
    }
  }
</style>

<!-- Hero Section -->
<section class="page-hero">
  <div class="container">
    <span class="tag">Product Capabilities</span>
    <h1>Powerful Features for Complete Control</h1>
    <p>Everything you need to manage complaints efficiently, track performance, and deliver exceptional customer service.</p>
  </div>
</section>

<!-- Main Content -->
<div class="container py-5">
  
  <!-- Core Features -->
  <div class="text-center mb-5">
    <h2 class="section-heading reveal">Core Features</h2>
    <p class="section-description reveal">Comprehensive tools designed to streamline every aspect of complaint management</p>
  </div>
  
  <div class="row g-4 mb-5">
    <div class="col-lg-6 reveal">
      <div class="card-lite">
        <h5>📋 Smart Complaint Management</h5>
        <ul>
          <li>Log, assign, and track complaints from submission to resolution</li>
          <li>Automated ticket generation with unique reference numbers</li>
          <li>Priority-based assignment and intelligent routing</li>
          <li>Real-time SLA tracking with breach alerts and notifications</li>
          <li>Complete activity logs and audit trails for accountability</li>
          <li>Printable complaint slips with QR codes for tracking</li>
          <li>Customer communication portal with status updates</li>
        </ul>
      </div>
    </div>
    
    <div class="col-lg-6 reveal d2">
      <div class="card-lite">
        <h5>✅ Approval Workflows & Spare Parts</h5>
        <ul>
          <li>Multi-level approval workflows for spare parts requests</li>
          <li>Stock-aware approval system prevents over-issuance</li>
          <li>Detailed spare parts performas with items, costs, and quantities</li>
          <li>Real-time inventory tracking across multiple warehouses</li>
          <li>Automated reorder alerts for low-stock items</li>
          <li>Usage reports and cost analytics for budget control</li>
          <li>CSV/Excel export for inventory audits and reconciliation</li>
        </ul>
      </div>
    </div>
    
    <div class="col-lg-6 reveal d3">
      <div class="card-lite">
        <h5>👥 Employee & Team Management</h5>
        <ul>
          <li>Comprehensive employee profiles with skills and specializations</li>
          <li>Real-time availability tracking and workload distribution</li>
          <li>Leave management with approval workflows</li>
          <li>Performance metrics and KPI tracking for each technician</li>
          <li>Shift scheduling and attendance management</li>
          <li>Team collaboration tools and internal messaging</li>
          <li>Mobile app access for field technicians</li>
        </ul>
      </div>
    </div>
    
    <div class="col-lg-6 reveal d4">
      <div class="card-lite">
        <h5>📊 Reports & Analytics</h5>
        <ul>
          <li>Comprehensive complaint reports with filters and date ranges</li>
          <li>Technician performance reports with resolution times</li>
          <li>SLA compliance reports with breach analysis</li>
          <li>Spare parts usage and inventory cost reports</li>
          <li>Customer satisfaction metrics and feedback analysis</li>
          <li>Trend analysis and predictive insights dashboard</li>
          <li>Printable and exportable reports in multiple formats</li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Advanced Features -->
  <div class="text-center mb-5 mt-5">
    <h2 class="section-heading reveal">Advanced Capabilities</h2>
    <p class="section-description reveal">Powerful tools that set us apart from the competition</p>
  </div>
  
  <div class="row g-4 mb-5">
    <div class="col-md-4 reveal">
      <div class="feature-icon-card">
        <div class="feature-icon">📱</div>
        <h6>Mobile-First Design</h6>
        <p>Native iOS and Android apps with offline capability, push notifications, and real-time synchronization for field teams.</p>
      </div>
    </div>
    
    <div class="col-md-4 reveal d2">
      <div class="feature-icon-card">
        <div class="feature-icon">🤖</div>
        <h6>AI-Powered Insights</h6>
        <p>Machine learning algorithms predict complaint trends, suggest optimal assignments, and identify potential SLA breaches.</p>
      </div>
    </div>
    
    <div class="col-md-4 reveal d3">
      <div class="feature-icon-card">
        <div class="feature-icon">🔔</div>
        <h6>Smart Notifications</h6>
        <p>Automated email, SMS, and push notifications keep teams and customers informed at every step of the resolution process.</p>
      </div>
    </div>
    
    <div class="col-md-4 reveal d4">
      <div class="feature-icon-card">
        <div class="feature-icon">🌍</div>
        <h6>Multi-Location Support</h6>
        <p>Manage operations across multiple branches, cities, or countries with location-based routing and reporting.</p>
      </div>
    </div>
    
    <div class="col-md-4 reveal d5">
      <div class="feature-icon-card">
        <div class="feature-icon">🔐</div>
        <h6>Enterprise Security</h6>
        <p>Bank-grade encryption, role-based access control, audit logs, and SOC 2 compliance for complete data protection.</p>
      </div>
    </div>
    
    <div class="col-md-4 reveal d6">
      <div class="feature-icon-card">
        <div class="feature-icon">⚡</div>
        <h6>Real-Time Dashboards</h6>
        <p>Live visualization of complaints, SLA breaches, team performance, and trends without manual refresh.</p>
      </div>
    </div>
  </div>

  <!-- Key Benefits -->
  <div class="text-center mb-5 mt-5">
    <h2 class="section-heading reveal">Why Teams Love Our Platform</h2>
    <p class="section-description reveal">Benefits that drive real business results</p>
  </div>
  
  <div class="row g-4 mb-5">
    <div class="col-md-4 reveal">
      <div class="numbered-card">
        <div class="number-badge">1</div>
        <h6>50% Faster Resolutions</h6>
        <p>Automated workflows and intelligent routing reduce average resolution time by half, improving customer satisfaction.</p>
      </div>
    </div>
    
    <div class="col-md-4 reveal d2">
      <div class="numbered-card">
        <div class="number-badge">2</div>
        <h6>Complete Visibility</h6>
        <p>Real-time dashboards and comprehensive reports provide full transparency into operations and team performance.</p>
      </div>
    </div>
    
    <div class="col-md-4 reveal d3">
      <div class="numbered-card">
        <div class="number-badge">3</div>
        <h6>Cost Control</h6>
        <p>Inventory management and approval workflows prevent unnecessary expenses and reduce spare parts waste by 30%.</p>
      </div>
    </div>
    
    <div class="col-md-4 reveal d4">
      <div class="numbered-card">
        <div class="number-badge">4</div>
        <h6>Data-Driven Decisions</h6>
        <p>Advanced analytics and trend analysis help identify bottlenecks and optimize resource allocation.</p>
      </div>
    </div>
    
    <div class="col-md-4 reveal d5">
      <div class="numbered-card">
        <div class="number-badge">5</div>
        <h6>Seamless Collaboration</h6>
        <p>Built-in communication tools and mobile access keep teams connected and coordinated across locations.</p>
      </div>
    </div>
    
    <div class="col-md-4 reveal d6">
      <div class="numbered-card">
        <div class="number-badge">6</div>
        <h6>Scalable Architecture</h6>
        <p>Cloud-based infrastructure grows with your business, handling thousands of complaints without performance issues.</p>
      </div>
    </div>
  </div>

  <!-- Integrations -->
  <div class="text-center mb-5 mt-5">
    <h2 class="section-heading reveal">Integrations & APIs</h2>
    <p class="section-description reveal">Connect with your existing tools and workflows seamlessly</p>
  </div>
  
  <div class="reveal">
    <div class="integration-grid">
      <div class="integration-card">
        <div class="integration-icon">📧</div>
        <h6>Email Services</h6>
      </div>
      <div class="integration-card">
        <div class="integration-icon">💬</div>
        <h6>SMS Gateways</h6>
      </div>
      <div class="integration-card">
        <div class="integration-icon">📱</div>
        <h6>WhatsApp API</h6>
      </div>
      <div class="integration-card">
        <div class="integration-icon">🔗</div>
        <h6>REST APIs</h6>
      </div>
      <div class="integration-card">
        <div class="integration-icon">💳</div>
        <h6>Payment Gateways</h6>
      </div>
      <div class="integration-card">
        <div class="integration-icon">📊</div>
        <h6>CRM Systems</h6>
      </div>
      <div class="integration-card">
        <div class="integration-icon">🔔</div>
        <h6>Webhooks</h6>
      </div>
      <div class="integration-card">
        <div class="integration-icon">☁️</div>
        <h6>Cloud Storage</h6>
      </div>
    </div>
  </div>

  <!-- CTA Section -->
  <div class="cta-box reveal">
    <div class="container">
      <h3>Experience All Features Today</h3>
      <p>Start your free trial and see how our comprehensive platform can transform your complaint management.</p>
      <a href="{{ route('frontend.register') }}" class="btn btn-light btn-lg me-3">Start Free Trial</a>
      <a href="{{ route('frontend.contact') }}" class="btn btn-outline-light btn-lg">Schedule Demo</a>
      <p class="mt-4 mb-0 small opacity-75">No credit card required • Full access for 14 days • Setup in minutes</p>
    </div>
  </div>

</div>

@endsection