@extends('frontend.layouts.app')

@section('title', 'About Us')

@section('content')
  <style>
    :root { --blue:#0d6efd; --blue2:#3b82f6; --slate:#64748b; }
    .page-hero { background: linear-gradient(135deg, var(--blue), var(--blue2)); color:#fff; padding: 70px 20px; border-radius: 12px; margin-bottom: 24px; text-align:center; position:relative; overflow:hidden; }
    .page-hero h1 { font-weight: 800; letter-spacing: -0.02em; }
    .fade-in { animation: fadeInUp .6s ease-out both; }
    @keyframes fadeInUp { from { opacity:0; transform: translateY(12px);} to { opacity:1; transform: translateY(0);} }
    .card-lite { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:20px; transition: box-shadow .3s, transform .3s; }
    .card-lite:hover { box-shadow: 0 12px 24px rgba(0,0,0,.06); transform: translateY(-3px); }
    .section-sub { color:#64748b; }
    .pill { display:inline-block; padding:6px 12px; border-radius:999px; background:#eef2ff; color:#3b82f6; font-weight:600; font-size:.85rem; }
    .timeline { position:relative; padding-left:24px; }
    .timeline::before { content:""; position:absolute; left:8px; top:0; bottom:0; width:2px; background:#e5e7eb; }
    .t-item { position:relative; margin-bottom:18px; }
    /* .t-item::before { content:""; position:absolute; left:-2px; top:6px; width:10px; height:10px; border-radius:50%; background:var(--blue); } */
    .accordion .item { border:1px solid #e5e7eb; border-radius:10px; overflow:hidden; }
    .accordion .q { background:#fff; padding:14px 16px; cursor:pointer; }
    .accordion .a { background:#f8fafc; padding:14px 16px; display:none; color:#334155; }
  </style>

  <section class="page-hero fade-in">
    <span class="pill mb-2">Our Story</span>
    <h1 class="mb-2">About CMS</h1>
    <p class="mb-0">We help operations teams respond faster and resolve smarter with data-driven tools.</p>
  </section>

  <div class="row g-3">
    <div class="col-md-6">
      <div class="card-lite h-100 fade-in" style="animation-delay: .05s;">
        <h4 class="mb-2">Our Mission</h4>
        <p class="section-sub mb-3">Deliver a modern, reliable complaint management platform that scales with your business.</p>
        <ul class="mb-0 text-muted">
          <li class="mb-1">Speed up resolutions with automation and SLAs</li>
          <li class="mb-1">Increase accountability with approvals and logs</li>
          <li class="mb-1">Make better decisions with real-time insights</li>
        </ul>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card-lite h-100 fade-in" style="animation-delay: .1s;">
        <h4 class="mb-2">What We Offer</h4>
        <ul class="mb-0 text-muted">
          <li class="mb-1">End‑to‑end complaint workflow</li>
          <li class="mb-1">Employee management and leaves</li>
          <li class="mb-1">Inventory-aware approvals</li>
          <li class="mb-1">Reports and analytics</li>
        </ul>
      </div>
    </div>
  </div>

  <div class="row g-3 mt-1">
    <div class="col-md-6">
      <div class="card-lite h-100 fade-in" style="animation-delay: .15s;">
        <h4 class="mb-2">Our Values</h4>
        <p class="section-sub">We obsess over reliability, clarity, and speed.</p>
        <ul class="mb-0 text-muted">
          <li class="mb-1">Transparency over guesswork</li>
          <li class="mb-1">Automation over repetition</li>
          <li class="mb-1">Data over assumptions</li>
        </ul>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card-lite h-100 fade-in" style="animation-delay: .2s;">
        <h4 class="mb-2">Journey</h4>
        <div class="timeline">
          <div class="t-item"><strong>2019</strong> — Prototype for internal ops</div>
          <div class="t-item"><strong>2021</strong> — Public beta with first clients</div>
          <div class="t-item"><strong>2023</strong> — Advanced approvals & SLA suite</div>
          <div class="t-item"><strong>2025</strong> — Real-time insights and exports</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mt-1">
    <div class="col-md-12">
      <div class="card-lite fade-in" style="animation-delay: .25s;">
        <h4 class="mb-2">FAQs</h4>
        <div class="accordion" id="faq">
          <div class="item mb-2">
            <div class="q" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display==='block'?'none':'block'">How do SLAs work?</div>
            <div class="a">Define response and resolution targets per category; dashboards show breaches and trends.</div>
          </div>
          <div class="item mb-2">
            <div class="q" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display==='block'?'none':'block'">Can I export data?</div>
            <div class="a">Yes, generate printable views and CSV exports from the Reports section.</div>
          </div>
          <div class="item">
            <div class="q" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display==='block'?'none':'block'">Is there role-based access?</div>
            <div class="a">Roles and permissions restrict who can view, edit, approve, and export data.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection


