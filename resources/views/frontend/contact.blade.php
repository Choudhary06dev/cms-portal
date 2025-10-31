@extends('frontend.layouts.app')

@section('title', 'Contact')

@section('content')
  <style>
    .contact-hero {
      background: linear-gradient(135deg, #0d6efd, #3b82f6);
      color: #fff;
      padding: 60px 20px;
      border-radius: 12px;
      margin-bottom: 24px;
      text-align: center;
    }
    .contact-hero h1 { font-weight: 700; }
    .contact-card { transition: box-shadow 0.3s ease, transform 0.3s ease; }
    .contact-card:hover { box-shadow: 0 8px 20px rgba(0,0,0,0.08); transform: translateY(-3px); }
    .required::after { content: ' *'; color: #dc3545; }
    .info-item { margin-bottom: 8px; }
  </style>

  <section class="contact-hero">
    <h1 class="mb-2">Contact Us</h1>
    <p class="mb-0">Have questions about CMS? Send us a message and we’ll get back to you.</p>
  </section>

  <div class="row g-3">
    <div class="col-lg-6">
      <div class="p-4 bg-white rounded shadow-sm h-100 contact-card">
        @if(session('status'))
          <div class="alert alert-success mb-3">{{ session('status') }}</div>
        @endif
        <form method="POST" action="#">
          @csrf
          <div class="mb-3">
            <label class="form-label required">Your Name</label>
            <input type="text" name="name" class="form-control" placeholder="John Doe" required>
          </div>
          <div class="mb-3">
            <label class="form-label required">Email</label>
            <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
          </div>
          <div class="mb-3">
            <label class="form-label required">Message</label>
            <textarea name="message" rows="5" class="form-control" placeholder="How can we help you?" required></textarea>
          </div>
          <button class="btn btn-primary">Send Message</button>
        </form>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="p-4 bg-white rounded shadow-sm h-100 contact-card">
        <h5 class="mb-3">Company</h5>
        <div class="info-item">Email: <a href="mailto:info@example.com">info@example.com</a></div>
        <div class="info-item">Phone: +92 300 0000000</div>
        <div class="info-item mb-0">Address: 123 Main Street, Lahore</div>
      </div>
    </div>
  </div>
@endsection


