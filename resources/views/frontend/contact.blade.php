@extends('layouts.public')

@section('title', 'Contact Us - ALTAMASH MOBILE')

@section('content')
<div class="bg-light py-4 border-bottom mb-5">
    <div class="container">
        <h1 class="fw-bold mb-0">Contact Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5 pb-5">
    <div class="row g-5">
        <!-- Contact Info -->
        <div class="col-lg-5">
            <h2 class="fw-bold mb-4">Get In Touch</h2>
            <p class="text-muted mb-5">Have questions about our products, need a repair, or want to check on an order? Our team is here to help you.</p>
            
            <div class="d-flex mb-4">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                    <i class="fa-solid fa-location-dot fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Our Location</h5>
                    <p class="text-muted mb-0">New narsala road, <br>opposite Dhanashree apartment<br>Narsala, Nagpur, Maharashtra, 440034.</p>
                </div>
            </div>

            <div class="d-flex mb-4">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                    <i class="fa-solid fa-phone fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Phone Number</h5>
                    <p class="text-muted mb-0">9226087986</p>
                </div>
            </div>

            <div class="d-flex mb-4">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                    <i class="fa-solid fa-envelope fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Email Address</h5>
                    <p class="text-muted mb-0">Altamashmobiles01@gmail.com<br>support@gmail.com</p>
                </div>
            </div>
            
            <div class="d-flex mb-4">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                    <i class="fa-solid fa-clock fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Working Hours</h5>
                    <p class="text-muted mb-0">Monday - Sunday<br>10:00 AM - 9:00 PM</p>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                <h3 class="fw-bold mb-4">Send us a Message</h3>
                <form action="{{ route('contact.submit') }}" method="POST">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Your Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()?->name) }}" required placeholder="John Doe">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Your Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()?->email) }}" required placeholder="john@example.com">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <select class="form-select" name="subject" required>
                            <option value="">Select a subject...</option>
                            <option value="General Inquiry" {{ old('subject') == 'General Inquiry' ? 'selected' : '' }}>General Inquiry</option>
                            <option value="Product Question" {{ old('subject') == 'Product Question' ? 'selected' : '' }}>Product Question</option>
                            <option value="Repair Service" {{ old('subject') == 'Repair Service' ? 'selected' : '' }}>Repair Service</option>
                            <option value="Order Status" {{ old('subject') == 'Order Status' ? 'selected' : '' }}>Order Status</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="message" rows="5" required placeholder="How can we help you?">{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary-custom btn-lg w-100 fw-bold py-3"><i class="fa-solid fa-paper-plane me-2"></i> Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


