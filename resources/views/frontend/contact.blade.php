@extends('frontend.layouts.app')

@section('title', 'Contact')

@section('content')
  <div class="row g-3">
    <div class="col-lg-6">
      <div class="p-4 bg-white rounded shadow-sm h-100">
        <h1 class="mb-3">Contact</h1>
        <p class="text-muted">Have questions about CMS? Send us a message and we’ll get back to you.</p>
        <form method="POST" action="#">
          @csrf
          <div class="mb-3">
            <label class="form-label">Your Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea name="message" rows="4" class="form-control" required></textarea>
          </div>
          <button class="btn btn-primary">Send Message</button>
        </form>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="p-4 bg-white rounded shadow-sm h-100">
        <h5 class="mb-3">Company</h5>
        <p class="mb-1">Email: <a href="mailto:info@example.com">info@example.com</a></p>
        <p class="mb-1">Phone: +92 300 0000000</p>
        <p class="mb-0">Address: 123 Main Street, Lahore</p>
      </div>
    </div>
  </div>
@endsection


