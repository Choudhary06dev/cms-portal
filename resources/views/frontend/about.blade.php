@extends('frontend.layouts.app')

@section('title', 'About Us')

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
    padding: 100px 20px; 
    border-radius: 0; 
    margin-bottom: 0; 
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
      radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
      radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
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
  
  /* Animations */
  .fade-in { 
    animation: fadeInUp .6s ease-out both; 
  }
  
  @keyframes fadeInUp { 
    from { opacity:0; transform: translateY(20px);} 
    to { opacity:1; transform: translateY(0);} 
  }
  
  /* Cards */
  .card-lite { 
    background:#fff; 
    border:none;
    border-radius:16px; 
    padding:35px; 
    transition: all .3s ease;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    height: 100%;
  }
  
  .card-lite:hover { 
    box-shadow: 0 15px 40px rgba(13, 110, 253, 0.15); 
    transform: translateY(-5px); 
  }
  
  .card-lite h4 {
    color: var(--dark);
    font-weight: 700;
    margin-bottom: 15px;
    font-size: 1.5rem;
  }
  
  .card-lite ul {
    padding-left: 0;
    list-style: none;
  }
  
  .card-lite ul li {
    padding-left: 28px;
    position: relative;
    margin-bottom: 12px;
    color: #64748b;
  }
  
  .card-lite ul li::before {
    content: "✓";
    position: absolute;
    left: 0;
    color: var(--blue);
    font-weight: bold;
    font-size: 1.1rem;
  }
  
  .section-sub { 
    color:#64748b; 
    font-size: 1rem;
    line-height: 1.6;
  }
  
  .pill { 
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
  
  /* Stats Section */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 20px;
    margin-top: 30px;
  }
  
  .stat-box {
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
  }
  
  .stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--blue), var(--blue2));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
    margin-bottom: 5px;
  }
  
  .stat-label {
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 600;
  }
  
  /* Timeline */
  .timeline { 
    position:relative; 
    padding-left:40px; 
  }
  
  .timeline::before { 
    content:""; 
    position:absolute; 
    left:14px; 
    top:8px; 
    bottom:8px; 
    width:3px; 
    background: linear-gradient(180deg, var(--blue), var(--blue2));
    border-radius: 2px;
  }
  
  .t-item { 
    position:relative; 
    margin-bottom:25px; 
    padding-left: 10px;
  }
  
  .t-item::before {
    content: "";
    position: absolute;
    left: -33px;
    top: 5px;
    width: 12px;
    height: 12px;
    background: var(--blue);
    border: 3px solid #fff;
    border-radius: 50%;
    box-shadow: 0 0 0 4px #e8f0fe;
  }
  
  .t-item strong {
    color: var(--blue);
    font-size: 1.1rem;
    font-weight: 700;
  }
  
  .t-item span {
    color: #334155;
    display: block;
    margin-top: 5px;
  }
  
  /* Team Section */
  .team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 25px;
    margin-top: 30px;
  }
  
  .team-card {
    text-align: center;
    padding: 25px;
    background: #fff;
    border-radius: 12px;
    transition: all 0.3s ease;
    border: 2px solid #f0f0f0;
  }
  
  .team-card:hover {
    border-color: var(--blue);
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(13, 110, 253, 0.1);
  }
  
  .team-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--blue), var(--blue2));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    font-size: 2rem;
  }
  
  .team-card h6 {
    font-weight: 700;
    margin-bottom: 5px;
    color: var(--dark);
  }
  
  .team-card p {
    font-size: 0.9rem;
    color: #64748b;
    margin: 0;
  }
  
  /* Values Grid */
  .values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 30px;
  }
  
  .value-card {
    padding: 25px;
    background: #f8f9fa;
    border-radius: 12px;
    border-left: 4px solid var(--blue);
  }
  
  .value-icon {
    font-size: 2rem;
    margin-bottom: 10px;
  }
  
  .value-card h6 {
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }
  
  .value-card p {
    color: #64748b;
    font-size: 0.95rem;
    margin: 0;
  }
  
  /* FAQ Accordion */
  .accordion .item { 
    border:none;
    border-radius:12px; 
    overflow:hidden; 
    margin-bottom: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  }
  
  .accordion .q { 
    background:#fff; 
    padding:18px 24px; 
    cursor:pointer;
    font-weight: 600;
    color: var(--dark);
    transition: all 0.3s ease;
    position: relative;
    padding-right: 50px;
  }
  
  .accordion .q::after {
    content: "+";
    position: absolute;
    right: 24px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.5rem;
    color: var(--blue);
    transition: transform 0.3s ease;
  }
  
  .accordion .q:hover {
    background: #f8f9fa;
  }
  
  .accordion .q.active::after {
    transform: translateY(-50%) rotate(45deg);
  }
  
  .accordion .a { 
    background:#f8fafc; 
    padding:20px 24px; 
    display:none; 
    color:#334155;
    line-height: 1.6;
    border-top: 1px solid #e5e7eb;
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
    margin: 0 auto 40px;
    font-size: 1.1rem;
  }
  
  /* CTA Section */
  .cta-box {
    background: linear-gradient(135deg, var(--blue), var(--blue2));
    color: white;
    padding: 60px 40px;
    border-radius: 16px;
    text-align: center;
    margin-top: 50px;
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
  }
