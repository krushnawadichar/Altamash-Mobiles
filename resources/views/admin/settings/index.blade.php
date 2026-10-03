@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h2 class="fw-bold m-0"><i class="fa-solid fa-cog text-primary me-2"></i> Store Settings</h2>
        <button type="submit" form="settingsForm" class="btn btn-primary px-4"><i class="fa-solid fa-save me-2"></i> Save Changes</button>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <!-- Settings Nav -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-0">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active text-start py-3 px-4 border-bottom rounded-0 fw-bold" id="v-pills-general-tab" data-bs-toggle="pill" data-bs-target="#v-pills-general" type="button" role="tab">
                        <i class="fa-solid fa-store me-2"></i> General Info
                    </button>
                    <button class="nav-link text-start py-3 px-4 border-bottom rounded-0 fw-bold" id="v-pills-contact-tab" data-bs-toggle="pill" data-bs-target="#v-pills-contact" type="button" role="tab">
                        <i class="fa-solid fa-address-book me-2"></i> Contact Details
                    </button>
                    <button class="nav-link text-start py-3 px-4 border-bottom rounded-0 fw-bold" id="v-pills-currency-tab" data-bs-toggle="pill" data-bs-target="#v-pills-currency" type="button" role="tab">
                        <i class="fa-solid fa-money-bill-wave me-2"></i> Currency & Tax
                    </button>
                    <button class="nav-link text-start py-3 px-4 rounded-0 fw-bold" id="v-pills-api-tab" data-bs-toggle="pill" data-bs-target="#v-pills-api" type="button" role="tab">
                        <i class="fa-solid fa-code me-2"></i> API Integrations
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <form action="#" method="POST" id="settingsForm" onsubmit="event.preventDefault(); alert('Settings saved successfully! (Demo mode)');">
            @csrf
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-lg-5">
                    <div class="tab-content" id="v-pills-tabContent">
                        
                        <!-- General Info Tab -->
                        <div class="tab-pane fade show active" id="v-pills-general" role="tabpanel">
                            <h4 class="fw-bold mb-4 border-bottom pb-2">General Information</h4>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Store Name</label>
                                <input type="text" class="form-control" name="store_name" value="ALTAMASH MOBILE">
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Tagline / Slogan</label>
                                <input type="text" class="form-control" name="store_tagline" value="Your trusted electronics partner">
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Store Logo (Light)</label>
                                    <input type="file" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Store Logo (Dark)</label>
                                    <input type="file" class="form-control">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">About Store</label>
                                <textarea class="form-control" rows="4">We are a premium electronics retail store providing the best smartphones, laptops, and home appliances.</textarea>
                            </div>
                        </div>

                        <!-- Contact Details Tab -->
                        <div class="tab-pane fade" id="v-pills-contact" role="tabpanel">
                            <h4 class="fw-bold mb-4 border-bottom pb-2">Contact Details</h4>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Primary Email</label>
                                    <input type="email" class="form-control" value="support@gmail.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Support Phone</label>
                                    <input type="text" class="form-control" value="+91 98765 43210">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Physical Address</label>
                                <textarea class="form-control" rows="3">123 Tech Park Avenue, Cyber City, Bangalore, India 560001</textarea>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">WhatsApp Number</label>
                                    <input type="text" class="form-control" value="+91 98765 43210">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Support Hours</label>
                                    <input type="text" class="form-control" value="Mon - Sat (10:00 AM - 9:00 PM)">
                                </div>
                            </div>
                        </div>

                        <!-- Currency & Tax Tab -->
                        <div class="tab-pane fade" id="v-pills-currency" role="tabpanel">
                            <h4 class="fw-bold mb-4 border-bottom pb-2">Currency & Tax Configurations</h4>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Base Currency</label>
                                    <select class="form-select">
                                        <option value="INR" selected>INR (₹) - Indian Rupee</option>
                                        <option value="USD">USD ($) - US Dollar</option>
                                        <option value="EUR">EUR (€) - Euro</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Currency Position</label>
                                    <select class="form-select">
                                        <option value="left" selected>Left (₹100)</option>
                                        <option value="right">Right (100₹)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Default Tax Rate (%)</label>
                                <input type="number" class="form-control" value="18" step="0.01">
                                <div class="form-text">This tax rate will be applied by default if a product has no specific tax assigned.</div>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="taxIncluded" checked>
                                <label class="form-check-label fw-bold" for="taxIncluded">Prices entered include tax</label>
                            </div>
                        </div>

                        <!-- API Integrations Tab -->
                        <div class="tab-pane fade" id="v-pills-api" role="tabpanel">
                            <h4 class="fw-bold mb-4 border-bottom pb-2">API Integrations</h4>
                            
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-body">
                                    <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-credit-card me-2"></i> Razorpay Payment Gateway</h5>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Razorpay Key ID</label>
                                        <input type="text" class="form-control" value="rzp_test_xxxxxx">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Razorpay Key Secret</label>
                                        <input type="password" class="form-control" value="xxxxxxxxxxxxxxxxx">
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="razorpayEnable" checked>
                                        <label class="form-check-label fw-bold" for="razorpayEnable">Enable Razorpay Checkout</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .nav-pills .nav-link {
        color: #4b5563;
        transition: all 0.2s;
    }
    .nav-pills .nav-link:hover {
        background-color: #f3f4f6;
    }
    .nav-pills .nav-link.active {
        background-color: #f8f9fa;
        color: var(--primary-color);
        border-right: 4px solid var(--primary-color) !important;
    }
</style>
@endpush
@endsection


