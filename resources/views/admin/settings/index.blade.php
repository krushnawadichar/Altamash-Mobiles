@extends('layouts.app')

@section('title', 'Settings - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-sliders-h me-2 text-primary"></i> Settings
        </h2>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
            <!-- General Settings -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-store me-2"></i> General Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="shop_name" class="form-label fw-bold">Shop Name</label>
                            <input type="text" class="form-control" id="shop_name" 
                                   name="shop_name" value="{{ $settingsGrouped['general']->where('key', 'shop_name')->first()->value ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label for="shop_address" class="form-label fw-bold">Address</label>
                            <textarea class="form-control" id="shop_address" 
                                      name="shop_address" rows="2">{{ $settingsGrouped['general']->where('key', 'shop_address')->first()->value ?? '' }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="shop_phone" class="form-label fw-bold">Phone</label>
                            <input type="text" class="form-control" id="shop_phone" 
                                   name="shop_phone" value="{{ $settingsGrouped['general']->where('key', 'shop_phone')->first()->value ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label for="shop_email" class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" id="shop_email" 
                                   name="shop_email" value="{{ $settingsGrouped['general']->where('key', 'shop_email')->first()->value ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label for="gst_number" class="form-label fw-bold">GST Number</label>
                            <input type="text" class="form-control" id="gst_number" 
                                   name="gst_number" value="{{ $settingsGrouped['general']->where('key', 'gst_number')->first()->value ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label for="currency" class="form-label fw-bold">Currency</label>
                            <input type="text" class="form-control" id="currency" 
                                   name="currency" value="{{ $settingsGrouped['general']->where('key', 'currency')->first()->value ?? 'PKR' }}">
                        </div>

                        <div class="mb-3">
                            <label for="timezone" class="form-label fw-bold">Timezone</label>
                            <select class="form-select" id="timezone" name="timezone">
                                @foreach(timezone_identifiers_list() as $tz)
                                    <option value="{{ $tz }}" {{ ($settingsGrouped['general']->where('key', 'timezone')->first()->value ?? 'Asia/Karachi') == $tz ? 'selected' : '' }}>
                                        {{ $tz }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice & Tax Settings -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-file-invoice me-2"></i> Invoice & Tax Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="invoice_prefix" class="form-label fw-bold">Invoice Prefix</label>
                            <input type="text" class="form-control" id="invoice_prefix" 
                                   name="invoice_prefix" value="{{ $settingsGrouped['invoice']->where('key', 'invoice_prefix')->first()->value ?? 'INV-' }}">
                        </div>

                        <div class="mb-3">
                            <label for="default_gst" class="form-label fw-bold">Default GST (%)</label>
                            <input type="number" step="0.01" class="form-control" id="default_gst" 
                                   name="default_gst" value="{{ $settingsGrouped['tax']->where('key', 'default_gst')->first()->value ?? '18' }}">
                        </div>
                    </div>
                </div>

                <!-- Backup -->
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-database me-2"></i> Backup
                        </h5>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-warning" onclick="createBackup()">
                            <i class="fas fa-download me-1"></i> Create Backup
                        </button>
                        <button type="button" class="btn btn-info" onclick="restoreBackup()">
                            <i class="fas fa-upload me-1"></i> Restore Backup
                        </button>
                    </div>
                </div>
            </div>

            <!-- Logo Upload -->
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-image me-2"></i> Shop Logo
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(isset($settingsGrouped['general']->where('key', 'shop_logo')->first()->value))
                            <div class="mb-3">
                                <img src="{{ Storage::url($settingsGrouped['general']->where('key', 'shop_logo')->first()->value) }}" 
                                     alt="Shop Logo" style="max-width: 200px;">
                            </div>
                        @endif
                        <input type="file" class="form-control" id="shop_logo" 
                               name="shop_logo" accept="image/*">
                        <small class="text-muted">Upload shop logo (PNG, JPG, JPEG)</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Save Settings
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function createBackup() {
    if (confirm('Are you sure you want to create a backup?')) {
        $.ajax({
            url: '{{ route("admin.settings.backup") }}',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                toastr.success('Backup created successfully!');
                window.location.href = response.download_url;
            },
            error: function() {
                toastr.error('Failed to create backup.');
            }
        });
    }
}

function restoreBackup() {
    if (confirm('Are you sure you want to restore from backup? This will overwrite current data.')) {
        $('#restoreBackupModal').modal('show');
    }
}
</script>
@endpush
@endsection