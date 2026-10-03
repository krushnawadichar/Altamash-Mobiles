@extends('layouts.public')

@section('title', 'Checkout - ALTAMASH MOBILE')

@section('content')
<div class="bg-light py-4 border-bottom mb-5">
    <div class="container">
        <h1 class="fw-bold mb-0">Checkout</h1>
    </div>
</div>

<div class="container mb-5 pb-5">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
        @csrf
        <div class="row g-5">
            <!-- Billing Details -->
            <div class="col-lg-7">
                <h4 class="fw-bold mb-4">Billing & Shipping Details</h4>
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Full Address <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control" rows="3" required>{{ old('address') }}</textarea>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control" value="{{ old('city') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">State <span class="text-danger">*</span></label>
                                <input type="text" name="state" class="form-control" value="{{ old('state') }}" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary & Payment -->
            <div class="col-lg-5">
                <h4 class="fw-bold mb-4">Your Order</h4>
                <div class="card border-0 shadow-sm rounded-4 bg-light mb-4">
                    <div class="card-body p-4">
                        <div class="table-responsive mb-3">
                            <table class="table table-borderless table-sm mb-0">
                                <thead class="border-bottom text-muted">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart as $item)
                                    <tr>
                                        <td class="py-2">{{ $item['name'] }} <strong class="text-muted">x {{ $item['quantity'] }}</strong></td>
                                        <td class="text-end py-2">₹{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="border-top">
                                    <tr>
                                        <th class="pt-3">Total</th>
                                        <th class="text-end pt-3 text-primary fs-5">₹{{ number_format($total, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <input type="hidden" name="total_amount" id="total_amount" value="{{ $total }}">
                    </div>
                </div>

                <h4 class="fw-bold mb-4">Payment Method</h4>
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="form-check mb-3 p-3 border rounded">
                            <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="payment_cod" value="Cash on Delivery" checked>
                            <label class="form-check-label fw-bold d-block w-100" for="payment_cod">
                                Cash on Delivery (COD)
                                <div class="text-muted small fw-normal mt-1">Pay with cash upon delivery.</div>
                            </label>
                        </div>
                        
                        <div class="form-check p-3 border rounded">
                            <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="payment_razorpay" value="Razorpay">
                            <label class="form-check-label fw-bold d-block w-100" for="payment_razorpay">
                                Online Payment (Razorpay)
                                <div class="text-muted small fw-normal mt-1">Pay securely via Credit/Debit Card, UPI, or NetBanking.</div>
                                <div class="mt-2"><img src="https://razorpay.com/assets/razorpay-logo.svg" alt="Razorpay" style="height: 20px;"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <button type="button" id="pay-btn" class="btn btn-primary-custom w-100 py-3 fw-bold fs-5">Place Order</button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.getElementById('pay-btn').onclick = function(e){
        e.preventDefault();
        
        // Validate form
        const form = document.getElementById('checkout-form');
        if(!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const totalAmount = parseFloat(document.getElementById('total_amount').value);
        
        if (paymentMethod === 'Razorpay') {
            const razorpayKey = "{{ config('services.razorpay.key') }}";
            
            if (!razorpayKey) {
                alert("Razorpay is not configured properly. Missing Key ID.");
                return;
            }

            // Calculate amount in paise
            let rzpAmount = totalAmount * 100;
            
            // Razorpay test accounts have a strict max limit. 
            // If using a test key, cap it at a small amount (e.g. ₹100) for testing purposes
            // to prevent "Amount exceeds maximum amount allowed" errors.
            if (razorpayKey.startsWith('rzp_test_')) {
                rzpAmount = Math.min(rzpAmount, 10000); // Cap at ₹100 INR (10000 paise) for testing
            }

            var options = {
                "key": razorpayKey, 
                "amount": rzpAmount.toFixed(0), 
                "currency": "INR",
                "name": "ALTAMASH MOBILE",
                "description": "Order Payment",
                "image": "https://razorpay.com/assets/razorpay-logo.svg",
                "handler": function (response){
                    var input = document.createElement("input");
                    input.setAttribute("type", "hidden");
                    input.setAttribute("name", "razorpay_payment_id");
                    input.setAttribute("value", response.razorpay_payment_id);
                    form.appendChild(input);

                    const btn = document.getElementById('pay-btn');
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Processing Payment...';
                    btn.disabled = true;

                    form.submit();
                },
                "prefill": {
                    "name": document.querySelector('input[name="name"]').value,
                    "email": document.querySelector('input[name="email"]').value,
                    "contact": document.querySelector('input[name="phone"]').value
                },
                "theme": {
                    "color": "#0d6efd"
                }
            };
            var rzp1 = new Razorpay(options);
            rzp1.on('payment.failed', function (response){
                alert("Payment Failed: " + response.error.description);
            });
            rzp1.open();
            
        } else {
            // Submit form for COD
            const btn = document.getElementById('pay-btn');
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Processing...';
            btn.disabled = true;
            form.submit();
        }
    };
</script>
@endpush
@endsection


