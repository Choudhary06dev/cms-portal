@extends('frontend.layouts.app')

@section('title', 'Contact')

@section('content')
    <style>
        :root {
            --navy-primary: #003366;
            --navy-dark: #001f3f;
            --navy-light: #004d99;
            --navy-accent: #0066cc;
            --blue: var(--navy-primary);
            --blue2: var(--navy-light);
            --dark: #1a1a1a;
        }

        /* Hero Section */
        .contact-hero {
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy-primary) 50%, var(--navy-light) 100%);
            color: #fff;
            padding: 100px 20px;
            border-radius: 0;
            margin-bottom: 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .contact-hero::before {
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

        .contact-hero .container {
            position: relative;
            z-index: 1;
        }

        .contact-hero h1 {
            font-weight: 800;
            font-size: 3.5rem;
            text-shadow: 0 2px 20px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
        }

        .contact-hero p {
            font-size: 1.25rem;
            max-width: 700px;
            margin: 0 auto;
            opacity: 0.95;
        }

        .pill {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: #fff;
            font-weight: 600;
            font-size: .9rem;
            margin-bottom: 15px;
        }

        /* Contact Cards */
        .contact-card {
            background: #fff;
            border: none;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
        }

        .contact-card:hover {
            box-shadow: 0 15px 40px rgba(13, 110, 253, 0.15);
            transform: translateY(-5px);
        }

        .contact-card h5 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 25px;
        }

        /* Form Styles */
        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .required::after {
            content: ' *';
            color: #dc3545;
        }

        .form-control,
        .form-select {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        }

        .btn-primary {
            padding: 14px 32px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: none;
            background: linear-gradient(135deg, var(--blue), var(--blue2));
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(13, 110, 253, 0.3);
        }

        /* Info Cards */
        .info-card {
            background: #fff;
            border: none;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            text-align: center;
            height: 100%;
        }

        .info-card:hover {
            box-shadow: 0 10px 30px rgba(13, 110, 253, 0.12);
            transform: translateY(-5px);
        }

        .info-icon {
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

        .info-card h6 {
            font-weight: 700;
            color: var(--dark);
            font-size: 1.1rem;
            margin-bottom: 12px;
        }

        .info-card p {
            color: #64748b;
            margin: 0;
            line-height: 1.6;
        }

        .info-card a {
            color: var(--blue);
            text-decoration: none;
            font-weight: 600;
        }

        .info-card a:hover {
            text-decoration: underline;
        }

        /* Contact Details */
        .contact-details {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-top: 25px;
        }

        .detail-item {
            display: flex;
            align-items: start;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .detail-icon {
            width: 45px;
            height: 45px;
            background: var(--blue);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
            font-size: 1.2rem;
        }

        .detail-content h6 {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 5px;
            font-size: 1rem;
        }

        .detail-content p {
            color: #64748b;
            margin: 0;
            line-height: 1.6;
        }

        .detail-content a {
            color: var(--blue);
            text-decoration: none;
            font-weight: 600;
        }

        /* Social Links */
        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }

        .social-link {
            width: 50px;
            height: 50px;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blue);
            font-size: 1.3rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-link:hover {
            background: var(--blue);
            color: white;
            border-color: var(--blue);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }

        /* Map Section */
        .map-container {
            background: #f8f9fa;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .map-container iframe {
            width: 100%;
            height: 400px;
            border: none;
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

        /* Animations */
        .fade-in {
            animation: fadeInUp 0.6s ease-out both;
        }

        .fade-in.d2 {
            animation-delay: 0.1s;
        }

        .fade-in.d3 {
            animation-delay: 0.2s;
        }

        .fade-in.d4 {
            animation-delay: 0.3s;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Alert Styles */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 16px 20px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .contact-hero h1 {
                font-size: 2.2rem;
            }

            .contact-hero p {
                font-size: 1rem;
            }

            .contact-card {
                padding: 25px;
            }

            .section-heading {
                font-size: 1.8rem;
            }
        }
    </style>

    <!-- Hero Section -->
    <section class="contact-hero fade-in">
        <div class="container">
            <span class="pill">Get in Touch</span>
            <h1>Contact Us</h1>
            <p>Have questions about CMS? Our team is here to help. Send us a message and we'll get back to you within 24
                hours.</p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container py-5">

        <!-- Contact Form & Details -->
        <div class="row g-4 mb-5">
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="contact-card fade-in">
                    <h5>Send us a Message</h5>

                    @if (session('status'))
                        <div class="alert alert-success mb-4">
                            <strong>✓ Success!</strong> {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="#">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Your Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="you@example.com"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+92 300 0000000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company" class="form-control" placeholder="Your Company">
                            </div>
                            <div class="col-12">
                                <label class="form-label required">Subject</label>
                                <select name="subject" class="form-select" required>
                                    <option value="">Select a subject</option>
                                    <option value="general">General Inquiry</option>
                                    <option value="sales">Sales & Pricing</option>
                                    <option value="support">Technical Support</option>
                                    <option value="demo">Request a Demo</option>
                                    <option value="partnership">Partnership Opportunities</option>
                                    <option value="feedback">Feedback & Suggestions</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label required">Message</label>
                                <textarea name="message" rows="5" class="form-control" placeholder="Tell us how we can help you..." required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <span>Send Message</span> →
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="col-lg-5">
                <div class="contact-card fade-in d2">
                    <h5>Contact Information</h5>
                    <p class="text-muted mb-4">Reach out to us through any of these channels. We're here to assist you.</p>

                    <div class="contact-details">
                        <div class="detail-item">
                            <div class="detail-icon">📧</div>
                            <div class="detail-content">
                                <h6>Email</h6>
                                {{-- <p><a href="mailto:info@cmsplatform.com">info@cmsplatform.com</a></p> --}}
                                <p><a href="mailto:support@cmsplatform.com">info@nexertechsolutions.com</a></p>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-icon">📱</div>
                            <div class="detail-content">
                                <h6>Phone</h6>
                                {{-- <p><a href="tel:+923000000000">+92 300 0000000</a></p> --}}
                                <p><a href="tel: +92 300 7924806">+ +92 300 7924806</a></p>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-icon">📍</div>
                            <div class="detail-content">
                                <h6>Office Address</h6>
                                <p>Lahore, Pakistan<br>
                                    Office#915 High,Q Tower Gulberg V, jail Road.</p>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-icon">⏰</div>
                            <div class="detail-content">
                                <h6>Business Hours</h6>
                                <p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM<br>Sunday: Closed</p>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">

                    <!-- Social Links -->
                    <h6 class="mb-3">Follow Us</h6>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#" class="text-primary">
                            <i class="bi bi-facebook" style="font-size: 1.5rem;"></i>
                        </a>
                        <a href="#" class="text-info">
                            <i class="bi bi-twitter" style="font-size: 1.5rem;"></i>
                        </a>
                        <a href="#" class="text-danger">
                            <i class="bi bi-youtube" style="font-size: 1.5rem;"></i>
                        </a>
                        <a href="#" class="text-primary">
                            <i class="bi bi-linkedin" style="font-size: 1.5rem;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Contact Cards -->
        <div class="text-center mb-5">
            <h2 class="section-heading fade-in">Quick Contact</h2>
            <p class="section-description fade-in">Choose the best way to reach us based on your needs</p>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4 fade-in">
                <div class="info-card">
                    <div class="info-icon">💬</div>
                    <h6>Live Chat</h6>
                    <p class="mb-3">Get instant answers from our support team. Available 24/7.</p>
                    <a href="#" class="btn btn-sm btn-outline-primary">Start Chat</a>
                </div>
            </div>

            <div class="col-md-4 fade-in d2">
                <div class="info-card">
                    <div class="info-icon">📞</div>
                    <h6>Schedule a Call</h6>
                    <p class="mb-3">Book a convenient time to speak with our experts.</p>
                    <a href="#" class="btn btn-sm btn-outline-primary">Book Now</a>
                </div>
            </div>

            <div class="col-md-4 fade-in d3">
                <div class="info-card">
                    <div class="info-icon">🎯</div>
                    <h6>Request Demo</h6>
                    <p class="mb-3">See our platform in action with a personalized demo.</p>
                    <a href="#" class="btn btn-sm btn-outline-primary">Get Demo</a>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="text-center mb-4">
            <h2 class="section-heading fade-in">Find Us</h2>
            <p class="section-description fade-in">Visit our office in Lahore, Pakistan</p>
        </div>

        <div class="map-container fade-in">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3402.038834841451!2d74.35265931512392!3d31.51965998138596!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39190483e58107d9%3A0xc23abe6ccc7e2462!2sGulberg%20III%2C%20Lahore%2C%20Punjab%2C%20Pakistan!5e0!3m2!1sen!2s!4v1234567890123!5m2!1sen!2s"
                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

        <!-- FAQ Section -->
        <div class="text-center my-5">
            <h2 class="section-heading fade-in">Need Help?</h2>
            <p class="section-description fade-in">Check out our <a href="{{ route('frontend.about') }}#faq">FAQ
                    section</a> or <a href="#">Help Center</a> for quick answers to common questions.</p>
        </div>

    </div>

@endsection