</style>

<!-- Hero Section -->
<section class="page-hero fade-in">
  <div class="container">
    <span class="pill">Our Story</span>
    <h1>Transforming Complaint Management</h1>
    <p>We empower operations teams to respond faster, resolve smarter, and deliver exceptional customer experiences with data-driven complaint management solutions.</p>
  </div>
</section>

<!-- Main Content -->
<div class="container py-5">
  
  <!-- Mission & Vision -->
  <div class="row g-4 mb-5">
    <div class="col-lg-6">
      <div class="card-lite fade-in" style="animation-delay: .05s;">
        <h4>🎯 Our Mission</h4>
        <p class="section-sub mb-3">To deliver a modern, reliable, and scalable complaint management platform that transforms how organizations handle customer service operations and drive continuous improvement.</p>
        <ul class="mb-0">
          <li>Speed up resolutions with intelligent automation and SLA tracking</li>
          <li>Increase accountability through approval workflows and audit logs</li>
          <li>Enable data-driven decisions with real-time insights and analytics</li>
          <li>Empower teams with mobile-first tools and seamless collaboration</li>
        </ul>
      </div>
    </div>
    
    <div class="col-lg-6">
      <div class="card-lite fade-in" style="animation-delay: .1s;">
        <h4>🚀 Our Vision</h4>
        <p class="section-sub mb-3">To become the leading complaint management platform that sets the standard for operational excellence and customer satisfaction across industries worldwide.</p>
        <div class="stats-grid">
          <div class="stat-box">
            <div class="stat-number">500+</div>
            <div class="stat-label">Organizations</div>
          </div>
          <div class="stat-box">
            <div class="stat-number">50K+</div>
            <div class="stat-label">Complaints</div>
          </div>
          <div class="stat-box">
            <div class="stat-number">98%</div>
            <div class="stat-label">Satisfaction</div>
          </div>
          <div class="stat-box">
            <div class="stat-number">24/7</div>
            <div class="stat-label">Support</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- What We Offer -->
  <div class="row mb-5">
    <div class="col-12">
      <div class="card-lite fade-in" style="animation-delay: .15s;">
        <h4>💼 What We Offer</h4>
        <p class="section-sub mb-4">A comprehensive suite of tools designed to streamline every aspect of complaint management and service operations.</p>
        <div class="row">
          <div class="col-md-6">
            <ul class="mb-md-0">
              <li>End-to-end complaint workflow automation</li>
              <li>Smart ticket routing and assignment</li>
              <li>Employee management and leave tracking</li>
              <li>Real-time performance dashboards</li>
              <li>SLA monitoring and breach alerts</li>
            </ul>
          </div>
          <div class="col-md-6">
            <ul class="mb-0">
              <li>Inventory and spare parts management</li>
              <li>Multi-level approval workflows</li>
              <li>Customer self-service portal</li>
              <li>Advanced reports and analytics</li>
              <li>Mobile app for field technicians</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Core Values -->
  <div class="row mb-5">
    <div class="col-12 text-center mb-4">
      <h2 class="section-heading fade-in" style="animation-delay: .2s;">Our Core Values</h2>
      <p class="section-description fade-in" style="animation-delay: .25s;">The principles that guide everything we do and every decision we make.</p>
    </div>
    <div class="col-12">
      <div class="values-grid fade-in" style="animation-delay: .3s;">
        <div class="value-card">
          <div class="value-icon">🎯</div>
          <h6>Customer First</h6>
          <p>Every feature we build starts with understanding and solving real customer pain points.</p>
        </div>
        <div class="value-card">
          <div class="value-icon">⚡</div>
          <h6>Speed & Efficiency</h6>
          <p>We obsess over performance, automation, and reducing resolution times for our users.</p>
        </div>
        <div class="value-card">
          <div class="value-icon">🔍</div>
          <h6>Transparency</h6>
          <p>Clear communication, honest reporting, and full visibility into every process.</p>
        </div>
        <div class="value-card">
          <div class="value-icon">📊</div>
          <h6>Data-Driven</h6>
          <p>Decisions backed by analytics and insights, not assumptions or guesswork.</p>
        </div>
        <div class="value-card">
          <div class="value-icon">🔒</div>
          <h6>Reliability</h6>
          <p>Building robust, secure systems that teams can depend on 24/7.</p>
        </div>
        <div class="value-card">
          <div class="value-icon">🚀</div>
          <h6>Innovation</h6>
          <p>Continuously improving and adapting to emerging technologies and methodologies.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Journey Timeline -->
  <div class="row mb-5">
    <div class="col-lg-6">
      <div class="card-lite fade-in" style="animation-delay: .35s;">
        <h4>📅 Our Journey</h4>
        <p class="section-sub mb-4">From a simple internal tool to a comprehensive enterprise solution.</p>
        <div class="timeline">
          <div class="t-item">
            <strong>2019</strong>
            <span>Started as an internal prototype to manage service complaints for a telecom company.</span>
          </div>
          <div class="t-item">
            <strong>2021</strong>
            <span>Launched public beta with first 10 enterprise clients and mobile app release.</span>
          </div>
          <div class="t-item">
            <strong>2023</strong>
            <span>Introduced advanced SLA suite, multi-level approvals, and inventory management.</span>
          </div>
          <div class="t-item">
            <strong>2024</strong>
            <span>Expanded to 500+ organizations across 6 industries with 24/7 support.</span>
          </div>
          <div class="t-item">
            <strong>2025</strong>
            <span>Real-time analytics dashboard, AI-powered insights, and advanced reporting.</span>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-6">
      <div class="card-lite fade-in" style="animation-delay: .4s;">
        <h4>👥 Our Team</h4>
        <p class="section-sub mb-3">A dedicated team of experts committed to your success.</p>
        <div class="team-grid">
          <div class="team-card">
            <div class="team-icon">👨‍💼</div>
            <h6>Leadership</h6>
            <p>Experienced executives driving vision</p>
          </div>
          <div class="team-card">
            <div class="team-icon">💻</div>
            <h6>Engineering</h6>
            <p>Building scalable solutions</p>
          </div>
          <div class="team-card">
            <div class="team-icon">🎨</div>
            <h6>Design</h6>
            <p>Creating intuitive experiences</p>
          </div>
          <div class="team-card">
            <div class="team-icon">🤝</div>
            <h6>Support</h6>
            <p>24/7 customer success team</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- FAQ Section -->
  <div class="row mb-5">
    <div class="col-12 text-center mb-4">
      <h2 class="section-heading fade-in" style="animation-delay: .45s;">Frequently Asked Questions</h2>
      <p class="section-description fade-in" style="animation-delay: .5s;">Get answers to common questions about our platform and services.</p>
    </div>
    <div class="col-12">
      <div class="fade-in" style="animation-delay: .55s;">
        <div class="accordion" id="faq">
          <div class="item">
            <div class="q" onclick="toggleFaq(this)">How do SLAs work in the system?</div>
            <div class="a">You can define custom response and resolution time targets for different complaint categories and priorities. Our dashboard provides real-time tracking of SLA compliance, shows upcoming breaches with alerts, and generates comprehensive reports on SLA performance trends to help you maintain service quality standards.</div>
          </div>
          <div class="item">
            <div class="q" onclick="toggleFaq(this)">Can I export data and generate reports?</div>
            <div class="a">Yes, our platform offers flexible export options including CSV, Excel, and PDF formats. You can generate detailed reports on complaints, technician performance, resolution times, customer satisfaction, inventory usage, and more. All reports can be scheduled for automatic generation and distribution.</div>
          </div>
          <div class="item">
            <div class="q" onclick="toggleFaq(this)">Is there role-based access control?</div>
            <div class="a">Absolutely. Our system includes comprehensive role-based permissions that control who can view, create, edit, approve, delete, and export data. You can create custom roles tailored to your organization's structure and assign granular permissions for different modules and features.</div>
          </div>
          <div class="item">
            <div class="q" onclick="toggleFaq(this)">Does the system support mobile access?</div>
            <div class="a">Yes, we provide native mobile apps for iOS and Android, plus a responsive web interface. Field technicians can log complaints, update status, capture photos, track time, request spare parts, and complete tasks directly from their mobile devices, even with limited connectivity.</div>
          </div>
          <div class="item">
            <div class="q" onclick="toggleFaq(this)">How does inventory management work?</div>
            <div class="a">Our inventory module tracks spare parts in real-time, manages stock levels across multiple warehouses, automates reorder alerts when stock is low, handles multi-level approval workflows for spare parts requests, and provides detailed usage reports to prevent overstocking or shortages.</div>
          </div>
          <div class="item">
            <div class="q" onclick="toggleFaq(this)">What kind of support do you provide?</div>
            <div class="a">We offer 24/7 customer support via email, chat, and phone. Our support includes onboarding assistance, training sessions, technical troubleshooting, feature guidance, and dedicated account managers for enterprise clients. We also provide comprehensive documentation and video tutorials.</div>
          </div>
          <div class="item">
            <div class="q" onclick="toggleFaq(this)">Can the system integrate with other tools?</div>
            <div class="a">Yes, our platform supports integrations with popular CRM systems, email services, SMS gateways, payment processors, and can connect via REST APIs. We also offer webhooks for custom integrations and work with your team to ensure smooth data flow between systems.</div>
          </div>
          <div class="item">
            <div class="q" onclick="toggleFaq(this)">Is my data secure?</div>
            <div class="a">Security is our top priority. We use enterprise-grade encryption for data in transit and at rest, maintain SOC 2 compliance, perform regular security audits, implement multi-factor authentication, maintain daily backups, and follow industry best practices for data protection.</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- CTA Section -->
  <div class="row">
    <div class="col-12">
      <div class="cta-box fade-in" style="animation-delay: .6s;">
        <div class="container">
          <h3>Ready to Experience the Difference?</h3>
          <p>Join hundreds of organizations that have transformed their complaint management with our platform.</p>
          <a href="{{ route('frontend.register') }}" class="btn btn-light btn-lg me-3">Start Free Trial</a>
          <a href="{{ route('frontend.contact') }}" class="btn btn-outline-light btn-lg">Contact Us</a>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
function toggleFaq(element) {
  const answer = element.nextElementSibling;
  const isOpen = answer.style.display === 'block';
  
  // Close all FAQs
  document.querySelectorAll('.accordion .a').forEach(a => a.style.display = 'none');
  document.querySelectorAll('.accordion .q').forEach(q => q.classList.remove('active'));
  
  // Open clicked FAQ if it was closed
  if (!isOpen) {
    answer.style.display = 'block';
    element.classList.add('active');
  }
}
</script>

@endsection