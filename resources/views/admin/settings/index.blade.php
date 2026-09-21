@extends('layouts.admin')

@section('title', 'System & Shop Settings')
@section('page_title', 'Shop Configuration & Settings')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">Settings</h4>
            <small class="text-muted">Configure store identity, invoice branding, tax rates, and alert thresholds</small>
        </div>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">

            <!-- Shop Identity & Contact -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <span class="fw-bold"><i class="bi bi-shop me-1 text-primary"></i>Shop Identity & Branding</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Shop / Business Name <span class="text-danger">*</span></label>
                            <input type="text" name="shop_name" class="form-control" value="{{ old('shop_name', $settings['shop_name'] ?? 'MobileCare') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Shop Tagline</label>
                            <input type="text" name="shop_tagline" class="form-control" value="{{ old('shop_tagline', $settings['shop_tagline'] ?? 'Sales & Multi-Brand Service Center') }}">
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold small">Phone / Mobile <span class="text-danger">*</span></label>
                                <input type="text" name="shop_phone" class="form-control" value="{{ old('shop_phone', $settings['shop_phone'] ?? '9876543210') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold small">Email Address</label>
                                <input type="email" name="shop_email" class="form-control" value="{{ old('shop_email', $settings['shop_email'] ?? 'contact@mobileshop.com') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">GSTIN Number</label>
                            <input type="text" name="gst_number" class="form-control" placeholder="e.g. 27AAAAA0000A1Z5" value="{{ old('gst_number', $settings['gst_number'] ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Shop Physical Address</label>
                            <textarea name="shop_address" class="form-control" rows="2">{{ old('shop_address', $settings['shop_address'] ?? 'Shop #12, Galaxy Commercial Complex, Station Road') }}</textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold small">Change Shop Logo</label>
                            <input type="file" name="shop_logo" class="form-control" accept="image/*">
                            @if(!empty($settings['shop_logo']))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $settings['shop_logo']) }}" class="rounded border p-1" height="50">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tax, Alerts & Invoice Terms -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <span class="fw-bold"><i class="bi bi-gear-wide-connected me-1 text-primary"></i>Taxes & Inventory Alerts</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold small">Default GST / Tax Rate (%)</label>
                                <input type="number" step="0.01" name="default_tax_percent" class="form-control" value="{{ old('default_tax_percent', $settings['default_tax_percent'] ?? 18) }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold small">Low Stock Alert Threshold</label>
                                <input type="number" name="low_stock_threshold" class="form-control" value="{{ old('low_stock_threshold', $settings['low_stock_threshold'] ?? 5) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Currency Symbol</label>
                            <input type="text" name="currency_symbol" class="form-control" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '₹') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Invoice Terms & Conditions</label>
                            <textarea name="terms_conditions" class="form-control" rows="3">{{ old('terms_conditions', $settings['terms_conditions'] ?? '') }}</textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold small">Invoice Footer Note</label>
                            <input type="text" name="invoice_footer" class="form-control" value="{{ old('invoice_footer', $settings['invoice_footer'] ?? 'Thank you for choosing MobileCare! Visit again.') }}">
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm px-4">
                        <i class="bi bi-check-circle-fill me-1"></i> Save Configuration
                    </button>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection
